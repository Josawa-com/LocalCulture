@extends('layouts.app')

@section('title', 'Materi Budaya')

@php
    /*
     * 5 layer narasi untuk animasi zig-zag gaya two26.studio.
     * Teks besar = judul poin, badge = isi lengkap poin.
     */
    $cultureNarratives = [
        [
            'num'    => '1',
            'left'   => 'Keberagaman',
            'right'  => 'Budaya Indonesia',
            'desc'   => 'Indonesia merupakan negara kepulauan yang memiliki keberagaman budaya, tradisi, bahasa, kesenian, dan adat istiadat. Kebudayaan Indonesia terbentuk melalui perjalanan sejarah yang panjang, mulai dari kehidupan masyarakat prasejarah, perkembangan kerajaan-kerajaan Nusantara, masuknya berbagai pengaruh dari luar, hingga terbentuknya Indonesia sebagai negara modern.',
            /*
             | PENGATURAN POSISI (X & Y) PER SUBJUDUL
             | 'side'   : sisi acuan blok — 'right' = hitung dari kanan,
             |            'left'  = hitung dari kiri.
             | 'offset' : X — geser horizontal dari sisi acuan.
             |            Persen (mis. '-22%') atau px ('120px').
             |            Negatif = ke arah tengah, positif = ke arah tepi.
             | 'dy'     : Y — geser vertikal tambahan dari posisi barisnya.
             |            px atau em; negatif = naik, positif = turun.
             */
            'side' => 'left', 'offset' => '-17%', 'dy' => '-60px', 'icon' => 'islands',
        ],
        [
            'num'    => '2',
            'left'   => 'Awal Mula',
            'right'  => 'Perkembangan Budaya',
            'desc'   => 'Sejak dahulu, masyarakat Nusantara mengembangkan kebudayaan sesuai dengan kondisi alam, kepercayaan, dan kehidupan sosial masing-masing. Hal ini melahirkan beragam warisan budaya, seperti rumah adat, pakaian tradisional, tarian, musik, kuliner, serta upacara adat yang memiliki nilai dan makna tersendiri.',
            'side' => 'left', 'offset' => '10%', 'dy' => '0px', 'icon' => 'heritage',
        ],
        [
            'num'    => '3',
            'left'   => 'Pengaruh Budaya',
            'right'  => 'dari Luar',
            'desc'   => 'Seiring berjalannya waktu, interaksi antarmasyarakat dan bangsa lain turut memperkaya kebudayaan Indonesia. Pengaruh Hindu-Buddha, Islam, serta budaya Eropa dan Asia lainnya berinteraksi dengan tradisi lokal dan menghasilkan berbagai bentuk kebudayaan yang unik.',
            'side' => 'left', 'offset' => '-14%', 'dy' => '320px', 'icon' => 'exchange',
        ],
        [
            'num'    => '4',
            'left'   => 'Pentingnya Melestarikan',
            'right'  => 'Budaya',
            'desc'   => 'Hingga saat ini, kebudayaan menjadi bagian penting dari identitas bangsa Indonesia. Melestarikan dan mempelajari budaya berarti memahami sejarah, menghargai keberagaman, serta menjaga warisan leluhur agar tetap dikenal oleh generasi mendatang.',
            'side' => 'left', 'offset' => '11%', 'dy' => '330px', 'icon' => 'preserve',
        ],
        [
            'num'    => '5',
            'left'   => 'Jelajahi Kekayaan',
            'right'  => 'Budaya Indonesia',
            'desc'   => 'Melalui halaman ini, kamu dapat menjelajahi kekayaan budaya Indonesia berdasarkan pulau, provinsi, dan kabupaten/kota, serta mengenal sejarah, filosofi, dan nilai-nilai yang terkandung di dalamnya.',
            'side' => 'left', 'offset' => '-24%', 'dy' => '300px', 'icon' => 'explore',
        ],
    ];
@endphp

