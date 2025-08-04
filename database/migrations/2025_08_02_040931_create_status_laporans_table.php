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
        Schema::create('status_laporans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_id');
            $table->date('pengajuan_tanggal')->nullable();
            $table->date('progress_tanggal')->nullable();
            $table->date('selesai_tanggal')->nullable();

            $table->foreign('laporan_id')->references('id')->on('laporans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_laporans');
    }
};
