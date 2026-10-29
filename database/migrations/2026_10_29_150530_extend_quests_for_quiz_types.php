<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perluas tabel quests agar mendukung 4 jenis kuis (harian, mingguan,
     * bulanan, pengetahuan) plus kolom materi (subtitle) & target jumlah soal.
     */
    public function up(): void
    {
        Schema::table('quests', function (Blueprint $table) {
            // enum lama hanya daily/weekly -> string agar bisa monthly & knowledge
            $table->string('type')->change();

            $table->string('subtitle')->nullable()->after('title'); // materi/subjudul
            $table->unsignedSmallInteger('question_count')->default(0)->after('subtitle'); // target jumlah soal
        });
    }

    public function down(): void
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'question_count']);
        });
    }
};
