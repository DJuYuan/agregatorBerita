<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Image;
use App\Models\LogFailed;
use App\Models\LogSuccess;
use App\Models\Source;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessRssSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Source $source;

    /**
     * Waktu maksimal job boleh dieksekusi (timeout)
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("  [QUEUE] ━━━ Memulai Proses: {$this->source->name} ━━━");

        try {
            // Ambil User Agent dari sistem (atau gunakan default)
            $userAgent = \App\Models\SystemSetting::where('key', 'crawler_user_agent')
                        ->value('value') ?? 'MuaraJogja-Bot/1.0';

            // Eksekusi HTTP — Unduh dokumen XML
            $response = Http::withOptions(['verify' => false])
                ->timeout(30)
                ->withHeaders([
                    'User-Agent' => $userAgent,
                ])
                ->get($this->source->rss_url);

            if ($response->failed()) {
                throw new \Exception("HTTP Error: Status {$response->status()}");
            }

            $xmlContent = $response->body();

            // SANITASI XML
            $xmlContent = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xmlContent);

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent);

            if ($xml === false) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                $errorMsg = !empty($errors) ? $errors[0]->message : 'Format XML tidak valid';
                throw new \Exception("XML Parse Error: " . trim($errorMsg));
            }

            $items = $xml->channel->item ?? [];
            $totalItems = count($items);

            if ($totalItems === 0) {
                Log::warning("  [QUEUE] Tidak ada item berita ditemukan untuk {$this->source->name}.");
                return;
            }

            $fetchedCount = 0;

            foreach ($items as $item) {
                // Validasi GUID
                $guid = (string) ($item->guid ?? $item->link);
                if (empty($guid)) continue;

                $guidExists = Article::where('guid', $guid)->exists();
                if ($guidExists) continue;

                $title       = (string) $item->title;
                $link        = (string) $item->link;
                $description = (string) ($item->description ?? '');
                $pubDate     = (string) ($item->pubDate ?? '');

                if (empty($title) || empty($link)) continue;

                // Pembersihan HTML
                $description = strip_tags($description);
                $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                $description = trim($description);

                // Buat slug unik
                $slug = Str::slug($title);
                $originalSlug = $slug;
                $counter = 1;
                while (Article::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                // Tanggal Publikasi
                $publishedAt = null;
                if (!empty($pubDate)) {
                    try {
                        $publishedAt = \Carbon\Carbon::parse($pubDate);
                    } catch (\Exception $e) {
                        $publishedAt = null;
                    }
                }

                // Ekstrak tag dari RSS <category>
                $rssCategories = [];
                if (isset($item->category)) {
                    foreach ($item->category as $cat) {
                        $catStr = trim((string) $cat);
                        if (!empty($catStr)) {
                            $rssCategories[] = $catStr;
                        }
                    }
                }

                // Simpan Atomik (Transaksi DB)
                DB::transaction(function () use (
                    $guid, $title, $slug, $link,
                    $description, $publishedAt, $item, $rssCategories
                ) {
                    $article = Article::create([
                        'source_id'    => $this->source->id,
                        'guid'         => $guid,
                        'title'        => Str::limit($title, 255, ''),
                        'slug'         => Str::limit($slug, 255, ''),
                        'link'         => $link,
                        'description'  => $description ?: null,
                        'published_at' => $publishedAt,
                    ]);

                    // Otomatisasi Tags
                    $tagIds = [];
                    foreach ($rssCategories as $catName) {
                        $tagSlug = Str::slug($catName);
                        if (!empty($tagSlug)) {
                            $tag = \App\Models\Tag::firstOrCreate(
                                ['slug' => $tagSlug],
                                ['name' => $catName]
                            );
                            $tagIds[] = $tag->id;
                        }
                    }
                    if (!empty($tagIds)) {
                        $article->tags()->sync($tagIds);
                    }

                    $imageUrls = $this->extractImageUrls($item);
                    foreach ($imageUrls as $imageUrl) {
                        Image::create([
                            'article_id' => $article->id,
                            'image_url'  => $imageUrl,
                        ]);
                    }
                });

                $fetchedCount++;
            }

            // Update timestamp
            $this->source->update(['last_fetched_at' => now()]);

            // Catat log sukses
            LogSuccess::create([
                'source_id'     => $this->source->id,
                'total_fetched' => $fetchedCount,
                'fetched_at'    => now(),
            ]);

            // Catat Statistik Harian
            $today = now()->toDateString();
            $dailyStat = \App\Models\SourceDailyStat::firstOrCreate(
                ['source_id' => $this->source->id, 'date' => $today]
            );
            $dailyStat->increment('total_articles', $fetchedCount);

            Log::info("  [QUEUE] ✔ Sukses: {$this->source->name} (Tersimpan: {$fetchedCount})");

        } catch (\Exception $e) {
            // Catat log gagal
            LogFailed::create([
                'source_id'     => $this->source->id,
                'error_message' => $e->getMessage(),
                'failed_at'     => now(),
            ]);

            // Catat Statistik Harian (Error)
            $today = now()->toDateString();
            $dailyStat = \App\Models\SourceDailyStat::firstOrCreate(
                ['source_id' => $this->source->id, 'date' => $today]
            );
            $dailyStat->increment('total_errors');

            Log::error("  [QUEUE] ✘ Gagal: {$this->source->name} -> {$e->getMessage()}");
        } finally {
            // =========================================================
            // PENGUJIAN MEMORI: Catat konsumsi RAM di akhir setiap Job
            // Membuktikan Worker stabil dan bebas kebocoran memori (Memory Leak)
            // =========================================================
            $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
            Log::debug("  [QUEUE RAM MONITOR] Konsumsi Akhir Memori untuk {$this->source->name}: {$memoryUsage} MB");
        }
    }

    /**
     * Ekstraksi URL gambar dari item RSS.
     */
    private function extractImageUrls(\SimpleXMLElement $item): array
    {
        $imageUrls = [];

        // 1. Cek <enclosure>
        if (isset($item->enclosure)) {
            $enclosureUrl  = (string) $item->enclosure['url'];
            $enclosureType = (string) $item->enclosure['type'];
            if (!empty($enclosureUrl) && str_starts_with($enclosureType, 'image/')) {
                $imageUrls[] = $enclosureUrl;
            }
        }

        // 2. Cek <media:content>
        $mediaNamespaces = ['media', 'Media'];
        foreach ($mediaNamespaces as $ns) {
            $mediaContent = $item->children($ns, true);
            if (isset($mediaContent->content)) {
                $mediaUrl = (string) $mediaContent->content->attributes()->url;
                if (empty($mediaUrl)) {
                    $mediaUrl = (string) $mediaContent->content['url'];
                }
                if (!empty($mediaUrl)) {
                    $imageUrls[] = $mediaUrl;
                }
            }
        }

        // 3. Regex tag img
        if (isset($item->description)) {
            preg_match_all('/<img[^>]+src=([\'"])?((?(1)[^\1]*|[^\s>]+))(?(1)\1)/', (string)$item->description, $matches);
            if (!empty($matches[2])) {
                foreach ($matches[2] as $src) {
                    $imageUrls[] = $src;
                }
            }
        }

        return array_unique(array_filter($imageUrls));
    }
}
