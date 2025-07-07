<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            // Ubah kolom kategori jadi nullable
            $table->string('kategori')->nullable()->change();

            // Hapus default dari status (gunakan DB raw)
            $table->enum('status', ['pengajuan', 'diproses', 'selesai'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            // Balikkan ke tidak nullable dan ada default
            $table->string('kategori')->nullable(false)->change();
            $table->enum('status', ['pengajuan', 'diproses', 'selesai'])->default('pengajuan')->change();
        });
    }
};
