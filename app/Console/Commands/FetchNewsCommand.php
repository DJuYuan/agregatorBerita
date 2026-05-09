<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Image;
use App\Models\LogFailed;
use App\Models\LogSuccess;
use App\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     */
    protected $description = 'Mengambil berita dari semua sumber RSS yang aktif';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // =====================================================
        // LANGKAH 1: Tarik semua sumber yang aktif (Normal Hierarchy)
        // Mengeksekusi sesuai urutan di database (Opsi C).
        // Sumber pertama akan menyedot semua berita global Harian Jogja.
        // =====================================================
        $sources = Source::where('is_active', true)->get();

        if ($sources->isEmpty()) {
            $this->warn('Tidak ada sumber RSS aktif yang ditemukan.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$sources->count()} sumber aktif.");

        foreach ($sources as $source) {
            $this->newLine();
            $this->info("━━━ Memproses: {$source->name} ━━━");
            $this->line("URL: {$source->rss_url}");

            try {
                // =====================================================
                // LANGKAH 2: Eksekusi HTTP — Unduh dokumen XML
                // =====================================================
                $response = Http::withOptions(['verify' => false])
                    ->timeout(30)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    ])
                    ->get($source->rss_url);

                if ($response->failed()) {
                    throw new \Exception("HTTP Error: Status {$response->status()}");
                }

                $xmlContent = $response->body();

                // SANITASI XML: Mengganti '&' yang tidak memiliki format entity menjadi '&amp;'
                // Ini mencegah error "EntityRef: expecting ';'" pada simplexml_load_string
                $xmlContent = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xmlContent);

                // =====================================================
                // LANGKAH 3: Pecah XML — Parse menggunakan simplexml
                // =====================================================
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($xmlContent);

                if ($xml === false) {
                    $errors = libxml_get_errors();
                    libxml_clear_errors();
                    $errorMsg = !empty($errors) ? $errors[0]->message : 'Format XML tidak valid';
                    throw new \Exception("XML Parse Error: " . trim($errorMsg));
                }

                // Cari node item (RSS 2.0: channel->item)
                $items = $xml->channel->item ?? [];
                $totalItems = count($items);

                if ($totalItems === 0) {
                    $this->warn("  Tidak ada item berita ditemukan.");
                    continue;
                }

                $this->line("  Ditemukan {$totalItems} item berita.");

                $fetchedCount = 0;

                foreach ($items as $item) {
                    // =====================================================
                    // LANGKAH 4: Validasi Duplikasi — Cek GUID
                    // =====================================================
                    $guid = (string) ($item->guid ?? $item->link);

                    if (empty($guid)) {
                        $this->warn("  ⚠ Item tanpa GUID/link, dilewati.");
                        continue;
                    }

                    // Jika GUID sudah ada, lewati (skip)
                    $exists = Article::where('guid', $guid)->exists();
                    if ($exists) {
                        continue;
                    }

                    // Siapkan data artikel
                    $title       = (string) ($item->title ?? 'Tanpa Judul');
                    $link        = (string) ($item->link ?? '');
                    $description = (string) ($item->description ?? '');
                    $pubDate     = (string) ($item->pubDate ?? '');

                    // =====================================================
                    // LANGKAH 4 & 5: Pembersihan + Konstruksi Snippet Cerdas
                    // =====================================================
                    // Lapis 1: Bersihkan tag HTML dan decode entity HTML
                    $description = strip_tags($description);
                    $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                    $description = trim($description);

                    // Lapis 2 + Konstruksi Dinamis: Jalankan mesin snippet cerdas
                    $description = $this->buildSmartSnippet($description);

                    // Buat slug unik dari judul
                    $slug = Str::slug($title);
                    $slug = $this->makeUniqueSlug($slug);

                    // Parse tanggal publikasi
                    $publishedAt = null;
                    if (!empty($pubDate)) {
                        try {
                            $publishedAt = \Carbon\Carbon::parse($pubDate);
                        } catch (\Exception $e) {
                            $publishedAt = null;
                        }
                    }

                    // =====================================================
                    // LANGKAH 5 & 6: Ekstraksi Aset + Penyimpanan Atomik
                    // =====================================================
                    DB::transaction(function () use (
                        $source, $guid, $title, $slug, $link,
                        $description, $publishedAt, $item
                    ) {
                        // Insert artikel dan ambil ID-nya
                        $article = Article::create([
                            'source_id'    => $source->id,
                            'guid'         => $guid,
                            'title'        => Str::limit($title, 255, ''),
                            'slug'         => Str::limit($slug, 255, ''),
                            'link'         => $link,
                            'description'  => $description ?: null,
                            'published_at' => $publishedAt,
                        ]);

                        // Ekstraksi URL gambar dari berbagai sumber di XML
                        $imageUrls = $this->extractImageUrls($item);

                        // Insert semua gambar ke tabel images
                        foreach ($imageUrls as $imageUrl) {
                            Image::create([
                                'article_id' => $article->id,
                                'image_url'  => $imageUrl,
                            ]);
                        }
                    });

                    $fetchedCount++;
                }

                // Update waktu terakhir fetch di tabel sources
                $source->update(['last_fetched_at' => now()]);

                // Catat log sukses
                LogSuccess::create([
                    'source_id'     => $source->id,
                    'total_fetched' => $fetchedCount,
                    'fetched_at'    => now(),
                ]);

                $this->info("  ✔ Berhasil menyimpan {$fetchedCount} artikel baru.");

            } catch (\Exception $e) {
                // Catat log gagal
                LogFailed::create([
                    'source_id'     => $source->id,
                    'error_message' => $e->getMessage(),
                    'failed_at'     => now(),
                ]);

                $this->error("  ✘ Gagal: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info('════ Proses fetch selesai. ════');

        return self::SUCCESS;
    }

    /**
     * Ekstraksi URL gambar dari item RSS.
     *
     * Mencari di beberapa lokasi umum:
     * - <enclosure> (RSS standar)
     * - <media:content> (Media RSS namespace)
     * - <media:thumbnail>
     * - Tag <img> di dalam <description>
     *
     * @return array<string>
     */
    private function extractImageUrls(\SimpleXMLElement $item): array
    {
        $imageUrls = [];

        // 1. Cek <enclosure> dengan tipe gambar
        if (isset($item->enclosure)) {
            $enclosureUrl  = (string) $item->enclosure['url'];
            $enclosureType = (string) $item->enclosure['type'];

            if (!empty($enclosureUrl) && str_starts_with($enclosureType, 'image/')) {
                $imageUrls[] = $enclosureUrl;
            }
        }

        // 2. Cek <media:content> (namespace media)
        $mediaNamespaces = ['media', 'Media'];
        foreach ($mediaNamespaces as $ns) {
            $mediaContent = $item->children($ns, true);

            if (isset($mediaContent->content)) {
                // Cek attributes() terlebih dahulu (PHP 8 SimpleXML behavior)
                $mediaUrl = (string) $mediaContent->content->attributes()->url;
                if (empty($mediaUrl)) {
                    $mediaUrl = (string) $mediaContent->content['url'];
                }
                
                if (!empty($mediaUrl)) {
                    $imageUrls[] = $mediaUrl;
                }
            }

            if (isset($mediaContent->thumbnail)) {
                $thumbUrl = (string) $mediaContent->thumbnail->attributes()->url;
                if (empty($thumbUrl)) {
                    $thumbUrl = (string) $mediaContent->thumbnail['url'];
                }
                if (!empty($thumbUrl)) {
                    $imageUrls[] = $thumbUrl;
                }
            }
        }
        
        // 3. Cek tag imglink (Sering digunakan oleh portal lokal seperti iNews)
        $imglink = (string) ($item->imglink ?? '');
        if (!empty($imglink)) {
            $imageUrls[] = $imglink;
        }

        // 3. Fallback: Cari tag <img> di dalam description
        if (empty($imageUrls)) {
            $descHtml = (string) ($item->description ?? '');
            if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $descHtml, $matches)) {
                foreach ($matches[1] as $imgSrc) {
                    $imageUrls[] = $imgSrc;
                }
            }
        }

        // Hapus duplikat
        return array_values(array_unique($imageUrls));
    }

    /**
     * Buat slug yang unik. Jika slug sudah ada,
     * tambahkan suffix angka (-1, -2, dst).
     */
    private function makeUniqueSlug(string $slug): string
    {
        $original = $slug;
        $counter  = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Bangun snippet cerdas menggunakan Logika Validasi Pra-Masuk (Look-Ahead Validation).
     *
     * Algoritma:
     * 1. Bersihkan teks promosi dan sampah dari portal (Lapis 2 Regex).
     * 2. Pecah teks menjadi kalimat-kalimat berdasarkan tanda titik.
     * 3. Evaluasi setiap kalimat SEBELUM dimasukkan (look-ahead).
     * 4. Tolak kalimat jika total karakter setelah ditambah akan > 250.
     * 5. Edge Case: Jika penampung masih kosong, paksa masuk kalimat pertama.
     * 6. Hentikan jika sudah 3 kalimat.
     */
    private function buildSmartSnippet(string $text): string
    {
        // --- LAPIS 2: Bersihkan teks sampah & promosi dari portal ---

        // Hapus konten di dalam kurung siku, misal: [Kompas.com], [WARTAWAN], dsb.
        $text = preg_replace('/\[.*?\]/', '', $text);

        // Hapus frasa promosi populer beserta sisa kalimat setelahnya (case-insensitive)
        $text = preg_replace('/(?i)(baca juga|baca selengkapnya|simak juga|artikel terkait|lihat juga|klik di sini|selengkapnya di|sumber:)[^.]*\.?/', '', $text);

        // Hapus titik-titik gantung (ellipsis) dalam berbagai bentuk
        $text = preg_replace('/\.{2,}/', '', $text);
        $text = str_replace('…', '', $text);

        // Hapus spasi berlebih yang dihasilkan dari pembersihan di atas
        $text = preg_replace('/\s{2,}/', ' ', $text);
        $text = trim($text);

        // Jika teks sudah kosong setelah dibersihkan, kembalikan string kosong
        if (empty($text)) {
            return '';
        }

        // --- KONSTRUKSI PARAGRAF DINAMIS (Look-Ahead Validation) ---

        // Pecah menjadi kalimat berdasarkan tanda titik
        $rawSentences = explode('.', $text);

        // Bersihkan whitespace per kalimat dan buang yang kosong
        $sentences = array_values(array_filter(array_map('trim', $rawSentences)));

        $snippet       = '';
        $sentenceCount = 0;
        $maxLength     = 250;

        foreach ($sentences as $sentence) {
            if (empty($sentence)) {
                continue;
            }

            // Hitung panjang jika kalimat ini ditambahkan (+ spasi + titik = 2 karakter overhead)
            $overhead         = empty($snippet) ? 1 : 2; // titik saja, atau spasi+titik
            $simulatedLength  = strlen($snippet) + strlen($sentence) + $overhead;

            if ($simulatedLength > $maxLength) {
                // EDGE CASE: Jika penampung masih kosong (ini kalimat pertama)
                // Paksa masuk agar data tidak kosong, lalu langsung berhenti.
                if (empty($snippet)) {
                    $snippet = $sentence . '.';
                }
                // Dalam kondisi apapun, tolak dan hentikan perulangan
                break;
            }

            // Kalimat aman, masukkan ke penampung
            $snippet .= (empty($snippet) ? '' : ' ') . $sentence . '.';
            $sentenceCount++;

            // REM JUMLAH: Hentikan jika sudah mencapai 3 kalimat
            if ($sentenceCount >= 3) {
                break;
            }
        }

        return trim($snippet);
    }
}
