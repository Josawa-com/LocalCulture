<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regency;
use App\Models\Show;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.shows.index', [
            'shows' => Show::with('regency')->orderByDesc('starts_at')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.shows.create', ['regencies' => Regency::with('province')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateShow($request);

        $validated['slug'] = str($validated['title'])->slug().'-'.str()->random(5);

        Show::create($validated);

        return redirect()->route('admin.shows.index')->with('success', 'Pertunjukan ditambahkan.');
    }

    public function edit(Show $show): View
    {
        return view('admin.shows.edit', [
            'show' => $show,
            'regencies' => Regency::with('province')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Show $show)
    {
        $show->update($this->validateShow($request));

        return redirect()->route('admin.shows.index')->with('success', 'Pertunjukan diperbarui.');
    }

    public function destroy(Show $show)
    {
        $show->delete();

        return redirect()->route('admin.shows.index')->with('success', 'Pertunjukan dihapus.');
    }

    private function validateShow(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['required', 'date'],
            'location_name' => ['required', 'string', 'max:255'],
            'regency_id' => ['nullable', 'exists:regencies,id'],
            'ticket_price' => ['required', 'numeric', 'min:0'],
            'organizer' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
