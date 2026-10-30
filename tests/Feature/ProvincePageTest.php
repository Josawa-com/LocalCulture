<?php

namespace Tests\Feature;

use App\Models\Island;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvincePageTest extends TestCase
{
    use RefreshDatabase;

    private function jawaWithProvinces(): Island
    {
        $jawa = Island::factory()->create(['name' => 'Jawa', 'slug' => 'jawa']);

        foreach (['dki-jakarta' => 'DKI Jakarta', 'jawa-barat' => 'Jawa Barat', 'banten' => 'Banten'] as $slug => $name) {
            Province::factory()->create([
                'island_id' => $jawa->id,
                'name' => $name,
                'slug' => $slug,
            ]);
        }

        return $jawa;
    }

    public function test_province_page_lists_six_cards_from_config(): void
    {
        $jawa = $this->jawaWithProvinces();

        $html = $this->actingAs(User::factory()->create())
            ->get(route('culture.island', $jawa))
            ->assertOk()
            ->getContent();

        $names = ['DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur'];

        foreach ($names as $name) {
            $this->assertStringContainsString($name, $html, "Kartu provinsi {$name} tidak tampil.");
        }

        // Tepat 6 kartu (satu <li> per provinsi).
        $this->assertSame(6, substr_count($html, 'class="panel group flex h-full flex-col overflow-hidden'));
    }

    public function test_regency_and_city_counts_are_parsed_from_filename(): void
    {
        $jawa = $this->jawaWithProvinces();

        $this->actingAs(User::factory()->create())
            ->get(route('culture.island', $jawa))
            ->assertOk()
            // MonasJakarta1dan5kota.png
            ->assertSee('1 kabupaten')
            ->assertSee('5 kota')
            // GedungSateJawaBarat18dan9kota.png
            ->assertSee('18 kabupaten')
            ->assertSee('9 kota')
            // MasjidAgungBanten4dan4kota.png
            ->assertSee('4 kabupaten')
            // CandiBudhdaJawaTengah29dan6kota.png
            ->assertSee('29 kabupaten')
            // KeratonJogja4dan1kota.png
            ->assertSee('1 kota')
            // PatungSuraBuayaJawaTimur29dan9kota.png
            ->assertSee('29 kabupaten');
    }

    public function test_each_card_shows_its_image(): void
    {
        $jawa = $this->jawaWithProvinces();

        $html = $this->actingAs(User::factory()->create())
            ->get(route('culture.island', $jawa))
            ->assertOk()
            ->getContent();

        foreach (config('province_maps.jawa.provinces') as $row) {
            $expected = 'Provinsi%20Jawa/'.rawurlencode($row['file']);
            $this->assertStringContainsString($expected, $html, "Gambar {$row['file']} tidak dirender.");
        }
    }

    public function test_cards_link_to_their_province_page(): void
    {
        $jawa = $this->jawaWithProvinces();
        $banten = Province::where('slug', 'banten')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->get(route('culture.island', $jawa))
            ->assertOk()
            ->assertSee('href="'.route('culture.province', [$jawa, $banten]).'"', false);
    }

    public function test_guest_sees_locked_cards_for_other_provinces(): void
    {
        $jawa = $this->jawaWithProvinces();
        $allowed = Province::where('slug', 'dki-jakarta')->firstOrFail();

        $this->withSession(['guest_province_id' => $allowed->id])
            ->get(route('culture.island', $jawa))
            ->assertOk()
            ->assertSee('title="Terkunci"', false)
            // Provinsi yang dipilih tidak terkunci.
            ->assertSee('href="'.route('culture.province', [$jawa, $allowed]).'"', false);
    }

    public function test_island_without_config_falls_back_to_database(): void
    {
        $sumatera = Island::factory()->create(['name' => 'Sumatera', 'slug' => 'sumatera']);
        Province::factory()->create([
            'island_id' => $sumatera->id,
            'name' => 'Aceh',
            'slug' => 'aceh',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('culture.island', $sumatera))
            ->assertOk()
            ->assertSee('Aceh');
    }

    public function test_missing_image_file_is_not_rendered_as_broken(): void
    {
        $jawa = $this->jawaWithProvinces();

        config()->set('province_maps.jawa.provinces', [
            ['file' => 'FileTidakAda99dan9kota.png', 'name' => 'Provinsi Hantu', 'slug' => null, 'note' => '—'],
        ]);

        $html = $this->actingAs(User::factory()->create())
            ->get(route('culture.island', $jawa))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Provinsi Hantu', $html);
        $this->assertStringNotContainsString('FileTidakAda99dan9kota.png', $html);
    }
}
