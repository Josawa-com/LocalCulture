<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CultureRaya') - Jelajah Budaya Nusantara</title>
    <link rel="icon" type="image/png" href="{{ asset('gambar/CultureRayaLogo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('gambar/CultureRayaLogo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-parchment text-bark-900 antialiased font-sans">
    {{-- Skip link untuk aksesibilitas --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 rounded-md bg-brass-600 px-3 py-2 text-xs font-semibold text-bark-950 shadow-raised">
        Lompati ke konten utama
    </a>

    <header class="sticky top-0 z-40 border-b border-bark-200 bg-parchment/90 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4">
            {{-- Brand: logo utuh + nama --}}
            <a href="{{ route('home') }}" class="group flex items-center gap-3" aria-label="CultureRaya beranda">
                <img src="{{ asset('gambar/CultureRayaLogo.png') }}" alt="Logo CultureRaya" width="48" height="48" class="h-11 w-11 object-contain transition-transform duration-200 group-hover:scale-105" loading="lazy">
                <div class="leading-tight">
                    <span class="block display-lg tracking-tight text-bark-900">Culture<span class="text-brass-700">Raya</span></span>
                    <span class="kicker">Jelajahi Budaya Indonesia</span>
                </div>
            </a>

            {{-- Desktop nav --}}
            @php
                $navItems = [
                    ['label' => 'Materi Budaya', 'short' => 'Budaya', 'route' => 'culture.islands', 'active' => 'culture.*'],
                    ['label' => 'Kuis', 'short' => 'Kuis', 'route' => 'quests.index', 'active' => 'quests.*'],
                    ['label' => 'Mystery', 'short' => 'Mystery', 'route' => 'mystery.index', 'active' => 'mystery.*'],
                    ['label' => 'Pertunjukan', 'short' => 'Pertunjukan', 'route' => 'shows.index', 'active' => 'shows.*'],
                    ['label' => 'Sanggar', 'short' => 'Sanggar', 'route' => 'venues.index', 'active' => 'venues.*'],
                    ['label' => 'Peringkat', 'short' => 'Peringkat', 'route' => 'leaderboard', 'active' => 'leaderboard'],
                ];
            @endphp
            <ul class="hidden items-center gap-1 md:flex">
                @foreach($navItems as $item)
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

            {{-- User menu: avatar & nama langsung membuka halaman profil --}}
            <div class="flex items-center gap-2">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="hidden items-center gap-2 rounded-md bg-bark-900 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-bark-800 sm:flex">⚙️ Admin</a>
                    @endif
                    <a href="{{ route('profile') }}" @if(request()->routeIs('profile')) aria-current="page" @endif
                       class="inline-flex items-center gap-2 rounded-full border bg-white/60 py-1.5 pl-1.5 pr-3 text-xs font-medium transition {{ request()->routeIs('profile') ? 'border-brass-500 text-bark-950 shadow-sm' : 'border-bark-300 text-bark-800 hover:border-bark-400 hover:bg-white' }}"
                       aria-label="Buka profil {{ auth()->user()->name }}">
                        <x-avatar :name="auth()->user()->name" :path="auth()->user()->avatar_path" size="xs"/>
                        <span class="hidden max-w-24 truncate lg:block">{{ auth()->user()->name }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-xs font-medium text-bark-700 hover:bg-bark-100">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary">Daftar</a>
                @endauth
            </div>
        </nav>

        {{-- Mobile nav --}}
        <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-4 pb-3 text-sm md:hidden">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}" @if(request()->routeIs($item['active'])) aria-current="page" @endif
                   class="flex shrink-0 items-center gap-1.5 rounded-md px-2.5 py-1.5 {{ request()->routeIs($item['active']) ? 'bg-bark-900 font-semibold text-white' : 'text-bark-700 hover:bg-bark-100' }}">
                    {{ $item['short'] }}
                    @if(request()->routeIs($item['active']))
                        <span class="h-1.5 w-1.5 rounded-full bg-brass-400" aria-hidden="true"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </header>

    {{-- Main --}}
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

    <main id="main-content" class="w-full flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-bark-200 bg-bark-50 py-8">
        <div class="mx-auto max-w-7xl px-4 text-center">
            <p class="display inline-block font-semibold text-bark-900">
                <img src="{{ asset('gambar/CultureRayaLogo.png') }}" alt="Logo CultureRaya" width="20" height="20" class="inline mr-2 align-middle"> CultureRaya
            </p>
            <p class="mt-2 text-sm text-bark-600">Jelajahi, pelajari, dan lestarikan budaya Indonesia.</p>
            <nav class="mt-4 flex flex-wrap justify-center gap-3 text-xs text-bark-600">
                <a href="{{ route('culture.islands') }}" class="hover:text-bark-900">Materi</a>
                <a href="{{ route('quests.index') }}" class="hover:text-bark-900">Kuis Harian & Mingguan</a>
                <a href="{{ route('mystery.index') }}" class="hover:text-bark-900">Mystery Budaya</a>
                <a href="{{ route('shows.index') }}" class="hover:text-bark-900">Pertunjukan</a>
                <a href="{{ route('venues.index') }}" class="hover:text-bark-900">Tempat Latihan</a>
            </nav>
            <p class="mt-6 text-xs text-bark-500">&copy; {{ date('Y') }} CultureRaya · Dibuat untuk lomba sosial budaya</p>
        </div>
    </footer>
</body>
</html>
