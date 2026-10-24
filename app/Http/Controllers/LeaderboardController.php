<?php

namespace App\Http\Controllers;

use App\Models\QuestAttempt;
use App\Models\Reputation;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        $top = Reputation::query()
            ->join('users', 'users.id', '=', 'reputations.user_id')
            ->orderByDesc('reputations.total_score')
            ->limit(20)
            ->get(['reputations.*', 'users.name', 'users.avatar_path']);

        // Grafik performa user login (skor per attempt terakhir)
        $myAttempts = auth()->user()
            ? QuestAttempt::with('quest')->where('user_id', auth()->id())->where('status', 1)->orderBy('finished_at')->limit(20)->get()
            : collect();

        return view('leaderboard', [
            'top' => $top,
            'myAttempts' => $myAttempts,
        ]);
    }
}
