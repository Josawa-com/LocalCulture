<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuestAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.quests.index', [
            'quests' => Quest::withCount(['questions', 'attempts'])
                ->orderByDesc('created_at')
                ->paginate(15),
            'types' => Quest::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('admin.quests.create', [
            'types' => Quest::TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Quest::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'question_count' => ['required', 'integer', 'between:1,50'],
            'available_on' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:available_on'],
            'time_limit' => ['required', 'integer', 'between:5,120'],
        ]);

        $quest = Quest::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Langsung dibawa ke pengisian soal sesuai jumlah soal yang ditentukan.
        return redirect()
            ->route('admin.quests.questions', $quest)
            ->with('success', 'Kuis dibuat! Sekarang isi '.$quest->question_count.' soal.');
    }

    public function edit(Quest $quest): View
    {
        return view('admin.quests.edit', [
            'quest' => $quest->load('questions'),
            'types' => Quest::TYPES,
        ]);
    }

    public function update(Request $request, Quest $quest)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Quest::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'question_count' => ['required', 'integer', 'between:1,50'],
            'available_on' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:available_on'],
            'time_limit' => ['required', 'integer', 'between:5,120'],
        ]);

        $quest->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Kuis diperbarui.');
    }

    public function destroy(Quest $quest)
    {
        $quest->delete();

        return redirect()->route('admin.quests.index')->with('success', 'Kuis dihapus.');
    }
}
