<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jumlah kunjungan per user per kabupaten/kota.
        // Dipakai untuk menandai wilayah "sering dikunjungi" di halaman
        // pemilihan kabupaten/kota.
        Schema::create('regency_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('regency_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('visits')->default(0);
            $table->timestamp('last_visited_at')->nullable();
            $table->timestamps();

            // Satu baris per user per wilayah; kunjungan berikutnya menambah angka.
            $table->unique(['user_id', 'regency_id']);
            $table->index('regency_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regency_visits');
    }
};
