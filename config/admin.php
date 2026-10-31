<?php

/*
|--------------------------------------------------------------------------
| Akun Admin
|--------------------------------------------------------------------------
|
| Hanya ada SATU akun admin di aplikasi ini. Nilainya dipakai oleh
| DatabaseSeeder (lihat database/seeders/DatabaseSeeder.php).
|
| Ubah lewat .env bila perlu:
|
|   ADMIN_EMAIL=anonimexe676767@gmail.com
|   ADMIN_PASSWORD=admin123
|
| Nilai bawaan di bawah ini adalah akun yang dipakai untuk demo/lomba.
|
*/

return [

    'email' => env('ADMIN_EMAIL', 'anonimexe676767@gmail.com'),

    'password' => env('ADMIN_PASSWORD', 'admin123'),

    'name' => env('ADMIN_NAME', 'Admin CultureRaya'),

];
