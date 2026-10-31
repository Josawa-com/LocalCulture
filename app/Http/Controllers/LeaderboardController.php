<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    /**
     * Tiga periode leaderboard yang bisa dipilih user.
     * Tiap periode hanya menghitung kuis dengan jenis yang sama.
     *
     * @var array<string, array{label: string, type: string, description: string}>
     */
    public const PERIODS = [
        'daily' => [
            'label' => 'Harian',
            'type' => 'daily',
            'description' => 'Total skor kuis harian yang diselesaikan hari ini.',
        ],
        'weekly' => [
            'label' => 'Mingguan',
            'type' => 'weekly',
            'description' => 'Total skor kuis mingguan yang diselesaikan minggu ini.',
        ],
        'monthly' => [
            'label' => 'Bulanan',
            'type' => 'monthly',
            'description' => 'Total skor kuis bulanan yang diselesaikan bulan ini.',
        ],
    ];

    public function index(Request $request): View
    {
        $period = (string) $request->query('periode', 'daily');

        if (! array_key_exists($period, self::PERIODS)) {
            $period = 'daily';
        }

        $start = $this->periodStart($period);
        $leaderboard = $this->rankedRows($start, self::PERIODS[$period]['type']);
        $currentUserId = $request->user()?->id;

        return view('leaderboard', [
            'period' => $period,
            'periods' => self::PERIODS,
            'periodRange' => $this->periodRange($period, $start),
            'leaderboard' => $leaderboard,
            'podium' => array_slice($leaderboard, 0, 3),
            'me' => $currentUserId
                ? collect($leaderboard)->firstWhere('user_id', $currentUserId)
                : null,
            'stats' => [
                'participants' => count($leaderboard),
                'quests' => array_sum(array_column($leaderboard, 'quests')),
                'score' => array_sum(array_column($leaderboard, 'score')),
            ],
            // Papan peringkat Mystery Budaya (minigame).
            'mysteryLeaders' => $this->mysteryLeaderboard(),
        ]);
    }

    /**
     * Peringkat Mystery Budaya: skor tertinggi per pemain dari misteri
     * yang berhasil dipecahkan.
     *
     * @return Collection<int, array{rank: int, user_id: int, name: string, score: int, solved: int}>
     */
    private function mysteryLeaderboard()
    {
        $rows = DB::table('mystery_attempts')
            ->join('users', 'users.id', '=', 'mystery_attempts.user_id')
            ->where('mystery_attempts.status', 1)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('best_score')
            ->orderBy('users.name')
            ->limit(10)
            ->selectRaw('users.id as user_id, users.name, MAX(mystery_attempts.score) as best_score, COUNT(*) as solved')
            ->get();

        $rank = 0;
        $prev = null;

        return $rows->map(function ($row) use (&$rank, &$prev) {
            $score = (int) $row->best_score;
            $rank = ($prev !== null && $score === $prev) ? $rank : $rank + 1;
            $prev = $score;

            return [
                'rank' => $rank,
                'user_id' => (int) $row->user_id,
                'name' => $row->name,
                'score' => $score,
                'solved' => (int) $row->solved,
            ];
        });
    }

    /**
     * Peringkat dihitung dari attempt kuis yang sudah selesai pada periode berjalan,
     * hanya untuk kuis dengan jenis yang sesuai tab (harian/mingguan/bulanan).
     * Skor sama mendapat peringkat sama (competition ranking).
     *
     * @return list<array{rank: int, user_id: int, name: string, avatar_path: ?string, island: ?string, province: ?string, score: int, quests: int, correct: int}>
     */
    private function rankedRows(Carbon $start, string $questType): array
    {
        $rows = DB::table('quest_attempts')
            ->join('users', 'users.id', '=', 'quest_attempts.user_id')
            ->join('quests', 'quests.id', '=', 'quest_attempts.quest_id')
            ->leftJoin('islands', 'islands.id', '=', 'users.island_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'users.province_id')
            ->where('quest_attempts.status', 1)
            ->where('quests.type', $questType)
            ->whereNotNull('quest_attempts.finished_at')
            ->where('quest_attempts.finished_at', '>=', $start)
            ->groupBy('users.id', 'users.name', 'users.avatar_path', 'islands.name', 'provinces.name')
            ->orderByDesc('score')
            ->orderByDesc('correct')
            ->orderBy('users.name')
            ->selectRaw('
                users.id as user_id,
                users.name,
                users.avatar_path,
                islands.name as island_name,
                provinces.name as province_name,
                SUM(quest_attempts.total_score) as score,
                COUNT(*) as quests_completed,
                SUM(quest_attempts.correct_count) as correct_count
            ')
            ->get();

        $leaderboard = [];
        $previousScore = null;
        $previousRank = 0;

        foreach ($rows as $index => $row) {
            $score = (int) $row->score;
            $rank = $previousScore !== null && $score === $previousScore
                ? $previousRank
                : $index + 1;

            $leaderboard[] = [
                'rank' => $rank,
                'user_id' => (int) $row->user_id,
                'name' => $row->name,
                'avatar_path' => $row->avatar_path,
                'island' => $row->island_name,
                'province' => $row->province_name,
                'score' => $score,
                'quests' => (int) $row->quests_completed,
                'correct' => (int) $row->correct_count,
            ];

            $previousScore = $score;
            $previousRank = $rank;
        }

        return $leaderboard;
    }

    private function periodStart(string $period): Carbon
    {
        return match ($period) {
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->startOfDay(),
        };
    }

    private function periodRange(string $period, Carbon $start): string
    {
        return match ($period) {
            'weekly' => $start->locale('id')->translatedFormat('d M').' – '.now()->locale('id')->translatedFormat('d M Y'),
            'monthly' => $start->locale('id')->translatedFormat('F Y'),
            default => $start->locale('id')->translatedFormat('d F Y'),
        };
    }
}
