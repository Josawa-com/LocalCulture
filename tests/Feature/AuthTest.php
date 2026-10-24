<?php

namespace Tests\Feature;

use App\Models\Island;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_with_island_and_province(): void
    {
        $island = Island::factory()->create();
        $province = Province::factory()->for($island)->create();

        $response = $this->post('/daftar', [
            'name' => 'Rezka Tester',
            'email' => 'rezka@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'island_id' => $island->id,
            'province_id' => $province->id,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'rezka@example.com']);
        $this->assertAuthenticated();
    }

    public function test_registration_creates_reputation_row(): void
    {
        $island = Island::factory()->create();
        $province = Province::factory()->for($island)->create();

        $this->post('/daftar', [
            'name' => 'Rezka',
            'email' => 'rezka2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'island_id' => $island->id,
            'province_id' => $province->id,
        ]);

        $user = User::where('email', 'rezka2@example.com')->first();
        $this->assertNotNull($user->reputation);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_login_rejects_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registered_user_never_gets_admin_role(): void
    {
        $island = Island::factory()->create();
        $province = Province::factory()->for($island)->create();

        // Mencoba menyuntikkan role=admin lewat mass assignment
        $this->post('/daftar', [
            'name' => 'Hacker',
            'email' => 'hack@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'island_id' => $island->id,
            'province_id' => $province->id,
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'hack@example.com', 'role' => 'user']);
    }
}
