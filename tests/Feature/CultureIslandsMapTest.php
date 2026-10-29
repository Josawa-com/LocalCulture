<?php

namespace Tests\Feature;

use App\Models\Island;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CultureIslandsMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_material_page_shows_the_island_map_images(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        $images = config('island_maps.images');

        $this->assertCount(7, $images);
        $this->assertSame(7, substr_count($html, 'class="pulau-item"'));
        $this->assertStringContainsString('Mulai Jelajah dari Pulau', $html);

        foreach ($images as $image) {
            // Nama file di-encode di URL (ada file berisi "&").
            $this->assertStringContainsString('Pulau/'.rawurlencode($image['file']), $html);
        }
    }

    public function test_each_image_position_comes_from_config(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        foreach (config('island_maps.images') as $image) {
            // x / y / w / z diambil apa adanya dari config; h dihitung dari rasio gambar.
            $style = sprintf('--x: %s; --y: %s; --w: %s; --h: ', $image['x'], $image['y'], $image['w']);

            $this->assertStringContainsString($style, $html, "Posisi {$image['file']} tidak dirender.");
            $this->assertStringContainsString('--z: '.$image['z'].';', $html);
        }
    }

    public function test_image_height_is_computed_from_real_image_ratio(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();
        $aspect = (float) config('island_maps.aspect');

        foreach (config('island_maps.images') as $image) {
            $size = getimagesize(public_path('Pulau/'.$image['file']));
            $this->assertNotFalse($size, "Gambar {$image['file']} tidak ditemukan.");

            // Tinggi (%) = lebar (%) x (tinggi/lebar gambar) x rasio kotak
            $expected = round($image['w'] * ($size[1] / $size[0]) * $aspect, 3);

            $this->assertStringContainsString(
                '--h: '.$expected.';',
                $html,
                "Tinggi {$image['file']} salah (harusnya {$expected})."
            );
        }
    }

    public function test_rotation_angle_is_rendered_per_image(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        foreach (config('island_maps.images') as $image) {
            $expected = ($image['r'] ?? 0).'deg;';
            $this->assertStringContainsString(
                '--r: '.$expected,
                $html,
                "Rotasi {$image['file']} tidak dirender."
            );
        }
    }

    public function test_rotation_changes_the_clamping_box_size(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        // Sudut miring -> kotak pembatas harus membesar agar tidak terpotong.
        $images = config('island_maps.images');
        $images[0]['r'] = 20;
        config()->set('island_maps.images', $images);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        preg_match('~--w: ([\d.]+); --h: ([\d.]+); --wr: ([\d.]+); --hr: ([\d.]+); --r: 20deg~', $html, $m);

        $this->assertNotEmpty($m, 'Baris style untuk gambar yang diputar tidak ditemukan.');

        [, $w, $h, $wr, $hr] = array_map('floatval', $m);

        $this->assertGreaterThan($w, $wr, 'Kotak pembatas horizontal harus lebih lebar setelah diputar.');
        $this->assertGreaterThan($h, $hr, 'Kotak pembatas vertikal harus lebih tinggi setelah diputar.');
    }

    public function test_rotation_of_zero_keeps_original_size(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        // Semua gambar saat ini r = 0 -> wr harus sama dengan w.
        preg_match_all('~--w: ([\d.]+); --h: ([\d.]+); --wr: ([\d.]+); --hr: ([\d.]+); --r: 0deg~', $html, $m, PREG_SET_ORDER);

        $this->assertNotEmpty($m);

        foreach ($m as $row) {
            $this->assertEqualsWithDelta((float) $row[1], (float) $row[3], 0.01);
            $this->assertEqualsWithDelta((float) $row[2], (float) $row[4], 0.01);
        }
    }

    public function test_map_fit_scales_the_whole_stage_instead_of_shifting_images(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        // Skala & geser diberikan ke KOTAK peta, bukan ke tiap gambar.
        $this->assertMatchesRegularExpression(
            '/--pulau-fit-scale: [\d.]+; --pulau-fit-shift: -?[\d.]+;/',
            $html
        );

        // Tidak boleh ada jepitan posisi per-gambar (itu yang menggeser X).
        $this->assertStringNotContainsString('left: clamp(', $html);
        $this->assertStringNotContainsString('top: clamp(', $html);
    }

    public function test_fit_scale_matches_content_bounds(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        preg_match('/--pulau-fit-scale: ([\d.]+); --pulau-fit-shift: (-?[\d.]+);/', $html, $m);
        $scale = (float) $m[1];
        $shift = (float) $m[2];

        // Hitung ulang dari config: batas kiri/kanan seluruh gambar.
        $left = null;
        $right = null;

        foreach (config('island_maps.images') as $image) {
            $size = getimagesize(public_path('Pulau/'.$image['file']));
            $aspect = (float) config('island_maps.aspect');
            $height = $image['w'] * ($size[1] / $size[0]) * $aspect;
            $rotation = $image['r'] ?? 0;
            $cos = abs(cos(deg2rad($rotation)));
            $sin = abs(sin(deg2rad($rotation)));
            $rotatedWidth = $image['w'] * $cos + ($height / $aspect) * $sin;

            $left = $left === null ? $image['x'] - $rotatedWidth / 2 : min($left, $image['x'] - $rotatedWidth / 2);
            $right = $right === null ? $image['x'] + $rotatedWidth / 2 : max($right, $image['x'] + $rotatedWidth / 2);
        }

        $width = $right - $left;

        if ($width > 100) {
            $this->assertEqualsWithDelta(100 / $width, $scale, 0.001);
            $this->assertEqualsWithDelta(-$left, $shift, 0.01);
        } else {
            $this->assertEqualsWithDelta(1.0, $scale, 0.001);
        }

        // Skala tidak boleh memperbesar gambar.
        $this->assertLessThanOrEqual(1.0, $scale);
    }

    public function test_images_are_the_click_target_so_transparent_areas_do_not_block(): void
    {
        Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        // Pembungkus tidak menangkap klik; gambar yang menjadi sasaran.
        $this->assertMatchesRegularExpression(
            '/\.pulau-item\s*\{[^}]*pointer-events:\s*none;/s',
            $html
        );
        $this->assertMatchesRegularExpression(
            '/\.pulau-item img\s*\{[^}]*pointer-events:\s*auto;/s',
            $html
        );
    }

    public function test_province_count_is_taken_from_the_last_number_in_the_filename(): void
    {
        Island::factory()->create(['name' => 'Sumatera', 'slug' => 'sumatera']);

        $this->get(route('culture.islands'))
            ->assertOk()
            // PulauSumatra10.png -> 10
            ->assertSee('10 provinsi')
            // PulauKalimantan5.png -> 5
            ->assertSee('5 provinsi')
            // KepulauanMaluku2.png -> 2
            ->assertSee('2 provinsi')
            // KepulauanNusaTenggara&Bali3.png -> 3
            ->assertSee('3 provinsi');
    }

    public function test_island_image_links_to_its_island_page(): void
    {
        $island = Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        $this->get(route('culture.islands'))
            ->assertOk()
            ->assertSee('href="'.route('culture.island', $island).'"', false);
    }

    public function test_images_at_the_top_get_the_tooltip_below(): void
    {
        Island::factory()->create(['name' => 'Sumatera', 'slug' => 'sumatera']);

        $html = $this->get(route('culture.islands'))->assertOk()->getContent();

        $above = substr_count($html, 'class="pulau-tip "');
        $below = substr_count($html, 'class="pulau-tip pulau-tip--below"');

        $this->assertSame(count(config('island_maps.images')), $above + $below);
        $this->assertGreaterThan(0, $below);
    }
}
