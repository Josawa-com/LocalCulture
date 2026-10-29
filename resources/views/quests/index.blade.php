@extends('layouts.app')

@section('title', 'Kuis Budaya')

@section('content')
    {{-- Header halaman --}}
    <header class="mx-auto max-w-4xl px-4 pt-10 text-center">
        <span class="kicker">Uji Wawasan Budaya</span>
        <h1 class="display-lg mt-2 text-3xl text-bark-950 sm:text-4xl">Kuis Budaya Nusantara</h1>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-bark-600">
            Pilih jenis kuis di bawah. Setiap kuis berisi soal pilihan ganda dengan batas waktu
            per soal, dan hanya bisa dikerjakan satu kali per akun.
        </p>
    </header>

    {{-- Empat kategori kuis, berurutan dari atas ke bawah --}}
    <div class="mx-auto max-w-4xl space-y-10 px-4 py-10">
        @foreach($sections as $section)
            <section aria-labelledby="quiz-{{ $section['type'] }}">
                {{-- Judul kategori + garis ornamen --}}
                <div class="mb-1 flex items-baseline justify-between gap-3">
                    <h2 id="quiz-{{ $section['type'] }}" class="display-lg text-xl text-bark-950">
                        {{ $section['label'] }}
                    </h2>
                    @if($section['quests']->isNotEmpty())
                        <span class="text-xs font-medium text-bark-500">
                            {{ $section['quests']->count() }} kuis tersedia
                        </span>
                    @endif
                </div>
                <p class="mb-5 max-w-2xl text-sm text-bark-600">{{ $section['description'] }}</p>

                @if($section['quests']->isEmpty())
                    {{-- Kosong: tampilkan placeholder rapi (belum dibuat admin) --}}
                    <div class="rounded-lg border border-dashed border-bark-300 bg-bark-50/50 px-5 py-8 text-center">
                        <p class="text-sm font-medium text-bark-600">Belum ada kuis untuk kategori ini.</p>
                        <p class="mt-1 text-xs text-bark-500">Kuis akan muncul di sini setelah ditambahkan admin.</p>
                    </div>
                @else
                    {{-- Daftar kuis: tiap kuis = kotak tombol dengan deskripsi --}}
                    <ul class="space-y-3">
                        @foreach($section['quests'] as $quest)
                            @php
                                $total = $quest->question_count ?: $quest->questions_count;
                                $ready = $total > 0 && $quest->questions_count >= $total;
                                $attempt = $attempts->get($quest->id);
                                $finished = $attempt && $attempt->status === 1;
                            @endphp
                            <li>
                                <div class="panel panel-hover flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-bold text-bark-950">{{ $quest->title }}</h3>
                                            <span class="tag bg-brass-100 text-brass-800">{{ $quest->typeLabel() }}</span>
                                            @unless($ready)
                                                <span class="tag bg-bark-100 text-bark-600">Belum lengkap</span>
                                            @endunless
                                        </div>

                                        @if($quest->subtitle)
                                            <p class="mt-1 text-sm text-bark-700">
                                                <span class="font-medium text-bark-500">Materi:</span>
                                                {{ $quest->subtitle }}
                                            </p>
                                        @endif

                                        @if($quest->description)
                                            <p class="mt-1 line-clamp-2 text-sm text-bark-600">{{ $quest->description }}</p>
                                        @endif

                                        <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-medium text-bark-500">
                                            <span>{{ $total }} soal</span>
                                            <span>{{ $quest->time_limit }} detik/soal</span>
                                        </p>
                                    </div>

                                    <div class="shrink-0">
                                        @auth
                                            @if($finished)
                                                <a href="{{ route('quests.result', $attempt) }}" class="btn-secondary w-full sm:w-auto">
                                                    Lihat Hasil
                                                </a>
                                            @else
                                                <form method="POST" action="{{ route('quests.start', $quest) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-primary w-full sm:w-auto" @disabled(! $ready)>
                                                        {{ $ready ? ($attempt ? 'Lanjutkan Kuis' : 'Kerjakan Kuis') : 'Belum tersedia' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn-primary w-full sm:w-auto">Masuk untuk ikut</a>
                                        @endauth
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>
@endsection
