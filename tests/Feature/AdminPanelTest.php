<?php

namespace Tests\Feature;

use App\Models\InterestRequest;
use App\Models\Quest;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin(): void
    {
        $this->get('/rezka')->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/rezka')->assertForbidden();
        $this->actingAs($user)->get(route('admin.quests.index'))->assertForbidden();
    }

    public function test_admin_can_create_daily_and_weekly_quest(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (['daily', 'weekly'] as $type) {
            $this->actingAs($admin)->post(route('admin.quests.store'), [
                'type' => $type,
                'title' => "Kuis {$type} Test",
                'description' => 'Deskripsi',
                'question_count' => 3,
                'available_on' => today()->toDateString(),
                'time_limit' => 7,
                'is_active' => 1,
            ])->assertRedirect();
        }

        $this->assertDatabaseHas('quests', ['type' => 'daily', 'title' => 'Kuis daily Test']);
        $this->assertDatabaseHas('quests', ['type' => 'weekly', 'title' => 'Kuis weekly Test']);
    }

    public function test_admin_can_add_question_to_quest(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quest = Quest::factory()->create();

        $this->actingAs($admin)->post(route('admin.questions.store', $quest), [
            'questions' => [
                [
                    'question' => 'Angklung dari mana?',
                    'options' => ['Jawa Barat', 'Bali', 'Papua', 'Aceh'],
                    'correct_index' => 0,
                ],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('questions', ['quest_id' => $quest->id, 'correct_index' => 0]);
    }

    public function test_admin_can_create_show_with_location(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.shows.store'), [
            'title' => 'Festival Budaya 2026',
            'description' => 'Festival tahunan',
            'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
            'location_name' => 'Lapangan Merdeka',
            'ticket_price' => 50000,
            'organizer' => 'Dinas Budaya',
        ])->assertRedirect(route('admin.shows.index'));

        $this->assertDatabaseHas('shows', ['title' => 'Festival Budaya 2026', 'ticket_price' => 50000]);
    }

    public function test_admin_can_create_venue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.venues.store'), [
            'name' => 'Sanggar Tari Nusantara',
            'type' => 'sanggar_tari',
            'address' => 'Jl. Budaya No. 1',
            'phone' => '081234567890',
            'description' => 'Sanggar tari tradisional',
        ])->assertRedirect(route('admin.venues.index'));

        $this->assertDatabaseHas('venues', ['name' => 'Sanggar Tari Nusantara', 'type' => 'sanggar_tari']);
    }

    public function test_admin_can_follow_up_interest_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $request = InterestRequest::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)->patch(route('admin.requests.update', $request), [
            'status' => 'contacted',
            'admin_note' => 'Sudah dihubungi via WhatsApp, latihan tiap Selasa.',
        ])->assertRedirect();

        $request->refresh();
        $this->assertEquals('contacted', $request->status);
        $this->assertNotNull($request->responded_at);
    }

    public function test_user_can_send_interest_request_once(): void
    {
        $user = User::factory()->create();
        $venue = Venue::factory()->create();

        $this->actingAs($user)->post(route('venues.interested', $venue))->assertRedirect();
        $this->actingAs($user)->post(route('venues.interested', $venue))->assertRedirect();

        $this->assertEquals(1, InterestRequest::where('user_id', $user->id)->where('venue_id', $venue->id)->count());
    }
}