@section('content')
    {{-- ============================================================
         Hero: animasi zig-zag gaya two26.studio dengan narasi budaya
         ============================================================ --}}
    <section class="t26" id="t26-culture-narrative">
        <style>
            /* ---------- Welcome fullscreen (gaya teatika, dibalik) ---------- */
            .t26-welcome {
                position: relative;
                display: grid;
                place-items: center;
                width: 100%;
                min-height: 100vh;
                min-height: 100svh; /* fullscreen di desktop & mobile */
                /* TANPA overflow:hidden — lingkaran tak terpotong saat
                   menyentuh tepi bawah (batas kotak mengenal budaya) */
                cursor: none; /* kursor digantikan lingkaran */
                z-index: 10; /* lingkaran melampaui kotak di bawahnya */
            }
            /* Foto kolase budaya: fullscreen di belakang teks */
            .t26-welcome-bg {
                position: absolute;
                inset: 0;
                z-index: 0;
            }
            .t26-welcome-bg img {
                width: 100%;
                height: 100%;
                object-fit: cover; /* fullscreen, proporsional terisi penuh */
                display: block;
                user-select: none;
            }
            /* Gradasi putih di bagian bawah agar menyatu dengan section
               di bawahnya + lapisan putih tipis agar teks tetap terbaca */
            .t26-welcome-bg::after {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(180deg,
                        rgb(253 248 245 / .25) 0%,
                        rgb(253 248 245 / 0) 28%,
                        rgb(253 248 245 / 0) 55%,
                        rgb(253 248 245 / .55) 80%,
                        #FDF8F5 100%);
            }
            .t26-welcome-stack {
                position: relative;
                /* Teks sengaja dinaikkan dari tengah layar */
                transform: translateY(-10vh);
                will-change: transform;
            }
            .t26-welcome-title {
                position: relative;
                z-index: 1;
                max-width: 12em;
                padding: 0 .5em;
                font-family: var(--t26-font-heading);
                font-size: clamp(52px, 8.3vw, 130px);
                font-weight: 500;
                line-height: 1.05;
                letter-spacing: .14em; /* jarak antar huruf dilebarkan */
                text-transform: uppercase;
                text-align: center;
                text-wrap: balance;
                color: var(--t26-black);
                user-select: none;
                /* Bayangan putih lembut agar teks tetap tajam di atas foto */
                text-shadow:
                    0 1px 0 rgb(253 248 245 / .55),
                    0 0 24px rgb(253 248 245 / .65),
                    0 0 48px rgb(253 248 245 / .45);
            }
            /* Pemenggalan 4 baris: SELAMAT / DATANG DI / BUDAYA / INDONESIA */
            .t26-welcome-title .t26-line {
                display: block;
            }
            /* Lingkaran kursor: putih + mix-blend-mode difference.
               Di atas latar parchment → lingkaran tampak HITAM.
               Di atas teks hitam → teks ter-invert jadi PUTIH.
               Yang di-invert adalah teks aslinya, bukan lapisan kedua,
               sehingga posisi teks tersorot SELALU identik.
               z-index tinggi agar tak tertutup kotak utama. */
            .t26-orb {
                position: absolute;
                z-index: 999;
                left: 0;
                top: 0;
                width: clamp(120px, 16vw, 220px);
                height: clamp(120px, 16vw, 220px);
                border-radius: 50%;
                background: var(--t26-white);
                mix-blend-mode: difference;
                transform: translate(-50%, -50%) scale(0);
                pointer-events: none;
                will-change: transform;
            }
            @media (pointer: coarse) {
                /* Perangkat sentuh: kursor normal, efek lingkaran nonaktif */
                .t26-welcome { cursor: auto; }
                .t26-orb { display: none; }
            }
            @media (prefers-reduced-motion: reduce) {
                .t26-welcome { cursor: auto; }
                .t26-orb { display: none; }
            }

            /* ---------- Scaling system (satuan em, meniru two26) ---------- */
            .t26 {
                --t26-orange: #FF5400;
                --t26-black: #1F1814;
                --t26-white: #FDF8F5;
                --t26-font-heading: var(--font-display, Georgia, serif);
                --t26-font-body: var(--font-sans, ui-sans-serif, system-ui, sans-serif);
                font-size: clamp(11.02px, 1.111vw, 21.33px);
            }
            @media screen and (max-width: 991px) {
                .t26 { font-size: clamp(12.26px, 1.918vw, 16.48px); }
            }
            @media screen and (max-width: 479px) {
                .t26 { font-size: clamp(10.66px, 4.102vw, 12.28px); }
            }

            .t26 * { box-sizing: border-box; margin: 0; padding: 0; }

            /* ---------- Kotak utama: header + body + islands satu kesatuan.
               Gradasi memutus sambungan tajam antar bagian: putih pekat di
               atas (dari welcome), sedikit tembus di tengah agar terasa
               ada kedalaman, putih penuh lagi di bawah. ---------- */
            .t26-main {
                position: relative;
                width: 100%;
                background: linear-gradient(180deg,
                    var(--t26-white) 0%,
                    var(--t26-white) 8%,
                    rgb(253 248 245 / .94) 26%,
                    rgb(253 248 245 / .88) 55%,
                    var(--t26-white) 84%,
                    var(--t26-white) 100%);
            }
            /* Tekstur titik halus ala kertas di seluruh kotak utama */
            .t26-main::before {
                content: "";
                position: absolute;
                inset: 0;
                background-image: radial-gradient(rgb(138 108 78 / .05) 1px, transparent 1px);
                background-size: 22px 22px;
                pointer-events: none;
            }

            /* ---------- Header: judul kecil 1 baris di tengah ---------- */
            .t26-header {
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
                padding: 2.5em 1.125em 1em;
            }
            .t26-heading-wrapper {
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
                max-width: 100%;
            }
            .t26-heading {
                position: relative;
                z-index: 2;
                font-family: var(--t26-font-heading);
                font-weight: 600;
                font-size: 2.2em;
                line-height: 110%;
                letter-spacing: .01em;
                text-transform: uppercase;
                text-align: center;
                white-space: nowrap;
                color: var(--t26-black);
                pointer-events: none;
            }
            /* Salinan oranye tipis di belakang (efek magnet dua lapis) */
            .t26-heading.is-orange {
                position: absolute;
                inset: 0;
                z-index: 1;
                color: var(--t26-orange);
                opacity: .9;
                will-change: transform;
            }

            /* ---------- Body ---------- */
            .t26-body {
                position: relative;
                display: flex;
                flex-flow: column;
                align-items: center;
                width: 100%;
                /* Tinggi mengikuti rasio asli path two26 (2187/1616 ≈ 1.353),
                   jadi sinar tampil utuh tanpa dipaksa melar */
                min-height: 136vw;
                overflow: hidden; /* kotak tertutup: garis & layers tak keluar */
            }

            /* ---------- Sinar SVG — setel persis seperti two26: svg mengalir
               normal (width 100%, height auto), light menentukan tinggi,
               dark absolute menimpanya. ---------- */
            .t26-path {
                position: relative;
                width: 100%;
                z-index: 1;
            }
            .t26-path-light { display: block; width: 100%; height: auto; opacity: .25; }
            .t26-path-dark {
                position: absolute;
                inset: .2em .2em auto;
                z-index: 2;
                display: block;
                width: calc(100% - .4em);
                height: auto;
            }
            .t26-path svg, .t26-path svg path { overflow: visible; }
            .t26-path-dark path { will-change: stroke-dashoffset; }
            .t26-path-mobile { display: none; }

            /* ---------- Layers narasi: absolute menimpa sinar (persis two26:
               .services-layers absolute di atas .services-path) ---------- */
            .t26-layers {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 3; /* di atas garis */
                display: flex;
                flex-flow: column;
                justify-content: center;
                align-items: center;
                gap: 12em; /* jeda antar kotak menyesuaikan tinggi sinar */
                width: 100%;
                height: 100%;
            }
            .t26-layer {
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
                height: 4em;
            }
            .t26-block {
                position: relative;
                display: inline-flex;
                align-items: center;
                gap: .6em;
                font-size: .85em;
            }
            .t26-num {
                font-family: var(--t26-font-body);
                font-size: 1.3em;
                font-weight: 700;
                color: var(--t26-black);
                opacity: .35;
                line-height: 1;
            }
            .t26-text {
                font-family: var(--t26-font-heading);
                font-size: 2.1em;
                font-weight: 600;
                line-height: 105%;
                text-transform: uppercase;
                color: var(--t26-orange);
                white-space: nowrap;
            }
            /* Animasi teks: tiap kata slide-up + fade (pola SplitText two26).
               Kata dibungkus JS; kondisi awal diatur CSS agar tak FOUC. */
            .t26-text.t26-ready .t26-word {
                display: inline-block;
                opacity: 0;
                transform: translateY(.55em);
                will-change: transform, opacity;
            }
            .t26-svg {
                width: 6.2em; /* logo diperbesar */
                flex-shrink: 0;
                /* Border hitam agar logo tidak menyatu dengan garis oranye */
                border: 3px solid var(--t26-black);
                border-radius: 18px;
                background: var(--t26-white);
                padding: .35em;
                transform-origin: bottom center; /* muncul NAIK dari garis */
                will-change: transform, width, opacity;
            }
            .t26-svg > * { width: 100%; height: 100%; object-fit: contain; display: block; }
            .t26-badge {
                position: absolute;
                bottom: calc(100% + .9em);
                right: -3em;
                width: 21em;
                padding: .9em 1.2em;
                border: 1px solid rgb(255 84 0 / .55);
                border-radius: 14px;
                background-color: var(--t26-white);
                box-shadow: 0 2px 4px rgb(31 24 20 / .06), 0 6px 16px rgb(31 24 20 / .07);
                transform-origin: bottom center;
                will-change: transform, opacity;
                z-index: 5;
            }
            .t26-badge p {
                font-family: var(--t26-font-body);
                font-size: .82em;
                font-weight: 500;
                line-height: 140%;
                color: #7c4a1e;
                text-align: left;
            }

            /* Offset X & Y tiap subjudul — dikendalikan dari array
               $cultureNarratives: 'side' + 'offset' (X), 'dy' (Y).
               CATATAN: dengan acuan 'left', offset POSITIF = ke KANAN,
               offset NEGATIF = ke kiri (nilai 30% berarti blok mulai
               30% dari tepi kiri layar). */
            .t26-side-right { right: var(--t26-off); }
            .t26-side-left  { left: var(--t26-off); }
            .t26-block { transform: translateY(var(--t26-dy, 0px)); }

            /* ---------- Grid pulau: KOTAK TERPISAH setelah kotak utama.
               Latar putih penuh, bukan bagian dari gradasi — jadi mustahil
               menimpa garis & teks narasi. ---------- */
            .t26-islands {
                position: relative;
                z-index: 10;
                width: 100%;
                padding: 4em 1.125em 5em;
                background: var(--t26-white);
            }
            .t26-islands-inner { max-width: 66em; margin: 0 auto; }
            .t26-islands-label {
                display: block;
                font-family: var(--t26-font-body);
                font-size: .85em;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .16em;
                color: var(--t26-black);
                opacity: .6;
                margin-bottom: 1.4em;
                text-align: center;
            }
            .t26-islands-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1em;
            }
            .t26-island-card {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: .3em;
                padding: 1.8em 1.2em;
                border: 1px solid rgb(255 84 0 / .3);
                border-radius: 16px;
                background: var(--t26-white);
                text-decoration: none;
                transition: border-color .2s var(--ease-out-quart, ease-out),
                            box-shadow .2s var(--ease-out-quart, ease-out),
                            transform .2s var(--ease-out-quart, ease-out);
            }
            .t26-island-card:hover {
                border-color: var(--t26-orange);
                box-shadow: 0 2px 4px rgb(31 24 20 / .06), 0 10px 24px rgb(255 84 0 / .13);
                transform: translateY(-3px);
            }
            .t26-island-name {
                font-family: var(--t26-font-heading);
                font-size: 1.35em;
                font-weight: 600;
                text-transform: uppercase;
                color: var(--t26-black);
                text-align: center;
                line-height: 100%;
            }
            .t26-island-meta {
                font-family: var(--t26-font-body);
                font-size: .78em;
                font-weight: 500;
                color: var(--t26-orange);
            }

            /* ---------- Responsif ---------- */
            @media screen and (max-width: 991px) {
                .t26-layers { gap: 10em; }
                .t26-text { white-space: normal; max-width: 8em; }
            }
            @media screen and (max-width: 767px) {
                .t26-heading { font-size: 1.35em; }
                .t26-path-desktop { display: none; }
                .t26-path-mobile { display: block; position: relative; width: 100%; opacity: .22; z-index: 0; }
                .t26-path-mobile-dark {
                    display: block;
                    position: absolute;
                    inset: 0 0 auto;
                    width: 100%;
                    z-index: 1;
                }
                .t26-body { min-height: 0; overflow: hidden; }
                .t26-layers {
                    position: static;
                    transform: none;
                    height: auto;
                    gap: 8em;
                    padding: 2em 0 4em;
                }
                .t26-layer { font-size: .72em; height: auto; }
                .t26-badge { display: none; }
            }
            @media screen and (max-width: 479px) {
                .t26-heading { font-size: 1.05em; }
            }

            @media (prefers-reduced-motion: reduce) {
                .t26 .t26-svg, .t26 .t26-badge { opacity: 1 !important; transform: none !important; width: 4.6em !important; }
                .t26 .t26-path-dark path { stroke-dashoffset: 0 !important; }
                .t26 .t26-text.t26-ready .t26-word { opacity: 1 !important; transform: none !important; }
                .t26 .t26-num { opacity: .35 !important; transform: none !important; }
            }

            /* ---------- Smooth scroll ---------- */
            html { scroll-behavior: smooth; }
        </style>

        {{-- Welcome fullscreen: foto kolase budaya + lingkaran invert (difference) --}}
        <div class="t26-welcome" data-t26-welcome>
            <div class="t26-welcome-bg" aria-hidden="true">
                <img src="{{ asset('gambar/Indonesian Cultural Collage.png') }}" alt="" loading="eager" decoding="async">
            </div>
            <h1 class="t26-welcome-title">
                <span class="t26-line">SELAMAT</span>
                <span class="t26-line">DATANG DI</span>
                <span class="t26-line">BUDAYA</span>
                <span class="t26-line">INDONESIA</span>
            </h1>
            <div class="t26-orb" data-t26-orb aria-hidden="true"></div>
        </div>

        {{-- KOTAK UTAMA: header + body + islands dalam SATU kotak bergradasi.
            Gradasi: putih (sambungan dari welcome) → sedikit tembus pandang
            di tengah agar ada kedalaman → putih pekat lagi di bawah. --}}
        <div class="t26-main">
            <div class="t26-header">
                <div class="t26-heading-wrapper" data-t26-heading>
                    <h2 class="t26-heading is-orange" aria-hidden="true">MENGENAL BUDAYA NUSANTARA</h2>
                    <h2 class="t26-heading">MENGENAL BUDAYA NUSANTARA</h2>
                </div>
            </div>

            <div class="t26-body" data-t26-body>
            {{-- Sinar desktop: PATH ASLI two26.studio — persis, tanpa diubah --}}
            <div class="t26-path t26-path-desktop">
                <svg class="t26-path-light" width="100%" height="auto" viewBox="0 0 1616 2187" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M1601 15C1241.24 56.6913 1025.14 308.977 1139.71 377.393C1191.45 408.29 1240.74 339.444 1181.39 282.059C1075.45 179.627 937.582 215.243 741.458 298.287C701.638 315.147 622.383 350.59 552.298 410.201C487.298 465.486 487.725 549.018 534.765 563.77C581.804 578.523 632.714 579.634 681.058 587.392C729.731 595.202 767.011 617.17 797.836 657.756C833.766 705.065 838.864 772.034 862.127 825.767C890.491 891.283 966.341 911.2 1033.5 915.638C1130.56 922.052 1195.74 891.215 1286.16 899.069C1347.7 904.414 1457.75 932.208 1466.92 1059.42C1477.81 1210.68 1340.4 1252.99 1221.76 1286.05C1118.2 1314.91 997.775 1397.54 862.127 1334.5C757.468 1285.86 666.411 1293.34 650.401 1384.21C634.391 1475.07 710.968 1532.13 837.283 1542.81C963.598 1553.5 1146.44 1534.93 1181.39 1676C1226.53 1858.21 954.075 1839.5 797.836 1839.5C661.891 1839.5 422.283 1807.72 385.5 1700C340.427 1568 635.939 1584.37 465.5 1917C414.516 2016.5 310 2165.31 15 2165.31"
                          stroke="#FF5400" stroke-width="34" stroke-linecap="round" opacity="0.25"/>
                </svg>
                <svg class="t26-path-dark" width="100%" height="auto" viewBox="0 0 1616 2187" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path data-t26-draw stroke="#FF5400" stroke-width="34" stroke-linecap="round"
                          d="M1601 15C1241.24 56.6913 1025.14 308.977 1139.71 377.393C1191.45 408.29 1240.74 339.444 1181.39 282.059C1075.45 179.627 937.582 215.243 741.458 298.287C701.638 315.147 622.383 350.59 552.298 410.201C487.298 465.486 487.725 549.018 534.765 563.77C581.804 578.523 632.714 579.634 681.058 587.392C729.731 595.202 767.011 617.17 797.836 657.756C833.766 705.065 838.864 772.034 862.127 825.767C890.491 891.283 966.341 911.2 1033.5 915.638C1130.56 922.052 1195.74 891.215 1286.16 899.069C1347.7 904.414 1457.75 932.208 1466.92 1059.42C1477.81 1210.68 1340.4 1252.99 1221.76 1286.05C1118.2 1314.91 997.775 1397.54 862.127 1334.5C757.468 1285.86 666.411 1293.34 650.401 1384.21C634.391 1475.07 710.968 1532.13 837.283 1542.81C963.598 1553.5 1146.44 1534.93 1181.39 1676C1226.53 1858.21 954.075 1839.5 797.836 1839.5C661.891 1839.5 422.283 1807.72 385.5 1700C340.427 1568 635.939 1584.37 465.5 1917C414.516 2016.5 310 2165.31 15 2165.31"/>
                </svg>
            </div>

            {{-- Sinar mobile: PATH ASLI two26.studio — persis --}}
            <div class="t26-path t26-path-mobile">
                <svg viewBox="0 0 407 1556" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M542.705 10C465.727 48.981 92.8237 79.5116 92.8237 185.592C92.8237 291.673 352.712 336 346.322 440.868C339.933 545.736 104.278 543.251 92.8237 652.131C79.6013 777.82 353.287 765.564 346.322 898.5C339.358 1031.44 63.9084 911.5 68 1046C72.0916 1180.5 335.245 1170.41 328.309 1279.59C320.5 1402.5 -37.4522 1513.46 -135.704 1545.09"
                          stroke="#FF5400" stroke-width="6"/>
                </svg>
                <svg class="t26-path-mobile-dark" viewBox="0 0 407 1556" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path data-t26-draw d="M542.705 10C465.727 48.981 92.8237 79.5116 92.8237 185.592C92.8237 291.673 352.712 336 346.322 440.868C339.933 545.736 104.278 543.251 92.8237 652.131C79.6013 777.82 353.287 765.564 346.322 898.5C339.358 1031.44 63.9084 911.5 68 1046C72.0916 1180.5 335.245 1170.41 328.309 1279.59C320.5 1402.5 -37.4522 1513.46 -135.704 1545.09"
                          stroke="#FF5400" stroke-width="6"/>
                </svg>
            </div>

            {{-- 5 layer narasi budaya --}}
            <div class="t26-layers" data-t26-layers>
                @foreach($cultureNarratives as $n)
                    <div class="t26-layer">
                        <div class="t26-block t26-side-{{ $n['side'] }}" style="--t26-off: {{ $n['offset'] }}; --t26-dy: {{ $n['dy'] ?? '0px' }}">
                            <span class="t26-num" data-t26-num>{{ $n['num'] }}</span>
                            {{-- Teks KIRI (dianimasi per kata) --}}
                            <div class="t26-text" data-t26-text>{{ $n['left'] }}</div>

                            {{-- Logo di TENGAH: selalu di atas garis, muncul dengan
                                bounce dari garis (pola .service-svg two26) --}}
                            <div class="t26-svg" data-t26-svg>
                                @if($n['icon'] === 'islands')
                                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <circle cx="38" cy="42" r="18" stroke="#FF5400" stroke-width="5"/>
                                        <circle cx="66" cy="62" r="12" stroke="#FF5400" stroke-width="5"/>
                                        <path d="M12 78c12-8 24-8 36 0s24 8 36 0" stroke="#FF5400" stroke-width="5" stroke-linecap="round"/>
                                    </svg>
                                @elseif($n['icon'] === 'heritage')
                                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M20 44 50 18l30 26" stroke="#FF5400" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M28 44v34M72 44v34M20 78h60" stroke="#FF5400" stroke-width="5" stroke-linecap="round"/>
                                        <path d="M42 78V58h16v20" stroke="#FF5400" stroke-width="5" stroke-linejoin="round"/>
                                    </svg>
                                @elseif($n['icon'] === 'exchange')
                                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M22 38h44l-10-10M78 62H34l10 10" stroke="#FF5400" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="50" cy="50" r="34" stroke="#FF5400" stroke-width="4" stroke-dasharray="6 8"/>
                                    </svg>
                                @elseif($n['icon'] === 'preserve')
                                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M50 12 80 24v22c0 18-12 32-30 40-18-8-30-22-30-40V24Z" stroke="#FF5400" stroke-width="5" stroke-linejoin="round"/>
                                        <path d="m38 50 8 8 16-16" stroke="#FF5400" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <circle cx="50" cy="42" r="20" stroke="#FF5400" stroke-width="5"/>
                                        <path d="m62 58 14 22-12-4-4 12-14-22" stroke="#FF5400" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="50" cy="42" r="6" fill="#FF5400"/>
                                    </svg>
                                @endif
                            </div>

                            {{-- Teks KANAN --}}
                            <div class="t26-text" data-t26-text>{{ $n['right'] }}</div>

                            <div class="t26-badge" data-t26-badge>
                                <p>{{ $n['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        </div><!-- /.t26-main -->

        {{-- Navigasi pulau: kotak TERPISAH, putih penuh, di luar kotak utama --}}
        <div class="t26-islands">
            <div class="t26-islands-inner">
                <span class="t26-islands-label">Mulai Jelajah dari Pulau</span>
                <div class="t26-islands-grid">
                    @foreach($islands as $island)
                        <a href="{{ route('culture.island', $island) }}" class="t26-island-card">
                            <span class="t26-island-name">{{ $island->name }}</span>
                            <span class="t26-island-meta">{{ $island->provinces_count }} provinsi · {{ $island->regencies_count }} kab/kota</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <script>
        /* ============================================================
           Narasi Budaya — scrub engine vanilla (pola two26.studio)
           - Sinar "dark" tergambar & terhapus mengikuti scroll (dua arah)
           - Ikon & badge muncul dengan bounce dan HILANG saat scroll naik
           - Heading oranye bergerak magnetik halus mengikuti kursor
           ============================================================ */
        (function () {
            'use strict';

            var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function clamp01(v) { return Math.min(1, Math.max(0, v)); }

            /* ---------- 1. Welcome fullscreen: lingkaran invert teks ---------- */
            function initWelcomeOrb() {
                var zone = document.querySelector('[data-t26-welcome]');
                var orb = document.querySelector('[data-t26-orb]');
                if (!zone || !orb || reduced) return;

                var x = 0, y = 0;        /* posisi ter-smooth */
                var tx = 0, ty = 0;      /* target kursor */
                var scale = 0, targetScale = 0;
                var raf = null;

                function frame() {
                    /* Lerp halus mengikuti kursor (seperti kursor kustom teatika) */
                    x += (tx - x) * 0.18;
                    y += (ty - y) * 0.18;
                    scale += (targetScale - scale) * 0.14;

                    /* Jepit posisi agar lingkaran tak keluar dari welcome:
                       bawahnya tidak menuruni kotak "mengenal budaya",
                       kiri/kanan/atas tak melebihi tepi layar. */
                    var r = orb.offsetWidth / 2;
                    var minX = r, maxX = zone.clientWidth - r;
                    var minY = r, maxY = zone.clientHeight - r;
                    var px = Math.min(maxX, Math.max(minX, x));
                    var py = Math.min(maxY, Math.max(minY, y));

                    orb.style.transform =
                        'translate(' + (px - r) + 'px, ' + (py - r) + 'px) scale(' + scale.toFixed(3) + ')';

                    if (Math.abs(tx - x) > 0.3 || Math.abs(ty - y) > 0.3 || Math.abs(targetScale - scale) > 0.001) {
                        raf = requestAnimationFrame(frame);
                    } else {
                        raf = null;
                    }
                }

                function kick() { if (raf === null) raf = requestAnimationFrame(frame); }

                zone.addEventListener('pointerenter', function () {
                    targetScale = 1;
                    kick();
                });
                zone.addEventListener('pointermove', function (e) {
                    var rect = zone.getBoundingClientRect();
                    tx = e.clientX - rect.left;
                    ty = e.clientY - rect.top;
                    if (scale < 0.01) { x = tx; y = ty; } /* lompat tanpa animasi saat masuk */
                    kick();
                }, { passive: true });
                zone.addEventListener('pointerleave', function () {
                    targetScale = 0;
                    kick();
                });
            }

            /* ---------- 2. Heading magnetik dua lapis ---------- */
            function initMagneticHeading() {
                var wrapper = document.querySelector('[data-t26-heading]');
                var orange = wrapper && wrapper.querySelector('.t26-heading.is-orange');
                if (!wrapper || !orange || reduced) return;

                var MAX = 3;
                var zone = wrapper.closest('.t26-header') || wrapper;

                zone.addEventListener('pointermove', function (e) {
                    var r = wrapper.getBoundingClientRect();
                    var dx = e.clientX - (r.left + r.width / 2);
                    var dy = e.clientY - (r.top + r.height / 2);
                    var dist = Math.hypot(dx, dy);
                    var maxR = Math.max(r.width, r.height) / 2 || 100;
                    var intensity = Math.min(1, Math.max(0.25, dist / maxR));
                    var angle = Math.atan2(dy, dx);
                    orange.style.transform =
                        'translate(' + (Math.cos(angle) * intensity * MAX).toFixed(2) + 'px, ' +
                                        (Math.sin(angle) * intensity * MAX).toFixed(2) + 'px)';
                }, { passive: true });

                zone.addEventListener('pointerleave', function () {
                    orange.style.transition = 'transform .6s cubic-bezier(.34,1.56,.64,1)';
                    orange.style.transform = 'translate(0,0)';
                    setTimeout(function () { orange.style.transition = ''; }, 650);
                });
            }

            /* ---------- 2. Scrub sinar + expansion layer (dua arah) ---------- */
            function initServicesScrub() {
                var section = document.querySelector('[data-t26-body]');
                if (!section) return;

                /* ============================================================
                   ⏱ KONTROL WAKTU (SEMUA ANGKA = PROGRESS SCROLL 0..1)
                   0 = garis mulai tergambar, 1 = garis selesai penuh.
                   ============================================================ */
                // [1] KAPAN SUBJUDUL MULAI & SELESAI
                var BOX_START = 0.04;  // ← garis 4% → subjudul pertama mulai
                var BOX_END   = 1.0;   // ← garis 100% → semua subjudul selesai
                var STEP      = 0.16;  // ← DURASI animasi 1 subjudul (16% scroll)

                var paths = Array.prototype.slice.call(section.querySelectorAll('[data-t26-draw]'));
                var layers = Array.prototype.slice.call(section.querySelectorAll('.t26-layer'));

                // [2] JADWAL TIAP SUBJUDUL (urutan muncul) — KUSTOM MANUAL
                //     Muncul: no1=0.24, no2=0.31, no3=0.59, no4=0.71, no5=0.94
                //     (no 1 = 0.14+0.10, no 2 = 0.34-0.03, no 3 = 0.54+0.05,
                //      no 4 = 0.74-0.03)
                //     Selesai = mulai + durasi. No 5 selesai saat garis penuh.
                //     DI SCOPE FUNGSI UTAMA agar apply() bisa mengaksesnya.
                var points = [0.24, 0.31, 0.59, 0.71, 0.94];
                // Fallback otomatis bila jumlah layer ≠ panjang points:
                if (points.length !== layers.length) {
                    points = [];
                    for (var k = 0; k < layers.length; k++) {
                        points.push(layers.length > 1
                            ? BOX_START + (BOX_END - BOX_START - STEP) * (k / (layers.length - 1))
                            : BOX_START);
                    }
                }

                var draws = paths.map(function (p) {
                    var len = p.getTotalLength ? p.getTotalLength() : 2500;
                    p.style.strokeDasharray = len;
                    p.style.strokeDashoffset = reduced ? 0 : len;
                    return { el: p, len: len };
                });

                var items = layers.map(function (layer, i, arr) {
                    var svg = layer.querySelector('[data-t26-svg]');
                    var badge = layer.querySelector('[data-t26-badge]');
                    var targetW = 0;
                    if (svg) {
                        targetW = svg.getBoundingClientRect().width ||
                                  parseFloat(getComputedStyle(svg).width) || 40;
                    }

                    return {
                        point: points[i],
                        svg: svg, badge: badge, targetW: targetW
                    };
                });

                /* Split teks KIRI & KANAN per-kata (pola SplitText two26):
                   tiap kata dibungkus span.t26-word agar bisa dianimasikan
                   berurutan (stagger). Satu layer bisa punya 2 teks. */
                var textItems = [];
                if (!reduced) {
                    layers.forEach(function (layer, i) {
                        var numEl = layer.querySelector('[data-t26-num]');
                        var textEls = Array.prototype.slice.call(layer.querySelectorAll('[data-t26-text]'));
                        var groups = [];

                        textEls.forEach(function (textEl) {
                            textEl.classList.add('t26-ready');
                            var words = textEl.textContent.trim().split(/\s+/);
                            textEl.textContent = '';
                            var spans = words.map(function (w, wi) {
                                var s = document.createElement('span');
                                s.className = 't26-word';
                                s.textContent = w;
                                if (wi > 0) textEl.appendChild(document.createTextNode(' '));
                                textEl.appendChild(s);
                                return s;
                            });
                            groups.push(spans);
                        });

                        if (groups.length) {
                            textItems.push({ index: i, groups: groups, num: numEl });
                        }
                    });
                } else {
                    /* Reduced motion: pastikan teks & nomor terlihat */
                    layers.forEach(function (layer) {
                        layer.querySelectorAll('[data-t26-text]').forEach(function (textEl) {
                            textEl.classList.add('t26-ready');
                        });
                    });
                }

                function reset() {
                    /* Kondisi awal = kondisi "sebelum kotak aktif".
                       Logo TIDAK di-set width 0 (lebar dikelola CSS;
                       animasinya translateY+scale via apply()). */
                    items.forEach(function (it) {
                        if (it.svg) {
                            it.svg.style.opacity = '0';
                            it.svg.style.transform = 'translateY(1.2em) scale(.6)';
                        }
                        if (it.badge) {
                            it.badge.style.transform = 'scale(0) rotate(0deg)';
                            it.badge.style.opacity = '0';
                        }
                    });
                    /* Teks: kata-kata tersembunyi (CSS .t26-word sudah 0). */
                }
                if (!reduced) reset();

                var current = 0;
                var target = 0;
                var ticking = false;

                function measure() {
                    var rect = section.getBoundingClientRect();
                    var vh = window.innerHeight || 1;
                    var startY = rect.top + window.scrollY - vh * 0.7;
                    var endY = rect.top + window.scrollY + rect.height - vh * 0.7;
                    var p = (window.scrollY - startY) / Math.max(1, endY - startY);
                    target = clamp01(p);
                }

                /* Progress kotak dinormalisasi ke [0,1] dalam BOX_START..BOX_END */
                function boxProgress(p) {
                    var span = Math.max(0.001, BOX_END - BOX_START);
                    return clamp01((p - BOX_START) / span);
                }

                function apply(p) {
                    /* Sinar: tergambar saat turun, terhapus saat naik */
                    draws.forEach(function (d) {
                        d.el.style.strokeDashoffset = d.len * (1 - clamp01(p));
                    });

                    var bp = boxProgress(p);

                    /* LOGO: animasi seperti TEKS PANJANG — slide-up + fade
                       mengikuti scroll (bukan pop width), sama dengan kata.
                       Origin tetap bawah agar terasa tumbuh dari garis.
                       Durasi tiap kotak = jarak ke jadwal kotak berikutnya
                       (no 5 dipangkas agar selesai tepat saat garis penuh). */
                    items.forEach(function (it, idx) {
                        var dur = (idx < items.length - 1)
                            ? Math.min(STEP, points[idx + 1] - points[idx])
                            : (BOX_END - points[idx]); /* no 5: selesai di p=1 */
                        var local = (bp - it.point) / Math.max(0.001, dur);

                        if (it.svg) {
                            if (local <= 0) {
                                it.svg.style.opacity = '0';
                                it.svg.style.transform = 'translateY(1.2em) scale(.6)';
                            } else {
                                var ts = clamp01(local * 1.6);
                                var se = 1 - Math.pow(1 - ts, 3); /* cubic out */
                                it.svg.style.opacity = String(se);
                                it.svg.style.transform =
                                    'translateY(' + (1.2 * (1 - se)).toFixed(3) + 'em) scale(' + (0.6 + 0.4 * se).toFixed(4) + ')';
                            }
                        }
                        if (it.badge) {
                            if (local <= 0) {
                                it.badge.style.opacity = '0';
                                it.badge.style.transform = 'scale(0) rotate(0deg)';
                            } else {
                                // [3] KETERLAMBATAN BADGE setelah judulnya (0.15)
                                //     Perbesar = badge makin terlambat muncul.
                                var tb = clamp01((local - 0.15) / STEP);
                                var e2 = 1 + 3.2 * Math.pow(tb - 1, 3) + 2.2 * Math.pow(tb - 1, 2);
                                it.badge.style.opacity = String(clamp01(local * 3));
                                it.badge.style.transform = 'scale(' + e2.toFixed(4) + ') rotate(' + (-4 * tb).toFixed(2) + 'deg)';
                            }
                        }
                    });

                    /* Teks kiri & kanan: slide-up per-kata dengan stagger.
                       Kata-kata kedua kelompok berlanjut setelah kelompok
                       pertama — seperti SplitText two26 yang memecah
                       seluruh baris. Scroll naik = kata turun & memudar. */
                    textItems.forEach(function (ti) {
                        var it = items[ti.index];
                        if (!it) return;
                        /* Durasi sama dengan logo: sampai jadwal kotak berikutnya,
                           kotak terakhir selesai tepat saat garis penuh (p=1). */
                        var dur = (ti.index < items.length - 1)
                            ? Math.min(STEP, points[ti.index + 1] - points[ti.index])
                            : (BOX_END - points[ti.index]);
                        var local = (bp - it.point) / Math.max(0.001, dur);

                        if (local <= 0) {
                            ti.groups.forEach(function (spans) {
                                spans.forEach(function (s) {
                                    s.style.opacity = '0';
                                    s.style.transform = 'translateY(.55em)';
                                });
                            });
                            if (ti.num) {
                                ti.num.style.opacity = '0';
                                ti.num.style.transform = 'translateY(.4em)';
                            }
                            return;
                        }

                        var t = clamp01(local);
                        /* Total kata = gabungan kiri + kanan, stagger merata */
                        var wi = 0;
                        ti.groups.forEach(function (spans) {
                            spans.forEach(function (s) {
                                // [4] STAGGER ANTAR KATA
                                //     0.1 = jeda tiap kata setelah kata sebelumnya
                                //     0.55 = durasi animasi satu kata
                                var wt = clamp01((t - 0.1 * wi) / 0.55);
                                var we = 1 - Math.pow(1 - wt, 3); /* cubic out */
                                s.style.opacity = String(we);
                                s.style.transform = 'translateY(' + (0.55 * (1 - we)).toFixed(3) + 'em)';
                                wi++;
                            });
                        });

                        if (ti.num) {
                            var ne = 1 - Math.pow(1 - clamp01(t * 2), 3);
                            ti.num.style.opacity = String(0.35 * ne);
                            ti.num.style.transform = 'translateY(' + (0.4 * (1 - ne)).toFixed(3) + 'em)';
                        }
                    });
                }

                function frame() {
                    measure();
                    current += (target - current) * 0.14;
                    if (Math.abs(target - current) < 0.0005) current = target;
                    apply(current);
                    if (current !== target || target > 0) {
                        requestAnimationFrame(frame);
                    } else {
                        ticking = false;
                    }
                }

                function kick() {
                    if (!ticking) { ticking = true; requestAnimationFrame(frame); }
                }

                if (reduced) { apply(1); return; }

                window.addEventListener('scroll', kick, { passive: true });
                window.addEventListener('resize', kick);
                kick();
            }

            function init() {
                initWelcomeOrb();
                initMagneticHeading();
                initServicesScrub();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
@endsection
