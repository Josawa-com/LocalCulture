<?php

namespace Tests\Feature;

use App\Models\Island;
use App\Models\Mystery;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestAccessAndMysteryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_auto_gets_the_first_province_they_open_without_a_notice(): void
    {
        $island = Island::factory()->create();
        $province = Province::factory()->for($island)->create();
        Regency::factory()->for($province)->create();

        // Tamu membuka provinsi tanpa memilih lebih dulu: provinsi itu
        // otomatis menjadi pilihannya, tanpa notifikasi apa pun.
        $this->get(route('culture.province', [$island, $province]))
            ->assertOk()
            ->assertSessionHas('guest_province_id', $province->id)
            ->assertSessionMissing('info');

        // Tetap bisa lanjut ke wilayah di provinsi yang sama.
        $regency = $province->regencies()->first();
        $this->get(route('culture.regency', [$island, $province, $regency]))->assertOk();
    }

    public function test_guest_cannot_access_province_other_than_chosen(): void
    {
        $island = Island::factory()->create();
        $chosen = Province::factory()->for($island)->create(['name' => 'Aceh', 'slug' => 'aceh']);
        $other = Province::factory()->for($island)->create(['name' => 'Bali', 'slug' => 'bali']);
        Regency::factory()->for($other)->create();

        $this->post(route('culture.choose-province'), ['province_id' => $chosen->id]);

        $this->get(route('culture.province', [$island, $other]))->assertForbidden();
    }

    public function test_logged_in_user_can_access_all_provinces(): void
    {
        $user = User::factory()->create();
        $island = Island::factory()->create();
        $provinceA = Province::factory()->for($island)->create();
        $provinceB = Province::factory()->for($island)->create();
        Regency::factory()->for($provinceA)->create();
        Regency::factory()->for($provinceB)->create();

        $this->actingAs($user)->get(route('culture.province', [$island, $provinceA]))->assertOk();
        $this->actingAs($user)->get(route('culture.province', [$island, $provinceB]))->assertOk();
    }

    public function test_mystery_guess_correct_answer_scores_full_points(): void
    {
        $user = User::factory()->create();
        $user->reputation()->create();
        $mystery = Mystery::factory()->create([
            'answer' => 'Rumah Gadang',
            'origin' => 'Sumatera Barat',
            'points' => 300,
            'clue_penalty' => 50,
        ]);

        // Tanpa membuka petunjuk -> poin penuh 300
        $response = $this->actingAs($user)->postJson(route('mystery.guess', $mystery), [
            'guess' => 'rumah gadang',
        ]);

        $response->assertOk()->assertJson(['is_correct' => true, 'score' => 300]);
        $this->assertEquals(300, $user->reputation()->first()->total_score);
        $this->assertEquals(1, $user->reputation()->first()->mysteries_solved);
    }

    public function test_mystery_score_decreases_by_50_per_clue(): void
    {
        $user = User::factory()->create();
        $user->reputation()->create();
        $mystery = Mystery::factory()->create([
            'answer' => 'Angklung',
            'origin' => 'Jawa Barat',
            'points' => 300,
            'clue_penalty' => 50,
        ]);

        // Buka 3 petunjuk -> 300 - (3 x 50) = 150
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($user)->postJson(route('mystery.reveal', $mystery))->assertOk();
        }

        $this->actingAs($user)->postJson(route('mystery.guess', $mystery), [
            'guess' => 'angklung',
        ])->assertJson(['score' => 150]);
    }

    public function test_wrong_mystery_guess_records_attempt_without_score(): void
    {
        $user = User::factory()->create();
        $user->reputation()->create();
        $mystery = Mystery::factory()->create(['answer' => 'Tari Kecak']);

        $this->actingAs($user)->postJson(route('mystery.guess', $mystery), [
            'guess' => 'Tari Saman',
        ])->assertJson(['is_correct' => false, 'score' => 0]);

        $this->assertEquals(0, $user->reputation()->first()->total_score);
        $this->assertEquals(1, $user->mysteryAttempts()->count());
    }

    public function test_mystery_reveal_returns_clue_by_index(): void
    {
        $user = User::factory()->create();
        $mystery = Mystery::factory()->create([
            'clues' => ['Petunjuk pertama', 'Petunjuk kedua', 'Petunjuk ketiga'],
            'max_clues' => 3,
        ]);

        // Clue dibuka BERTAHAP (server yang menentukan urutan),
        // jadi permintaan pertama selalu mengembalikan clue pertama.
        $this->actingAs($user)->postJson(route('mystery.reveal', $mystery))
            ->assertOk()
            ->assertJsonPath('clue', 'Petunjuk pertama')
            ->assertJsonPath('clues_revealed', 1);

        $this->actingAs($user)->postJson(route('mystery.reveal', $mystery))
            ->assertOk()
            ->assertJsonPath('clue', 'Petunjuk kedua')
            ->assertJsonPath('clues_revealed', 2);
    }
}
