@extends('layouts.app')

@section('title', 'Mengerjakan Kuis')

@section('content')
    @php
        $progress = $totalQuestions > 0 ? round(($questionNumber - 1) / $totalQuestions * 100) : 0;
    @endphp

    <div class="mx-auto max-w-3xl">
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

        {{-- Form timeout (dikirim otomatis bila waktu habis) --}}
        <form id="timeout-form" method="POST" action="{{ route('quests.timeout', $attempt) }}" class="hidden">
            @csrf
            <input type="hidden" name="answer_row" value="{{ $answerRowId }}">
        </form>

        <p class="mt-4 text-center text-xs text-bark-500">
            Skor bertambah bila jawaban benar, dengan bonus kecepatan.
        </p>
    </div>

    <script>
        (function () {
            const limit = {{ (int) $timeLimit }};
            const timerEl = document.getElementById('timer');
            const form = document.getElementById('answer-form');
            const timeoutForm = document.getElementById('timeout-form');

            let remaining = limit;
            let submitted = false;

            const render = () => {
                timerEl.innerHTML = remaining + '<span class="text-sm text-bark-500">s</span>';
                // Warna peringatan saat waktu hampir habis
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

            // Cegah kiriman ganda saat jawaban dikirim
            form.addEventListener('submit', (e) => {
                if (submitted) {
                    e.preventDefault();
                    return;
                }

                submitted = true;
            });

            render();
            setTimeout(tick, 1000);
        })();
    </script>
@endsection
