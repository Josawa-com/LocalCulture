@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    @php
        $stats = [
            ['value' => $cultureCount, 'label' => 'Materi Budaya'],
            ['value' => $provinceCount, 'label' => 'Provinsi'],
            ['value' => $regencyCount, 'label' => 'Kabupaten/Kota'],
            ['value' => $questCount + $mysteryCount, 'label' => 'Kuis & Misteri'],
        ];
    @endphp

    {{-- ============================================================
         HERO — layar pembuka
         ============================================================ --}}
    <section class="relative overflow-hidden border-b border-bark-200">
        {{-- Latar: foto kolase budaya + gradasi parchment --}}
        <div class="absolute inset-0" aria-hidden="true">
            <img src="{{ asset('gambar/Indonesian Cultural Collage.png') }}"
                 alt=""
                 class="h-full w-full object-cover object-center opacity-[0.18]"
                 loading="eager" decoding="async">
            <div class="absolute inset-0 bg-gradient-to-b from-parchment/70 via-parchment/85 to-parchment"></div>
            <div class="absolute inset-0 bg-[radial-gradient(rgb(138_108_78_/_0.06)_1px,transparent_1px)] [background-size:22px_22px]"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:py-24">
            <div class="grid items-center gap-12 lg:grid-cols-[1.15fr_0.85fr]">
                {{-- Kiri: teks --}}
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-brass-300 bg-brass-50/80 px-3 py-1 text-xs font-semibold text-brass-800">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-brass-600" aria-hidden="true"></span>
                        Arsip Budaya Digital Nusantara
                    </span>

                    <h1 class="display-lg mt-5 text-4xl leading-[1.05] tracking-tight text-bark-950 sm:text-5xl lg:text-6xl">
                        Jelajahi
                        <span class="relative inline-block">
                            <span class="relative z-10 text-brass-700">Budaya</span>
                            <span class="absolute inset-x-0 bottom-1 z-0 h-3 bg-brass-200/70" aria-hidden="true"></span>
                        </span>
                        <br class="hidden sm:block">
                        Nusantara
                    </h1>

                    <p class="mt-5 max-w-lg text-base leading-relaxed text-bark-700">
                        Dari rumah adat Sumatera hingga tarian Papua — pelajari filosofi,
                        makna, dan warisan budaya Indonesia dalam satu tempat. Lengkap dengan
                        kuis, minigame, dan papan peringkat.
                    </p>

                    {{-- Tombol aksi utama --}}
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ route('culture.islands') }}" class="btn-primary">
                            <x-icon name="book-open" size="16"/>
                            Mulai Jelajah
                        </a>
                        <a href="{{ route('mystery.index') }}" class="btn-accent">
                            <x-icon name="sparkles" size="16"/>
                            Main Mystery
                        </a>
                        <a href="{{ route('quests.index') }}" class="btn-secondary">
                            Kerjakan Kuis
                        </a>
                    </div>

                    {{-- Statistik ringkas --}}
                    <dl class="mt-10 grid max-w-lg grid-cols-2 gap-x-6 gap-y-5 border-t border-bark-200 pt-6 sm:grid-cols-4">
                        @foreach($stats as $stat)
                            <div>
                                <dd class="display-lg text-2xl tabular-nums text-bark-950">{{ number_format($stat['value'], 0, ',', '.') }}</dd>
                                <dt class="mt-0.5 text-[11px] font-medium uppercase tracking-wide text-bark-500">{{ $stat['label'] }}</dt>
                            </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Kanan: kartu visual --}}
                <div class="relative hidden lg:block">
                    {{-- Kartu utama --}}
                    <div class="relative overflow-hidden rounded-2xl border border-bark-200 bg-white shadow-raised">
                        <div class="aspect-[4/5] w-full bg-gradient-to-br from-bark-100 to-brass-100">
                            <img src="{{ asset('gambar/CandiBorobudur-removebg-preview.png') }}"
                                 alt="Candi Borobudur"
                                 class="h-full w-full object-contain p-8 drop-shadow-xl"
                                 loading="eager" decoding="async">
                        </div>
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-bark-950/85 to-transparent p-5 pt-14">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-brass-200">Warisan Dunia</p>
                            <p class="display-lg mt-1 text-lg text-white">Candi Borobudur</p>
                            <p class="mt-0.5 text-xs text-white/70">Jawa Tengah · Abad ke-8</p>
                        </div>
                    </div>

                    {{-- Kartu kecil menumpuk --}}
                    <div class="absolute -left-10 top-10 w-36 rotate-[-6deg] overflow-hidden rounded-xl border border-bark-200 bg-white p-2 shadow-raised">
                        <img src="{{ asset('gambar/WayangSemarBG.png') }}" alt="Wayang Semar"
                             class="h-28 w-full object-contain" loading="lazy" decoding="async">
                        <p class="mt-1 text-center text-[10px] font-semibold uppercase tracking-wide text-bark-600">Wayang</p>
                    </div>

                    <div class="absolute -bottom-6 -right-6 w-32 rotate-[8deg] overflow-hidden rounded-xl border border-bark-200 bg-white p-2 shadow-raised">
                        <img src="{{ asset('gambar/TariKecak.png') }}" alt="Tari Kecak"
                             class="h-24 w-full object-contain" loading="lazy" decoding="async">
                        <p class="mt-1 text-center text-[10px] font-semibold uppercase tracking-wide text-bark-600">Tari Kecak</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         EMPAT PILAR
         ============================================================ --}}
    <section class="mx-auto max-w-7xl px-4 py-16">
        <div class="mx-auto max-w-2xl text-center">
            <span class="kicker">Apa yang bisa kamu lakukan</span>
            <h2 class="display-lg mt-2 text-3xl text-bark-950 sm:text-4xl">Empat Cara Menjelajah</h2>
            <p class="mt-3 text-sm leading-relaxed text-bark-600">
                Belajar, bermain, menonton, dan berlatih — semuanya dalam satu platform budaya.
            </p>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                [
                    'icon' => 'book-open',
                    'title' => 'Materi Budaya',
                    'text' => 'Telusuri budaya dari 6 pulau, '.$provinceCount.' provinsi, hingga tingkat kabupaten/kota.',
                    'route' => route('culture.islands'),
                    'cta' => 'Jelajahi Materi',
                    'count' => $cultureCount.' materi',
                ],
                [
                    'icon' => 'graduation',
                    'title' => 'Kuis Budaya',
                    'text' => 'Uji wawasan dengan kuis harian, mingguan, bulanan, dan kuis pengetahuan.',
                    'route' => route('quests.index'),
                    'cta' => 'Kerjakan Kuis',
                    'count' => $questCount.' kuis tersedia',
                ],
                [
                    'icon' => 'sparkles',
                    'title' => 'Mystery Budaya',
                    'text' => 'Tebak budaya dari petunjuk. Poin maksimal 300, salah 7 kali gagal.',
                    'route' => route('mystery.index'),
                    'cta' => 'Main Mystery',
                    'count' => $mysteryCount.' misteri',
                ],
                [
                    'icon' => 'ticket',
                    'title' => 'Pertunjukan & Sanggar',
                    'text' => 'Temukan pertunjukan budaya terdekat dan sanggar latihan di sekitarmu.',
                    'route' => route('shows.index'),
                    'cta' => 'Lihat Pertunjukan',
                    'count' => $upcomingCount.' acara mendatang',
                ],
            ] as $pillar)
                <a href="{{ $pillar['route'] }}"
                   class="panel panel-hover group flex flex-col p-5">
                    <span class="grid h-10 w-10 place-items-center rounded-lg bg-brass-100 text-brass-800">
                        <x-icon :name="$pillar['icon']" size="20"/>
                    </span>
                    <h3 class="display-lg mt-4 text-base text-bark-950">{{ $pillar['title'] }}</h3>
                    <p class="mt-1.5 flex-1 text-sm leading-relaxed text-bark-600">{{ $pillar['text'] }}</p>
                    <span class="mt-4 flex items-center justify-between text-xs font-semibold text-brass-700">
                        {{ $pillar['cta'] }}
                        <span class="text-bark-400 transition group-hover:translate-x-0.5 group-hover:text-brass-700">
                            <x-icon name="arrow-right" size="14"/>
                        </span>
                    </span>
                    <span class="mt-1 text-[11px] text-bark-400">{{ $pillar['count'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============================================================
         JELAJAH PER PULAU
         ============================================================ --}}
    <section class="border-y border-bark-200 bg-bark-50/60">
        <div class="mx-auto max-w-7xl px-4 py-16">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <span class="kicker">Enam Pulau</span>
                    <h2 class="display-lg mt-2 text-3xl text-bark-950">Mulai dari Pulau Mana?</h2>
                </div>
                <a href="{{ route('culture.islands') }}" class="text-sm font-semibold text-brass-700 hover:underline">
                    Lihat semua materi →
                </a>
            </div>

            <ul class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($islands as $island)
                    <li>
                        <a href="{{ route('culture.island', $island) }}"
                           class="panel panel-hover group flex items-center justify-between gap-3 px-5 py-4">
                            <div class="min-w-0">
                                <h3 class="display-lg text-base text-bark-950">{{ $island->name }}</h3>
                                <p class="mt-0.5 text-xs text-bark-500">
                                    {{ $island->provinces_count }} provinsi · {{ $island->regencies_count }} kab/kota
                                </p>
                            </div>
                            <span class="shrink-0 text-bark-300 transition group-hover:translate-x-0.5 group-hover:text-brass-600">
                                <x-icon name="chevron-right" size="18"/>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ============================================================
         SOROTAN BUDAYA
         ============================================================ --}}
    @if($featuredCultures->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16">
            <div class="mx-auto max-w-2xl text-center">
                <span class="kicker">Sorotan</span>
                <h2 class="display-lg mt-2 text-3xl text-bark-950">Sekilas Kekayaan Budaya</h2>
                <p class="mt-3 text-sm text-bark-600">
                    Sebagian materi yang bisa kamu pelajari — dari rumah adat hingga kuliner khas.
                </p>
            </div>

            <ul class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($featuredCultures as $culture)
                    <li class="panel panel-hover group flex h-full flex-col p-5">
                        <span class="tag w-fit bg-brass-100 text-brass-800">
                            {{ $culture->category?->name ?? 'Budaya' }}
                        </span>
                        <h3 class="display-lg mt-3 text-base leading-tight text-bark-950">{{ $culture->name }}</h3>
                        <p class="mt-1.5 line-clamp-3 flex-1 text-sm leading-relaxed text-bark-600">
                            {{ $culture->description }}
                        </p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- ============================================================
         CTA PENUTUP
         ============================================================ --}}
    <section class="border-t border-bark-200 bg-bark-950">
        <div class="mx-auto max-w-4xl px-4 py-16 text-center">
            <span class="kicker text-brass-300">Gratis & Terbuka</span>
            <h2 class="display-lg mt-2 text-3xl leading-tight text-white sm:text-4xl">
                Lestarikan budaya, mulai dari rasa ingin tahu.
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-white/70">
                Buat akun gratis untuk mengerjakan kuis, memecahkan misteri, dan bersaing
                di papan peringkat bersama pemain lain.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @guest
                    <a href="{{ route('register') }}" class="btn-accent">Daftar Gratis</a>
                    <a href="{{ route('login') }}" class="rounded-md border border-white/25 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                        Sudah punya akun? Masuk
                    </a>
                @else
                    <a href="{{ route('quests.index') }}" class="btn-accent">Kerjakan Kuis</a>
                    <a href="{{ route('profile') }}" class="rounded-md border border-white/25 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                        Buka Profil
                    </a>
                @endguest
            </div>
        </div>
    </section>
@endsection
