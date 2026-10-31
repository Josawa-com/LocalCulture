<?php

namespace Tests\Feature;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Island;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use Database\Seeders\BantulCultureSeeder;
use Database\Seeders\GunungKidulCultureSeeder;
use Database\Seeders\KulonProgoCultureSeeder;
use Database\Seeders\SlemanCultureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GunungKidulMaterialTest extends TestCase
{
    use RefreshDatabase;

    private Island $jawa;

    private Province $jogja;

    private Regency $gunungKidul;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jawa = Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);
        $this->jogja = Province::factory()->create([
            'island_id' => $this->jawa->id,
            'name' => 'DI Yogyakarta',
            'slug' => 'di-yogyakarta',
        ]);
        $this->gunungKidul = Regency::factory()->create([
            'province_id' => $this->jogja->id,
            'name' => 'Gunungkidul',
            'slug' => 'gunung-kidul',
        ]);

        foreach (['Tradisi & Kesenian', 'Tarian Tradisional', 'Alat Musik Tradisional', 'Kuliner Khas', 'Pakaian Adat'] as $name) {
            CultureCategory::factory()->create(['name' => $name, 'slug' => str($name)->slug()]);
        }
    }

    public function test_seeder_creates_gunungkidul_materials(): void
    {
        $this->seed(GunungKidulCultureSeeder::class);

        $this->assertGreaterThanOrEqual(30, $this->gunungKidul->cultures()->count());

        foreach ([
            'sejarah-gunungkidul',
            'rasulan-gunungkidul',
            'nyadran-gunungkidul',
            'bersih-desa-gunungkidul',
            'wiwitan-gunungkidul',
            'bersih-telaga',
            'kenduri-gunungkidul',
            'jathilan-gunungkidul',
            'reog-gunungkidul',
            'gejog-lesung-gunungkidul',
            'karawitan-gunungkidul',
            'wayang-kulit-gunungkidul',
            'campursari',
            'tiwul',
            'gatot',
            'gaplek-gunungkidul',
            'walang-goreng',
            'sego-abang',
            'sayur-lombok-ijo',
            'bakmi-jawa-gunungkidul',
            'kerajinan-batu-alam-gunungkidul',
            'batik-gunungkidul',
            'situs-sokoliman',
            'goa-pindul',
            'goa-jomblang',
            'pantai-wediombo',
            'nilai-budaya-gunungkidul',
        ] as $slug) {
            $this->assertDatabaseHas('cultures', ['slug' => $slug]);
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(GunungKidulCultureSeeder::class);
        $first = $this->gunungKidul->cultures()->count();

        $this->seed(GunungKidulCultureSeeder::class);

        $this->assertSame($first, $this->gunungKidul->cultures()->count(), 'Seeder Gunungkidul membuat duplikat.');
    }

    public function test_regency_page_lists_gunungkidul_materials(): void
    {
        $this->seed(GunungKidulCultureSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get(route('culture.regency', [$this->jawa, $this->jogja, $this->gunungKidul]))
            ->assertOk()
            ->assertSee('Gunungkidul')
            ->assertSee('Rasulan')
            ->assertSee('Tiwul')
            ->assertSee('Walang Goreng')
            ->assertSee('Jathilan')
            ->assertSee('Kuliner Khas')
            ->assertSee('Tarian Tradisional');
    }

    public function test_detail_page_shows_full_article(): void
    {
        $this->seed(GunungKidulCultureSeeder::class);

        $rasulan = Culture::where('slug', 'rasulan-gunungkidul')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->gunungKidul, $rasulan]))
            ->assertOk()
            ->assertSee('Rasulan')
            ->assertSee('Merti Dusun')
            ->assertSee('wayang kulit')
            ->assertSee('Nilai Filosofis');
    }

    public function test_every_material_has_content_and_opens(): void
    {
        $this->seed(GunungKidulCultureSeeder::class);
        $user = User::factory()->create();

        foreach ($this->gunungKidul->cultures()->get() as $culture) {
            $this->assertNotEmpty(trim((string) $culture->content), "Materi {$culture->name} tanpa isi.");

            $this->actingAs($user)
                ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->gunungKidul, $culture]))
                ->assertOk();
        }
    }

    public function test_all_four_yogyakarta_regencies_have_materials(): void
    {
        $bantul = Regency::factory()->create([
            'province_id' => $this->jogja->id, 'name' => 'Bantul', 'slug' => 'bantul',
        ]);
        $kulonProgo = Regency::factory()->create([
            'province_id' => $this->jogja->id, 'name' => 'Kulon Progo', 'slug' => 'kulon-progo',
        ]);
        $sleman = Regency::factory()->create([
            'province_id' => $this->jogja->id, 'name' => 'Sleman', 'slug' => 'sleman',
        ]);

        $this->seed(KulonProgoCultureSeeder::class);
        $this->seed(BantulCultureSeeder::class);
        $this->seed(SlemanCultureSeeder::class);
        $this->seed(GunungKidulCultureSeeder::class);

        foreach ([$bantul, $kulonProgo, $sleman, $this->gunungKidul] as $regency) {
            $this->assertGreaterThan(20, $regency->cultures()->count(), "{$regency->name} kurang materi.");
        }

        // Tidak ada materi yang berpindah wilayah.
        $this->assertSame(0, $this->gunungKidul->cultures()->where('slug', 'saparan-bekakak')->count());
        $this->assertSame(0, $this->gunungKidul->cultures()->where('slug', 'tari-angguk')->count());
        $this->assertSame(0, $sleman->cultures()->where('slug', 'rasulan-gunungkidul')->count());
        $this->assertSame(0, $bantul->cultures()->where('slug', 'tiwul')->count());
    }
}
