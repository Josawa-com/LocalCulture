@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="mx-auto w-full max-w-sm">
        <div class="mb-6 text-center">
            <img src="{{ asset('gambar/CultureRayaLogo.png') }}" alt="Logo CultureRaya"
                 width="96" height="96" class="mx-auto h-20 w-20 object-contain">
            <h1 class="display-lg mt-3 text-xl text-bark-950">Masuk ke CultureRaya</h1>
            <p class="mt-1 text-sm text-bark-600">Gunakan akun yang sudah terdaftar.</p>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="panel space-y-4 p-6">
            @csrf

            <div>
                <label class="field-label" for="email">Email</label>
                <input id="email" name="email" type="email" required autofocus autocomplete="email"
                       value="{{ old('email') }}" class="field" placeholder="nama@email.com">
                @error('email') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="field-label" for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="field" placeholder="••••••••">
                @error('password') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-bark-600">
                <input type="checkbox" name="remember" value="1"
                       class="rounded-sm border-bark-300 text-brass-600 focus:ring-brass-600/30">
                Ingat saya
            </label>

            <button type="submit" class="btn-primary w-full">Masuk</button>
        </form>

        <p class="mt-4 text-center text-xs text-bark-500">
            Admin? Masuk lalu buka <span class="font-semibold text-bark-700">/rezka</span>
        </p>
    </div>
@endsection
