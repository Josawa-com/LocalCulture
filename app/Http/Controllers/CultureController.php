<?php

namespace App\Http\Controllers;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Island;
use App\Models\Province;
use App\Models\Regency;
use App\Models\RegencyVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CultureController extends Controller
{
    public function islands(Request $request): View
    {
        // Guest (belum login) hanya bisa menjelajahi satu provinsi per session
        $lockedProvinceId = $request->session()->get('guest_province_id');
        $islandMaps = $this->islandMaps();

        return view('culture.islands', [
            'islandMaps' => $islandMaps,
            'islandMapAspect' => (float) config('island_maps.aspect', 1.4),
            // Faktor skala & geser untuk layar sempit (lihat islandMapFit()).
            'islandMapFit' => $this->islandMapFit($islandMaps),
            'lockedProvinceId' => $lockedProvinceId,
            'lockedProvince' => $lockedProvinceId ? Province::find($lockedProvinceId) : null,
        ]);
    }

    /**
     * Hitung skala & geser untuk layar sempit.
     *
     * Semua gambar diperlakukan sebagai SATU kesatuan: posisi antar-gambar
     * tidak pernah diubah. Yang dilakukan hanya memperkecil/menggeser seluruh
     * kotak peta, sehingga tata letaknya sama persis dengan desktop — tidak ada
     * gambar yang bergeser sendiri-sendiri.
     *
     * @param  list<array{x: float, wr: float}>  $maps
     * @return array{scale: float, shift: float}
     */
    private function islandMapFit(array $maps): array
    {
        if ($maps === []) {
            return ['scale' => 1.0, 'shift' => 0.0];
        }

        // Batas kiri/kanan seluruh gambar (sudah memperhitungkan rotasi).
        $left = min(array_map(fn (array $map) => $map['x'] - $map['wr'] / 2, $maps));
        $right = max(array_map(fn (array $map) => $map['x'] + $map['wr'] / 2, $maps));
        $width = max(0.01, $right - $left);

        // Konten lebih lebar dari kotak -> perkecil seluruh kotak agar pas.
        if ($width > 100) {
            return ['scale' => round(100 / $width, 5), 'shift' => round(-$left, 3)];
        }

        // Konten lebih sempit -> cukup digeser agar berada di tengah.
        return ['scale' => 1.0, 'shift' => round((100 - $width) / 2 - $left, 3)];
    }

    /**
     * Siapkan data peta pulau untuk halaman materi.
     *
     * Posisi x/y/w/z dibaca apa adanya dari config/island_maps.php, sedangkan
     * jumlah provinsi diambil dari angka terakhir pada nama file gambar.
     *
     * 'h' = tinggi gambar dalam persen dari TINGGI kotak (dihitung dari rasio
     * gambar + rasio kotak). Dipakai untuk menjepit posisi vertikal agar gambar
     * tidak keluar kotak.
     *
     * 'wr'/'hr' = ukuran kotak pembatas setelah gambar diputar (persen), supaya
     * jepitan di layar kecil tetap presisi. 'r' = sudut rotasi (derajat).
     *
     * @return list<array{file: string, url: string, label: string, link: ?string, x: float, y: float, w: float, h: float, wr: float, hr: float, r: float, z: int, province_count: ?int}>
     */
    private function islandMaps(): array
    {
        $islandUrls = Island::pluck('slug', 'slug');
        $aspect = (float) config('island_maps.aspect', 1.4);

        return collect(config('island_maps.images', []))
            ->map(function (array $map) use ($islandUrls, $aspect): ?array {
                $file = basename((string) ($map['file'] ?? ''));
                $path = public_path('Pulau/'.$file);

                // Lewati gambar yang filenya tidak ada agar halaman tidak rusak.
                if ($file === '' || ! is_file($path)) {
                    return null;
                }

                // Angka terakhir sebelum ekstensi = jumlah provinsi.
                $provinceCount = preg_match('/(\d+)(?=\.[a-z0-9]+$)/i', $file, $matches)
                    ? (int) $matches[1]
                    : null;

                $islandSlug = $map['island'] ?? null;
                $width = (float) ($map['w'] ?? 25);
                $height = round($width * $this->imageRatio($path) * $aspect, 3);
                $rotation = (float) ($map['r'] ?? 0);

                // Ukuran kotak pembatas SETELAH diputar (dalam persen kotak).
                // Dipakai untuk menjepit posisi di layar kecil agar gambar
                // miring pun tetap utuh di dalam kotak.
                $radians = deg2rad($rotation);
                $cos = abs(cos($radians));
                $sin = abs(sin($radians));
                $rotatedWidth = round($width * $cos + ($height / $aspect) * $sin, 3);
                $rotatedHeight = round(($width * $aspect) * $sin + $height * $cos, 3);

                return [
                    'file' => $file,
                    // Nama file di-encode agar aman (ada file berisi "&").
                    'url' => asset('Pulau/'.rawurlencode($file)),
                    'label' => (string) ($map['label'] ?? pathinfo($file, PATHINFO_FILENAME)),
                    'link' => $islandSlug && $islandUrls->has($islandSlug)
                        ? route('culture.island', $islandSlug)
                        : null,
                    'x' => (float) ($map['x'] ?? 50),
                    'y' => (float) ($map['y'] ?? 50),
                    'w' => $width,
                    // Tinggi dalam persen tinggi kotak: w% x (tinggi/lebar gambar) x aspect
                    'h' => $height,
                    'wr' => $rotatedWidth,
                    'hr' => $rotatedHeight,
                    'r' => $rotation,
                    'z' => (int) ($map['z'] ?? 1),
                    'province_count' => $provinceCount,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Rasio tinggi/lebar gambar (untuk menghitung tinggi dalam persen).
     * Di-cache agar tidak membaca file setiap request.
     */
    private function imageRatio(string $path): float
    {
        return (float) Cache::remember(
            'island-map-ratio:'.md5($path.filemtime($path)),
            now()->addDay(),
            function () use ($path): float {
                $size = @getimagesize($path);

                return $size && $size[0] > 0 ? $size[1] / $size[0] : 0.667;
            }
        );
    }

    public function provinces(Request $request, Island $island): View
    {
        $lockedProvinceId = $request->user() ? null : $request->session()->get('guest_province_id');

        return view('culture.provinces', [
            'island' => $island->loadCount('regencies'),
            'provinces' => $this->provinceCards($island, $lockedProvinceId),
            'lockedProvinceId' => $lockedProvinceId,
        ]);
    }

    /**
     * Kartu provinsi satu pulau.
     *
     * Nama & jumlah kabupaten/kota dibaca dari nama file gambar
     * (config/province_maps.php), karena data di gambar adalah data resmi.
     * Bila pulau belum punya config, dipakai data dari database.
     *
     * @return list<array{name: string, slug: ?string, url: ?string, image: ?string, image_alt: string, note: ?string, regencies: ?int, cities: ?int, locked: bool}>
     */
    private function provinceCards(Island $island, ?int $lockedProvinceId): array
    {
        $mapping = config('province_maps.'.$island->slug);
        $dbProvinces = $island->provinces()->get()->keyBy('slug');

        if (! is_array($mapping) || empty($mapping['provinces'])) {
            // Fallback: tampilkan dari database apa adanya.
            return $dbProvinces->map(function (Province $province) use ($lockedProvinceId): array {
                return [
                    'name' => $province->name,
                    'slug' => $province->slug,
                    'url' => route('culture.province', [$province->island, $province]),
                    'image' => null,
                    'image_alt' => $province->name,
                    'note' => null,
                    'regencies' => null,
                    'cities' => null,
                    'locked' => $lockedProvinceId !== null && (int) $lockedProvinceId !== $province->id,
                ];
            })->values()->all();
        }

        $folder = trim((string) ($mapping['folder'] ?? ''), '/');

        // Encode tiap segmen folder agar aman (nama folder bisa berisi spasi).
        $folderUrl = collect(explode('/', $folder))
            ->map(fn (string $segment) => rawurlencode($segment))
            ->implode('/');

        return collect($mapping['provinces'])
            ->map(function (array $row) use ($island, $dbProvinces, $folder, $folderUrl, $lockedProvinceId): array {
                $file = basename((string) ($row['file'] ?? ''));
                $path = public_path($folder.'/'.$file);
                $exists = $file !== '' && is_file($path);

                // Pola: ...<kabupaten>dan<kota>kota.png
                [$regencies, $cities] = $this->parseCounts($file);

                $slug = $row['slug'] ?? null;
                $province = $slug ? $dbProvinces->get($slug) : null;

                return [
                    'name' => (string) ($row['name'] ?? pathinfo($file, PATHINFO_FILENAME)),
                    'slug' => $slug,
                    'url' => $province
                        ? route('culture.province', [$island, $province])
                        : null,
                    'image' => $exists ? asset($folderUrl.'/'.rawurlencode($file)) : null,
                    'image_alt' => (string) ($row['note'] ?? $row['name'] ?? ''),
                    'note' => $row['note'] ?? null,
                    'regencies' => $regencies,
                    'cities' => $cities,
                    'locked' => $province !== null
                        && $lockedProvinceId !== null
                        && (int) $lockedProvinceId !== $province->id,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Baca jumlah kabupaten & kota dari nama file gambar.
     * Contoh: "MonasJakarta1dan5kota.png" -> [1, 5]
     *
     * @return array{0: ?int, 1: ?int}
     */
    private function parseCounts(string $file): array
    {
        if (! preg_match('/(\d+)\s*dan\s*(\d+)\s*kota/i', $file, $matches)) {
            return [null, null];
        }

        return [(int) $matches[1], (int) $matches[2]];
    }

    /** Guest memilih satu provinsi untuk diakses sebelum login. */
    public function chooseProvince(Request $request)
    {
        $validated = $request->validate(['province_id' => ['required', 'exists:provinces,id']]);

        $request->session()->put('guest_province_id', $validated['province_id']);

        $province = Province::with('island')->find($validated['province_id']);

        return redirect()->route('culture.province', [$province->island, $province]);
    }

    public function regencies(Request $request, Island $island, Province $province): View
    {
        $this->guardGuestAccess($request, $province);

        $regencies = $province->regencies()
            ->withCount('cultures')
            ->orderBy('name')
            ->get();

        return view('culture.regencies', [
            'province' => $province->load('island'),
            'regencies' => $regencies,
            'mostVisitedId' => $this->mostVisitedRegencyId($regencies),
        ]);
    }

    /**
     * ID wilayah yang paling sering dikunjungi di provinsi ini.
     *
     * Mengembalikan null bila:
     *  - belum ada kunjungan sama sekali, atau
     *  - ada lebih dari satu wilayah dengan jumlah kunjungan tertinggi
     *    (seri) — karena tidak ada satu pun yang "paling banyak".
     *
     * @param  Collection<int, Regency>  $regencies
     */
    private function mostVisitedRegencyId($regencies): ?int
    {
        $totals = RegencyVisit::query()
            ->whereIn('regency_id', $regencies->pluck('id'))
            ->groupBy('regency_id')
            ->selectRaw('regency_id, SUM(visits) as total')
            ->pluck('total', 'regency_id');

        if ($totals->isEmpty()) {
            return null;
        }

        $max = (int) $totals->max();

        if ($max <= 0) {
            return null;
        }

        $leaders = $totals->filter(fn ($total) => (int) $total === $max);

        // Seri di puncak -> tidak ada yang paling banyak.
        return $leaders->count() === 1 ? (int) $leaders->keys()->first() : null;
    }

    public function regency(Request $request, Island $island, Province $province, Regency $regency): View
    {
        $this->guardGuestAccess($request, $province);

        // Catat kunjungan (hanya untuk user login) — sumber data badge
        // "Sering dikunjungi" di halaman pemilihan kabupaten/kota.
        if ($user = $request->user()) {
            RegencyVisit::record($user, $regency);
        }

        $regency->load(['province.island', 'cultures.category']);

        return view('culture.regency', [
            'regency' => $regency,
            'categories' => $regency->cultures->groupBy('culture_category_id'),
            'allCategories' => CultureCategory::orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, Island $island, Province $province, Regency $regency, Culture $culture): View
    {
        $this->guardGuestAccess($request, $province);

        $culture->load(['regency.province.island', 'category']);
        $related = $culture->regency->cultures()->where('id', '!=', $culture->id)->limit(4)->get();

        return view('culture.detail', [
            'culture' => $culture,
            'related' => $related,
        ]);
    }

    /** Filosofi Budaya per pulau. */
    public function philosophy(Request $request, Island $island): View
    {
        return view('culture.philosophy', [
            'island' => $island,
            'cultures' => $island->provinces()
                ->with(['regencies.cultures' => fn ($q) => $q->whereNotNull('philosophy')->with('category')])
                ->get()
                ->flatMap(fn ($p) => $p->regencies)
                ->flatMap(fn ($r) => $r->cultures)
                ->filter(fn ($c) => $c->philosophy !== null)
                ->take(12),
        ]);
    }

    /** Batasan: guest hanya boleh mengakses provinsi yang dipilih di session. */
    private function guardGuestAccess(Request $request, Province $province): void
    {
        if ($request->user() !== null) {
            return;
        }

        $locked = $request->session()->get('guest_province_id');

        if ($locked === null) {
            // Tamu yang belum memilih provinsi: provinsi yang sedang dibuka
            // otomatis dijadikan pilihannya. Tanpa notifikasi, tanpa memantul
            // balik ke daftar pulau.
            $request->session()->put('guest_province_id', $province->id);

            return;
        }

        abort_unless((int) $locked === $province->id, 403, 'Buat akun gratis untuk mengakses materi provinsi lain!');
    }
}
