@extends('layouts.app')

@section('title', 'Peringkat')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-10">
        {{-- Header --}}
        <header class="text-center">
            <span class="kicker">Papan Peringkat</span>
            <h1 class="display-lg mt-2 text-3xl text-bark-950 sm:text-4xl">Siapa Paling Jago Budaya?</h1>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-bark-600">
                Tiap tab hanya menghitung jenis kuisnya sendiri: harian untuk kuis harian,
                mingguan untuk kuis mingguan, bulanan untuk kuis bulanan.
            </p>
        </header>

        {{-- Pilihan periode --}}
        <div class="mt-7 flex flex-col items-center gap-2">
            <nav class="inline-flex rounded-lg border border-bark-200 bg-white p-1 shadow-card" aria-label="Pilih periode peringkat">
                @foreach($periods as $key => $meta)
                    <a href="{{ route('leaderboard', ['periode' => $key]) }}"
                       @if($period === $key) aria-current="page" @endif
                       class="rounded-md px-5 py-2 text-sm font-semibold transition-colors {{ $period === $key ? 'bg-bark-900 text-white shadow-sm' : 'text-bark-700 hover:bg-bark-100 hover:text-bark-900' }}">
                        {{ $meta['label'] }}
                    </a>
                @endforeach
            </nav>
            <p class="text-xs text-bark-500">
                {{ $periods[$period]['description'] }}
                <span class="mx-1 text-bark-300">·</span>
                {{ $periodRange }}
            </p>
        </div>

        @if($leaderboard === [])
            {{-- Kosong: belum ada kuis jenis ini yang diselesaikan pada periode berjalan --}}
            <div class="panel mt-8 px-6 py-14 text-center">
                <x-icon name="trophy" size="32" class="mx-auto text-bark-300"/>
                <h2 class="display-lg mt-4 text-lg text-bark-900">
                    Belum ada peringkat {{ strtolower($periods[$period]['label']) }}
                </h2>
                <p class="mx-auto mt-2 max-w-sm text-sm text-bark-600">
                    Belum ada kuis {{ strtolower($periods[$period]['label']) }} yang diselesaikan pada
                    {{ $periodRange }}. Peringkat akan terisi otomatis setelah ada yang menyelesaikannya.
                </p>
                <a href="{{ route('quests.index') }}" class="btn-primary mt-5">Lihat Daftar Kuis</a>
            </div>
        @else
            {{-- Podium tiga besar --}}
            @if(count($podium) > 0)
                @php
                    // Urutan visual: juara 2, juara 1, juara 3
                    $podiumOrder = collect([1, 0, 2])->map(fn ($i) => $podium[$i] ?? null)->filter();
                @endphp
                <div class="mt-8 grid gap-3 sm:grid-cols-3 sm:items-end">
                    @foreach($podiumOrder as $entry)
                        @php
                            $isFirst = $entry['rank'] === 1;
                            $medal = match ($entry['rank']) {
                                1 => 'bg-brass-500/20 text-brass-800 ring-brass-400',
                                2 => 'bg-bark-200/60 text-bark-700 ring-bark-300',
                                default => 'bg-batik-600/10 text-batik-700 ring-batik-600/30',
                            };
                        @endphp
                        <div class="panel relative flex flex-col items-center px-5 pb-5 pt-7 text-center {{ $isFirst ? 'sm:pb-8 sm:pt-9' : '' }}">
                            <span class="absolute right-4 top-4 grid h-7 w-7 place-items-center rounded-full text-xs font-bold ring-1 {{ $medal }}">
                                {{ $entry['rank'] }}
                            </span>
                            <x-avatar :name="$entry['name']" :path="$entry['avatar_path']" :size="$isFirst ? 'xl' : 'lg'"/>
                            <p class="mt-3 truncate text-sm font-bold text-bark-950">{{ $entry['name'] }}</p>
                            @if($entry['island'] || $entry['province'])
                                <p class="mt-0.5 truncate text-xs text-bark-500">
                                    {{ collect([$entry['province'], $entry['island']])->filter()->implode(' · ') }}
                                </p>
                            @endif
                            <p class="display-lg mt-3 text-2xl tabular-nums text-bark-950">{{ number_format($entry['score'], 0, ',', '.') }}</p>
                            <p class="kicker mt-0.5">poin</p>
                            <p class="mt-2 text-xs text-bark-600">{{ $entry['quests'] }} kuis · {{ $entry['correct'] }} benar</p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Tabel peringkat lengkap --}}
            <div class="panel mt-6 overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-bark-200 bg-bark-50 px-5 py-3">
                    <h2 class="display-lg text-sm text-bark-900">Peringkat {{ $periods[$period]['label'] }}</h2>
                    <p class="text-xs text-bark-500">
                        {{ $stats['participants'] }} peserta · {{ $stats['quests'] }} kuis selesai
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-bark-200 text-xs uppercase tracking-wide text-bark-500">
                            <tr>
                                <th scope="col" class="w-16 px-4 py-3 text-center">#</th>
                                <th scope="col" class="px-4 py-3">Pengguna</th>
                                <th scope="col" class="px-4 py-3 text-center">Kuis</th>
                                <th scope="col" class="px-4 py-3 text-center">Benar</th>
                                <th scope="col" class="px-4 py-3 text-right">Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bark-100">
                            @foreach($leaderboard as $entry)
                                @php $isMe = auth()->check() && auth()->id() === $entry['user_id']; @endphp
                                <tr class="{{ $isMe ? 'bg-brass-50' : 'hover:bg-bark-50' }}">
                                    <td class="px-4 py-3 text-center">
                                        <span class="display-lg text-sm tabular-nums {{ $entry['rank'] <= 3 ? 'text-bark-950' : 'text-bark-500' }}">
                                            {{ $entry['rank'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <x-avatar :name="$entry['name']" :path="$entry['avatar_path']" size="sm"/>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-bark-900">
                                                    {{ $entry['name'] }}
                                                    @if($isMe)
                                                        <span class="tag ml-1 bg-brass-100 text-brass-800">Kamu</span>
                                                    @endif
                                                </p>
                                                @if($entry['island'] || $entry['province'])
                                                    <p class="truncate text-xs text-bark-500">
                                                        {{ collect([$entry['province'], $entry['island']])->filter()->implode(' · ') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-bark-700">{{ $entry['quests'] }}</td>
                                    <td class="px-4 py-3 text-center text-bark-700">{{ $entry['correct'] }}</td>
                                    <td class="px-4 py-3 text-right font-bold tabular-nums text-bark-950">
                                        {{ number_format($entry['score'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Posisi user yang sedang login --}}
        @auth
            <div class="mt-6">
                @if($me)
                    <div class="panel flex flex-wrap items-center justify-between gap-3 border-brass-300 bg-brass-50/60 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="display-lg grid h-10 w-10 place-items-center rounded-full bg-bark-900 text-sm text-white">
                                {{ $me['rank'] }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-bark-950">Posisi kamu peringkat {{ $me['rank'] }}</p>
                                <p class="text-xs text-bark-600">
                                    {{ $me['score'] }} poin dari {{ $me['quests'] }} kuis · {{ $me['correct'] }} jawaban benar
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('quests.index') }}" class="btn-secondary btn-sm">Tambah Skor</a>
                    </div>
                @else
                    <div class="panel flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                        <p class="text-sm text-bark-600">
                            Kamu belum menyelesaikan kuis {{ strtolower($periods[$period]['label']) }} pada periode ini.
                        </p>
                        <a href="{{ route('quests.index') }}" class="btn-primary btn-sm">Lihat Daftar Kuis</a>
                    </div>
                @endif
            </div>
        @else
            <div class="mt-6 panel flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                <p class="text-sm text-bark-600">Masuk untuk melihat posisimu di papan peringkat.</p>
                <a href="{{ route('login') }}" class="btn-primary btn-sm">Masuk</a>
            </div>
        @endauth

        <p class="mt-6 text-center text-xs text-bark-500">
            Peringkat diperbarui otomatis setiap kali sebuah kuis diselesaikan.
        </p>
    </div>
@endsection
