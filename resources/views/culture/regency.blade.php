@extends('layouts.app')

@section('title', $regency->name)

@section('content')
    @php
        $totalCultures = $regency->cultures->count();
    @endphp

    <div class="mx-auto max-w-5xl px-4 py-10">
        {{-- Navigasi kembali --}}
        <a href="{{ route('culture.province', [$regency->province->island, $regency->province]) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-bark-600 transition hover:text-bark-900">
            <x-icon name="arrow-left" size="14"/>
            {{ $regency->province->name }}
        </a>

        {{-- Header --}}
        <header class="mt-4 border-b border-bark-200 pb-6">
            <span class="kicker">Materi Budaya</span>
            <h1 class="display-lg mt-1 text-3xl text-bark-950 sm:text-4xl">{{ $regency->name }}</h1>
            <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-bark-600">
                <span class="inline-flex items-center gap-1">
                    <x-icon name="map-pin" size="14"/>
                    {{ $regency->province->name }}, {{ $regency->province->island?->name }}
                </span>
                <span class="inline-flex items-center gap-1">
                    <x-icon name="book-open" size="14"/>
                    {{ $totalCultures }} materi
                </span>
            </p>

            @if($regency->description)
                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-bark-700">{{ $regency->description }}</p>
            @endif
        </header>

        @if($totalCultures === 0)
            <div class="panel mt-8 px-6 py-14 text-center">
                <x-icon name="book-open" size="32" class="mx-auto text-bark-300"/>
                <h2 class="display-lg mt-4 text-lg text-bark-900">Belum ada materi</h2>
                <p class="mx-auto mt-2 max-w-sm text-sm text-bark-600">
                    Materi budaya untuk {{ $regency->name }} belum ditambahkan.
                </p>
            </div>
        @else
            {{-- Daftar per kategori --}}
            <div class="mt-8 space-y-10">
                @foreach($allCategories as $category)
                    @php
                        $items = $regency->cultures->where('culture_category_id', $category->id);
                    @endphp

                    @continue($items->isEmpty())

                    <section aria-labelledby="kat-{{ $category->slug }}">
                        <div class="flex items-baseline justify-between gap-3">
                            <h2 id="kat-{{ $category->slug }}" class="display-lg text-xl text-bark-950">
                                {{ $category->name }}
                            </h2>
                            <span class="text-xs font-medium text-bark-500">{{ $items->count() }} materi</span>
                        </div>

                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach($items as $culture)
                                <li>
                                    <a href="{{ route('culture.detail', [$regency->province->island, $regency->province, $regency, $culture]) }}"
                                       class="panel panel-hover group flex h-full flex-col p-5">
                                        <div class="flex items-start justify-between gap-2">
                                            <h3 class="display-lg text-base leading-tight text-bark-950">
                                                {{ $culture->name }}
                                            </h3>
                                            <span class="mt-0.5 shrink-0 text-bark-300 transition group-hover:translate-x-0.5 group-hover:text-brass-600">
                                                <x-icon name="arrow-right" size="14"/>
                                            </span>
                                        </div>

                                        @if($culture->description)
                                            <p class="mt-1.5 line-clamp-3 flex-1 text-sm leading-relaxed text-bark-600">
                                                {{ $culture->description }}
                                            </p>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
@endsection
