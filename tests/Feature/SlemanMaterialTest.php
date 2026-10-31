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
use Database\Seeders\SlemanCultureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlemanMaterialTest extends TestCase
{
    use RefreshDatabase;

    private Island $jawa;

    private Province $jogja;

    private Regency $sleman;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jawa = Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);
        $this->jogja = Province::factory()->create([
            'island_id' => $this->jawa->id,
            'name' => 'DI Yogyakarta',
            'slug' => 'di-yogyakarta',
        ]);
        $this->sleman = Regency::factory()->create([
            'province_id' => $this->jogja->id,
            'name' => 'Sleman',
            'slug' => 'sleman',
        ]);

        foreach (['Tradisi & Kesenian', 'Tarian Tradisional', 'Alat Musik Tradisional', 'Kuliner Khas'] as $name) {
            CultureCategory::factory()->create(['name' => $name, 'slug' => str($name)->slug()]);
        }
    }

    public function test_seeder_creates_sleman_materials(): void
    {
        $this->seed(SlemanCultureSeeder::class);

        $this->assertGreaterThanOrEqual(25, $this->sleman->cultures()->count());

        foreach ([
            'sejarah-kabupaten-sleman',
            'saparan-bekakak',
            'jamasan-kyai-turunsih',
            'merti-kali',
            'tari-badui',
            'kuntulan',
            'jathilan-lancur',
            'reog-keprajuritan',
            'sruntul',
            'candi-prambanan',
            'candi-kalasan',
            'candi-sambisari',
            'candi-sari',
            'candi-ijo',
            'candi-barong',
            'jadah-tempe-kaliurang',
            'ayam-goreng-kalasan',
            'salak-pondoh',
            'bebek-bacem-nglengis',
        ] as $slug) {
            $this->assertDatabaseHas('cultures', ['slug' => $slug]);
        }
    }

    /**
     * Naskah sumber sendiri mengoreksi dua hal ini.
     */
    public function test_corrected_items_are_not_listed_as_sleman_specialties(): void
    {
        $this->seed(SlemanCultureSeeder::class);

        // Apem Wonolelo berada di Pleret, Kabupaten Bantul.
        $this->assertSame(0, Culture::where('slug', 'like', '%apem%')->count());

        // Tempe Pondoh belum punya bukti asal-usul lokal.
        $this->assertSame(0, Culture::where('slug', 'like', '%tempe-pondoh%')->count());

        // Salak Pondoh tetap ada karena memang identik dengan Sleman.
        $this->assertSame(1, Culture::where('slug', 'salak-pondoh')->count());
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(SlemanCultureSeeder::class);
        $first = $this->sleman->cultures()->count();

        $this->seed(SlemanCultureSeeder::class);

        $this->assertSame($first, $this->sleman->cultures()->count(), 'Seeder Sleman membuat duplikat.');
    }

    public function test_regency_page_lists_sleman_materials(): void
    {
        $this->seed(SlemanCultureSeeder::class);

        $this->actingAs(User::factory()->create())
            ->get(route('culture.regency', [$this->jawa, $this->jogja, $this->sleman]))
            ->assertOk()
            ->assertSee('Sleman')
            ->assertSee('Saparan Bekakak')
            ->assertSee('Candi Prambanan')
            ->assertSee('Salak Pondoh')
            ->assertSee('Tari Badui')
            ->assertSee('Kuliner Khas');
    }

    public function test_detail_page_shows_full_article(): void
    {
        $this->seed(SlemanCultureSeeder::class);

        $bekakak = Culture::where('slug', 'saparan-bekakak')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->sleman, $bekakak]))
            ->assertOk()
            ->assertSee('Saparan Bekakak')
            ->assertSee('Ambarketawang')
            ->assertSee('adonan tepung ketan')
            ->assertSee('Nilai Filosofis');
    }

    public function test_every_sleman_material_has_content_and_opens(): void
    {
        $this->seed(SlemanCultureSeeder::class);
        $user = User::factory()->create();

        foreach ($this->sleman->cultures()->get() as $culture) {
            $this->assertNotEmpty(trim((string) $culture->content), "Materi {$culture->name} tanpa isi.");

            $this->actingAs($user)
                ->get(route('culture.detail', [$this->jawa, $this->jogja, $this->sleman, $culture]))
                ->assertOk();
        }
    }

    public function test_yogyakarta_materials_do_not_collide_between_regencies(): void
    {
        $bantul = Regency::factory()->create([
            'province_id' => $this->jogja->id, 'name' => 'Bantul', 'slug' => 'bantul',
        ]);
        $kulonProgo = Regency::factory()->create([
            'province_id' => $this->jogja->id, 'name' => 'Kulon Progo', 'slug' => 'kulon-progo',
        ]);

        $this->seed(KulonProgoCultureSeeder::class);
        $this->seed(BantulCultureSeeder::class);
        $this->seed(SlemanCultureSeeder::class);

        $this->assertGreaterThan(0, $this->sleman->cultures()->count());
        $this->assertGreaterThan(0, $bantul->cultures()->count());
        $this->assertGreaterThan(0, $kulonProgo->cultures()->count());

        // Tiap materi hanya milik satu wilayah.
        $this->assertSame(0, $this->sleman->cultures()->where('slug', 'tari-angguk')->count());
        $this->assertSame(0, $this->sleman->cultures()->where('slug', 'kupatan-jolosutro')->count());
        $this->assertSame(0, $bantul->cultures()->where('slug', 'saparan-bekakak')->count());
        $this->assertSame(0, $kulonProgo->cultures()->where('slug', 'candi-prambanan')->count());
    }
}
