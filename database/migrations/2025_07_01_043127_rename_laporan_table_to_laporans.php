<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (rename the table).
     */
    public function up(): void
    {
        Schema::rename('laporan', 'laporans');
    }

    /**
     * Reverse the migrations (rename it back).
     */
    public function down(): void
    {
        Schema::rename('laporans', 'laporan');
    }
};
