<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom description ke tabel system_settings
     * agar setiap pengaturan dapat memiliki keterangan penjelasan
     * yang ditampilkan sebagai petunjuk (hint) di panel admin.
     */
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->text('description')->nullable()->after('label');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
