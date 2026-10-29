@extends('layouts.admin')

@section('title', 'Isi Soal Kuis')

@section('content')
    <div class="mx-auto max-w-4xl">
        <header class="mb-6">
            <a href="{{ route('admin.quests.index') }}" class="text-xs font-medium text-bark-500 hover:text-bark-900">← Kembali ke daftar kuis</a>
            <h1 class="display-lg mt-2 text-2xl text-bark-950">Isi Soal & Jawaban</h1>
            <p class="mt-1 text-sm text-bark-600">
                {{ $quest->title }} · {{ $quest->typeLabel() }}
                @if($quest->subtitle) · {{ $quest->subtitle }} @endif
            </p>
            <p class="mt-2 inline-flex items-center gap-2 rounded-md bg-brass-100 px-3 py-1.5 text-xs font-semibold text-brass-800">
                Target {{ $quest->question_count }} soal · tersimpan {{ $quest->questions->count() }}
            </p>
        </header>

        @php
            // Gabungkan soal tersimpan ke slot sesuai sort_order agar bisa diedit ulang.
            $saved = $quest->questions->keyBy('sort_order');
        @endphp

        <form method="POST" action="{{ route('admin.questions.store', $quest) }}" class="space-y-4">
            @csrf

            @for($i = 0; $i < $slots; $i++)
                @php
                    $existing = $saved->get($i);
                    $options = $existing?->options ?? ['', '', '', ''];
                    $options = array_pad($options, 4, '');
                @endphp

                <fieldset class="panel p-5">
                    <legend class="px-1 text-xs font-bold uppercase tracking-wide text-bark-500">
                        Soal {{ $i + 1 }}
                        @if($existing) <span class="ml-1 text-signal-success">✓ tersimpan</span> @endif
                    </legend>

                    <div class="mt-3">
                        <label class="field-label" for="q{{ $i }}">Pertanyaan</label>
                        <textarea id="q{{ $i }}" name="questions[{{ $i }}][question]" rows="2" class="field"
                                  placeholder="Tulis pertanyaan...">{{ old("questions.$i.question", $existing?->question) }}</textarea>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @for($o = 0; $o < 4; $o++)
                            <div>
                                <label class="field-label" for="q{{ $i }}o{{ $o }}">
                                    Pilihan {{ chr(65 + $o) }}
                                    <span class="text-xs font-normal text-bark-400">({{ $o === 0 ? 'A' : '' }}{{ $o === 1 ? 'B' : '' }}{{ $o === 2 ? 'C' : '' }}{{ $o === 3 ? 'D' : '' }})</span>
                                </label>
                                <input id="q{{ $i }}o{{ $o }}" name="questions[{{ $i }}][options][{{ $o }}]"
                                       type="text" class="field" value="{{ old("questions.$i.options.$o", $options[$o]) }}"
                                       placeholder="Teks pilihan {{ chr(65 + $o) }}">
                            </div>
                        @endfor
                    </div>

                    <div class="mt-3">
                        <label class="field-label" for="q{{ $i }}c">Jawaban Benar</label>
                        <select id="q{{ $i }}c" name="questions[{{ $i }}][correct_index]" class="field">
                            @foreach(['A', 'B', 'C', 'D'] as $idx => $letter)
                                <option value="{{ $idx }}" @selected((int) old("questions.$i.correct_index", $existing?->correct_index ?? 0) === $idx)>
                                    {{ $letter }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </fieldset>
            @endfor

            <div class="sticky bottom-4 flex flex-wrap gap-2 rounded-lg border border-bark-200 bg-white/95 p-3 shadow-raised backdrop-blur">
                <button type="submit" class="btn-primary">Simpan Semua Soal</button>
                <a href="{{ route('admin.quests.index') }}" class="btn-ghost">Selesai</a>
                <span class="ml-auto self-center text-xs text-bark-500">
                    Blok kosong akan dilewati — bisa diisi bertahap.
                </span>
            </div>
        </form>
    </div>
@endsection
