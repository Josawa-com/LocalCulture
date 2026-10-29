@extends('layouts.admin')

@section('title', 'Kelola Kuis')

@section('content')
    <div class="mx-auto max-w-6xl">
        <header class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <span class="kicker">Manajemen Konten</span>
                <h1 class="display-lg mt-1 text-2xl text-bark-950">Kelola Kuis</h1>
                <p class="mt-1 text-sm text-bark-600">Buat kuis harian, mingguan, bulanan, dan pengetahuan.</p>
            </div>
            <a href="{{ route('admin.quests.create') }}" class="btn-primary">+ Buat Kuis Baru</a>
        </header>

        @if($quests->isEmpty())
            <div class="rounded-lg border border-dashed border-bark-300 bg-bark-50/50 px-6 py-12 text-center">
                <p class="text-sm font-medium text-bark-700">Belum ada kuis.</p>
                <p class="mt-1 text-xs text-bark-500">Klik "Buat Kuis Baru" untuk menambahkan kuis pertama.</p>
            </div>
        @else
            <div class="panel overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-bark-200 bg-bark-50 text-xs uppercase tracking-wide text-bark-600">
                        <tr>
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Materi</th>
                            <th class="px-4 py-3">Soal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-bark-100">
                        @foreach($quests as $quest)
                            @php $complete = $quest->question_count > 0 && $quest->questions_count >= $quest->question_count; @endphp
                            <tr class="align-top">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-bark-900">{{ $quest->title }}</div>
                                    <div class="text-xs text-bark-500">{{ $quest->available_on?->translatedFormat('d M Y') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="tag bg-brass-100 text-brass-800">{{ $quest->typeLabel() }}</span>
                                </td>
                                <td class="px-4 py-3 text-bark-700">{{ $quest->subtitle ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-bark-900">{{ $quest->questions_count }}</span>
                                    <span class="text-bark-500">/ {{ $quest->question_count }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($complete)
                                        <span class="tag bg-signal-success/15 text-signal-success">Lengkap</span>
                                    @else
                                        <span class="tag bg-bark-100 text-bark-600">Belum lengkap</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.quests.questions', $quest) }}" class="btn-secondary btn-sm">Soal</a>
                                        <a href="{{ route('admin.quests.edit', $quest) }}" class="btn-ghost btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.quests.destroy', $quest) }}"
                                              onsubmit="return confirm('Hapus kuis ini beserta semua soalnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $quests->links() }}</div>
        @endif
    </div>
@endsection
