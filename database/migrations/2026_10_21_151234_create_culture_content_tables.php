<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pulau (island) - top level of culture navigation
        Schema::create('islands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Sumatera, Jawa, etc.
            $table->string('slug', 100)->unique();
            $table->string('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Provinsi
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('island_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->string('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Kabupaten/Kota
        Schema::create('regencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 100);
            $table->string('description')->nullable();
            $table->unique(['province_id', 'slug']);
            $table->timestamps();
        });

        // Materi budaya (culture material) per regency, category-based
        Schema::create('culture_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Rumah Adat, Pakaian Adat, Tarian, etc.
            $table->string('slug', 100)->unique();
            $table->string('icon', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('cultures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('culture_category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150);
            $table->text('description')->nullable();
            $table->longText('content')->nullable(); // detailed material content
            $table->string('image_path')->nullable();
            // Filosofi Budaya: philosophy content, nullable - only some cultures have it
            $table->text('philosophy')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->unique(['regency_id', 'culture_category_id', 'slug']);
            $table->timestamps();
        });

        // Mystery Budaya minigame
        Schema::create('mysteries', function (Blueprint $table) {
            $table->id();
            $table->string('answer', 150); // name of the culture item
            $table->string('origin', 150); // daerah asal
            $table->json('clues'); // ordered array of clues, revealed step by step
            $table->unsignedTinyInteger('max_clues')->default(5);
            $table->unsignedTinyInteger('time_limit')->default(60); // seconds
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Mystery attempts history
        Schema::create('mystery_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mystery_id')->constrained()->cascadeOnDelete();
            $table->string('guess', 150)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('clues_used')->default(1);
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mystery_attempts');
        Schema::dropIfExists('mysteries');
        Schema::dropIfExists('cultures');
        Schema::dropIfExists('culture_categories');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('islands');
    }
};
