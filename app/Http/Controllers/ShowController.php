<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\View\View;

class ShowController extends Controller
{
    public function index(): View
    {
        return view('shows.index', [
            'upcoming' => Show::with('regency.province')->where('starts_at', '>', now())->orderBy('starts_at')->paginate(9),
            'past' => Show::with('regency.province')->where('starts_at', '<=', now())->orderByDesc('starts_at')->limit(6)->get(),
        ]);
    }

    public function show(Show $show): View
    {
        $show->load('regency.province.island');

        return view('shows.detail', [
            'show' => $show,
            'otherShows' => Show::where('id', '!=', $show->id)->where('starts_at', '>', now())->orderBy('starts_at')->limit(3)->get(),
        ]);
    }
}
