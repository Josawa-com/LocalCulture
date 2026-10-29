@extends('layouts.guest')

@section('title', 'Daftar')

@section('content')
    <div class="mx-auto grid w-full max-w-5xl items-center gap-10 lg:grid-cols-2">
        {{-- Sisi brand --}}
        <div class="hidden lg:block">
            <img src="{{ asset('gambar/CultureRayaLogo.png') }}"
                 alt="Logo CultureRaya: gunungan wayang dengan buku terbuka"
                 width="240" height="160" class="w-56 object-contain">
            <h1 class="display-lg mt-4 text-2xl text-bark-950">Buat akun CultureRaya</h1>
            <p class="mt-2 max-w-sm text-sm leading-relaxed text-bark-600">
                Daftar gratis untuk mengerjakan kuis budaya, mengumpulkan poin, dan bersaing di papan peringkat.
            </p>
            <ul class="mt-5 space-y-2.5 text-sm text-bark-600">
                <li class="flex items-start gap-2"><span class="mt-0.5 text-signal-success">✓</span> Akses materi budaya seluruh provinsi</li>
                <li class="flex items-start gap-2"><span class="mt-0.5 text-signal-success">✓</span> Ikuti kuis harian, mingguan, bulanan & pengetahuan</li>
                <li class="flex items-start gap-2"><span class="mt-0.5 text-signal-success">✓</span> Main Mystery Budaya & masuk leaderboard</li>
                <li class="flex items-start gap-2"><span class="mt-0.5 text-signal-success">✓</span> Kesempatan tiket pertunjukan gratis</li>
            </ul>
        </div>

        {{-- Form --}}
        <div class="panel mx-auto w-full max-w-md p-6 sm:p-8">
            <h2 class="display-lg text-xl text-bark-950">Daftar Gratis</h2>
            <p class="mt-1 text-sm text-bark-600">Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-brass-700 hover:underline">Masuk di sini</a>
            </p>

            <form method="POST" action="{{ route('register.attempt') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="field-label" for="name">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required autofocus autocomplete="name"
                           value="{{ old('name') }}" class="field" placeholder="Nama kamu">
                    @error('name') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="field-label" for="email">Email</label>
                    <input id="email" name="email" type="email" required autocomplete="email"
                           value="{{ old('email') }}" class="field" placeholder="nama@email.com">
                    @error('email') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                </div>

                <fieldset class="grid gap-4 sm:grid-cols-2">
                    <legend class="sr-only">Asal daerah</legend>

                    <div>
                        <label class="field-label" for="island_id">Pulau Asal</label>
                        <select id="island_id" name="island_id" required
                                data-old="{{ old('island_id') }}" class="field">
                            <option value="">— Pilih pulau —</option>
                            @foreach($islands as $island)
                                <option value="{{ $island->id }}" @selected((string) old('island_id') === (string) $island->id)>
                                    {{ $island->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('island_id') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="field-label" for="province_id">Provinsi</label>
                        <select id="province_id" name="province_id" required
                                data-old="{{ old('province_id') }}" class="field">
                            <option value="">— Pilih provinsi —</option>
                            @foreach($islands as $island)
                                @foreach($island->provinces as $province)
                                    <option value="{{ $province->id }}" data-parent="{{ $island->id }}" hidden>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-bark-500">Pilih pulau dahulu.</p>
                        @error('province_id') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                    </div>
                </fieldset>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="field-label" for="password">Password</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                               class="field" aria-describedby="password-hint">
                        <p id="password-hint" class="mt-1 text-xs text-bark-500">Minimal 8 karakter.</p>
                        @error('password') <p class="mt-1 text-xs text-signal-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label" for="password_confirmation">Ulangi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               autocomplete="new-password" class="field">
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full">Buat Akun</button>
            </form>
        </div>
    </div>
@endsection
