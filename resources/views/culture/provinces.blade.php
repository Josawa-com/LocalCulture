@extends('layouts.app')

@section('title', 'Provinsi di '.$island->name)

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-10">
        {{-- Navigasi kembali --}}
        <a href="{{ route('culture.islands') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-bark-600 transition hover:text-bark-900">
            <x-icon name="arrow-left" size="14"/>
            Semua Pulau
        </a>

        {{-- Header --}}
        <header class="mt-4">
            <span class="kicker">Materi Budaya</span>
            <h1 class="display-lg mt-1 text-3xl text-bark-950 sm:text-4xl">Pulau {{ $island->name }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-bark-600">
                Pilih provinsi untuk melihat kabupaten/kota beserta materi budayanya.
                @if(count($provinces) > 0)
                    <span class="font-medium text-bark-800">{{ count($provinces) }} provinsi</span> tersedia di pulau ini.
                @endif
            </p>
        </header>

        {{-- Keterangan untuk tamu yang terkunci pada satu provinsi --}}
        @guest
            @if($lockedProvinceId)
                <div class="mt-6">
                    <x-flash type="info">
                        Sebagai tamu, kamu hanya bisa membuka satu provinsi.
                        <a href="{{ route('register') }}" class="font-semibold underline">Daftar gratis</a>
                        untuk akses semua provinsi.
                    </x-flash>
                </div>
            @endif
        @endguest

        {{-- Daftar provinsi --}}
        @if(count($provinces) === 0)
            <div class="panel mt-8 px-6 py-14 text-center">
                <x-icon name="map-pin" size="32" class="mx-auto text-bark-300"/>
                <h2 class="display-lg mt-4 text-lg text-bark-900">Belum ada provinsi</h2>
                <p class="mx-auto mt-2 max-w-sm text-sm text-bark-600">
                    Data provinsi untuk pulau ini belum tersedia.
                </p>
            </div>
        @else
            <ul class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($provinces as $province)
                    @php
                        $isLocked = $province['locked'] || $province['url'] === null;
                        $tag = $isLocked ? 'div' : 'a';
                    @endphp

                    <li>
                        <{{ $tag }}
                            @if(! $isLocked) href="{{ $province['url'] }}" @endif
                            class="panel group flex h-full flex-col overflow-hidden transition
                                   {{ $isLocked
                                        ? 'opacity-70'
                                        : 'hover:-translate-y-0.5 hover:border-brass-400 hover:shadow-raised' }}"
                            @if($isLocked) aria-disabled="true" @endif>

                            {{-- Teks: nama provinsi + jumlah kabupaten/kota --}}
                            <div class="flex-1 p-5">
                                <div class="flex items-start justify-between gap-2">
                                    <h2 class="display-lg text-lg leading-tight text-bark-950">
                                        {{ $province['name'] }}
                                    </h2>

                                    @if($isLocked)
                                        <span class="mt-0.5 shrink-0 text-bark-400" title="Terkunci">
                                            <x-icon name="lock" size="16"/>
                                        </span>
                                    @else
                                        <span class="mt-0.5 shrink-0 text-bark-300 transition group-hover:text-brass-600">
                                            <x-icon name="arrow-right" size="16"/>
                                        </span>
                                    @endif
                                </div>

                                @if($province['regencies'] !== null || $province['cities'] !== null)
                                    <p class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-medium text-bark-600">
                                        @if($province['regencies'] !== null)
                                            <span>{{ $province['regencies'] }} kabupaten</span>
                                        @endif
                                        @if($province['cities'] !== null)
                                            <span class="inline-flex items-center gap-1.5">
                                                <span class="h-1 w-1 rounded-full bg-bark-300" aria-hidden="true"></span>
                                                {{ $province['cities'] }} kota
                                            </span>
                                        @endif
                                    </p>
                                @endif

                                @if($province['note'])
                                    <p class="mt-1 text-xs text-bark-500">{{ $province['note'] }}</p>
                                @endif
                            </div>

                            {{-- Gambar menyatu dengan kartu: tanpa kotak, tanpa
                                 garis pemisah, latar sama dengan judul &
                                 keterangan di atasnya. --}}
                            @if($province['image'])
                                <div class="relative aspect-[4/3] w-full">
                                    <img src="{{ $province['image'] }}"
                                         alt="{{ $province['image_alt'] }}"
                                         loading="lazy" decoding="async"
                                         class="absolute inset-0 h-full w-full object-contain px-5 pb-5 transition-transform duration-500 group-hover:scale-105">
                                </div>
                            @else
                                <div class="relative aspect-[4/3] w-full">
                                    <span class="absolute inset-0 grid place-items-center text-bark-300">
                                        <x-icon name="landmark" size="28"/>
                                    </span>
                                </div>
                            @endif
                        </{{ $tag }}>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
