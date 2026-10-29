@extends('layouts.app')

@section('title', 'Hasil Kuis')

@section('content')
    @php
        $total = $attempt->answers->count();
        $correct = $attempt->correct_count;
        $wrong = $total - $correct;
        $percent = $total > 0 ? round($correct / $total * 100) : 0;
    @endphp

    <div class="mx-auto max-w-3xl">
        {{-- Ringkasan skor --}}
        <div class="panel overflow-hidden">
            <div class="bg-gradient-to-br from-bark-900 to-brass-700 px-6 py-8 text-center text-white">
                <span class="kicker text-brass-200">{{ $attempt->quest->typeLabel() }}</span>
                <h1 class="display-lg mt-1 text-2xl">{{ $attempt->quest->title }}</h1>

                <div class="mt-6 display-lg text-5xl tabular-nums">{{ $attempt->total_score }}</div>
                <p class="mt-1 text-sm text-white/80">Total skor</p>
            </div>

            <div class="grid grid-cols-3 divide-x divide-bark-200 border-b border-bark-200 text-center">
                <div class="px-3 py-4">
                    <div class="display-lg text-xl text-signal-success">{{ $correct }}</div>
                    <div class="kicker mt-0.5">Benar</div>
                </div>
                <div class="px-3 py-4">
                    <div class="display-lg text-xl text-signal-danger">{{ $wrong }}</div>
                    <div class="kicker mt-0.5">Salah</div>
                </div>
                <div class="px-3 py-4">
                    <div class="display-lg text-xl text-bark-900">{{ $percent }}%</div>
                    <div class="kicker mt-0.5">Akurasi</div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2 p-5">
                <a href="{{ route('quests.index') }}" class="btn-primary">Kuis Lainnya</a>
                <a href="{{ route('leaderboard') }}" class="btn-secondary">Lihat Peringkat</a>
            </div>
        </div>

        {{-- Rincian jawaban --}}
        <section class="mt-8">
            <h2 class="display-lg text-lg text-bark-950">Rincian Jawaban</h2>
            <ul class="mt-3 space-y-3">
                @foreach($attempt->answers as $i => $answer)
                    @php
                        $q = $answer->question;
                        $chosen = $answer->selected_index;
                        $correctIndex = $q->correct_index;
                    @endphp
                    <li class="panel p-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-medium text-bark-900">
                                <span class="mr-1 text-bark-400">{{ $i + 1 }}.</span>{{ $q->question }}
                            </p>
                            <span class="tag shrink-0 {{ $answer->is_correct ? 'bg-signal-success/15 text-signal-success' : 'bg-signal-danger/10 text-signal-danger' }}">
                                {{ $answer->is_correct ? 'Benar' : ($chosen === null ? 'Waktu habis' : 'Salah') }}
                            </span>
                        </div>

                        <div class="mt-3 grid gap-1.5 text-sm sm:grid-cols-2">
                            <p class="text-bark-600">
                                <span class="font-medium text-bark-500">Jawabanmu:</span>
                                {{ $chosen === null ? '—' : chr(65 + $chosen).'. '.($q->options[$chosen] ?? '') }}
                            </p>
                            <p class="text-bark-600">
                                <span class="font-medium text-bark-500">Kunci:</span>
                                {{ chr(65 + $correctIndex).'. '.($q->options[$correctIndex] ?? '') }}
                            </p>
                        </div>

                        <p class="mt-2 text-xs text-bark-500">
                            +{{ $answer->score }} poin · {{ number_format($answer->time_ms / 1000, 1) }} detik
                        </p>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
@endsection
