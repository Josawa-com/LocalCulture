<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lokasi pertunjukan & tempat latihan budaya
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 150);
            $table->enum('type', ['sanggar_tari', 'gamelan', 'karawitan', 'komunitas', 'lainnya'])->default('lainnya');
            $table->string('address');
            $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Pertunjukan budaya (shows)
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->timestamp('starts_at');
            $table->string('location_name');
            $table->foreignId('regency_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('ticket_price', 12, 2)->default(0);
            $table->string('organizer');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // "Tertarik" button requests from users → admin follow-up queue
        Schema::create('interest_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'contacted', 'done'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->unique(['user_id', 'venue_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_requests');
        Schema::dropIfExists('shows');
        Schema::dropIfExists('venues');
    }
};
