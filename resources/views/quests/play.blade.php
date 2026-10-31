@extends('layouts.app')

@section('title', 'Mengerjakan Kuis')

@section('content')
    @php
        $progress = $totalQuestions > 0 ? round(($questionNumber - 1) / $totalQuestions * 100) : 0;
        $isFirstQuestion = $questionNumber === 1;
    @endphp

    <div class="mx-auto max-w-3xl">

        {{-- ============================================================
             LAYAR 1: KETERANGAN AWAL (10 detik, bisa di-skip)
             ============================================================ --}}
        @if($isFirstQuestion)
            <div id="intro-overlay" class="panel mb-4 p-6 sm:p-8" role="dialog" aria-labelledby="intro-title">
                <span class="kicker">Sebelum Mulai</span>
                <h1 id="intro-title" class="display-lg mt-1 text-2xl text-bark-950">{{ $attempt->quest->title }}</h1>

                @if($attempt->quest->subtitle)
                    <p class="mt-1 text-sm text-bark-600">{{ $attempt->quest->subtitle }}</p>
                @endif

                {{-- Ringkasan aturan --}}
                <dl class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-md border border-bark-200 bg-bark-50 px-4 py-3">
                        <dt class="kicker">Jumlah Soal</dt>
                        <dd class="display-lg mt-0.5 text-2xl tabular-nums text-bark-950">{{ $totalQuestions }}</dd>
                    </div>
                    <div class="rounded-md border border-bark-200 bg-bark-50 px-4 py-3">
                        <dt class="kicker">Waktu per Soal</dt>
                        <dd class="display-lg mt-0.5 text-2xl tabular-nums text-bark-950">
                            {{ $timeLimit }}<span class="text-sm text-bark-500">s</span>
                        </dd>
                    </div>
                    <div class="rounded-md border border-bark-200 bg-bark-50 px-4 py-3">
                        <dt class="kicker">Total Waktu</dt>
                        <dd class="display-lg mt-0.5 text-2xl tabular-nums text-bark-950">
                            {{ $totalQuestions * $timeLimit }}<span class="text-sm text-bark-500">s</span>
                        </dd>
                    </div>
                </dl>

                <ul class="mt-5 space-y-1.5 text-sm text-bark-600">
                    <li class="flex gap-2"><span class="text-brass-600">•</span> Setiap soal punya waktu {{ $timeLimit }} detik. Waktu habis = soal dilewati otomatis.</li>
                    <li class="flex gap-2"><span class="text-brass-600">•</span> Skor bertambah bila benar, plus bonus kecepatan.</li>
                    <li class="flex gap-2"><span class="text-brass-600">•</span> Kuis hanya bisa dikerjakan satu kali per akun.</li>
                </ul>

                {{-- Hitungan mundur 10 detik + tombol skip --}}
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-bark-200 pt-5">
                    <div class="flex items-baseline gap-2">
                        <span class="display-lg text-3xl tabular-nums text-bark-950" id="intro-countdown" aria-live="polite">10</span>
                        <span class="text-sm text-bark-500">detik lagi</span>
                    </div>
                    <button type="button" id="intro-skip" class="btn-primary">
                        Lewati &amp; Mulai Sekarang
                        <x-icon name="arrow-right" size="16"/>
                    </button>
                </div>
            </div>

            {{-- ============================================================
                 LAYAR 2: PERSIAPAN (3 detik)
                 ============================================================ --}}
            <div id="ready-overlay" class="panel mb-4 hidden px-6 py-14 text-center" role="dialog" aria-labelledby="ready-title">
                <span class="kicker">Bersiap</span>
                <h2 id="ready-title" class="display-lg mt-2 text-xl text-bark-950">Kuis segera dimulai</h2>
                <div class="display-lg mt-6 text-6xl tabular-nums text-brass-700" id="ready-countdown" aria-live="assertive">3</div>
                <p class="mt-4 text-sm text-bark-600">Jangan lepas halaman ini.</p>
            </div>
        @endif

        {{-- ============================================================
             AREA KUIS (disembunyikan sampai hitungan selesai)
             ============================================================ --}}
        <div id="quiz-area" class="{{ $isFirstQuestion ? 'hidden' : '' }}">

            {{-- Header: judul kuis + progres --}}
            <header class="mb-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="kicker">{{ $attempt->quest->typeLabel() }}</span>
                        <h1 class="display-lg mt-1 text-xl text-bark-950 sm:text-2xl">{{ $attempt->quest->title }}</h1>
                        @if($attempt->quest->subtitle)
                            <p class="mt-0.5 text-sm text-bark-600">{{ $attempt->quest->subtitle }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-medium text-bark-500">Soal</div>
                        <div class="display-lg text-lg text-bark-900">
                            {{ $questionNumber }}<span class="text-sm text-bark-500">/{{ $totalQuestions }}</span>
                        </div>
                    </div>
                </div>

                {{-- Bar progres --}}
                <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-bark-200" role="progressbar"
                     aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="h-full rounded-full bg-brass-500 transition-[width] duration-300"
                         style="width: {{ $progress }}%"></div>
                </div>
            </header>

            {{-- Timer --}}
            <div class="mb-4 flex items-center justify-between gap-3 rounded-lg border border-bark-200 bg-white px-4 py-3">
                <div class="flex items-center gap-2 text-sm font-medium text-bark-700">
                    <span aria-hidden="true">⏱</span>
                    <span>Waktu tersisa</span>
                </div>
                <div id="timer" class="display-lg text-lg tabular-nums text-bark-950" aria-live="polite">
                    {{ $timeLimit }}<span class="text-sm text-bark-500">s</span>
                </div>
            </div>

            {{-- Kartu soal --}}
            <div class="panel p-6 sm:p-8">
                <p class="display-lg text-lg leading-snug text-bark-950 sm:text-xl">{{ $question->question }}</p>

                <form id="answer-form" method="POST" action="{{ route('quests.answer', $attempt) }}" class="mt-6 space-y-3">
                    @csrf
                    <input type="hidden" name="answer_row" value="{{ $answerRowId }}">

                    @foreach($question->options as $index => $option)
                        <label class="group flex cursor-pointer items-start gap-3 rounded-lg border border-bark-200 bg-white p-4 transition hover:border-brass-400 hover:bg-brass-50/40 has-[:checked]:border-brass-500 has-[:checked]:bg-brass-50">
                            <input type="radio" name="selected_index" value="{{ $index }}" required
                                   class="mt-0.5 h-4 w-4 border-bark-300 text-brass-600 focus:ring-brass-600/30">
                            <span class="flex-1 text-sm text-bark-800">
                                <span class="mr-1 font-bold text-bark-500">{{ chr(65 + $index) }}.</span>{{ $option }}
                            </span>
                        </label>
                    @endforeach

                    <button type="submit" class="btn-primary mt-2 w-full">Kirim Jawaban</button>
                </form>
            </div>

            <p class="mt-4 text-center text-xs text-bark-500">
                Skor bertambah bila jawaban benar, dengan bonus kecepatan.
            </p>
        </div>

        {{-- Form timeout (dikirim otomatis bila waktu habis) --}}
        <form id="timeout-form" method="POST" action="{{ route('quests.timeout', $attempt) }}" class="hidden">
            @csrf
            <input type="hidden" name="answer_row" value="{{ $answerRowId }}">
        </form>
    </div>

    <script>
        (function () {
            'use strict';

            const limit = {{ (int) $timeLimit }};
            const isFirst = {{ $isFirstQuestion ? 'true' : 'false' }};

            const timerEl = document.getElementById('timer');
            const form = document.getElementById('answer-form');
            const timeoutForm = document.getElementById('timeout-form');

            const quizArea = document.getElementById('quiz-area');
            const introOverlay = document.getElementById('intro-overlay');
            const readyOverlay = document.getElementById('ready-overlay');
            const introCountdown = document.getElementById('intro-countdown');
            const introSkip = document.getElementById('intro-skip');
            const readyCountdown = document.getElementById('ready-countdown');

            const INTRO_SECONDS = 10;
            const READY_SECONDS = 3;

            let remaining = limit;
            let submitted = false;
            let timerStarted = false;

            /* ---------------- Timer soal ---------------- */
            const render = () => {
                timerEl.innerHTML = remaining + '<span class="text-sm text-bark-500">s</span>';
                timerEl.classList.toggle('text-signal-danger', remaining <= 3);
                timerEl.classList.toggle('text-bark-950', remaining > 3);
            };

            const submitTimeout = () => {
                if (submitted) return;
                submitted = true;
                timeoutForm.submit();
            };

            const tick = () => {
                if (submitted) return;
                remaining -= 1;

                if (remaining <= 0) {
                    remaining = 0;
                    render();
                    submitTimeout();
                    return;
                }

                render();
                setTimeout(tick, 1000);
            };

            const startQuizTimer = () => {
                if (timerStarted || submitted) return;
                timerStarted = true;
                render();
                setTimeout(tick, 1000);
            };

            /* ---------------- Alur pembuka ---------------- */
            const showQuiz = () => {
                if (readyOverlay) readyOverlay.classList.add('hidden');
                quizArea.classList.remove('hidden');
                startQuizTimer();
            };

            const showReady = () => {
                if (!readyOverlay) {
                    showQuiz();
                    return;
                }

                if (introOverlay) introOverlay.classList.add('hidden');
                readyOverlay.classList.remove('hidden');

                let n = READY_SECONDS;
                readyCountdown.textContent = n;

                const step = () => {
                    n -= 1;

                    if (n <= 0) {
                        showQuiz();
                        return;
                    }

                    readyCountdown.textContent = n;
                    setTimeout(step, 1000);
                };

                setTimeout(step, 1000);
            };

            const startIntro = () => {
                if (!introOverlay) {
                    startQuizTimer();
                    return;
                }

                let n = INTRO_SECONDS;
                introCountdown.textContent = n;

                const step = () => {
                    n -= 1;

                    if (n <= 0) {
                        showReady();
                        return;
                    }

                    introCountdown.textContent = n;
                    setTimeout(step, 1000);
                };

                setTimeout(step, 1000);
            };

            if (isFirst) {
                // Tombol skip -> langsung ke persiapan 3 detik.
                if (introSkip) {
                    introSkip.addEventListener('click', showReady, { once: true });
                }
                startIntro();
            } else {
                startQuizTimer();
            }

            /* ---------------- Kirim jawaban ---------------- */
            form.addEventListener('submit', (e) => {
                if (submitted) {
                    e.preventDefault();
                    return;
                }
                submitted = true;
            });

            render();
        })();
    </script>
@endsection
