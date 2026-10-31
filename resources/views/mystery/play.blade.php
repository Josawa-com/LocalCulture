@extends('layouts.app')

@section('title', 'Mystery Budaya')

@section('content')
    @php
        $totalClues = count($clues);
        $initialClues = (int) ($attempt?->clues_revealed ?? 0);
        $initialWrong = (int) ($attempt?->wrong_guesses ?? 0);
        $maxWrong = (int) $mystery->max_wrong_guesses;
    @endphp

    <div class="mx-auto max-w-3xl px-4 py-10" id="mystery-app"
         data-reveal-url="{{ route('mystery.reveal', $mystery) }}"
         data-guess-url="{{ route('mystery.guess', $mystery) }}"
         data-max-wrong="{{ $maxWrong }}"
         data-points="{{ $mystery->points }}"
         data-penalty="{{ $mystery->clue_penalty }}"
         data-initial-clues="{{ $initialClues }}"
         data-initial-wrong="{{ $initialWrong }}"
         data-back-url="{{ route('mystery.index') }}">

        {{-- Navigasi kembali --}}
        <a href="{{ route('mystery.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-bark-600 transition hover:text-bark-900">
            <x-icon name="arrow-left" size="14"/>
            Semua Misteri
        </a>

        {{-- Header: skor & nyawa --}}
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-bark-200 bg-white px-4 py-3">
            <div>
                <div class="kicker">Poin bila benar sekarang</div>
                <div class="display-lg text-2xl tabular-nums text-bark-950">
                    <span id="score-value">{{ $mystery->scoreFor($initialClues) }}</span>
                    <span class="text-sm font-normal text-bark-500">/ {{ $mystery->points }}</span>
                </div>
            </div>

            <div class="text-right">
                <div class="kicker">Kesempatan</div>
                <div class="flex items-center justify-end gap-1.5" aria-live="polite">
                    <span id="lives-dots" class="flex gap-1">
                        @for($i = 0; $i < $maxWrong; $i++)
                            <span class="h-2.5 w-2.5 rounded-full {{ $i < $maxWrong - $initialWrong ? 'bg-signal-success' : 'bg-bark-200' }}"></span>
                        @endfor
                    </span>
                    <span id="lives-text" class="display-lg text-sm tabular-nums text-bark-900">
                        {{ $maxWrong - $initialWrong }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Pernyataan awal --}}
        <div class="panel mt-4 p-6 sm:p-8">
            <span class="kicker">Pernyataan</span>
            <p class="mt-2 text-base leading-relaxed text-bark-900">
                {{ $mystery->statement ?: 'Budaya apakah yang dimaksud?' }}
            </p>

            @if($mystery->origin)
                <p class="mt-3 text-xs text-bark-500">
                    Asal daerah: <span class="font-semibold text-bark-700">{{ $mystery->origin }}</span>
                </p>
            @endif
        </div>

        {{-- Petunjuk --}}
        <div class="panel mt-4 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-bark-200 bg-bark-50 px-5 py-3">
                <h2 class="display-lg text-sm text-bark-900">Petunjuk</h2>
                <p class="text-xs text-bark-500">
                    <span id="clue-count">{{ $initialClues }}</span>/{{ $totalClues }} terbuka
                    · <span class="font-semibold text-batik-700">{{ $mystery->clue_penalty }} poin</span> per petunjuk
                </p>
            </div>

            <ul class="divide-y divide-bark-100" id="clue-list">
                @for($i = 0; $i < $initialClues && $i < $totalClues; $i++)
                    <li class="flex gap-3 px-5 py-3" data-clue="{{ $i }}">
                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-brass-100 text-xs font-bold text-brass-800">{{ $i + 1 }}</span>
                        <p class="text-sm leading-relaxed text-bark-800">{{ $clues[$i] }}</p>
                    </li>
                @endfor
            </ul>

            <div class="px-5 py-4">
                <button type="button" id="reveal-btn"
                        class="btn-secondary w-full {{ $initialClues >= $totalClues ? 'hidden' : '' }}">
                    <x-icon name="eye" size="16"/>
                    <span id="reveal-label">Buka Petunjuk (−{{ $mystery->clue_penalty }} poin)</span>
                </button>
                <p id="reveal-note" class="mt-2 text-center text-xs text-bark-500 {{ $initialClues >= $totalClues ? '' : 'hidden' }}">
                    Semua petunjuk sudah terbuka.
                </p>
            </div>
        </div>

        {{-- Form tebakan --}}
        <form id="guess-form" class="panel mt-4 space-y-4 p-6" autocomplete="off">
            <div>
                <label class="field-label" for="guess">Tebakan kamu</label>
                <input id="guess" name="guess" type="text" required maxlength="150"
                       class="field" placeholder="Contoh: Rumah Gadang"
                       list="guess-suggestions" autocomplete="off" autocapitalize="words">
                <datalist id="guess-suggestions">
                    @foreach($regencies as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                <p class="mt-1 text-xs text-bark-500">Huruf besar/kecil tidak berpengaruh.</p>
            </div>

            <div id="feedback" class="hidden rounded-md px-4 py-3 text-sm font-medium" role="status" aria-live="polite"></div>

            <button type="submit" id="guess-btn" class="btn-primary w-full">Kirim Tebakan</button>
        </form>

        {{-- Hasil akhir --}}
        <div id="result" class="panel mt-4 hidden p-6 text-center"></div>

        <p class="mt-6 text-center text-xs text-bark-500">
            Salah {{ $maxWrong }} kali membuat misteri ini terkunci permanen.
        </p>
    </div>

    <script>
        (function () {
            'use strict';

            var app = document.getElementById('mystery-app');
            if (!app) return;

            var revealUrl = app.dataset.revealUrl;
            var guessUrl = app.dataset.guessUrl;
            var backUrl = app.dataset.backUrl;
            var maxWrong = parseInt(app.dataset.maxWrong, 10) || 7;

            var csrf = document.querySelector('meta[name="csrf-token"]');
            csrf = csrf ? csrf.getAttribute('content') : '';

            var scoreEl = document.getElementById('score-value');
            var livesEl = document.getElementById('lives-text');
            var dotsEl = document.getElementById('lives-dots');
            var clueCountEl = document.getElementById('clue-count');
            var clueList = document.getElementById('clue-list');
            var revealBtn = document.getElementById('reveal-btn');
            var revealLabel = document.getElementById('reveal-label');
            var revealNote = document.getElementById('reveal-note');
            var form = document.getElementById('guess-form');
            var input = document.getElementById('guess');
            var guessBtn = document.getElementById('guess-btn');
            var feedback = document.getElementById('feedback');
            var result = document.getElementById('result');

            var penalty = parseInt(app.dataset.penalty, 10) || 50;
            var points = parseInt(app.dataset.points, 10) || 300;
            var cluesRevealed = parseInt(app.dataset.initialClues, 10) || 0;
            var wrong = parseInt(app.dataset.initialWrong, 10) || 0;
            var totalClues = clueList.querySelectorAll('[data-clue]').length + (revealNote.classList.contains('hidden') ? 0 : 0);
            var busy = false;

            function scoreNow() {
                return Math.max(50, points - cluesRevealed * penalty);
            }

            function paintLives() {
                var left = Math.max(0, maxWrong - wrong);
                livesEl.textContent = left;

                dotsEl.querySelectorAll('span').forEach(function (dot, i) {
                    dot.className = 'h-2.5 w-2.5 rounded-full ' + (i < left ? 'bg-signal-success' : 'bg-bark-200');
                });
            }

            function paintScore() {
                scoreEl.textContent = scoreNow();
                clueCountEl.textContent = cluesRevealed;
            }

            function showFeedback(text, kind) {
                var styles = {
                    error: 'bg-batik-600/10 text-batik-700',
                    info: 'bg-bark-100 text-bark-700',
                    success: 'bg-signal-success/15 text-signal-success',
                };
                feedback.className = 'rounded-md px-4 py-3 text-sm font-medium ' + (styles[kind] || styles.info);
                feedback.textContent = text;
            }

            function finish(html) {
                result.innerHTML = html;
                result.classList.remove('hidden');
                form.classList.add('hidden');
                revealBtn.classList.add('hidden');
                result.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            function escapeHtml(s) {
                return String(s).replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }

            /* ---------- Buka petunjuk ---------- */
            revealBtn.addEventListener('click', function () {
                if (busy) return;
                busy = true;
                revealBtn.disabled = true;

                fetch(revealUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.clue === undefined) {
                            showFeedback(data.message || 'Tidak ada petunjuk lagi.', 'info');
                            return;
                        }

                        var li = document.createElement('li');
                        li.className = 'flex gap-3 px-5 py-3';
                        li.setAttribute('data-clue', data.index);
                        li.innerHTML =
                            '<span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-brass-100 text-xs font-bold text-brass-800">' +
                            (data.index + 1) + '</span>' +
                            '<p class="text-sm leading-relaxed text-bark-800">' + escapeHtml(data.clue) + '</p>';
                        clueList.appendChild(li);

                        cluesRevealed = data.clues_revealed;
                        paintScore();

                        if (cluesRevealed >= data.total_clues) {
                            revealBtn.classList.add('hidden');
                            revealNote.classList.remove('hidden');
                        }
                    })
                    .catch(function () {
                        showFeedback('Gagal membuka petunjuk. Coba lagi.', 'error');
                    })
                    .finally(function () {
                        busy = false;
                        revealBtn.disabled = false;
                    });
            });

            /* ---------- Kirim tebakan ---------- */
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (busy) return;

                var value = input.value.trim();
                if (!value) return;

                busy = true;
                guessBtn.disabled = true;
                guessBtn.textContent = 'Memeriksa...';

                fetch(guessUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ guess: value }),
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.is_correct) {
                            showFeedback('Benar! +' + data.score + ' poin', 'success');
                            finish(
                                '<div class="text-signal-success"><span class="display-lg text-lg">Terpecahkan!</span></div>' +
                                '<p class="display-lg mt-3 text-3xl text-bark-950">' + escapeHtml(data.answer) + '</p>' +
                                '<p class="mt-1 text-sm text-bark-600">' + escapeHtml(data.origin || '') + '</p>' +
                                '<p class="mt-4 display-lg text-2xl tabular-nums text-brass-700">+' + data.score + ' poin</p>' +
                                '<p class="mt-1 text-xs text-bark-500">Dengan ' + cluesRevealed + ' petunjuk terbuka · ' + wrong + ' tebakan salah</p>' +
                                '<a href="' + backUrl + '" class="btn-primary mt-5">Misteri Lainnya</a>'
                            );
                            return;
                        }

                        wrong = data.wrong_guesses;
                        paintLives();

                        if (data.failed) {
                            showFeedback('Kesempatan habis.', 'error');
                            finish(
                                '<div class="text-batik-700"><span class="display-lg text-lg">Gagal</span></div>' +
                                '<p class="mt-2 text-sm text-bark-600">Kamu salah ' + wrong + ' kali. Misteri ini terkunci permanen.</p>' +
                                '<p class="display-lg mt-4 text-xl text-bark-950">Jawabannya: ' + escapeHtml(data.answer) + '</p>' +
                                '<p class="mt-1 text-sm text-bark-600">' + escapeHtml(data.origin || '') + '</p>' +
                                '<a href="' + backUrl + '" class="btn-secondary mt-5">Kembali</a>'
                            );
                            return;
                        }

                        showFeedback(
                            'Salah. Sisa kesempatan: ' + data.attempts_left + ' dari ' + maxWrong + '.',
                            'error'
                        );
                        input.value = '';
                        input.focus();
                    })
                    .catch(function () {
                        showFeedback('Gagal mengirim tebakan. Coba lagi.', 'error');
                    })
                    .finally(function () {
                        busy = false;
                        guessBtn.disabled = false;
                        guessBtn.textContent = 'Kirim Tebakan';
                    });
            });

            paintLives();
            paintScore();
        })();
    </script>
@endsection
