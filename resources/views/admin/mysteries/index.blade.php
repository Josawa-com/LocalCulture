@extends('layouts.admin')

@section('title', 'Kelola Mystery')

@section('content')
    <div class="mx-auto max-w-6xl">
        <header class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <span class="kicker">Minigame</span>
                <h1 class="display-lg mt-1 text-2xl text-bark-950">Kelola Mystery Budaya</h1>
                <p class="mt-1 text-sm text-bark-600">
                    Buat pernyataan + petunjuk. Pemain diberi 3 petunjuk dan 7 kesempatan menebak.
                </p>
            </div>
            <a href="{{ route('admin.mysteries.create') }}" class="btn-primary">+ Buat Misteri</a>
        </header>

        @if($mysteries->isEmpty())
            <div class="rounded-lg border border-dashed border-bark-300 bg-bark-50/50 px-6 py-12 text-center">
                <p class="text-sm font-medium text-bark-700">Belum ada misteri.</p>
                <p class="mt-1 text-xs text-bark-500">Klik "Buat Misteri" untuk menambahkan yang pertama.</p>
            </div>
        @else
            <div class="panel overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-bark-200 bg-bark-50 text-xs uppercase tracking-wide text-bark-600">
                        <tr>
                            <th class="px-4 py-3">Pernyataan</th>
                            <th class="px-4 py-3">Jawaban</th>
                            <th class="px-4 py-3 text-center">Petunjuk</th>
                            <th class="px-4 py-3 text-center">Poin</th>
                            <th class="px-4 py-3 text-center">Dimainkan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-bark-100">
                        @foreach($mysteries as $mystery)
                            <tr class="align-top">
                                <td class="px-4 py-3">
                                    <div class="max-w-md text-sm text-bark-800 line-clamp-2">
                                        {{ $mystery->statement ?: '—' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-bark-900">{{ $mystery->answer }}</div>
                                    <div class="text-xs text-bark-500">{{ $mystery->origin ?: '—' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center text-bark-700">{{ count($mystery->availableClues()) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="font-medium text-bark-900">{{ $mystery->points }}</div>
                                    <div class="text-xs text-bark-500">−{{ $mystery->clue_penalty }}/clue</div>
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    <span class="text-signal-success">{{ $mystery->solved_count }} berhasil</span><br>
                                    <span class="text-batik-700">{{ $mystery->failed_count }} gagal</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($mystery->is_active)
                                        <span class="tag bg-signal-success/15 text-signal-success">Aktif</span>
                                    @else
                                        <span class="tag bg-bark-100 text-bark-600">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.mysteries.edit', $mystery) }}" class="btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.mysteries.destroy', $mystery) }}"
                                              onsubmit="return confirm('Hapus misteri ini beserta semua riwayat permainannya?')">
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

            <div class="mt-4">{{ $mysteries->links() }}</div>
        @endif
    </div>
@endsection
