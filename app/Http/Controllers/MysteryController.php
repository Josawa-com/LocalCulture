<?php

namespace App\Http\Controllers;

use App\Models\Mystery;
use App\Models\MysteryAttempt;
use App\Models\Regency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MysteryController extends Controller
{
    /** Halaman daftar misteri + papan peringkat. */
    public function index(Request $request): View
    {
        $user = $request->user();
        $mysteries = Mystery::where('is_active', true)->orderBy('id')->get();

        $attempts = $user
            ? MysteryAttempt::where('user_id', $user->id)->get()->keyBy('mystery_id')
            : collect();

        return view('mystery.index', [
            'mysteries' => $mysteries->map(fn (Mystery $m) => [
                'model' => $m,
                'attempt' => $attempts->get($m->id),
                'clue_count' => count($m->availableClues()),
            ]),
            'leaderboard' => $this->leaderboard(),
            'stats' => [
                'solved' => $attempts->where('status', MysteryAttempt::STATUS_SOLVED)->count(),
                'failed' => $attempts->where('status', MysteryAttempt::STATUS_FAILED)->count(),
                'total' => $mysteries->count(),
            ],
        ]);
    }

    /** Halaman bermain: pernyataan + clue bertahap + kolom tebakan. */
    public function play(Request $request, Mystery $mystery): View|RedirectResponse
    {
        abort_unless($mystery->is_active, 404);

        $attempt = $this->attemptFor($request, $mystery);

        // Sudah selesai -> tidak bisa diulang.
        if ($attempt && ! $attempt->isPlaying()) {
            return redirect()
                ->route('mystery.index')
                ->with('info', $attempt->isSolved()
                    ? 'Misteri ini sudah kamu pecahkan.'
                    : 'Kamu sudah gagal 7 kali pada misteri ini dan tidak bisa mengulanginya.');
        }

        return view('mystery.play', [
            'mystery' => $mystery,
            'attempt' => $attempt,
            'clues' => $mystery->availableClues(),
            'regencies' => $this->regencyNames(),
        ]);
    }

    /**
     * Buka satu clue. Setiap clue menambah clues_revealed, yang
     * mengurangi poin akhir sebesar clue_penalty.
     */
    public function reveal(Request $request, Mystery $mystery): JsonResponse
    {
        abort_unless($mystery->is_active, 404);

        $attempt = $this->attemptFor($request, $mystery);

        if (! $attempt) {
            return response()->json(['message' => 'Sesi tidak ditemukan.'], 404);
        }

        if (! $attempt->isPlaying()) {
            return response()->json([
                'message' => 'Permainan sudah selesai.',
                'status' => $attempt->status,
            ], 409);
        }

        $clues = $mystery->availableClues();
        $next = $attempt->clues_revealed; // index clue berikutnya (0-based)

        if ($next >= count($clues)) {
            return response()->json([
                'message' => 'Semua petunjuk sudah terbuka.',
                'clues_revealed' => $attempt->clues_revealed,
                'score' => $mystery->scoreFor($attempt->clues_revealed),
            ]);
        }

        $attempt->increment('clues_revealed');
        $attempt->refresh();

        return response()->json([
            'index' => $next,
            'clue' => $clues[$next],
            'clues_revealed' => $attempt->clues_revealed,
            'total_clues' => count($clues),
            'score' => $mystery->scoreFor($attempt->clues_revealed),
        ]);
    }

    /** Kirim tebakan. Salah 7x -> gagal permanen. */
    public function guess(Request $request, Mystery $mystery): JsonResponse
    {
        abort_unless($mystery->is_active, 404);

        $validated = $request->validate([
            'guess' => ['required', 'string', 'max:150'],
        ]);

        $attempt = $this->attemptFor($request, $mystery);

        if (! $attempt) {
            return response()->json(['message' => 'Sesi tidak ditemukan.'], 404);
        }

        if (! $attempt->isPlaying()) {
            return response()->json([
                'message' => 'Permainan sudah selesai.',
                'status' => $attempt->status,
            ], 409);
        }

        $isCorrect = $this->matches($validated['guess'], $mystery->answer);

        if ($isCorrect) {
            $score = $mystery->scoreFor($attempt->clues_revealed);

            $attempt->update([
                'guess' => $validated['guess'],
                'is_correct' => true,
                'status' => MysteryAttempt::STATUS_SOLVED,
                'clues_used' => max(1, $attempt->clues_revealed),
                'score' => $score,
                'finished_at' => now(),
            ]);

            $reputation = $request->user()->reputation()->firstOrCreate([]);
            $reputation->increment('total_score', $score);
            $reputation->increment('mysteries_solved');

            return response()->json([
                'is_correct' => true,
                'status' => MysteryAttempt::STATUS_SOLVED,
                'score' => $score,
                'answer' => $mystery->answer,
                'origin' => $mystery->origin,
                'wrong_guesses' => $attempt->wrong_guesses,
                'attempts_left' => 0,
            ]);
        }

        // Salah: tambah penghitung.
        $attempt->increment('wrong_guesses');
        $attempt->refresh();

        $maxWrong = $mystery->max_wrong_guesses;
        $failed = $attempt->wrong_guesses >= $maxWrong;

        $attempt->update([
            'guess' => $validated['guess'],
            'is_correct' => false,
            'status' => $failed ? MysteryAttempt::STATUS_FAILED : MysteryAttempt::STATUS_PLAYING,
            'score' => 0,
            'finished_at' => $failed ? now() : null,
        ]);

        return response()->json([
            'is_correct' => false,
            'status' => $failed ? MysteryAttempt::STATUS_FAILED : MysteryAttempt::STATUS_PLAYING,
            'failed' => $failed,
            'wrong_guesses' => $attempt->wrong_guesses,
            'attempts_left' => max(0, $maxWrong - $attempt->wrong_guesses),
            'answer' => $failed ? $mystery->answer : null,
            'origin' => $failed ? $mystery->origin : null,
            'score' => 0,
        ]);
    }

    /** Ambil / buat sesi bermain user untuk misteri ini. */
    private function attemptFor(Request $request, Mystery $mystery): ?MysteryAttempt
    {
        $user = $request->user();

        if (! $user) {
            return null;
        }

        return MysteryAttempt::firstOrCreate(
            ['user_id' => $user->id, 'mystery_id' => $mystery->id],
            [
                'status' => MysteryAttempt::STATUS_PLAYING,
                'clues_revealed' => 0,
                'wrong_guesses' => 0,
                'clues_used' => 0,
                'score' => 0,
            ]
        );
    }

    /** Bandingkan tebakan dengan jawaban (abaikan besar-kecil & spasi). */
    private function matches(string $guess, string $answer): bool
    {
        return (string) str($guess)->lower()->squish() === (string) str($answer)->lower()->squish();
    }

    /**
     * Skor tertinggi per pemain (hanya yang berhasil).
     *
     * @return Collection<int, array{rank: int, name: string, score: int, solved: int}>
     */
    private function leaderboard()
    {
        $rows = MysteryAttempt::query()
            ->join('users', 'users.id', '=', 'mystery_attempts.user_id')
            ->where('mystery_attempts.status', MysteryAttempt::STATUS_SOLVED)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('best_score')
            ->orderBy('users.name')
            ->limit(10)
            ->selectRaw('users.id, users.name, MAX(mystery_attempts.score) as best_score, COUNT(*) as solved')
            ->get();

        $rank = 0;
        $prev = null;

        return $rows->map(function ($row) use (&$rank, &$prev) {
            $score = (int) $row->best_score;
            $rank = ($prev !== null && $score === $prev) ? $rank : $rank + 1;
            $prev = $score;

            return [
                'rank' => $rank,
                'name' => $row->name,
                'score' => $score,
                'solved' => (int) $row->solved,
            ];
        });
    }

    /** Nama kabupaten/kota untuk daftar saran tebakan. */
    private function regencyNames(): array
    {
        return Regency::orderBy('name')->pluck('name')
            ->map(fn (string $name) => preg_replace('/^(Kab\.|Kota)\s+/', '', $name))
            ->unique()
            ->values()
            ->all();
    }
}
