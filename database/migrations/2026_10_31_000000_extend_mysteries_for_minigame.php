<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---------- mysteries: tambah kolom untuk minigame ----------
        Schema::table('mysteries', function (Blueprint $table) {
            // Pernyataan awal yang dibaca pemain sebelum menebak.
            $table->text('statement')->nullable()->after('origin');
            // Poin maksimal (default 300) & potongan tiap clue (default 50).
            $table->unsignedSmallInteger('points')->default(300)->after('clues');
            $table->unsignedSmallInteger('clue_penalty')->default(50)->after('points');
            // Jumlah salah maksimal sebelum gagal permanen.
            $table->unsignedTinyInteger('max_wrong_guesses')->default(7)->after('max_clues');
        });

        // ---------- mystery_attempts: jadi tabel "sesi" ----------
        Schema::table('mystery_attempts', function (Blueprint $table) {
            // 0 = berjalan, 1 = berhasil, 2 = gagal (salah 7x)
            $table->unsignedTinyInteger('status')->default(0)->after('clues_used');
            $table->unsignedTinyInteger('clues_revealed')->default(0)->after('status');
            $table->unsignedTinyInteger('wrong_guesses')->default(0)->after('clues_revealed');
            $table->timestamp('finished_at')->nullable()->after('score');

            // Satu sesi per user per misteri.
            $table->unique(['user_id', 'mystery_id']);
        });
    }

    public function down(): void
    {
        Schema::table('mystery_attempts', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'mystery_id']);
            $table->dropColumn(['status', 'clues_revealed', 'wrong_guesses', 'finished_at']);
        });

        Schema::table('mysteries', function (Blueprint $table) {
            $table->dropColumn(['statement', 'points', 'clue_penalty', 'max_wrong_guesses']);
        });
    }
};
