<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            // Tambahkan kolom lampiran untuk file (nullable)
            $table->string('lampiran')->nullable()->after('deskripsi');
            $table->enum('status', ['Pengajuan', 'Progress', 'Selesai'])->change();
        });

    }

    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropColumn('lampiran');
            // Kembalikan enum status ke semula (huruf kecil)
            $table->enum('status', ['pengajuan', 'diproses', 'selesai'])->change();
        });

    }
};
