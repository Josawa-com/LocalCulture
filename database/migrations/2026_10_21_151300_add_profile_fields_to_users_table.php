<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin'])->default('user')->after('password');
            $table->string('avatar_path')->nullable()->after('role');
            $table->foreignId('island_id')->nullable()->after('avatar_path')->constrained()->nullOnDelete();
            $table->foreignId('province_id')->nullable()->after('island_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['island_id']);
            $table->dropForeign(['province_id']);
            $table->dropColumn(['role', 'avatar_path', 'island_id', 'province_id']);
        });
    }
};
