<?php

namespace Tests\Feature;

use App\Models\Island;
use App\Models\Mystery;
use App\Models\MysteryAttempt;
use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeAndIntroTest extends TestCase
{
    use RefreshDatabase;

    /* ============================================================
       BERANDA
       ============================================================ */

    public function test_home_page_shows_introduction_and_navigation(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Jelajahi')
            ->assertSee('Nusantara')
            ->assertSee('Empat Cara Menjelajah')
            ->assertSee('Mulai Jelajah')
            ->assertSee('Materi Budaya')
            ->assertSee('Kuis Budaya')
            ->assertSee('Mystery Budaya')
            ->assertSee('Pertunjukan & Sanggar');
    }

    public function test_home_page_lists_islands(): void
    {
        Island::factory()->create(['name' => 'Sumatera', 'slug' => 'sumatera']);
        Island::factory()->create(['name' => 'Papua', 'slug' => 'papua']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Mulai dari Pulau Mana?')
            ->assertSee('Sumatera')
            ->assertSee('Papua');
    }

    public function test_home_shows_register_cta_for_guest(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Daftar Gratis');
    }

    public function test_home_shows_profile_cta_for_logged_in_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('home'))->assertOk()->assertSee('Buka Profil');
    }

    /* ============================================================
       JEDA KETERANGAN KUIS
       ============================================================ */

    private function startQuest(): array
    {
        $user = User::factory()->create();
        $user->reputation()->create();

        $quest = Quest::factory()->create([
            'type' => 'daily',
            'available_on' => today(),
            'ends_at' => today()->endOfDay(),
            'time_limit' => 7,
            'question_count' => 2,
        ]);

        Question::factory()->for($quest)->create([
            'question' => 'Soal pertama?',
            'options' => ['A', 'B', 'C', 'D'],
            'correct_index' => 0,
            'sort_order' => 0,
        ]);

        return [$user, $quest];
    }

    public function test_first_question_shows_intro_screen_with_ten_second_countdown(): void
    {
        [$user, $quest] = $this->startQuest();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();

        $html = $this->actingAs($user)->get(route('quests.play', $attempt))->assertOk()->getContent();

        $this->assertStringContainsString('id="intro-overlay"', $html);
        $this->assertStringContainsString('INTRO_SECONDS = 10', $html);
        $this->assertStringContainsString('id="intro-countdown"', $html);
    }

    public function test_intro_screen_mentions_time_limit_and_question_count(): void
    {
        [$user, $quest] = $this->startQuest();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)
            ->get(route('quests.play', $attempt))
            ->assertOk()
            ->assertSee('Jumlah Soal')
            ->assertSee('Waktu per Soal')
            ->assertSee('Total Waktu')
            ->assertSee('Setiap soal punya waktu 7 detik');
    }

    public function test_intro_screen_has_skip_button_and_three_second_ready_screen(): void
    {
        [$user, $quest] = $this->startQuest();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();

        $html = $this->actingAs($user)->get(route('quests.play', $attempt))->assertOk()->getContent();

        $this->assertStringContainsString('id="intro-skip"', $html);
        $this->assertStringContainsString('Lewati', $html);
        $this->assertStringContainsString('READY_SECONDS = 3', $html);
        $this->assertStringContainsString('id="ready-overlay"', $html);
    }

    public function test_quiz_area_is_hidden_until_countdown_finishes(): void
    {
        [$user, $quest] = $this->startQuest();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();

        $html = $this->actingAs($user)->get(route('quests.play', $attempt))->assertOk()->getContent();

        // Saat soal pertama, area kuis diberi kelas hidden.
        $this->assertMatchesRegularExpression('/id="quiz-area"\s+class="hidden"/', $html);
    }

    public function test_second_question_has_no_intro(): void
    {
        [$user, $quest] = $this->startQuest();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();

        // Jawab soal pertama.
        $this->get(route('quests.play', $attempt));
        $answer = $attempt->answers()->firstOrFail();
        $this->post(route('quests.answer', $attempt), [
            'answer_row' => $answer->id,
            'selected_index' => 0,
        ]);

        // Tambah soal kedua.
        Question::factory()->for($quest)->create(['sort_order' => 1]);

        $html = $this->actingAs($user)->get(route('quests.play', $attempt))->assertOk()->getContent();

        $this->assertStringNotContainsString('id="intro-overlay"', $html);
        $this->assertStringNotContainsString('id="quiz-area" class="hidden"', $html);
    }

    /* ============================================================
       PAPAN PERINGKAT MYSTERY
       ============================================================ */

    public function test_leaderboard_page_shows_mystery_board(): void
    {
        $this->get(route('leaderboard'))
            ->assertOk()
            ->assertSee('Peringkat Mystery Budaya')
            ->assertSee('Belum ada yang memecahkan misteri');
    }

    public function test_mystery_board_lists_solvers_by_best_score(): void
    {
        $low = User::factory()->create(['name' => 'Pemecah Pemula']);
        $high = User::factory()->create(['name' => 'Pemecah Ulung']);

        $m1 = Mystery::factory()->create();
        $m2 = Mystery::factory()->create();

        MysteryAttempt::create([
            'user_id' => $low->id, 'mystery_id' => $m1->id,
            'status' => MysteryAttempt::STATUS_SOLVED, 'score' => 150, 'finished_at' => now(),
        ]);
        MysteryAttempt::create([
            'user_id' => $high->id, 'mystery_id' => $m1->id,
            'status' => MysteryAttempt::STATUS_SOLVED, 'score' => 300, 'finished_at' => now(),
        ]);
        MysteryAttempt::create([
            'user_id' => $high->id, 'mystery_id' => $m2->id,
            'status' => MysteryAttempt::STATUS_SOLVED, 'score' => 250, 'finished_at' => now(),
        ]);

        $html = $this->get(route('leaderboard'))->assertOk()->getContent();

        $this->assertStringContainsString('Pemecah Ulung', $html);
        $this->assertStringContainsString('Pemecah Pemula', $html);

        // Skor terbaik (300) dipakai, bukan total (550).
        $table = substr($html, strpos($html, 'Peringkat Mystery Budaya'));
        $this->assertStringContainsString('300', $table);
        $this->assertLessThan(strpos($table, 'Pemecah Pemula'), strpos($table, 'Pemecah Ulung'));
    }

    public function test_failed_attempts_are_excluded_from_mystery_board(): void
    {
        $user = User::factory()->create(['name' => 'Pemain Gagal']);
        $mystery = Mystery::factory()->create();

        MysteryAttempt::create([
            'user_id' => $user->id, 'mystery_id' => $mystery->id,
            'status' => MysteryAttempt::STATUS_FAILED, 'score' => 0, 'finished_at' => now(),
        ]);

        $this->get(route('leaderboard'))
            ->assertOk()
            ->assertSee('Belum ada yang memecahkan misteri');
    }

    /* ============================================================
       HALAMAN LOGIN
       ============================================================ */

    public function test_login_page_no_longer_mentions_admin_panel(): void
    {
        $html = $this->get(route('login'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Admin? Masuk lalu buka', $html);
        $this->assertStringNotContainsString('/rezka', $html);
    }

    public function test_login_page_still_has_the_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk ke CultureRaya')
            ->assertSee('Email')
            ->assertSee('Password');
    }
}
