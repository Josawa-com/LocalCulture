<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mystery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MysteryAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.mysteries.index', [
            'mysteries' => Mystery::withCount([
                'attempts',
                'attempts as solved_count' => fn ($q) => $q->where('status', 1),
                'attempts as failed_count' => fn ($q) => $q->where('status', 2),
            ])->orderByDesc('created_at')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.mysteries.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMystery($request);

        Mystery::create($validated);

        return redirect()
            ->route('admin.mysteries.index')
            ->with('success', 'Misteri berhasil ditambahkan.');
    }

    public function edit(Mystery $mystery): View
    {
        return view('admin.mysteries.edit', ['mystery' => $mystery]);
    }

    public function update(Request $request, Mystery $mystery): RedirectResponse
    {
        $validated = $this->validateMystery($request);

        $mystery->update($validated);

        return back()->with('success', 'Misteri diperbarui.');
    }

    public function destroy(Mystery $mystery): RedirectResponse
    {
        $mystery->delete();

        return redirect()
            ->route('admin.mysteries.index')
            ->with('success', 'Misteri dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateMystery(Request $request): array
    {
        $validated = $request->validate([
            'statement' => ['required', 'string', 'max:2000'],
            'answer' => ['required', 'string', 'max:150'],
            'origin' => ['nullable', 'string', 'max:150'],
            'clues' => ['required', 'array', 'min:1', 'max:5'],
            'clues.*' => ['nullable', 'string', 'max:500'],
            'points' => ['required', 'integer', 'between:50,1000'],
            'clue_penalty' => ['required', 'integer', 'between:0,500'],
            'max_wrong_guesses' => ['required', 'integer', 'between:1,20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Buang clue yang kosong, lalu simpan sebagai array.
        $clues = collect($validated['clues'])
            ->map(fn ($c) => trim((string) $c))
            ->filter()
            ->values()
            ->all();

        $validated['clues'] = $clues;
        $validated['max_clues'] = count($clues);
        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
