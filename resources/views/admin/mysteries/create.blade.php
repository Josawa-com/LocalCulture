@extends('layouts.admin')

@section('title', 'Buat Misteri')

@section('content')
    <div class="mx-auto max-w-2xl">
        <header class="mb-6">
            <a href="{{ route('admin.mysteries.index') }}" class="text-xs font-medium text-bark-500 hover:text-bark-900">← Kembali ke daftar misteri</a>
            <h1 class="display-lg mt-2 text-2xl text-bark-950">Buat Misteri Baru</h1>
            <p class="mt-1 text-sm text-bark-600">
                Pemain membaca pernyataan, lalu menebak nama budayanya. Mereka bisa membuka
                maksimal 3 petunjuk; setiap petunjuk mengurangi poin.
            </p>
        </header>

        <form method="POST" action="{{ route('admin.mysteries.store') }}" class="panel space-y-5 p-6">
            @csrf

            <div>
                <label class="field-label" for="statement">Pernyataan Awal</label>
                <textarea id="statement" name="statement" rows="3" required class="field"
                          placeholder="Contoh: Bangunan ini beratap melengkung runcing seperti tanduk kerbau dan menjadi rumah adat suku Minangkabau.">{{ old('statement') }}</textarea>
                <p class="mt-1 text-xs text-bark-500">Petunjuk umum yang dibaca pemain sebelum menebak.</p>
                @error('statement') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="answer">Jawaban Benar</label>
                    <input id="answer" name="answer" type="text" required value="{{ old('answer') }}"
                           class="field" placeholder="Contoh: Rumah Gadang">
                    @error('answer') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="origin">Asal Daerah (opsional)</label>
                    <input id="origin" name="origin" type="text" value="{{ old('origin') }}"
                           class="field" placeholder="Contoh: Sumatera Barat">
                    @error('origin') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Petunjuk --}}
            <fieldset class="rounded-md border border-bark-200 p-4">
                <legend class="px-1 text-xs font-bold uppercase tracking-wide text-bark-500">
                    Petunjuk (maksimal 3 dipakai)
                </legend>
                <p class="mb-3 text-xs text-bark-500">
                    Petunjuk dibuka berurutan. Kosongkan yang tidak dipakai.
                </p>

                @for($i = 0; $i < 3; $i++)
                    <div class="{{ $i > 0 ? 'mt-3' : '' }}">
                        <label class="field-label" for="clue{{ $i }}">Petunjuk {{ $i + 1 }}</label>
                        <input id="clue{{ $i }}" name="clues[{{ $i }}]" type="text"
                               value="{{ old("clues.$i") }}" class="field"
                               placeholder="Contoh: Terdiri atas bilangan ganjil ruangan.">
                        @error("clues.$i") <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                    </div>
                @endfor
            </fieldset>

            {{-- Pengaturan poin --}}
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="field-label" for="points">Poin Maksimal</label>
                    <input id="points" name="points" type="number" min="50" max="1000" required
                           value="{{ old('points', 300) }}" class="field">
                    <p class="mt-1 text-xs text-bark-500">Bila langsung benar.</p>
                    @error('points') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="clue_penalty">Potongan per Petunjuk</label>
                    <input id="clue_penalty" name="clue_penalty" type="number" min="0" max="500" required
                           value="{{ old('clue_penalty', 50) }}" class="field">
                    @error('clue_penalty') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="max_wrong_guesses">Maks. Salah</label>
                    <input id="max_wrong_guesses" name="max_wrong_guesses" type="number" min="1" max="20" required
                           value="{{ old('max_wrong_guesses', 7) }}" class="field">
                    <p class="mt-1 text-xs text-bark-500">Setelah ini gagal permanen.</p>
                    @error('max_wrong_guesses') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-bark-700">
                <input type="checkbox" name="is_active" value="1" checked
                       class="rounded-sm border-bark-300 text-brass-600 focus:ring-brass-600/30">
                Aktifkan (tampil di halaman Mystery)
            </label>

            <div class="flex flex-wrap gap-2 border-t border-bark-200 pt-5">
                <button type="submit" class="btn-primary">Simpan Misteri</button>
                <a href="{{ route('admin.mysteries.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
