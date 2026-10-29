<?php

namespace Tests\Feature;

use App\Models\AttemptAnswer;
use App\Models\Island;
use App\Models\Province;
use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\Question;
use App\Models\Reputation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('profile'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_profile_with_quiz_history_and_correct_wrong_counts(): void
    {
        $user = User::factory()->create(['name' => 'Sari Budaya']);
        Reputation::factory()->for($user)->create([
            'total_score' => 480,
            'quests_completed' => 2,
        ]);

        $daily = Quest::factory()->create(['type' => 'daily', 'title' => 'Kuis Harian Jawa']);
        $weekly = Quest::factory()->create(['type' => 'weekly', 'title' => 'Kuis Mingguan Nusantara']);

        // 5 soal dijawab, 3 benar → 2 salah.
        $dailyAttempt = QuestAttempt::factory()->for($daily)->for($user)->create([
            'total_score' => 320,
            'correct_count' => 3,
            'finished_at' => now(),
        ]);
        $this->createAnswers($dailyAttempt, 5, 3);

        $weeklyAttempt = QuestAttempt::factory()->for($weekly)->for($user)->create([
            'total_score' => 160,
            'correct_count' => 1,
            'finished_at' => now(),
        ]);
        $this->createAnswers($weeklyAttempt, 4, 1);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Sari Budaya')
            ->assertSee('Riwayat Kuis')
            ->assertSee('Kuis Harian Jawa')
            ->assertSee('Kuis Mingguan Nusantara')
            ->assertSee('Kuis Bulanan')
            // Ringkasan harian: 3 benar, 2 salah dari 5 soal.
            ->assertSee('3 benar')
            ->assertSee('2 salah')
            ->assertSee('dari 5 soal')
            // Ringkasan mingguan: 1 benar, 3 salah dari 4 soal.
            ->assertSee('1 benar')
            ->assertSee('3 salah')
            ->assertSee('dari 4 soal')
            ->assertSee('480');
    }

    public function test_profile_shows_empty_state_when_user_has_no_history(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Belum ada riwayat kuis harian.')
            ->assertSee('Belum ada riwayat kuis mingguan.')
            ->assertSee('Belum ada riwayat kuis bulanan.');
    }

    public function test_profile_has_settings_and_logout_button(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('Pengaturan')
            ->assertSee('Ganti Password')
            ->assertSee('Keluar dari Akun')
            ->assertSee('action="'.route('logout').'"', false);
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create(['name' => 'Nama Lama']);
        $island = Island::factory()->create();
        $province = Province::factory()->for($island)->create();

        $this->actingAs($user)->post(route('profile.update'), [
            'name' => 'Nama Baru',
            'island_id' => $island->id,
            'province_id' => $province->id,
        ])->assertRedirect()->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame($island->id, $user->island_id);
        $this->assertSame($province->id, $user->province_id);
    }

    public function test_province_must_belong_to_selected_island(): void
    {
        $user = User::factory()->create(['name' => 'Nama Lama']);
        $island = Island::factory()->create();
        $otherIsland = Island::factory()->create();
        $province = Province::factory()->for($otherIsland)->create();

        $this->actingAs($user)->post(route('profile.update'), [
            'name' => 'Nama Baru',
            'island_id' => $island->id,
            'province_id' => $province->id,
        ])->assertSessionHasErrors('province_id');

        $this->assertSame('Nama Lama', $user->fresh()->name);
    }

    public function test_user_can_upload_and_remove_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'avatar' => UploadedFile::fake()->image('foto.jpg', 200, 200),
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);

        $storedPath = $user->avatar_path;

        $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'remove_avatar' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertNull($user->fresh()->avatar_path);
        Storage::disk('public')->assertMissing($storedPath);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password-lama')]);

        $this->actingAs($user)->post(route('profile.password'), [
            'current_password' => 'password-lama',
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertTrue(Hash::check('password-baru-123', $user->fresh()->password));
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password-lama')]);

        $this->actingAs($user)->post(route('profile.password'), [
            'current_password' => 'salah',
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password-lama', $user->fresh()->password));
    }

    public function test_navigation_profile_link_goes_straight_to_profile_page(): void
    {
        $user = User::factory()->create(['name' => 'Sari Budaya']);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('profile').'"', false)
            // Tombol keluar tidak lagi ada di navigasi — hanya di halaman profil.
            ->assertDontSee('>Keluar<', false);
    }

    /**
     * Buat sejumlah jawaban untuk sebuah attempt.
     */
    private function createAnswers(QuestAttempt $attempt, int $total, int $correct): void
    {
        for ($i = 0; $i < $total; $i++) {
            AttemptAnswer::factory()->create([
                'quest_attempt_id' => $attempt->id,
                'question_id' => Question::factory()->for($attempt->quest),
                'is_correct' => $i < $correct,
                'score' => $i < $correct ? 100 : 0,
                'answered_at' => now(),
            ]);
        }
    }
}
