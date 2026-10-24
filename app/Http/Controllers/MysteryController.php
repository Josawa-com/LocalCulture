<?php

namespace App\Http\Controllers;

use App\Models\Mystery;
use App\Models\MysteryAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MysteryController extends Controller
{
    public function index(): View
    {
        $solvedIds = auth()->user()
            ? MysteryAttempt::where('user_id', auth()->id())->where('is_correct', true)->pluck('mystery_id')
            : collect();

        return view('mystery.index', [
            'mysteries' => Mystery::where('is_active', true)->get()->map(fn (Mystery $m) => [
                'model' => $m,
                'solved' => $solvedIds->contains($m->id),
            ]),
        ]);
    }

    public function play(Request $request, Mystery $mystery): View
    {
        $solved = MysteryAttempt::where('user_id', $request->user()?->id)->where('mystery_id', $mystery->id)->where('is_correct', true)->exists();
        abort_if($solved, 403, 'Mystery ini sudah kamu pecahkan.');

        return view('mystery.play', [
            'mystery' => $mystery,
            'clueIndex' => 0, // client reveals step by step via API
        ]);
    }

    /** Reveal clue bertahap - tiap clue mengurangi skor maksimal. */
    public function reveal(Request $request, Mystery $mystery)
    {
        $clueIndex = $request->integer('clue', 0);

        return response()->json([
            'clue' => $mystery->clues[$clueIndex] ?? null,
            'clue_index' => $clueIndex,
        ]);
    }

    public function guess(Request $request, Mystery $mystery)
    {
        $validated = $request->validate([
            'guess' => ['required', 'string', 'max:150'],
            'clues_used' => ['required', 'integer', 'between:1,10'],
        ]);

        $isCorrect = (string) str($validated['guess'])->lower()->squish() === (string) str($mystery->answer)->lower()->squish();
        $score = 0;
        if ($isCorrect) {
            // Semakin sedikit petunjuk, semakin tinggi skor
            $score = max(50, 500 - ($validated['clues_used'] - 1) * 100);
        }

        $attempt = MysteryAttempt::create([
            'user_id' => $request->user()->id,
            'mystery_id' => $mystery->id,
            'guess' => $validated['guess'],
            'is_correct' => $isCorrect,
            'clues_used' => $validated['clues_used'],
            'score' => $score,
        ]);

        if ($isCorrect) {
            $reputation = $request->user()->reputation()->firstOrCreate([]);
            $reputation->increment('total_score', $score);
            $reputation->increment('mysteries_solved');
        }

        return response()->json([
            'is_correct' => $isCorrect,
            'score' => $score,
            'answer' => $isCorrect ? $mystery->answer : null,
            'origin' => $isCorrect ? $mystery->origin : null,
        ]);
    }
}
