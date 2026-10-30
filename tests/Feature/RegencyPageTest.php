<?php

namespace Tests\Feature;

use App\Models\Island;
use App\Models\Province;
use App\Models\Regency;
use App\Models\RegencyVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegencyPageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Island $jawa;

    private Province $jogja;

    /** @var array<string, Regency> */
    private array $regencies = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->jawa = Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);
        $this->jogja = Province::factory()->create([
            'island_id' => $this->jawa->id,
            'name' => 'DI Yogyakarta',
            'slug' => 'di-yogyakarta',
        ]);

        foreach (['Kulon Progo', 'Bantul', 'Sleman', 'Gunung Kidul', 'Kota Jogja'] as $name) {
            $this->regencies[$name] = Regency::factory()->create([
                'province_id' => $this->jogja->id,
                'name' => $name,
                'slug' => str($name)->slug(),
            ]);
        }
    }

    private function listUrl(): string
    {
        return route('culture.province', [$this->jawa, $this->jogja]);
    }

    private function regencyUrl(string $name): string
    {
        return route('culture.regency', [$this->jawa, $this->jogja, $this->regencies[$name]]);
    }

    public function test_page_lists_five_regencies_without_images(): void
    {
        $html = $this->actingAs($this->user)->get($this->listUrl())->assertOk()->getContent();

        foreach (array_keys($this->regencies) as $name) {
            $this->assertStringContainsString($name, $html, "Wilayah {$name} tidak tampil.");
        }

        // Halaman ini hanya tombol — tidak ada gambar wilayah.
        $this->assertStringNotContainsString('Provinsi Jawa/', $html);
    }

    public function test_no_popular_badge_when_there_are_no_visits(): void
    {
        $this->actingAs($this->user)
            ->get($this->listUrl())
            ->assertOk()
            ->assertDontSee('Sering dikunjungi');
    }

    public function test_visiting_a_regency_records_a_visit(): void
    {
        $this->actingAs($this->user)->get($this->regencyUrl('Bantul'))->assertOk();

        $this->assertDatabaseHas('regency_visits', [
            'user_id' => $this->user->id,
            'regency_id' => $this->regencies['Bantul']->id,
            'visits' => 1,
        ]);

        // Kunjungan berikutnya menambah, bukan membuat baris baru.
        $this->actingAs($this->user)->get($this->regencyUrl('Bantul'))->assertOk();

        $this->assertSame(1, RegencyVisit::where('user_id', $this->user->id)->count());
        $this->assertSame(2, RegencyVisit::where('user_id', $this->user->id)->value('visits'));
    }

    public function test_guest_visits_are_not_recorded(): void
    {
        $this->withSession(['guest_province_id' => $this->jogja->id])
            ->get($this->regencyUrl('Bantul'))
            ->assertOk();

        $this->assertSame(0, RegencyVisit::count());
    }

    public function test_most_visited_regency_gets_the_badge(): void
    {
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Bantul']->id, 'visits' => 5]);
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Sleman']->id, 'visits' => 2]);

        $html = $this->actingAs($this->user)->get($this->listUrl())->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'Sering dikunjungi'));

        // Badge menempel pada Bantul.
        $this->assertMatchesRegularExpression('/Bantul.*?Sering dikunjungi/s', $html);
    }

    public function test_badge_disappears_when_top_visits_are_tied(): void
    {
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Bantul']->id, 'visits' => 3]);
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Kota Jogja']->id, 'visits' => 3]);

        $this->actingAs($this->user)
            ->get($this->listUrl())
            ->assertOk()
            ->assertDontSee('Sering dikunjungi');
    }

    public function test_badge_returns_when_a_tie_is_broken(): void
    {
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Bantul']->id, 'visits' => 3]);
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Kota Jogja']->id, 'visits' => 3]);

        $this->actingAs($this->user)->get($this->regencyUrl('Kota Jogja'))->assertOk();

        $html = $this->actingAs($this->user)->get($this->listUrl())->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'Sering dikunjungi'));
        $this->assertMatchesRegularExpression('/Kota Jogja.*?Sering dikunjungi/s', $html);
    }

    public function test_visits_from_different_users_are_summed(): void
    {
        $other = User::factory()->create();

        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Sleman']->id, 'visits' => 2]);
        RegencyVisit::create(['user_id' => $other->id, 'regency_id' => $this->regencies['Sleman']->id, 'visits' => 2]);
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Bantul']->id, 'visits' => 3]);

        // Sleman 4 (2+2) > Bantul 3 -> Sleman yang menang.
        $html = $this->actingAs($this->user)->get($this->listUrl())->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/Sleman.*?Sering dikunjungi/s', $html);
    }

    public function test_badge_only_counts_regencies_in_the_current_province(): void
    {
        $otherProvince = Province::factory()->create([
            'island_id' => $this->jawa->id,
            'name' => 'Jawa Barat',
            'slug' => 'jawa-barat',
        ]);
        $otherRegency = Regency::factory()->create([
            'province_id' => $otherProvince->id,
            'name' => 'Bandung',
            'slug' => 'bandung',
        ]);

        // Wilayah provinsi lain jauh lebih sering dikunjungi.
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $otherRegency->id, 'visits' => 99]);
        RegencyVisit::create(['user_id' => $this->user->id, 'regency_id' => $this->regencies['Bantul']->id, 'visits' => 1]);

        $html = $this->actingAs($this->user)->get($this->listUrl())->assertOk()->getContent();

        // Bantul tetap unggul di provinsi ini.
        $this->assertMatchesRegularExpression('/Bantul.*?Sering dikunjungi/s', $html);
    }
}
