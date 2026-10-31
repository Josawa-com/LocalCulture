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
use Database\Seeders\KotaJogjaCultureSeeder;
use Database\Seeders\KulonProgoCultureSeeder;
use Database\Seeders\SlemanCultureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KotaJogjaMaterialTest extends TestCase
{
    use RefreshDatabase;

    private Island $jawa;

    private Province $jogja;

    private Regency $kotaJogja;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jawa = Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);
        $this->jogja = Province::factory()->create([
            'island_id' => $this->jawa->id,
            'name' => 'DI Yogyakarta',
            'slug' => 'di-yogyakarta',
        ]);
        $this->kotaJogja = Regency::factory()->create([
            'province_id' => $this->jogja->id,
            'name' => 'Kota Jogja',
            'slug' => 'kota-jogja',
        ]);

        foreach (['Tradisi & Kesenian', 'Tarian Tradisional', 'Alat Musik Tradisional', 'Kuliner Khas', 'Pakaian Adat'] as $name) {
            CultureCategory::factory()->create(['name' => $name, 'slug' => str($name)->slug()]);
        }
    }

    public function test_seeder_creates_kota_jogja_materials(): void
    {
        $this->seed(KotaJogjaCultureSeeder::class);

        $this->assertGreaterThanOrEqual(30, $this->kotaJogja->cultures()->count());

        foreach ([
            'sejarah-kota-yogyakarta',
            'unggah-ungguh-andhap-asor',
            'nilai-sosial-jogja',
            'bahasa-jawa-jogja',
            'filosofi-hidup-jogja',
            'sekaten',
            'garebeg',
            'labuhan-keraton',
            'siraman-pusaka',
            'keraton-yogyakarta',
            'abdi-dalem',
            'sumbu-filosofis',
            'tari-bedhaya',
            'tari-serimpi',
            'tari-golek',
            'wayang-jogja',
            'karawitan-macapat',
            'jemparingan-jogja',
            'batik-jogja',
            'pakaian-adat-jogja',
            'gudeg',
            'bakpia',
            'yangko-jogja',
            'kipo',
            'kopi-joss',
            'brongkos',
            'tamansari',
            'kotagede',
            'museum-sonobudoyo',
            'benteng-vredeburg',
        ] as $slug) {
            $this->assertDatabaseHas('cultures', ['slug' => $slug]);
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(KotaJogjaCultureSeeder::class);
        $first = $this->kotaJogja->cultures()->count();

        $this->seed(KotaJogjaCultureSeeder::class);

        $this->assertSame($first, $this->kotaJogja->cultures()->count(), 'Seeder Kota Jogja membuat duplikat.');
    }

    public function test_regency_page_lists_materials(): void
    {
        $this->seed(KotaJogjaCultureSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get(route('culture.regency', [$this->jawa, $this->jogja, $this->kotaJogja]))
            ->assertOk()
            ->assertSee('Keraton Ngayogyakarta Hadiningrat')
            ->assertSee('Sekaten')
            ->assertSee('Garebeg')
            ->assertSee('Gudeg')
            ->assertSee('Tari Bedhaya')
            ->assertSee('Batik')
            ->assertSee('Tradisi & Kesenian');
    }

    public function test_detail_page_shows_full_article(): void
    {
        $this->seed(KotaJogjaCultureSeeder::class);

        $keraton = Culture::where('slug', 'keraton-yogyakarta')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->kotaJogja, $keraton]))
            ->assertOk()
            ->assertSee('Keraton Ngayogyakarta Hadiningrat')
            ->assertSee('Hamengku Buwono I')
            ->assertSee('1755')
            ->assertSee('Nilai Filosofis');
    }

    public function test_every_material_has_content_and_opens(): void
    {
        $this->seed(KotaJogjaCultureSeeder::class);
        $user = User::factory()->create();

        foreach ($this->kotaJogja->cultures()->get() as $culture) {
            $this->assertNotEmpty(trim((string) $culture->content), "Materi {$culture->name} tanpa isi.");

            $this->actingAs($user)
                ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->kotaJogja, $culture]))
                ->assertOk();
        }
    }

    public function test_complete_diy_has_materials_in_all_regencies(): void
    {
        $regencies = [
            'bantul' => 'Bantul',
            'kulon-progo' => 'Kulon Progo',
            'sleman' => 'Sleman',
            'gunung-kidul' => 'Gunungkidul',
        ];

        $models = [];
        foreach ($regencies as $slug => $name) {
            $models[$slug] = Regency::factory()->create([
                'province_id' => $this->jogja->id,
                'name' => $name,
                'slug' => $slug,
            ]);
        }

        $this->seed(KulonProgoCultureSeeder::class);
        $this->seed(BantulCultureSeeder::class);
        $this->seed(SlemanCultureSeeder::class);
        $this->seed(GunungKidulCultureSeeder::class);
        $this->seed(KotaJogjaCultureSeeder::class);

        foreach ($models as $slug => $model) {
            $this->assertGreaterThan(20, $model->cultures()->count(), "{$model->name} kurang materi.");
        }

        $this->assertGreaterThan(20, $this->kotaJogja->cultures()->count(), 'Kota Jogja kurang materi.');

        // Tidak ada materi yang berpindah wilayah.
        $this->assertSame(0, $this->kotaJogja->cultures()->where('slug', 'saparan-bekakak')->count());
        $this->assertSame(0, $this->kotaJogja->cultures()->where('slug', 'rasulan-gunungkidul')->count());
        $this->assertSame(0, $this->kotaJogja->cultures()->where('slug', 'tiwul')->count());
        $this->assertSame(0, $models['sleman']->cultures()->where('slug', 'gudeg')->count());
    }
}
