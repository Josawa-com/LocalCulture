<?php

namespace App\Http\Controllers;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Island;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CultureController extends Controller
{
    public function islands(Request $request): View
    {
        // Guest (belum login) hanya bisa menjelajahi satu provinsi per session
        $lockedProvinceId = $request->session()->get('guest_province_id');

        return view('culture.islands', [
            'islands' => Island::withCount(['provinces', 'regencies'])->orderBy('name')->get(),
            'lockedProvinceId' => $lockedProvinceId,
            'lockedProvince' => $lockedProvinceId ? Province::find($lockedProvinceId) : null,
        ]);
    }

    public function provinces(Request $request, Island $island): View
    {
        $lockedProvinceId = $request->user() ? null : $request->session()->get('guest_province_id');

        return view('culture.provinces', [
            'island' => $island->loadCount('regencies'),
            'provinces' => $island->provinces()->withCount('regencies')->orderBy('name')->get(),
            'lockedProvinceId' => $lockedProvinceId,
        ]);
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

        return view('culture.regencies', [
            'province' => $province->load('island'),
            'regencies' => $province->regencies()->withCount('cultures')->orderBy('name')->get(),
        ]);
    }

    public function regency(Request $request, Island $island, Province $province, Regency $regency): View
    {
        $this->guardGuestAccess($request, $province);

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
            abort(redirect()->route('culture.islands')->with('info', 'Pilih satu provinsi untuk mulai menjelajah, atau daftar untuk akses penuh.'));
        }

        abort_unless((int) $locked === $province->id, 403, 'Buat akun gratis untuk mengakses materi provinsi lain!');
    }
}
