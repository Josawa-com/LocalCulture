<?php

namespace App\Http\Controllers;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Island;
use App\Models\Mystery;
use App\Models\Province;
use App\Models\Quest;
use App\Models\Regency;
use App\Models\Show;
use App\Models\Venue;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'islands' => Island::withCount(['provinces', 'regencies'])->orderBy('name')->get(),
            'provinceCount' => Province::count(),
            'regencyCount' => Regency::count(),
            'cultureCount' => Culture::count(),
            'categoryCount' => CultureCategory::count(),
            'questCount' => Quest::available()->count(),
            'mysteryCount' => Mystery::where('is_active', true)->count(),
            'venueCount' => Venue::count(),
            'upcomingCount' => Show::where('starts_at', '>', now())->count(),
            'nextShows' => Show::with('regency.province')->where('starts_at', '>', now())->orderBy('starts_at')->limit(3)->get(),
            'todayQuest' => Quest::available()->where('type', 'daily')->first(),
            'weeklyQuest' => Quest::available()->where('type', 'weekly')->first(),
            'featuredCultures' => Culture::with('category')->inRandomOrder()->limit(6)->get(),
        ]);
    }
}
