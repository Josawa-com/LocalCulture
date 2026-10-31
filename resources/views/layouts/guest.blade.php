<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CultureRaya')</title>
    <link rel="icon" type="image/png" href="{{ asset('gambar/CultureRayaLogo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-parchment text-bark-900 antialiased font-sans">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 rounded-md bg-brass-600 px-3 py-2 text-xs font-semibold text-bark-950">
        Lompati ke konten utama
    </a>

    <main id="main-content" class="flex flex-1 items-center justify-center px-4 py-10">
        <div class="w-full">
            @if (session('success') || session('error') || $errors->any())
                <div class="mx-auto mb-4 max-w-md">
                    @if (session('success'))<x-flash type="success">{{ session('success') }}</x-flash>@endif
                    @if (session('error'))<x-flash type="error">{{ session('error') }}</x-flash>@endif
                    @if ($errors->any())<x-flash type="error">{{ $errors->first() }}</x-flash>@endif
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="px-4 py-8 text-center">
        <a href="{{ route('home') }}"
           class="btn-secondary w-full max-w-md justify-center gap-2 px-6 py-3 text-sm sm:w-auto">
            <span aria-hidden="true">←</span>
            Kembali ke Beranda
        </a>
    </footer>
</body>
</html>
