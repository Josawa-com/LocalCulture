@extends('layouts.app')

@section('title', 'Sanggar & Tempat Latihan')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:py-10">

        {{-- Header --}}
        <header class="text-center">
            <span class="kicker">Tempat Latihan</span>
            <h1 class="display-lg mt-2 text-3xl text-bark-950 sm:text-4xl">Sanggar Seni Yogyakarta</h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-bark-600">
                Dari tari klasik, karawitan, wayang, hingga batik — temukan tempat belajar budaya
                di lima kabupaten/kota Daerah Istimewa Yogyakarta.
                Ketuk nama sanggar atau tombol peta untuk membukanya langsung di Google Maps.
            </p>
        </header>

        {{-- Pencarian & filter --}}
        <form method="GET" action="{{ route('venues.index') }}" class="mt-7" role="search">
            <div class="mx-auto flex max-w-2xl flex-col gap-2 sm:flex-row">
                <label for="venue-search" class="sr-only">Cari sanggar atau materi</label>
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-bark-400">
                        <x-icon name="search" size="16" />
                    </span>
                    <input id="venue-search" type="search" name="q" value="{{ $search }}"
                           placeholder="Cari nama sanggar, tari, batik, gamelan…"
                           class="field pl-9">
                </div>
                <button type="submit" class="btn-primary shrink-0 sm:px-6">Cari</button>
            </div>

            {{-- Filter wilayah --}}
            <div class="mt-4">
                <p class="mb-2 text-center text-xs font-semibold uppercase tracking-wider text-bark-500">Wilayah</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ route('venues.index', array_filter(['q' => $search, 'kategori' => $category])) }}"
                       @if($regencySlug === '') aria-current="page" @endif
                       class="tag border px-3 py-1.5 text-xs transition {{ $regencySlug === '' ? 'border-bark-900 bg-bark-900 text-white' : 'border-bark-300 bg-white/60 text-bark-700 hover:border-bark-400 hover:bg-white' }}">
                        Semua
                    </a>
                    @foreach($regencies as $regency)
                        <a href="{{ route('venues.index', array_filter(['q' => $search, 'kategori' => $category, 'wilayah' => $regency->slug])) }}"
                           @if($regencySlug === $regency->slug) aria-current="page" @endif
                           class="tag border px-3 py-1.5 text-xs transition {{ $regencySlug === $regency->slug ? 'border-bark-900 bg-bark-900 text-white' : 'border-bark-300 bg-white/60 text-bark-700 hover:border-bark-400 hover:bg-white' }}">
                            {{ $regency->slug === 'kota-jogja' ? 'Kota Yogyakarta' : $regency->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Filter kategori --}}
            @if($categories->isNotEmpty())
                <div class="mt-3">
                    <p class="mb-2 text-center text-xs font-semibold uppercase tracking-wider text-bark-500">Jenis</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <a href="{{ route('venues.index', array_filter(['q' => $search, 'wilayah' => $regencySlug])) }}"
                           @if($category === '') aria-current="page" @endif
                           class="tag border px-3 py-1.5 text-xs transition {{ $category === '' ? 'border-brass-600 bg-brass-600 text-white' : 'border-bark-300 bg-white/60 text-bark-700 hover:border-bark-400 hover:bg-white' }}">
                            Semua jenis
                        </a>
                        @foreach($categories as $item)
                            <a href="{{ route('venues.index', array_filter(['q' => $search, 'wilayah' => $regencySlug, 'kategori' => $item])) }}"
                               @if($category === $item) aria-current="page" @endif
                               class="tag border px-3 py-1.5 text-xs transition {{ $category === $item ? 'border-brass-600 bg-brass-600 text-white' : 'border-bark-300 bg-white/60 text-bark-700 hover:border-bark-400 hover:bg-white' }}">
                                {{ $item }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </form>

        {{-- Ringkasan hasil --}}
        <p class="mt-6 text-center text-xs text-bark-500" aria-live="polite">
            {{ $total }} sanggar ditemukan
            @if($search !== '')
                untuk “{{ $search }}”
            @endif
        </p>

        @if($total === 0)
            {{-- Kosong --}}
            <div class="panel mt-6 px-6 py-14 text-center">
                <x-icon name="search" size="32" class="mx-auto text-bark-300" />
                <h2 class="display-lg mt-4 text-lg text-bark-900">Belum ada sanggar yang cocok</h2>
                <p class="mx-auto mt-2 max-w-sm text-sm text-bark-600">
                    Coba kata kunci lain, atau lihat semua sanggar yang terdaftar.
                </p>
                <a href="{{ route('venues.index') }}" class="btn-primary mt-5">Lihat Semua Sanggar</a>
            </div>
        @else
            {{-- Daftar per wilayah --}}
            <div class="mt-6 space-y-8">
                @foreach($grouped as $slug => $venues)
                    @php
                        $regency = $venues->first()->regency;
                        $regencyName = match ($regency?->slug) {
                            'kota-jogja' => 'Kota Yogyakarta',
                            null => 'Lainnya',
                            default => $regency->name,
                        };
                        $verifiedCount = $venues->filter(fn ($v) => $v->hasCoordinates())->count();
                    @endphp

                    <section aria-labelledby="wilayah-{{ $slug }}">
                        {{-- Judul wilayah --}}
                        <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1 border-b border-bark-200 pb-2">
                            <h2 id="wilayah-{{ $slug }}" class="display-lg flex items-center gap-2 text-lg text-bark-950">
                                <span class="h-2 w-2 rounded-full bg-brass-500" aria-hidden="true"></span>
                                {{ $regencyName }}
                            </h2>
                            <p class="text-xs text-bark-500">
                                {{ $venues->count() }} sanggar
                                @if($verifiedCount > 0)
                                    <span class="mx-1 text-bark-300">·</span>
                                    {{ $verifiedCount }} berkoordinat
                                @endif
                            </p>
                        </div>

                        <ul class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($venues as $venue)
                                <li class="panel panel-hover flex flex-col p-4">
                                    {{-- Nama: tautan ke Google Maps --}}
                                    <a href="{{ $venue->mapsUrl() }}" target="_blank" rel="noopener noreferrer"
                                       class="group flex items-start justify-between gap-2"
                                       title="Cari {{ $venue->name }} di Google Maps">
                                        <h3 class="display-lg text-base leading-snug text-bark-950 transition-colors group-hover:text-brass-700">
                                            {{ $venue->name }}
                                        </h3>
                                        <span class="mt-0.5 shrink-0 text-bark-400 transition-colors group-hover:text-brass-700" aria-hidden="true">
                                            <x-icon name="arrow-right" size="15" />
                                        </span>
                                    </a>

                                    {{-- Kategori + status koordinat --}}
                                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                        <span class="tag bg-brass-100 text-brass-800">{{ $venue->category ?? 'Lainnya' }}</span>
                                        @if($venue->hasCoordinates())
                                            <span class="tag bg-signal-success/10 text-signal-success">
                                                <x-icon name="map-pin" size="11" /> Koordinat terverifikasi
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Yang dipelajari --}}
                                    <p class="mt-2.5 flex-1 text-xs leading-relaxed text-bark-600">
                                        {{ $venue->skills ?: $venue->description ?: 'Tempat belajar seni dan budaya tradisional.' }}
                                    </p>

                                    {{-- Aksi --}}
                                    <div class="mt-3 flex items-center gap-2">
                                        <a href="{{ $venue->mapsUrl() }}" target="_blank" rel="noopener noreferrer"
                                           class="btn-secondary btn-sm flex-1 justify-center gap-1.5">
                                            <x-icon name="map-pin" size="13" />
                                            Buka di Google Maps
                                        </a>
                                        @auth
                                            <form method="POST" action="{{ route('venues.interested', $venue) }}">
                                                @csrf
                                                <button type="submit" class="btn-ghost btn-sm" title="Tandai tertarik ikut latihan">
                                                    <x-icon name="handshake" size="13" />
                                                    <span class="sr-only">Tertarik ikut latihan di {{ $venue->name }}</span>
                                                </button>
                                            </form>
                                        @endauth
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>

            {{-- Catatan kaki --}}
            <p class="mx-auto mt-10 max-w-2xl text-center text-xs leading-relaxed text-bark-500">
                Sebagian besar sanggar belum memiliki koordinat resmi. Tombol peta mencari namanya langsung
                di Google Maps; periksa kembali alamat dan jadwal latihan sebelum datang.
            </p>
        @endif
    </div>
@endsection
