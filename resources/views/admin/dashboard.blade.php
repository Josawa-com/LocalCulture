@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="mx-auto max-w-6xl">
        <header class="mb-8">
            <span class="kicker">Panel Admin</span>
            <h1 class="display-lg mt-1 text-2xl text-bark-950">Dashboard</h1>
            <p class="mt-2 text-sm text-bark-600">Kelola konten CultureRaya dari satu tempat.</p>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['label' => 'Pengguna', 'value' => $stats['users'], 'icon' => '👥'],
                ['label' => 'Kuis', 'value' => $stats['quests'], 'icon' => '📝'],
                ['label' => 'Pertunjukan', 'value' => $stats['shows'], 'icon' => '🎭'],
                ['label' => 'Sanggar', 'value' => $stats['venues'], 'icon' => '🏛️'],
            ] as $card)
                <div class="panel p-5">
                    <div class="text-2xl">{{ $card['icon'] }}</div>
                    <div class="display-lg mt-2 text-2xl text-bark-950">{{ $card['value'] }}</div>
                    <div class="kicker mt-1">{{ $card['label'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <section class="panel p-6">
                <h2 class="display-lg text-lg text-bark-950">Aksi Cepat</h2>
                <p class="mt-1 text-sm text-bark-600">Tambahkan konten baru.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.quests.create') }}" class="btn-primary">+ Buat Kuis</a>
                    <a href="{{ route('admin.quests.index') }}" class="btn-secondary">Kelola Kuis</a>
                </div>
            </section>

            <section class="panel p-6">
                <h2 class="display-lg text-lg text-bark-950">Permintaan Menunggu</h2>
                @if($pendingRequests->isEmpty())
                    <p class="mt-2 text-sm text-bark-600">Tidak ada permintaan baru.</p>
                @else
                    <ul class="mt-3 space-y-2">
                        @foreach($pendingRequests as $request)
                            <li class="rounded-md border border-bark-200 p-3 text-sm">
                                <span class="font-medium text-bark-900">{{ $request->user->name }}</span>
                                <span class="text-bark-500">→ {{ $request->venue->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
@endsection
