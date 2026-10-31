<?php

namespace Tests\Feature;

use App\Models\Mystery;
use App\Models\MysteryAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MysteryGameTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mystery $mystery;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user->reputation()->create();

        $this->mystery = Mystery::create([
            'answer' => 'Rumah Gadang',
            'origin' => 'Sumatera Barat',
            'statement' => 'Bangunan beratap runcing seperti tanduk kerbau.',
            'clues' => ['Atapnya disebut gonjong.', 'Bilangan ruangannya ganjil.', 'Rumah adat suku Minangkabau.'],
            'max_clues' => 3,
            'points' => 300,
            'clue_penalty' => 50,
            'max_wrong_guesses' => 7,
            'is_active' => true,
        ]);
    }

    public function test_index_lists_active_mysteries(): void
    {
        $this->actingAs($this->user)
            ->get(route('mystery.index'))
            ->assertOk()
            ->assertSee('Mystery Budaya')
            ->assertSee('Bangunan beratap runcing')
            ->assertSee('Pemain Terbaik');
    }

    public function test_guest_cannot_open_play_page(): void
    {
        $this->get(route('mystery.play', $this->mystery))->assertRedirect(route('login'));
    }

    public function test_play_page_shows_statement_and_rules(): void
    {
        $this->actingAs($this->user)
            ->get(route('mystery.play', $this->mystery))
            ->assertOk()
            ->assertSee('Bangunan beratap runcing seperti tanduk kerbau.')
            ->assertSee('Pernyataan')
            ->assertSee('300');
    }

    public function test_revealing_clues_reduces_score_by_50_each(): void
    {
        $expected = [250, 200, 150];

        foreach ($expected as $i => $score) {
            $this->actingAs($this->user)
                ->postJson(route('mystery.reveal', $this->mystery))
                ->assertOk()
                ->assertJson([
                    'clues_revealed' => $i + 1,
                    'score' => $score,
                ]);
        }
    }

    public function test_clues_cannot_be_revealed_beyond_available(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($this->user)->postJson(route('mystery.reveal', $this->mystery));
        }

        // Keempat: tidak ada clue baru.
        $this->actingAs($this->user)
            ->postJson(route('mystery.reveal', $this->mystery))
            ->assertOk()
            ->assertJsonMissing(['index' => 3]);
    }

    public function test_correct_guess_without_clues_gives_full_points(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('mystery.guess', $this->mystery), ['guess' => 'rumah gadang'])
            ->assertOk()
            ->assertJson([
                'is_correct' => true,
                'score' => 300,
                'answer' => 'Rumah Gadang',
            ]);

        $this->assertSame(1, $this->user->reputation()->value('mysteries_solved'));
        $this->assertSame(300, $this->user->reputation()->value('total_score'));
    }

    public function test_correct_guess_after_three_clues_gives_150(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($this->user)->postJson(route('mystery.reveal', $this->mystery));
        }

        $this->actingAs($this->user)
            ->postJson(route('mystery.guess', $this->mystery), ['guess' => 'RUMAH GADANG'])
            ->assertJson(['is_correct' => true, 'score' => 150]);
    }

    public function test_guess_is_case_and_space_insensitive(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('mystery.guess', $this->mystery), ['guess' => '  Rumah   Gadang  '])
            ->assertJson(['is_correct' => true]);
    }

    public function test_wrong_guess_counts_down_attempts(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('mystery.guess', $this->mystery), ['guess' => 'salah'])
            ->assertOk()
            ->assertJson([
                'is_correct' => false,
                'attempts_left' => 6,
                'wrong_guesses' => 1,
                'status' => MysteryAttempt::STATUS_PLAYING,
            ]);
    }

    public function test_seven_wrong_guesses_fail_permanently(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            $response = $this->actingAs($this->user)
                ->postJson(route('mystery.guess', $this->mystery), ['guess' => 'salah-'.$i]);

            if ($i < 7) {
                $response->assertJson(['failed' => false, 'attempts_left' => 7 - $i]);
            } else {
                $response->assertJson([
                    'failed' => true,
                    'attempts_left' => 0,
                    'answer' => 'Rumah Gadang',
                ]);
            }
        }

        $attempt = MysteryAttempt::where('user_id', $this->user->id)->firstOrFail();
        $this->assertSame(MysteryAttempt::STATUS_FAILED, $attempt->status);
        $this->assertNotNull($attempt->finished_at);
    }

    public function test_failed_mystery_cannot_be_replayed(): void
    {
        MysteryAttempt::create([
            'user_id' => $this->user->id,
            'mystery_id' => $this->mystery->id,
            'status' => MysteryAttempt::STATUS_FAILED,
            'wrong_guesses' => 7,
            'clues_revealed' => 0,
            'clues_used' => 0,
            'score' => 0,
            'finished_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->get(route('mystery.play', $this->mystery))
            ->assertRedirect(route('mystery.index'))
            ->assertSessionHas('info');
    }

    public function test_solved_mystery_cannot_be_replayed(): void
    {
        MysteryAttempt::create([
            'user_id' => $this->user->id,
            'mystery_id' => $this->mystery->id,
            'status' => MysteryAttempt::STATUS_SOLVED,
            'wrong_guesses' => 1,
            'clues_revealed' => 2,
            'clues_used' => 2,
            'score' => 200,
            'finished_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->get(route('mystery.play', $this->mystery))
            ->assertRedirect(route('mystery.index'));
    }

    public function test_guess_after_failure_is_rejected(): void
    {
        MysteryAttempt::create([
            'user_id' => $this->user->id,
            'mystery_id' => $this->mystery->id,
            'status' => MysteryAttempt::STATUS_FAILED,
            'wrong_guesses' => 7,
            'score' => 0,
            'finished_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->postJson(route('mystery.guess', $this->mystery), ['guess' => 'Rumah Gadang'])
            ->assertStatus(409);
    }

    public function test_leaderboard_shows_best_score_per_player(): void
    {
        $other = User::factory()->create(['name' => 'Pemain Lain']);

        // User utama: 2 kemenangan (200 & 300) -> best 300
        foreach ([200, 300] as $i => $score) {
            MysteryAttempt::create([
                'user_id' => $this->user->id,
                'mystery_id' => Mystery::factory()->create()->id,
                'status' => MysteryAttempt::STATUS_SOLVED,
                'score' => $score,
                'finished_at' => now(),
            ]);
        }

        MysteryAttempt::create([
            'user_id' => $other->id,
            'mystery_id' => Mystery::factory()->create()->id,
            'status' => MysteryAttempt::STATUS_SOLVED,
            'score' => 100,
            'finished_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->get(route('mystery.index'))
            ->assertOk()
            ->assertSee('Pemain Lain')
            ->assertSeeInOrder([$this->user->name, 'Pemain Lain']);
    }

    public function test_failed_attempts_do_not_appear_on_leaderboard(): void
    {
        MysteryAttempt::create([
            'user_id' => $this->user->id,
            'mystery_id' => $this->mystery->id,
            'status' => MysteryAttempt::STATUS_FAILED,
            'score' => 0,
            'finished_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('mystery.index'))->assertOk();

        // Nama hanya boleh muncul di bagian "progres", bukan tabel peringkat.
        $this->assertStringContainsString('Belum ada yang memecahkan misteri', $response->getContent());
    }

    public function test_admin_can_create_update_and_delete_mystery(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.mysteries.store'), [
            'statement' => 'Tarian tanpa musik dari Bali.',
            'answer' => 'Tari Kecak',
            'origin' => 'Bali',
            'clues' => ['Bunyi cak-cak-cak.', 'Puluhan pria melingkar.', 'Kisah Ramayana.'],
            'points' => 300,
            'clue_penalty' => 50,
            'max_wrong_guesses' => 7,
            'is_active' => 1,
        ])->assertRedirect(route('admin.mysteries.index'));

        $mystery = Mystery::where('answer', 'Tari Kecak')->firstOrFail();
        $this->assertCount(3, $mystery->availableClues());
        $this->assertSame(3, $mystery->max_clues);

        $this->actingAs($admin)->put(route('admin.mysteries.update', $mystery), [
            'statement' => 'Diperbarui.',
            'answer' => 'Tari Kecak',
            'origin' => 'Bali',
            'clues' => ['Satu.', 'Dua.', ''],
            'points' => 250,
            'clue_penalty' => 50,
            'max_wrong_guesses' => 5,
            'is_active' => 1,
        ])->assertRedirect();

        $mystery->refresh();
        $this->assertSame('Diperbarui.', $mystery->statement);
        $this->assertCount(2, $mystery->availableClues()); // clue kosong dibuang
        $this->assertSame(250, $mystery->points);

        $this->actingAs($admin)->delete(route('admin.mysteries.destroy', $mystery))
            ->assertRedirect(route('admin.mysteries.index'));

        $this->assertDatabaseMissing('mysteries', ['id' => $mystery->id]);
    }

    public function test_regular_user_cannot_manage_mysteries(): void
    {
        $this->actingAs($this->user)->get(route('admin.mysteries.index'))->assertForbidden();
        $this->actingAs($this->user)->post(route('admin.mysteries.store'), [])->assertForbidden();
    }

    public function test_inactive_mystery_is_hidden_and_not_playable(): void
    {
        $this->mystery->update(['is_active' => false]);

        $this->actingAs($this->user)
            ->get(route('mystery.index'))
            ->assertOk()
            ->assertDontSee('Bangunan beratap runcing');

        $this->actingAs($this->user)
            ->get(route('mystery.play', $this->mystery))
            ->assertNotFound();
    }
}
