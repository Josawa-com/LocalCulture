<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regency;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.venues.index', [
            'venues' => Venue::with('regency')->withCount('interestRequests')->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.venues.create', ['regencies' => Regency::with('province')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateVenue($request);

        $validated['slug'] = str($validated['name'])->slug().'-'.str()->random(5);

        Venue::create($validated);

        return redirect()->route('admin.venues.index')->with('success', 'Tempat latihan ditambahkan.');
    }

    public function edit(Venue $venue): View
    {
        return view('admin.venues.edit', [
            'venue' => $venue,
            'regencies' => Regency::with('province')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Venue $venue)
    {
        $venue->update($this->validateVenue($request));

        return redirect()->route('admin.venues.index')->with('success', 'Tempat latihan diperbarui.');
    }

    public function destroy(Venue $venue)
    {
        $venue->delete();

        return redirect()->route('admin.venues.index')->with('success', 'Tempat latihan dihapus.');
    }

    private function validateVenue(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:sanggar_tari,gamelan,karawitan,komunitas,lainnya'],
            'address' => ['required', 'string', 'max:255'],
            'regency_id' => ['nullable', 'exists:regencies,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
