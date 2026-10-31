@extends('layouts.app')

@section('title', 'Mystery Budaya')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-10">
        {{-- Header --}}
        <header class="text-center">
            <span class="kicker">Minigame</span>
            <h1 class="display-lg mt-2 text-3xl text-bark-950 sm:text-4xl">Mystery Budaya</h1>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-bark-600">
                Ada pernyataan tentang sebuah budaya Nusantara. Tebak namanya.
                Butuh bantuan? Buka petunjuk — tapi setiap petunjuk mengurangi
                <span class="font-semibold text-bark-800">50 poin</span> dari skor maksimal
                <span class="font-semibold text-bark-800">300</span>.
            </p>
        </header>

        {{-- Aturan singkat --}}
        <div class="mt-6 grid gap-3 sm:grid-cols-3">
            @foreach([
                ['icon' => 'sparkles', 'title' => 'Poin Maksimal', 'text' => '300 poin bila langsung menebak benar.'],
                ['icon' => 'eye', 'title' => '3 Petunjuk', 'text' => 'Tiap petunjuk terbuka memotong 50 poin.'],
                ['icon' => 'alert-circle', 'title' => '7 Kali Salah', 'text' => 'Salah 7 kali = gagal, tidak bisa diulang.'],
            ] as $rule)
                <div class="panel p-4">
                    <div class="flex items-center gap-2 text-brass-700">
                        <x-icon :name="$rule['icon']" size="16"/>
                        <span class="text-xs font-bold uppercase tracking-wide">{{ $rule['title'] }}</span>
                    </div>
                    <p class="mt-1.5 text-xs leading-relaxed text-bark-600">{{ $rule['text'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Progres pemain --}}
        @auth
            @if($stats['total'] > 0)
                <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 rounded-lg border border-bark-200 bg-white px-4 py-3 text-xs">
                    <span class="font-semibold text-bark-800">Progres kamu</span>
                    <span class="text-signal-success">{{ $stats['solved'] }} terpecahkan</span>
                    <span class="text-batik-700">{{ $stats['failed'] }} gagal</span>
                    <span class="text-bark-500">dari {{ $stats['total'] }} misteri</span>
                </div>
            @endif
        @endauth

        {{-- Daftar misteri --}}
        <section class="mt-8">
            <h2 class="display-lg text-lg text-bark-950">Daftar Misteri</h2>

            @if($mysteries->isEmpty())
                <div class="panel mt-3 px-6 py-14 text-center">
                    <x-icon name="sparkles" size="32" class="mx-auto text-bark-300"/>
                    <h3 class="display-lg mt-4 text-base text-bark-900">Belum ada misteri</h3>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-bark-600">
                        Misteri akan muncul di sini setelah ditambahkan admin.
                    </p>
                </div>
            @else
                <ul class="mt-3 space-y-3">
                    @foreach($mysteries as $row)
                        @php
                            $mystery = $row['model'];
                            $attempt = $row['attempt'];
                            $solved = $attempt?->isSolved() ?? false;
                            $failed = $attempt?->isFailed() ?? false;
                        @endphp

                        <li>
                            <div class="panel flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold text-bark-950">Misteri #{{ $loop->iteration }}</h3>

                                        @if($solved)
                                            <span class="tag bg-signal-success/15 text-signal-success">Terpecahkan</span>
                                        @elseif($failed)
                                            <span class="tag bg-batik-600/10 text-batik-700">Gagal</span>
                                        @elseif($attempt)
                                            <span class="tag bg-brass-100 text-brass-800">
                                                Sedang berjalan · {{ $attempt->wrong_guesses }}/{{ $mystery->max_wrong_guesses }} salah
                                            </span>
                                        @endif
                                    </div>

                                    <p class="mt-1.5 line-clamp-2 text-sm text-bark-700">
                                        {{ $mystery->statement ?: 'Tebak budaya dari petunjuk yang tersedia.' }}
                                    </p>

                                    <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-medium text-bark-500">
                                        <span>{{ $row['clue_count'] }} petunjuk</span>
                                        <span>maks {{ $mystery->points }} poin</span>
                                        @if($solved)
                                            <span class="font-bold text-signal-success">{{ $attempt->score }} poin diraih</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    @auth
                                        @if($solved)
                                            <span class="btn-secondary btn-sm cursor-default">Selesai</span>
                                        @elseif($failed)
                                            <span class="btn-ghost btn-sm cursor-default">Tidak bisa diulang</span>
                                        @else
                                            <a href="{{ route('mystery.play', $mystery) }}" class="btn-primary w-full sm:w-auto">
                                                {{ $attempt ? 'Lanjutkan' : 'Mulai' }}
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn-primary w-full sm:w-auto">Masuk untuk bermain</a>
                                    @endauth
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Papan peringkat --}}
        <section class="mt-10">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <div>
                    <span class="kicker">Peringkat</span>
                    <h2 class="display-lg text-lg text-bark-950">Pemain Terbaik</h2>
                </div>
                <p class="text-xs text-bark-500">Berdasarkan skor tertinggi per pemain</p>
            </div>

            @if($leaderboard->isEmpty())
                <div class="panel mt-3 px-6 py-10 text-center">
                    <p class="text-sm text-bark-600">Belum ada yang memecahkan misteri. Jadilah yang pertama!</p>
                </div>
            @else
                <div class="panel mt-3 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-bark-200 bg-bark-50 text-xs uppercase tracking-wide text-bark-600">
                            <tr>
                                <th scope="col" class="w-16 px-4 py-3 text-center">#</th>
                                <th scope="col" class="px-4 py-3">Pemain</th>
                                <th scope="col" class="px-4 py-3 text-center">Terpecahkan</th>
                                <th scope="col" class="px-4 py-3 text-right">Skor Terbaik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bark-100">
                            @foreach($leaderboard as $entry)
                                @php $isMe = auth()->check() && auth()->user()->name === $entry['name']; @endphp
                                <tr class="{{ $isMe ? 'bg-brass-50' : '' }}">
                                    <td class="px-4 py-3 text-center font-bold tabular-nums text-bark-700">{{ $entry['rank'] }}</td>
                                    <td class="px-4 py-3 font-semibold text-bark-900">
                                        {{ $entry['name'] }}
                                        @if($isMe) <span class="tag ml-1 bg-brass-100 text-brass-800">Kamu</span> @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-bark-700">{{ $entry['solved'] }}</td>
                                    <td class="px-4 py-3 text-right font-bold tabular-nums text-bark-950">{{ $entry['score'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
