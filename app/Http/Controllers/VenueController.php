<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(Request $request): View
    {
        $venues = Venue::query()
            ->with('regency.province')
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q')->squish().'%'))
            ->when($request->filled('province'), fn ($q) => $q->whereHas('regency', fn ($r) => $r->where('province_id', $request->integer('province'))))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('venues.index', [
            'venues' => $venues,
            'provinces' => Province::orderBy('name')->get(),
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
