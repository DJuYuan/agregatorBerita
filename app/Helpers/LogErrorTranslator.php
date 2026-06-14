<?php

namespace App\Helpers;

class LogErrorTranslator
{
    /**
     * Menerjemahkan pesan error teknis menjadi bahasa manusia yang lebih mudah dipahami.
     * Tetap menyertakan kode/pesan asli di dalam kurung siku untuk keperluan debugging.
     *
     * @param string $errorMessage
     * @return string
     */
    public static function translate(?string $errorMessage): string
    {
        if (empty($errorMessage)) {
            return 'Terjadi kegagalan yang tidak diketahui.';
        }

        $lowerMsg = strtolower($errorMessage);

        // 1. Pemblokiran & Izin (403 / 401)
        if (str_contains($lowerMsg, 'status 403') || str_contains($lowerMsg, 'status 401')) {
            return "Akses Ditolak: Sumber memblokir akses bot kita. [{$errorMessage}]";
        }

        // 2. Tidak Ditemukan (404)
        if (str_contains($lowerMsg, 'status 404')) {
            return "URL Tidak Valid: Alamat RSS tidak ditemukan atau telah dihapus. [{$errorMessage}]";
        }

        // 3. Server Down (500, 502, 503, 504)
        if (preg_match('/status 50[0-4]/', $lowerMsg)) {
            return "Server Down: Sumber sedang mengalami gangguan atau maintenance. [{$errorMessage}]";
        }

        // 4. Masalah Koneksi Jaringan & Resolusi Host (cURL 6)
        if (str_contains($lowerMsg, 'curl error 6') || str_contains($lowerMsg, 'could not resolve host')) {
            return "Koneksi Gagal: Domain web tidak dapat dihubungi. [{$errorMessage}]";
        }

        // 5. Masalah Timeout (cURL 28, cURL 7)
        if (str_contains($lowerMsg, 'curl error 28') || str_contains($lowerMsg, 'curl error 7') || str_contains($lowerMsg, 'timeout')) {
            return "Waktu Habis: Server sumber lambat merespons. [{$errorMessage}]";
        }

        // 6. Masalah Format XML (Parse Error)
        if (str_contains($lowerMsg, 'xml parse error')) {
            return "Format Rusak: Sumber tidak memberikan data RSS/XML yang valid. [{$errorMessage}]";
        }

        // Jika tidak ada pola yang cocok, kembalikan pesan aslinya
        return "Error Tidak Dikenali: {$errorMessage}";
    }
}
