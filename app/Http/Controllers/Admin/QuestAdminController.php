<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestAdminController extends Controller
{
    public function index(): View
    {
        return view('admin.quests.index', [
            'quests' => Quest::withCount(['questions', 'attempts'])->orderByDesc('available_on')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.quests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:daily,weekly'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'available_on' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:available_on'],
            'time_limit' => ['required', 'integer', 'between:5,120'],
        ]);

        $quest = Quest::create([...$validated, 'is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.quests.questions', $quest)->with('success', 'Quest dibuat! Sekarang tambahkan soal.');
    }

    public function edit(Quest $quest): View
    {
        return view('admin.quests.edit', ['quest' => $quest->load('questions')]);
    }

    public function update(Request $request, Quest $quest)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:daily,weekly'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'available_on' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:available_on'],
            'time_limit' => ['required', 'integer', 'between:5,120'],
        ]);

        $quest->update([...$validated, 'is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Quest diperbarui.');
    }

    public function destroy(Quest $quest)
    {
        $quest->delete();

        return redirect()->route('admin.quests.index')->with('success', 'Quest dihapus.');
    }
}
