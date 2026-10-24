<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - CultureRaya</title>
    <link rel="icon" type="image/png" href="{{ asset('gambar/CultureRayaLogo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-parchment text-bark-900 antialiased font-sans">
    <header class="sticky top-0 z-40 border-b border-bark-200 bg-parchment/90 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3" aria-label="Admin CultureRaya">
                <img src="{{ asset('gambar/CultureRayaLogo.png') }}" alt="Logo CultureRaya" width="40" height="40" class="h-10 w-10 object-contain">
                <div class="leading-tight">
                    <span class="block display-lg tracking-tight text-bark-900">Admin<span class="text-brass-700">Panel</span></span>
                    <span class="kicker">CultureRaya</span>
                </div>
            </a>

            @php
                $adminNavItems = [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
                    ['label' => 'Kuis', 'route' => 'admin.quests.index', 'active' => 'admin.quests.*'],
                    ['label' => 'Pertunjukan', 'route' => 'admin.shows.index', 'active' => 'admin.shows.*'],
                    ['label' => 'Sanggar', 'route' => 'admin.venues.index', 'active' => 'admin.venues.*'],
                    ['label' => 'Permintaan', 'route' => 'admin.requests.index', 'active' => 'admin.requests.*'],
                ];
            @endphp
            <ul class="hidden items-center gap-1 md:flex">
                @foreach($adminNavItems as $item)
                    <li>
                        <a href="{{ route($item['route']) }}" @if(request()->routeIs($item['active'])) aria-current="page" @endif
                           class="relative rounded-md px-3 py-2 text-sm transition {{ request()->routeIs($item['active']) ? 'font-semibold text-bark-950' : 'font-medium text-bark-700 hover:bg-bark-100 hover:text-bark-900' }}">
                            {{ $item['label'] }}
                            @if(request()->routeIs($item['active']))
                                <span class="absolute inset-x-3 bottom-0.5 h-0.5 rounded-full bg-brass-500" aria-hidden="true"></span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="hidden rounded-md px-3 py-2 text-xs font-medium text-bark-700 hover:bg-bark-100 sm:block">← Situs Utama</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md px-3 py-2 text-xs font-medium text-batik-700 hover:bg-rose-50">Keluar</button>
                </form>
            </div>
        </nav>

        {{-- Mobile nav --}}
        <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-4 pb-3 text-sm md:hidden">
            @foreach($adminNavItems as $item)
                <a href="{{ route($item['route']) }}" @if(request()->routeIs($item['active'])) aria-current="page" @endif
                   class="flex shrink-0 items-center gap-1.5 rounded-md px-2.5 py-1.5 {{ request()->routeIs($item['active']) ? 'bg-bark-900 font-semibold text-white' : 'text-bark-700 hover:bg-bark-100' }}">
                    {{ $item['label'] }}
                    @if(request()->routeIs($item['active']))
                        <span class="h-1.5 w-1.5 rounded-full bg-brass-400" aria-hidden="true"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </header>

    @if (session('success') || session('error') || session('info') || $errors->any())
        <div class="mx-auto w-full max-w-3xl px-4 pt-4">
            @if (session('success'))
                <x-flash type="success">{{ session('success') }}</x-flash>
            @endif
            @if (session('error'))
                <x-flash type="error">{{ session('error') }}</x-flash>
            @endif
            @if (session('info'))
                <x-flash type="info">{{ session('info') }}</x-flash>
            @endif
            @if ($errors->any())
                <x-flash type="error">{{ $errors->first() }}</x-flash>
            @endif
        </div>
    @endif

    <main id="main-content" class="mx-auto w-full max-w-7xl flex-1 px-4 py-8">
        @yield('content')
    </main>

    <footer class="border-t border-bark-200 bg-bark-50 py-6 text-center">
        <p class="text-xs text-bark-500">&copy; {{ date('Y') }} CultureRaya Admin Panel</p>
    </footer>
</body>
</html>
