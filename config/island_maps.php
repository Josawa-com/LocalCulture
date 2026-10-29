<?php

/**
 * Peta pulau di halaman Materi Budaya (/budaya).
 *
 * ============================================================
 * CARA MENGATUR POSISI GAMBAR (manual, per gambar)
 * ============================================================
 * Tiap gambar diposisikan SENDIRI-SENDIRI (absolute), jadi mengubah
 * x/y satu gambar TIDAK menggeser gambar lain.
 *
 *   x : jarak dari TEPI KIRI kotak, dalam persen
 *       0 = paling kiri, 50 = tengah, 100 = paling kanan
 *   y : jarak dari TEPI ATAS kotak, dalam persen
 *       0 = paling atas, 50 = tengah, 100 = paling bawah
 *   w : lebar gambar, persen dari lebar kotak
 *   r : ROTASI gambar, dalam DERAJAT (boleh desimal, mis. -12.5)
 *       0   = tegak seperti aslinya
 *       +   = searah jarum jam  (mis. 10  -> miring ke kanan)
 *       -   = berlawanan jarum jam (mis. -10 -> miring ke kiri)
 *       Gambar berputar pada TITIK TENGAHNYA, jadi x/y tidak berubah.
 *       Kartu keterangan (nama pulau + jumlah provinsi) TETAP TEGAK.
 *   z : urutan tumpuk — angka lebih besar tampil di DEPAN
 *
 * Gambar diambil dari folder public/Pulau/.
 *
 * Jumlah provinsi OTOMATIS dibaca dari angka terakhir pada nama file:
 *   PulauSumatra10.png  -> 10 provinsi
 *   PulauJawa6.png      -> 6 provinsi
 *
 * 'island' = slug pulau tujuan saat gambar diklik (lihat tabel islands).
 *            Kosongkan (null) bila gambar tidak perlu bisa diklik.
 */

return [

    /*
    | Rasio kotak tak terlihat (lebar : tinggi).
    | 1.4 = agak melebar, cocok untuk bentang Nusantara dari barat ke timur.
    */
    'aspect' => 1.4,

    'images' => [

        // ---------- SUMATERA (barat, agak ke atas) ----------
        [
            'file' => 'PulauSumatra10.png',
            'label' => 'Sumatera',
            'island' => 'sumatera',
            'x' => 0, 'y' => 40, 'w' => 28, 'z' => 3, 'r' => -5,
        ],

        // ---------- JAWA (tengah-barat, bawah) ----------
        [
            'file' => 'PulauJawa6.png',
            'label' => 'Jawa',
            'island' => 'jawa',
            'x' => 25, 'y' => 67, 'w' => 30, 'z' => 5, 'r' => 0,
        ],

        // ---------- KALIMANTAN (tengah, atas) ----------
        [
            'file' => 'PulauKalimantan5.png',
            'label' => 'Kalimantan',
            'island' => 'kalimantan',
            'x' => 35, 'y' => 35, 'w' => 28, 'z' => 2, 'r' => 0,
        ],

        // ---------- SULAWESI (tengah-timur) ----------
        [
            'file' => 'PulauSulawesi6.png',
            'label' => 'Sulawesi',
            'island' => 'sulawesi',
            'x' => 56, 'y' => 40, 'w' => 27, 'z' => 4, 'r' => 0,
        ],

        // ---------- BALI & NUSA TENGGARA (bawah, agak ke timur) ----------
        [
            'file' => 'KepulauanNusaTenggara&Bali3.png',
            'label' => 'Bali & Nusa Tenggara',
            'island' => 'bali-nusa-tenggara',
            'x' => 52, 'y' => 71, 'w' => 32, 'z' => 6, 'r' => 0,
        ],

        // ---------- MALUKU (timur) ----------
        [
            'file' => 'KepulauanMaluku2.png',
            'label' => 'Maluku',
            'island' => 'maluku-papua',
            'x' => 80, 'y' => 57, 'w' => 35, 'z' => 1, 'r' => 0,
        ],

        // ---------- PAPUA (paling timur) ----------
        [
            'file' => 'PulauPapua6.png',
            'label' => 'Papua',
            'island' => 'maluku-papua',
            'x' => 100, 'y' => 50, 'w' => 43, 'z' => 3, 'r' => 0,
        ],

    ],

];
