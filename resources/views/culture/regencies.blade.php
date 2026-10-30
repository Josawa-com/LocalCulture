@extends('layouts.app')

@section('title', 'Kabupaten & Kota '.$province->name)

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-10">
        {{-- Navigasi kembali --}}
        <a href="{{ route('culture.island', $province->island) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-bark-600 transition hover:text-bark-900">
            <x-icon name="arrow-left" size="14"/>
            Provinsi {{ $province->island?->name }}
        </a>

        {{-- Header --}}
        <header class="mt-4">
            <span class="kicker">Materi Budaya</span>
            <h1 class="display-lg mt-1 text-3xl text-bark-950 sm:text-4xl">{{ $province->name }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-bark-600">
                Pilih kabupaten/kota untuk melihat materi budayanya.
                @if($regencies->isNotEmpty())
                    <span class="font-medium text-bark-800">{{ $regencies->count() }} wilayah</span> tersedia.
                @endif
            </p>
        </header>

        {{-- Daftar wilayah --}}
        @if($regencies->isEmpty())
            <div class="panel mt-8 px-6 py-14 text-center">
                <x-icon name="map-pin" size="32" class="mx-auto text-bark-300"/>
                <h2 class="display-lg mt-4 text-lg text-bark-900">Belum ada wilayah</h2>
                <p class="mx-auto mt-2 max-w-sm text-sm text-bark-600">
                    Data kabupaten/kota untuk provinsi ini belum tersedia.
                </p>
            </div>
        @else
            <ul class="mt-8 grid gap-3 sm:grid-cols-2">
                @foreach($regencies as $regency)
                    @php $isPopular = $mostVisitedId !== null && $mostVisitedId === $regency->id; @endphp

                    <li>
                        <a href="{{ route('culture.regency', [$province->island, $province, $regency]) }}"
                           class="panel panel-hover group relative flex items-center justify-between gap-3 px-5 py-4
                                  {{ $isPopular ? 'border-brass-400 bg-brass-50/50' : '' }}">

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="display-lg text-base leading-tight text-bark-950">
                                        {{ $regency->name }}
                                    </h2>

                                    {{-- Penanda wilayah paling sering dikunjungi --}}
                                    @if($isPopular)
                                        <span class="tag bg-brass-500 text-bark-950 shadow-sm">
                                            <x-icon name="sparkles" size="12"/>
                                            Sering dikunjungi
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-1 text-xs text-bark-500">
                                    {{ $regency->cultures_count }} materi budaya
                                </p>
                            </div>

                            <span class="shrink-0 text-bark-300 transition group-hover:translate-x-0.5 group-hover:text-brass-600">
                                <x-icon name="arrow-right" size="16"/>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
