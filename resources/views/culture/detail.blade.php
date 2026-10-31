@extends('layouts.app')

@section('title', $culture->name)

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10">
        {{-- Navigasi kembali --}}
        <a href="{{ route('culture.regency', [$culture->regency->province->island, $culture->regency->province, $culture->regency]) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-bark-600 transition hover:text-bark-900">
            <x-icon name="arrow-left" size="14"/>
            {{ $culture->regency->name }}
        </a>

        {{-- Header --}}
        <header class="mt-5">
            <div class="flex flex-wrap items-center gap-2">
                @if($culture->category)
                    <span class="tag bg-brass-100 text-brass-800">{{ $culture->category->name }}</span>
                @endif
                <span class="text-xs text-bark-500">
                    {{ $culture->regency->name }}, {{ $culture->regency->province->name }}
                </span>
            </div>

            <h1 class="display-lg mt-3 text-3xl leading-tight text-bark-950 sm:text-4xl">{{ $culture->name }}</h1>

            @if($culture->description)
                <p class="mt-3 text-base leading-relaxed text-bark-700">{{ $culture->description }}</p>
            @endif
        </header>

        <div class="rule-ornament my-8" aria-hidden="true"></div>

        {{-- Isi materi --}}
        <article class="prose-culture text-bark-800">
            @foreach(preg_split('/\R{2,}/', trim((string) $culture->content)) as $paragraph)
                @if(trim($paragraph) !== '')
                    <p class="text-[15px] leading-[1.75] text-bark-800">{{ trim($paragraph) }}</p>
                @endif
            @endforeach
        </article>

        {{-- Nilai filosofis --}}
        @if($culture->philosophy)
            <aside class="panel mt-8 border-brass-300 bg-brass-50/60 p-5">
                <div class="flex items-center gap-2 text-brass-800">
                    <x-icon name="sparkles" size="16"/>
                    <h2 class="text-xs font-bold uppercase tracking-wide">Nilai Filosofis</h2>
                </div>
                <p class="mt-2 text-sm leading-relaxed text-bark-700">{{ $culture->philosophy }}</p>
            </aside>
        @endif

        {{-- Materi terkait --}}
        @if($related->isNotEmpty())
            <section class="mt-10 border-t border-bark-200 pt-8">
                <h2 class="display-lg text-lg text-bark-950">Materi Lain di {{ $culture->regency->name }}</h2>
                <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach($related as $item)
                        <li>
                            <a href="{{ route('culture.detail', [$culture->regency->province->island, $culture->regency->province, $culture->regency, $item]) }}"
                               class="panel panel-hover group flex items-center justify-between gap-3 px-4 py-3">
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold text-bark-900">{{ $item->name }}</span>
                                    <span class="mt-0.5 block text-xs text-bark-500">{{ $item->category?->name }}</span>
                                </span>
                                <span class="shrink-0 text-bark-300 transition group-hover:text-brass-600">
                                    <x-icon name="chevron-right" size="16"/>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
@endsection
