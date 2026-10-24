<?php

namespace App\Http\Controllers;

use App\Models\MysteryAttempt;
use App\Models\QuestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('profile.show', [
            'user' => $user->load(['island', 'province', 'reputation']),
            'attempts' => QuestAttempt::with('quest')->where('user_id', $user->id)->where('status', 1)->latest('finished_at')->limit(10)->get(),
            'mysteries' => MysteryAttempt::with('mystery')->where('user_id', $user->id)->latest()->limit(10)->get(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'island_id' => ['nullable', 'exists:islands,id'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }
        unset($validated['avatar']);

        $request->user()->update($validated);

        return back()->with('success', 'Profil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password diperbarui!');
    }
}
