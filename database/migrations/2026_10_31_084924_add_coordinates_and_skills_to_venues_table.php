<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            // Kategori tampilan (Tari, Batik, Karawitan, Wayang, Pertunjukan).
            $table->string('category', 40)->nullable()->after('type');

            // Apa saja yang dipelajari di tempat ini.
            $table->string('skills', 255)->nullable()->after('description');

            // Koordinat hanya diisi bila sudah terverifikasi.
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['category', 'skills', 'latitude', 'longitude']);
        });
    }
};
