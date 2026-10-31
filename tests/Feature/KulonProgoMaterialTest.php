<?php

namespace Tests\Feature;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Island;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use Database\Seeders\KulonProgoCultureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KulonProgoMaterialTest extends TestCase
{
    use RefreshDatabase;

    private Island $jawa;

    private Province $jogja;

    private Regency $kulonProgo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jawa = Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);
        $this->jogja = Province::factory()->create([
            'island_id' => $this->jawa->id,
            'name' => 'DI Yogyakarta',
            'slug' => 'di-yogyakarta',
        ]);
        $this->kulonProgo = Regency::factory()->create([
            'province_id' => $this->jogja->id,
            'name' => 'Kulon Progo',
            'slug' => 'kulon-progo',
        ]);

        // Kategori yang dipakai seeder.
        foreach (['Tradisi & Kesenian', 'Tarian Tradisional', 'Alat Musik Tradisional', 'Kuliner Khas', 'Pakaian Adat'] as $name) {
            CultureCategory::factory()->create([
                'name' => $name,
                'slug' => str($name)->slug(),
            ]);
        }
    }

    private function seedKulonProgo(): void
    {
        $this->seed(KulonProgoCultureSeeder::class);
    }

    public function test_seeder_creates_all_materials(): void
    {
        $this->seedKulonProgo();

        $count = $this->kulonProgo->cultures()->count();

        $this->assertGreaterThanOrEqual(35, $count, "Materi Kulon Progo hanya {$count}.");
        $this->assertDatabaseHas('cultures', ['slug' => 'tari-angguk', 'name' => 'Tari Angguk']);
        $this->assertDatabaseHas('cultures', ['slug' => 'geblek-kulon-progo']);
        $this->assertDatabaseHas('cultures', ['slug' => 'krumpyung']);
        $this->assertDatabaseHas('cultures', ['slug' => 'sawah-surjan']);
        $this->assertDatabaseHas('cultures', ['slug' => 'larakan-kyai-depok']);
        $this->assertDatabaseHas('cultures', ['slug' => 'gula-kelapa-kulon-progo']);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seedKulonProgo();
        $first = $this->kulonProgo->cultures()->count();

        $this->seedKulonProgo();

        $this->assertSame($first, $this->kulonProgo->cultures()->count(), 'Seeder membuat duplikat.');
    }

    public function test_regency_page_lists_material_by_category(): void
    {
        $this->seedKulonProgo();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('culture.regency', [$this->jawa, $this->jogja, $this->kulonProgo]))
            ->assertOk()
            ->assertSee('Kulon Progo')
            ->assertSee('Tarian Tradisional')
            ->assertSee('Tari Angguk')
            ->assertSee('Alat Musik Tradisional')
            ->assertSee('Krumpyung')
            ->assertSee('Kuliner Khas')
            ->assertSee('Geblek')
            ->assertSee('Pakaian Adat')
            ->assertSee('Lurik')
            ->assertSee('Tradisi & Kesenian');
    }

    public function test_regency_page_reports_material_count(): void
    {
        $this->seedKulonProgo();
        $count = $this->kulonProgo->cultures()->count();

        $this->actingAs(User::factory()->create())
            ->get(route('culture.regency', [$this->jawa, $this->jogja, $this->kulonProgo]))
            ->assertOk()
            ->assertSee($count.' materi');
    }

    public function test_culture_detail_shows_full_content_and_philosophy(): void
    {
        $this->seedKulonProgo();

        $angguk = Culture::where('slug', 'tari-angguk')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->kulonProgo, $angguk]))
            ->assertOk()
            ->assertSee('Tari Angguk')
            ->assertSee('Warisan Budaya Takbenda Indonesia pada tahun 2016')
            ->assertSee('Nilai Filosofis')
            ->assertSee('Materi Lain di Kulon Progo');
    }

    public function test_every_material_has_content_and_opens_without_error(): void
    {
        $this->seedKulonProgo();
        $user = User::factory()->create();

        foreach ($this->kulonProgo->cultures()->get() as $culture) {
            $this->assertNotEmpty(trim((string) $culture->content), "Materi {$culture->name} tidak punya isi.");

            $this->actingAs($user)
                ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->kulonProgo, $culture]))
                ->assertOk();
        }
    }

    public function test_materials_are_grouped_under_existing_categories(): void
    {
        $this->seedKulonProgo();

        $categories = $this->kulonProgo->cultures()
            ->with('category')
            ->get()
            ->pluck('category.name')
            ->unique()
            ->sort()
            ->values()
            ->all();

        $this->assertSame(
            ['Alat Musik Tradisional', 'Kuliner Khas', 'Pakaian Adat', 'Tarian Tradisional', 'Tradisi & Kesenian'],
            $categories
        );
    }

    public function test_regency_without_material_shows_empty_state(): void
    {
        $sleman = Regency::factory()->create([
            'province_id' => $this->jogja->id,
            'name' => 'Sleman',
            'slug' => 'sleman',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('culture.regency', [$this->jawa, $this->jogja, $sleman]))
            ->assertOk()
            ->assertSee('Belum ada materi');
    }
}
