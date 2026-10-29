@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    @php
        $reputation = $user->reputation;
        $accuracy = $summary['answers'] > 0 ? round($summary['correct'] / $summary['answers'] * 100) : 0;
    @endphp

    <div class="mx-auto max-w-5xl space-y-6 px-4 py-10">
        {{-- Kartu identitas --}}
        <section class="panel overflow-hidden">
            <div class="bg-gradient-to-br from-bark-900 via-bark-800 to-brass-700 px-6 py-8">
                <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left">
                    <x-avatar :name="$user->name" :path="$user->avatar_path" size="xl"
                              class="ring-4 ring-white/20"/>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                            <h1 class="display-lg text-2xl text-white">{{ $user->name }}</h1>
                            @if($user->isAdmin())
                                <span class="tag bg-brass-400/20 text-brass-200">Admin</span>
                            @endif
                        </div>
                        <p class="mt-1 truncate text-sm text-white/70">{{ $user->email }}</p>
                        <p class="mt-2 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-xs text-white/70 sm:justify-start">
                            @if($user->province || $user->island)
                                <span class="inline-flex items-center gap-1">
                                    <x-icon name="map-pin" size="14"/>
                                    {{ collect([$user->province?->name, $user->island?->name])->filter()->implode(', ') }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1">
                                <x-icon name="calendar" size="14"/>
                                Bergabung {{ $user->created_at?->locale('id')->translatedFormat('F Y') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Statistik akun --}}
            <div class="grid grid-cols-2 divide-bark-200 border-t border-bark-200 sm:grid-cols-4 sm:divide-x">
                <div class="px-4 py-4 text-center">
                    <div class="display-lg text-2xl tabular-nums text-bark-950">{{ number_format($reputation?->total_score ?? 0, 0, ',', '.') }}</div>
                    <div class="kicker mt-0.5">Total Skor</div>
                </div>
                <div class="px-4 py-4 text-center">
                    <div class="display-lg text-2xl tabular-nums text-bark-950">{{ $reputation?->quests_completed ?? 0 }}</div>
                    <div class="kicker mt-0.5">Kuis Selesai</div>
                </div>
                <div class="px-4 py-4 text-center">
                    <div class="display-lg text-2xl tabular-nums text-bark-950">{{ $summary['best_score'] }}</div>
                    <div class="kicker mt-0.5">Skor Terbaik</div>
                </div>
                <div class="px-4 py-4 text-center">
                    <div class="display-lg text-2xl tabular-nums text-bark-950">{{ $accuracy }}%</div>
                    <div class="kicker mt-0.5">Akurasi Jawaban</div>
                </div>
            </div>
        </section>

        {{-- Riwayat kuis per jenis --}}
        <section aria-labelledby="riwayat-kuis">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <div>
                    <span class="kicker">Aktivitas</span>
                    <h2 id="riwayat-kuis" class="display-lg text-xl text-bark-950">Riwayat Kuis</h2>
                </div>
                <a href="{{ route('quests.index') }}" class="text-xs font-semibold text-brass-700 hover:underline">Kerjakan kuis baru →</a>
            </div>

            <div class="mt-4 space-y-4">
                @foreach($history as $section)
                    <div class="panel overflow-hidden">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-bark-200 bg-bark-50 px-5 py-3">
                            <div class="flex items-center gap-2">
                                <h3 class="display-lg text-sm text-bark-900">{{ $section['label'] }}</h3>
                                <span class="tag bg-bark-100 text-bark-600">{{ $section['attempts']->count() }} riwayat</span>
                            </div>

                            {{-- Ringkasan benar/salah jenis kuis ini --}}
                            <div class="flex items-center gap-4 text-xs font-medium">
                                <span class="inline-flex items-center gap-1 text-signal-success">
                                    <span class="h-2 w-2 rounded-full bg-signal-success" aria-hidden="true"></span>
                                    {{ $section['correct'] }} benar
                                </span>
                                <span class="inline-flex items-center gap-1 text-signal-danger">
                                    <span class="h-2 w-2 rounded-full bg-signal-danger" aria-hidden="true"></span>
                                    {{ $section['wrong'] }} salah
                                </span>
                                <span class="text-bark-500">dari {{ $section['total'] }} soal</span>
                            </div>
                        </div>

                        @if($section['attempts']->isEmpty())
                            <p class="px-5 py-6 text-sm text-bark-500">
                                Belum ada riwayat {{ strtolower($section['label']) }}.
                                <a href="{{ route('quests.index') }}" class="font-semibold text-brass-700 hover:underline">Mulai sekarang</a>.
                            </p>
                        @else
                            <ul class="divide-y divide-bark-100">
                                @foreach($section['attempts'] as $attempt)
                                    @php
                                        $answered = (int) $attempt->answers_count;
                                        $correct = (int) $attempt->correct_count;
                                        $wrong = max(0, $answered - $correct);
                                    @endphp
                                    <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-bark-900">{{ $attempt->quest?->title ?? 'Kuis dihapus' }}</p>
                                            <p class="mt-0.5 text-xs text-bark-500">
                                                {{ $attempt->finished_at?->locale('id')->translatedFormat('d M Y · H:i') ?? '—' }}
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-3 text-xs">
                                            <span class="tag bg-signal-success/15 text-signal-success">{{ $correct }} benar</span>
                                            <span class="tag bg-signal-danger/10 text-signal-danger">{{ $wrong }} salah</span>
                                            <span class="w-16 text-right font-bold tabular-nums text-bark-900">{{ $attempt->total_score }} poin</span>
                                            <a href="{{ route('quests.result', $attempt) }}" class="btn-ghost btn-sm">Detail</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Ringkasan keseluruhan --}}
            @if($summary['attempts'] > 0)
                <p class="mt-3 text-xs text-bark-500">
                    Total: {{ $summary['attempts'] }} kuis selesai ·
                    {{ $summary['correct'] }} jawaban benar dari {{ $summary['answers'] }} soal.
                </p>
            @endif
        </section>

        {{-- Pengaturan akun --}}
        <section aria-labelledby="pengaturan">
            <div>
                <span class="kicker">Akun</span>
                <h2 id="pengaturan" class="display-lg text-xl text-bark-950">Pengaturan</h2>
                <p class="mt-1 text-sm text-bark-600">Atur data profil, keamanan akun, dan sesi masukmu.</p>
            </div>

            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                {{-- Form data profil --}}
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="panel space-y-4 p-6">
                    @csrf

                    <h3 class="display-lg text-sm text-bark-900">Data Profil</h3>

                    <div>
                        <label class="field-label" for="name">Nama Lengkap</label>
                        <input id="name" name="name" type="text" required autocomplete="name"
                               value="{{ old('name', $user->name) }}" class="field">
                        @error('name') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="island_id">Pulau Asal</label>
                            <select id="island_id" name="island_id" data-old="{{ old('island_id', $user->island_id) }}" class="field">
                                <option value="">— Pilih pulau —</option>
                                @foreach($islands as $island)
                                    <option value="{{ $island->id }}" @selected((string) old('island_id', $user->island_id) === (string) $island->id)>
                                        {{ $island->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('island_id') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="field-label" for="province_id">Provinsi</label>
                            <select id="province_id" name="province_id" data-old="{{ old('province_id', $user->province_id) }}" class="field">
                                <option value="">— Pilih provinsi —</option>
                                @foreach($islands as $island)
                                    @foreach($island->provinces as $province)
                                        <option value="{{ $province->id }}" data-parent="{{ $island->id }}" hidden>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('province_id') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <span class="field-label">Foto Profil</span>
                        <div class="flex items-center gap-4">
                            <x-avatar :name="$user->name" :path="$user->avatar_path" size="lg"/>
                            <div class="min-w-0 flex-1">
                                <input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp"
                                       class="block w-full text-xs text-bark-600 file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-bark-900 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-bark-800">
                                <p class="mt-1 text-xs text-bark-500">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
                                @if($user->avatar_path)
                                    <label class="mt-2 inline-flex items-center gap-2 text-xs text-bark-600">
                                        <input type="checkbox" name="remove_avatar" value="1"
                                               class="rounded-sm border-bark-300 text-batik-600 focus:ring-batik-600/30">
                                        Hapus foto saat ini
                                    </label>
                                @endif
                                @error('avatar') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Simpan Profil</button>
                </form>

                <div class="space-y-4">
                    {{-- Form password --}}
                    <form method="POST" action="{{ route('profile.password') }}" class="panel space-y-4 p-6">
                        @csrf

                        <h3 class="display-lg text-sm text-bark-900">Ganti Password</h3>

                        <div>
                            <label class="field-label" for="current_password">Password Saat Ini</label>
                            <input id="current_password" name="current_password" type="password" required
                                   autocomplete="current-password" class="field">
                            @error('current_password') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="field-label" for="password">Password Baru</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                   class="field" aria-describedby="password-hint">
                            <p id="password-hint" class="mt-1 text-xs text-bark-500">Minimal 8 karakter.</p>
                            @error('password') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="field-label" for="password_confirmation">Ulangi Password Baru</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   autocomplete="new-password" class="field">
                        </div>

                        <button type="submit" class="btn-secondary">Perbarui Password</button>
                    </form>

                    {{-- Sesi / keluar --}}
                    <div class="panel p-6">
                        <h3 class="display-lg text-sm text-bark-900">Sesi Masuk</h3>
                        <p class="mt-1 text-sm text-bark-600">
                            Keluar dari akun ini. Kamu perlu masuk kembali untuk mengerjakan kuis.
                        </p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="btn-danger">
                                <x-icon name="logout" size="16"/>
                                Keluar dari Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
