<?php

/**
 * Daftar provinsi per pulau di halaman Materi Budaya.
 *
 * ============================================================
 * CARA MENAMBAH / MENGATUR
 * ============================================================
 * Gambar diletakkan di folder public/<folder>/.
 *
 * Jumlah kabupaten & kota dibaca OTOMATIS dari nama file dengan pola:
 *
 *      <Nama><JumlahKabupaten>dan<JumlahKota>kota.png
 *
 * Contoh:
 *      CandiBudhdaJawaTengah29dan6kota.png  -> 29 kabupaten, 6 kota
 *      MonasJakarta1dan5kota.png            -> 1 kabupaten, 5 kota
 *
 * 'name'  = nama provinsi yang tampil di tombol.
 * 'slug'  = slug provinsi di database (dipakai untuk tautan).
 *           Kosongkan (null) bila tombol belum bisa diklik.
 * 'file'  = nama file gambar di dalam folder.
 * 'note'  = keterangan tambahan (opsional), mis. nama ikon/gambar.
 */

return [

    'jawa' => [
        'folder' => 'Provinsi Jawa',

        'provinces' => [
            [
                'file' => 'MonasJakarta1dan5kota.png',
                'name' => 'DKI Jakarta',
                'slug' => 'dki-jakarta',
                'note' => 'Monas',
            ],
            [
                'file' => 'GedungSateJawaBarat18dan9kota.png',
                'name' => 'Jawa Barat',
                'slug' => 'jawa-barat',
                'note' => 'Gedung Sate',
            ],
            [
                'file' => 'MasjidAgungBanten4dan4kota.png',
                'name' => 'Banten',
                'slug' => 'banten',
                'note' => 'Masjid Agung Banten',
            ],
            [
                'file' => 'CandiBudhdaJawaTengah29dan6kota.png',
                'name' => 'Jawa Tengah',
                'slug' => 'jawa-tengah',
                'note' => 'Candi Borobudur',
            ],
            [
                'file' => 'TuguJogja4dan1kota.png',
                'name' => 'DI Yogyakarta',
                'slug' => 'di-yogyakarta',
                'note' => 'Keraton Yogyakarta',
            ],
            [
                'file' => 'PatungSuraBuayaJawaTimur29dan9kota.png',
                'name' => 'Jawa Timur',
                'slug' => 'jawa-timur',
                'note' => 'Patung Sura & Baya',
            ],
        ],
    ],

];
