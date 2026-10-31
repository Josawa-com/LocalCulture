<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationDockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Menu utama yang wajib muncul di dock navigasi bawah.
     *
     * @return array<string, string>
     */
    private function expectedMenu(): array
    {
        return [
            'Budaya' => 'culture.islands',
            'Kuis' => 'quests.index',
            'Mystery' => 'mystery.index',
            'Pertunjukan' => 'shows.index',
            'Sanggar' => 'venues.index',
            'Peringkat' => 'leaderboard',
        ];
    }

    public function test_dock_appears_on_every_public_page(): void
    {
        foreach (['/', '/budaya', '/kuis', '/misteri', '/pertunjukan', '/sanggar', '/leaderboard'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertStringContainsString('nav-dock', $html, "Dock tidak ada di {$path}.");
            $this->assertStringContainsString('nav-dock__glass', $html, "Lapisan kaca tidak ada di {$path}.");
            $this->assertStringContainsString('Navigasi utama', $html, "Label navigasi tidak ada di {$path}.");
        }
    }

    public function test_dock_lists_all_menu_items_with_correct_links(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        foreach ($this->expectedMenu() as $label => $routeName) {
            $this->assertStringContainsString(route($routeName), $html, "Tautan {$label} tidak ada.");
            $this->assertStringContainsString('>'.$label.'<', $html, "Label {$label} tidak ada.");
        }
    }

    public function test_dock_marks_the_current_page_as_active(): void
    {
        $cases = [
            '/budaya' => 'Budaya',
            '/kuis' => 'Kuis',
            '/misteri' => 'Mystery',
            '/pertunjukan' => 'Pertunjukan',
            '/sanggar' => 'Sanggar',
            '/leaderboard' => 'Peringkat',
        ];

        foreach ($cases as $path => $label) {
            $html = $this->get($path)->assertOk()->getContent();

            // Hanya satu tautan yang ditandai aktif.
            $this->assertSame(1, substr_count($html, 'nav-dock__link is-active'), "Penanda aktif ganda di {$path}.");
            $this->assertMatchesRegularExpression(
                '/nav-dock__link is-active"[^>]*title="[^"]*">\s*<span class="nav-dock__icon">\s*<svg[^>]*>.*?<\/svg>\s*<\/span>\s*<span>'.$label.'<\/span>/s',
                $html,
                "Menu aktif di {$path} bukan {$label}."
            );
        }
    }

    public function test_old_top_and_mobile_nav_are_removed(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // Menu tidak lagi menumpuk di header atas.
        $this->assertStringNotContainsString('hidden items-center gap-1 md:flex', $html);
        $this->assertStringNotContainsString('gap-2 overflow-x-auto px-4 pb-3 text-sm md:hidden', $html);
    }

    public function test_layout_reserves_space_so_dock_never_covers_content(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('pb-28', $html);
        $this->assertStringContainsString('md:pb-32', $html);
    }

    public function test_dock_is_reachable_by_keyboard_and_screen_readers(): void
    {
        $html = $this->get('/kuis')->assertOk()->getContent();

        // Dock memakai elemen <nav> berlabel, bukan sekumpulan <div>.
        $this->assertMatchesRegularExpression('/<nav class="nav-dock" aria-label="Navigasi utama">/', $html);

        // Tautan aktif ditandai aria-current untuk pembaca layar.
        $this->assertStringContainsString('aria-current="page"', $html);

        // Ikon di dalam tautan bersifat dekoratif.
        $this->assertStringContainsString('aria-hidden="true"', $html);
    }
}
