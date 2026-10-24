<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionAdminController extends Controller
{
    public function index(Quest $quest): View
    {
        return view('admin.questions.index', [
            'quest' => $quest->load('questions'),
        ]);
    }

    public function store(Request $request, Quest $quest)
    {
        $validated = $request->validate([
            'question' => ['required', 'string'],
            'options' => ['required', 'array', 'size:4', 'min:1'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'between:0,3'],
            'points' => ['required', 'integer', 'between:10,1000'],
        ]);

        $quest->questions()->create([
            ...$validated,
            'sort_order' => $quest->questions()->count(),
        ]);

        return back()->with('success', 'Soal ditambahkan.');
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question' => ['required', 'string'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'between:0,3'],
            'points' => ['required', 'integer', 'between:10,1000'],
        ]);

        $question->update($validated);

        return back()->with('success', 'Soal diperbarui.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return back()->with('success', 'Soal dihapus.');
    }
}
