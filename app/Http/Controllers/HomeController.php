<?php

namespace App\Http\Controllers;

use App\Models\Culture;
use App\Models\Island;
use App\Models\Province;
use App\Models\Quest;
use App\Models\Show;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'islands' => Island::withCount(['provinces', 'regencies'])->orderBy('name')->get(),
            'provinceCount' => Province::count(),
            'cultureCount' => Culture::count(),
            'upcomingCount' => Show::where('starts_at', '>', now())->count(),
            'nextShows' => Show::with('regency.province')->where('starts_at', '>', now())->orderBy('starts_at')->limit(3)->get(),
            'todayQuest' => Quest::available()->where('type', 'daily')->first(),
            'weeklyQuest' => Quest::available()->where('type', 'weekly')->first(),
        ]);
    }
}
