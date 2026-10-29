@extends('layouts.admin')

@section('title', 'Buat Kuis')

@section('content')
    <div class="mx-auto max-w-2xl">
        <header class="mb-6">
            <a href="{{ route('admin.quests.index') }}" class="text-xs font-medium text-bark-500 hover:text-bark-900">← Kembali ke daftar kuis</a>
            <h1 class="display-lg mt-2 text-2xl text-bark-950">Buat Kuis Baru</h1>
            <p class="mt-1 text-sm text-bark-600">
                Isi data kuis. Setelah disimpan, kamu langsung diarahkan ke pengisian soal
                sesuai jumlah soal yang kamu tentukan.
            </p>
        </header>

        <form method="POST" action="{{ route('admin.quests.store') }}" class="panel space-y-5 p-6">
            @csrf

            <div>
                <label class="field-label" for="type">Jenis Kuis</label>
                <select id="type" name="type" required class="field">
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="title">Judul Kuis</label>
                <input id="title" name="title" type="text" required value="{{ old('title') }}"
                       class="field" placeholder="Contoh: Kuis Harian Budaya Jawa">
                @error('title') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="subtitle">Subjudul / Materi</label>
                <input id="subtitle" name="subtitle" type="text" value="{{ old('subtitle') }}"
                       class="field" placeholder="Contoh: Rumah adat & tarian tradisional">
                <p class="mt-1 text-xs text-bark-500">Materi yang diujikan — tampil di halaman kuis.</p>
                @error('subtitle') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="description">Deskripsi Singkat</label>
                <textarea id="description" name="description" rows="3" class="field"
                          placeholder="Penjelasan singkat tentang kuis ini (opsional)">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="question_count">Jumlah Soal</label>
                    <input id="question_count" name="question_count" type="number" min="1" max="50" required
                           value="{{ old('question_count', 5) }}" class="field">
                    <p class="mt-1 text-xs text-bark-500">Menentukan berapa blok soal yang harus diisi.</p>
                    @error('question_count') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="time_limit">Batas Waktu (detik/soal)</label>
                    <input id="time_limit" name="time_limit" type="number" min="5" max="120" required
                           value="{{ old('time_limit', 7) }}" class="field">
                    @error('time_limit') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="available_on">Tanggal Mulai</label>
                    <input id="available_on" name="available_on" type="date" required
                           value="{{ old('available_on', today()->toDateString()) }}" class="field">
                    @error('available_on') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="ends_at">Berakhir (opsional)</label>
                    <input id="ends_at" name="ends_at" type="date" value="{{ old('ends_at') }}" class="field">
                    @error('ends_at') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-bark-700">
                <input type="checkbox" name="is_active" value="1" checked
                       class="rounded-sm border-bark-300 text-brass-600 focus:ring-brass-600/30">
                Aktifkan kuis (tampil di halaman kuis)
            </label>

            <div class="flex flex-wrap gap-2 border-t border-bark-200 pt-5">
                <button type="submit" class="btn-primary">Simpan & Isi Soal →</button>
                <a href="{{ route('admin.quests.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
