<?php

namespace Tests\Feature;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Island;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use Database\Seeders\BantulCultureSeeder;
use Database\Seeders\KulonProgoCultureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BantulMaterialTest extends TestCase
{
    use RefreshDatabase;

    private Island $jawa;

    private Province $jogja;

    private Regency $bantul;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jawa = Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);
        $this->jogja = Province::factory()->create([
            'island_id' => $this->jawa->id,
            'name' => 'DI Yogyakarta',
            'slug' => 'di-yogyakarta',
        ]);
        $this->bantul = Regency::factory()->create([
            'province_id' => $this->jogja->id,
            'name' => 'Bantul',
            'slug' => 'bantul',
        ]);

        foreach (['Tradisi & Kesenian', 'Tarian Tradisional', 'Alat Musik Tradisional', 'Kuliner Khas'] as $name) {
            CultureCategory::factory()->create(['name' => $name, 'slug' => str($name)->slug()]);
        }
    }

    public function test_seeder_creates_all_bantul_materials(): void
    {
        $this->seed(BantulCultureSeeder::class);

        $count = $this->bantul->cultures()->count();
        $this->assertGreaterThanOrEqual(30, $count, "Materi Bantul hanya {$count}.");

        foreach ([
            'sejarah-budaya-bantul',
            'gua-selarong',
            'pembentukan-kabupaten-bantul',
            'kupatan-jolosutro',
            'nguras-enceh',
            'labuhan-parangkusumo',
            'pisungsung-jaladri',
            'merti-dusun-bantul',
            'rabu-pungkasan-wonokromo',
            'cembengan',
            'sholawat-maulud-jawi',
            'tari-sholawat-montro',
            'jathilan-bantul',
            'gejog-lesung',
            'karawitan-wayang-bantul',
            'gerabah-kasongan',
            'kerajinan-kulit-manding',
            'batik-giriloyo',
            'tatah-sungging-wukirsari',
            'mie-lethek',
            'sate-klathak',
            'geplak-bantul',
            'gudeg-manggar',
            'miedes-pundong',
            'wedang-uwuh',
            'makam-imogiri',
            'watu-gilang',
        ] as $slug) {
            $this->assertDatabaseHas('cultures', ['slug' => $slug]);
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(BantulCultureSeeder::class);
        $first = $this->bantul->cultures()->count();

        $this->seed(BantulCultureSeeder::class);

        $this->assertSame($first, $this->bantul->cultures()->count(), 'Seeder Bantul membuat duplikat.');
    }

    public function test_regency_page_lists_bantul_materials(): void
    {
        $this->seed(BantulCultureSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get(route('culture.regency', [$this->jawa, $this->jogja, $this->bantul]))
            ->assertOk()
            ->assertSee('Bantul')
            ->assertSee('Kupatan Jolosutro')
            ->assertSee('Nguras Enceh')
            ->assertSee('Gerabah Kasongan')
            ->assertSee('Mie Lethek')
            ->assertSee('Sate Klathak')
            ->assertSee('Kuliner Khas')
            ->assertSee('Tarian Tradisional');
    }

    public function test_detail_page_shows_full_article(): void
    {
        $this->seed(BantulCultureSeeder::class);

        $angguk = Culture::where('slug', 'kupatan-jolosutro')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->bantul, $angguk]))
            ->assertOk()
            ->assertSee('Kupatan Jolosutro')
            ->assertSee('Jolosutro')
            ->assertSee('Warisan Budaya Takbenda')
            ->assertSee('Nilai Filosofis');
    }

    public function test_every_bantul_material_has_content_and_opens(): void
    {
        $this->seed(BantulCultureSeeder::class);
        $user = User::factory()->create();

        foreach ($this->bantul->cultures()->get() as $culture) {
            $this->assertNotEmpty(trim((string) $culture->content), "Materi {$culture->name} tanpa isi.");

            $this->actingAs($user)
                ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->bantul, $culture]))
                ->assertOk();
        }
    }

    public function test_bantul_and_kulon_progo_do_not_collide(): void
    {
        $kulonProgo = Regency::factory()->create([
            'province_id' => $this->jogja->id,
            'name' => 'Kulon Progo',
            'slug' => 'kulon-progo',
        ]);

        $this->seed(KulonProgoCultureSeeder::class);
        $this->seed(BantulCultureSeeder::class);

        $this->assertGreaterThan(0, $this->bantul->cultures()->count());
        $this->assertGreaterThan(0, $kulonProgo->cultures()->count());

        // Tidak ada materi yang berpindah wilayah.
        $this->assertSame(0, $this->bantul->cultures()->where('slug', 'tari-angguk')->count());
        $this->assertSame(0, $kulonProgo->cultures()->where('slug', 'kupatan-jolosutro')->count());
    }
}
