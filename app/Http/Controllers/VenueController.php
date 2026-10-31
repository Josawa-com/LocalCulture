<?php

namespace App\Http\Controllers;

use App\Models\Regency;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueController extends Controller
{
    /**
     * Daftar sanggar & tempat latihan seni budaya di DIY.
     *
     * Dikelompokkan per kabupaten/kota agar mudah dipindai di layar HP.
     * Setiap kartu membuka Google Maps dengan pencarian nama sanggarnya.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $category = (string) $request->query('kategori', '');
        $regencySlug = (string) $request->query('wilayah', '');

        $venues = Venue::query()
            ->with('regency')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('skills', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%');
                });
            })
            ->when($category !== '', fn ($q) => $q->where('category', $category))
            ->when($regencySlug !== '', fn ($q) => $q->whereHas(
                'regency',
                fn ($r) => $r->where('slug', $regencySlug)
            ))
            ->orderBy('name')
            ->get();

        // Kelompokkan per wilayah, urut sesuai urutan resmi DIY.
        $regencyOrder = ['kota-jogja', 'sleman', 'bantul', 'kulon-progo', 'gunung-kidul'];

        $grouped = $venues
            ->groupBy(fn (Venue $venue) => $venue->regency?->slug ?? 'lainnya')
            ->sortBy(fn ($group, $slug) => array_search($slug, $regencyOrder, true) === false
                ? PHP_INT_MAX
                : array_search($slug, $regencyOrder, true));

        return view('venues.index', [
            'grouped' => $grouped,
            'total' => $venues->count(),
            'categories' => Venue::query()
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
            'regencies' => Regency::query()
                ->whereIn('slug', $regencyOrder)
                ->get()
                ->sortBy(fn (Regency $regency) => array_search($regency->slug, $regencyOrder, true))
                ->values(),
            'search' => $search,
            'category' => $category,
            'regencySlug' => $regencySlug,
        ]);
    }

    /** Tombol "Tertarik" - masuk antrian admin. */
    public function interested(Request $request, Venue $venue)
    {
        $request->user()->interestRequests()->firstOrCreate(
            ['venue_id' => $venue->id],
            ['status' => 'pending']
        );

        return back()->with('success', 'Permintaanmu terkirim! Admin akan menghubungi dengan info lebih lanjut.');
    }

    public function myRequests(Request $request): View
    {
        return view('venues.requests', [
            'requests' => $request->user()->interestRequests()->with('venue.regency.province')->latest()->get(),
        ]);
    }
}
