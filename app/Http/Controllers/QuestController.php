<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\QuestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestController extends Controller
{
    public function index(): View
    {
        return view('quests.index', [
            'dailyQuests' => Quest::available()->where('type', 'daily')->orderBy('available_on', 'desc')->get(),
            'weeklyQuests' => Quest::available()->where('type', 'weekly')->orderBy('available_on', 'desc')->get(),
        ]);
    }

    /** Mulai attempt: satu-satunya jalan masuk; dibuat via transaction agar anti-duplikat. */
    public function start(Request $request, Quest $quest)
    {
        $attempt = DB::transaction(function () use ($quest, $request) {
            $existing = QuestAttempt::where('quest_id', $quest->id)->where('user_id', $request->user()->id)->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }

            return QuestAttempt::create([
                'quest_id' => $quest->id,
                'user_id' => $request->user()->id,
                'started_at' => now(),
                'status' => 0,
            ]);
        }, 3);

        if ($attempt->status === 1) {
            return redirect()->route('quests.result', $attempt)->with('error', 'Kuis ini sudah pernah kamu kerjakan dan tidak bisa diulang.');
        }

        return redirect()->route('quests.play', $attempt);
    }

    /** Halaman main: server mencatat shown_at saat soal dikirim. */
    public function play(Request $request, QuestAttempt $attempt): View
    {
        $this->authorizeAttempt($request, $attempt);

        if ($attempt->status === 1) {
            return redirect()->route('quests.result', $attempt);
        }

        $question = $attempt->quest->questions()
            ->whereNotIn('id', $attempt->answers()->pluck('question_id'))
            ->orderBy('sort_order')
            ->first();

        if (! $question) {
            return $this->finish($attempt);
        }

        // Record when this question was served (server-side timer source of truth)
        $answer = $attempt->answers()->create([
            'question_id' => $question->id,
            'shown_at' => now(),
        ]);

        return view('quests.play', [
            'attempt' => $attempt,
            'question' => $question,
            'answerRowId' => $answer->id,
            'timeLimit' => $attempt->quest->time_limit,
            'questionNumber' => $attempt->answers()->count(),
            'totalQuestions' => $attempt->quest->questions()->count(),
        ]);
    }

    public function submitAnswer(Request $request, QuestAttempt $attempt)
    {
        $this->authorizeAttempt($request, $attempt);

        $validated = $request->validate([
            'answer_row' => ['required', 'integer'],
            'selected_index' => ['required', 'integer', 'between:0,3'],
        ]);

        $answer = $attempt->answers()->where('id', $validated['answer_row'])->whereNull('answered_at')->firstOrFail();
        $question = $answer->question;

        // SERVER-SIDE timing: hitung dari shown_at, bukan dari klaim client
        $timeMs = (int) max(0, (now()->getTimestampMs() - $answer->shown_at->getTimestampMs()));

        if ($timeMs > $attempt->quest->time_limit * 1000 + 1000) { // 1s grace for network
            return $this->recordTimeout($attempt, $answer, $question);
        }

        $isCorrect = $validated['selected_index'] === $question->correct_index;
        // Skor: benar + bonus kecepatan (maks 50% dari poin)
        $speedBonus = $isCorrect ? (int) floor($question->points * 0.5 * max(0, 1 - $timeMs / ($attempt->quest->time_limit * 1000))) : 0;
        $score = $isCorrect ? $question->points + $speedBonus : 0;

        $answer->update([
            'selected_index' => $validated['selected_index'],
            'is_correct' => $isCorrect,
            'time_ms' => $timeMs,
            'score' => $score,
            'answered_at' => now(),
        ]);

        $attempt->increment('total_score', $score);
        if ($isCorrect) {
            $attempt->increment('correct_count');
        }
        $attempt->increment('total_time_ms', $timeMs);

        return redirect()->route('quests.play', $attempt);
    }

    public function timeout(Request $request, QuestAttempt $attempt)
    {
        $this->authorizeAttempt($request, $attempt);

        $answer = $attempt->answers()->where('id', $request->integer('answer_row'))->whereNull('answered_at')->firstOrFail();

        return $this->recordTimeout($attempt, $answer, $answer->question);
    }

    private function recordTimeout(QuestAttempt $attempt, $answer, $question)
    {
        $answer->update([
            'selected_index' => null,
            'is_correct' => false,
            'time_ms' => $attempt->quest->time_limit * 1000,
            'score' => 0,
            'answered_at' => now(),
        ]);
        $attempt->increment('total_time_ms', $attempt->quest->time_limit * 1000);

        return redirect()->route('quests.play', $attempt);
    }

    public function finish(QuestAttempt $attempt)
    {
        if ($attempt->status === 0) {
            $attempt->update(['status' => 1, 'finished_at' => now()]);

            // Update reputasi user
            $reputation = $attempt->user->reputation()->firstOrCreate([]);
            $reputation->increment('total_score', $attempt->total_score);
            $reputation->increment('quests_completed');
        }

        return redirect()->route('quests.result', $attempt);
    }

    public function result(Request $request, QuestAttempt $attempt): View
    {
        $this->authorizeAttempt($request, $attempt);

        return view('quests.result', [
            'attempt' => $attempt->load(['answers.question', 'quest']),
        ]);
    }

    private function authorizeAttempt(Request $request, QuestAttempt $attempt): void
    {
        abort_unless($attempt->user_id === $request->user()?->id, 403);
    }
}
