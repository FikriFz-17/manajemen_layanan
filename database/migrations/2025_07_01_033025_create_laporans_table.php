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
            Schema::create('laporans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // relasi ke users
                $table->string('resi')->unique(); // contoh: 010725-01
                $table->string('judul_masalah');
                $table->string('kategori');
                $table->enum('status', ['pengajuan', 'diproses', 'selesai'])->default('pengajuan');
                $table->date('tanggal_pengajuan');
                $table->date('tanggal_selesai')->nullable();
                $table->integer('estimasi')->nullable(); // misalnya estimasi hari penyelesaian
                $table->text('deskripsi');
                $table->text('penyelesaian')->nullable();
                $table->timestamps();

                // Foreign key constraint
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
