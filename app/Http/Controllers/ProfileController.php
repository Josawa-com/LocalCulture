<?php

namespace App\Http\Controllers;

use App\Models\Island;
use App\Models\Province;
use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** Jenis kuis yang riwayatnya selalu ditampilkan di profil. */
    private const HISTORY_TYPES = ['daily', 'weekly', 'monthly'];

    public function show(Request $request): View
    {
        $user = $request->user()->load(['island', 'province', 'reputation']);

        $attempts = QuestAttempt::query()
            ->with('quest')
            ->withCount('answers')
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->latest('finished_at')
            ->get();

        // Riwayat per jenis kuis; tipe di luar daftar tetap tampil bila ada datanya.
        $history = collect(Quest::TYPES)
            ->map(fn (string $label, string $type) => $this->historySection($type, $label, $attempts))
            ->filter(fn (array $section) => in_array($section['type'], self::HISTORY_TYPES, true) || $section['attempts']->isNotEmpty())
            ->values();

        return view('profile.show', [
            'user' => $user,
            'history' => $history,
            'summary' => [
                'attempts' => $attempts->count(),
                'correct' => (int) $attempts->sum('correct_count'),
                'answers' => (int) $attempts->sum('answers_count'),
                'best_score' => (int) ($attempts->max('total_score') ?? 0),
            ],
            'islands' => Island::with('provinces')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'island_id' => ['nullable', 'exists:islands,id'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ]);

        $islandId = $validated['island_id'] ?? null;
        $provinceId = $validated['province_id'] ?? null;

        if ($islandId && $provinceId) {
            $provinceMatchesIsland = Province::whereKey($provinceId)
                ->where('island_id', $islandId)
                ->exists();

            if (! $provinceMatchesIsland) {
                throw ValidationException::withMessages([
                    'province_id' => 'Provinsi tidak sesuai dengan pulau yang dipilih.',
                ]);
            }
        }

        $attributes = [
            'name' => $validated['name'],
            'island_id' => $islandId,
            'province_id' => $provinceId,
        ];

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user);
            $attributes['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($user);
            $attributes['avatar_path'] = null;
        }

        $user->update($attributes);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Ringkasan satu jenis kuis: daftar attempt + total benar/salah.
     *
     * @param  Collection<int, QuestAttempt>  $attempts
     * @return array{type: string, label: string, attempts: Collection<int, QuestAttempt>, correct: int, total: int, wrong: int}
     */
    private function historySection(string $type, string $label, Collection $attempts): array
    {
        $items = $attempts->filter(fn (QuestAttempt $attempt) => $attempt->quest?->type === $type);
        $correct = (int) $items->sum('correct_count');
        $total = (int) $items->sum('answers_count');

        return [
            'type' => $type,
            'label' => $label,
            'attempts' => $items,
            'correct' => $correct,
            'total' => $total,
            'wrong' => max(0, $total - $correct),
        ];
    }

    private function deleteAvatar(User $user): void
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }
    }
}
