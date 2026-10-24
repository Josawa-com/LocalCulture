<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterestRequest;
use App\Models\Quest;
use App\Models\Show;
use App\Models\User;
use App\Models\Venue;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'quests' => Quest::count(),
                'shows' => Show::count(),
                'venues' => Venue::count(),
                'pendingRequests' => InterestRequest::where('status', 'pending')->count(),
            ],
            'pendingRequests' => InterestRequest::with(['user', 'venue'])->where('status', 'pending')->latest()->limit(5)->get(),
            'upcomingShows' => Show::where('starts_at', '>', now())->orderBy('starts_at')->limit(5)->get(),
        ]);
    }
}
