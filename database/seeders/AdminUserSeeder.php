<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Membuat (atau memperbarui) SATU akun admin.
 *
 * Kredensial diambil dari config/admin.php, yang membaca .env:
 *     ADMIN_EMAIL=anonimexe676767@gmail.com
 *     ADMIN_PASSWORD=admin123
 *
 * Seeder ini idempoten, jadi aman dijalankan berulang — termasuk otomatis
 * setiap kontainer Docker dinyalakan (lihat docker/entrypoint.sh).
 *
 *     php artisan db:seed --class=AdminUserSeeder --force
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('admin.email');
        $password = (string) config('admin.password');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => (string) config('admin.name'),
                'password' => Hash::make($password),
                'role' => 'admin',
            ]
        );

        $this->command?->info("Admin siap: {$email} / {$password}");
    }
}
