<?php

namespace Tests\Feature;

use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Kunci waktu agar batas harian/mingguan/bulanan deterministik.
        $this->travelTo(Carbon::parse('2026-10-29 12:00:00'));
    }

    public function test_guest_can_view_leaderboard_with_period_tabs(): void
    {
        $this->get(route('leaderboard'))
            ->assertOk()
            ->assertSee(route('leaderboard', ['periode' => 'daily']), false)
            ->assertSee(route('leaderboard', ['periode' => 'weekly']), false)
            ->assertSee(route('leaderboard', ['periode' => 'monthly']), false)
            ->assertSee('Belum ada peringkat harian');
    }

    public function test_unknown_period_falls_back_to_daily(): void
    {
        $this->get(route('leaderboard', ['periode' => 'tahunan']))
            ->assertOk()
            ->assertSee('Total skor kuis harian yang diselesaikan hari ini.');
    }

    public function test_daily_leaderboard_only_counts_attempts_finished_today(): void
    {
        $quest = Quest::factory()->create(['type' => 'daily']);

        $today = User::factory()->create(['name' => 'Pemain Hari Ini']);
        $yesterday = User::factory()->create(['name' => 'Pemain Kemarin']);

        QuestAttempt::factory()->for($quest)->for($today)->create([
            'total_score' => 300,
            'correct_count' => 3,
            'finished_at' => Carbon::parse('2026-10-29 09:00:00'),
        ]);
        QuestAttempt::factory()->for($quest)->for($yesterday)->create([
            'total_score' => 900,
            'correct_count' => 5,
            'finished_at' => Carbon::parse('2026-10-28 09:00:00'),
        ]);

        $this->get(route('leaderboard', ['periode' => 'daily']))
            ->assertOk()
            ->assertSee('Pemain Hari Ini')
            ->assertDontSee('Pemain Kemarin');
    }

    public function test_each_tab_only_counts_its_own_quiz_type(): void
    {
        $daily = Quest::factory()->create(['type' => 'daily']);
        $weekly = Quest::factory()->create(['type' => 'weekly']);
        $monthly = Quest::factory()->create(['type' => 'monthly']);

        $dailyPlayer = User::factory()->create(['name' => 'Pemain Harian']);
        $weeklyPlayer = User::factory()->create(['name' => 'Pemain Mingguan']);
        $monthlyPlayer = User::factory()->create(['name' => 'Pemain Bulanan']);

        QuestAttempt::factory()->for($daily)->for($dailyPlayer)->create(['finished_at' => Carbon::parse('2026-10-29 09:00:00')]);
        QuestAttempt::factory()->for($weekly)->for($weeklyPlayer)->create(['finished_at' => Carbon::parse('2026-10-27 09:00:00')]);
        QuestAttempt::factory()->for($monthly)->for($monthlyPlayer)->create(['finished_at' => Carbon::parse('2026-10-05 09:00:00')]);

        // Kuis harian tidak boleh bocor ke tab mingguan/bulanan.
        $this->get(route('leaderboard', ['periode' => 'weekly']))
            ->assertOk()
            ->assertSee('Pemain Mingguan')
            ->assertDontSee('Pemain Harian')
            ->assertDontSee('Pemain Bulanan');

        $this->get(route('leaderboard', ['periode' => 'monthly']))
            ->assertOk()
            ->assertSee('Pemain Bulanan')
            ->assertDontSee('Pemain Harian')
            ->assertDontSee('Pemain Mingguan');

        $this->get(route('leaderboard', ['periode' => 'daily']))
            ->assertOk()
            ->assertSee('Pemain Harian')
            ->assertDontSee('Pemain Mingguan')
            ->assertDontSee('Pemain Bulanan');
    }

    public function test_weekly_and_monthly_tabs_are_empty_when_only_daily_quizzes_were_played(): void
    {
        $daily = Quest::factory()->create(['type' => 'daily']);
        $user = User::factory()->create(['name' => 'Pemain Harian Saja']);

        QuestAttempt::factory()->for($daily)->for($user)->create([
            'total_score' => 400,
            'correct_count' => 4,
            'finished_at' => Carbon::parse('2026-10-29 09:00:00'),
        ]);

        // Harian tetap muncul.
        $this->get(route('leaderboard', ['periode' => 'daily']))
            ->assertOk()
            ->assertSee('Pemain Harian Saja');

        // Mingguan & bulanan harus kosong walau tanggalnya masih dalam rentang.
        $this->get(route('leaderboard', ['periode' => 'weekly']))
            ->assertOk()
            ->assertSee('Belum ada peringkat mingguan')
            ->assertDontSee('Pemain Harian Saja');

        $this->get(route('leaderboard', ['periode' => 'monthly']))
            ->assertOk()
            ->assertSee('Belum ada peringkat bulanan')
            ->assertDontSee('Pemain Harian Saja');
    }

    public function test_weekly_and_monthly_periods_include_older_attempts_in_range(): void
    {
        $weekly = Quest::factory()->create(['type' => 'weekly']);
        $monthly = Quest::factory()->create(['type' => 'monthly']);

        $thisWeek = User::factory()->create(['name' => 'Awal Minggu']);
        $thisMonth = User::factory()->create(['name' => 'Awal Bulan']);
        $lastMonth = User::factory()->create(['name' => 'Bulan Lalu']);

        QuestAttempt::factory()->for($weekly)->for($thisWeek)->create(['finished_at' => Carbon::parse('2026-10-27 08:00:00')]);
        QuestAttempt::factory()->for($monthly)->for($thisMonth)->create(['finished_at' => Carbon::parse('2026-10-05 08:00:00')]);
        QuestAttempt::factory()->for($monthly)->for($lastMonth)->create(['finished_at' => Carbon::parse('2026-09-20 08:00:00')]);

        $this->get(route('leaderboard', ['periode' => 'weekly']))
            ->assertOk()
            ->assertSee('Awal Minggu')
            ->assertDontSee('Awal Bulan')
            ->assertDontSee('Bulan Lalu');

        $this->get(route('leaderboard', ['periode' => 'monthly']))
            ->assertOk()
            ->assertSee('Awal Bulan')
            ->assertDontSee('Bulan Lalu');
    }

    public function test_leaderboard_ranks_users_by_total_period_score(): void
    {
        $quest = Quest::factory()->create(['type' => 'daily']);

        $low = User::factory()->create(['name' => 'Peringkat Tiga']);
        $high = User::factory()->create(['name' => 'Peringkat Satu']);
        $mid = User::factory()->create(['name' => 'Peringkat Dua']);

        // Dua attempt untuk user yang sama harus dijumlahkan.
        QuestAttempt::factory()->for(Quest::factory()->create(['type' => 'daily']))->for($high)->create(['total_score' => 200, 'correct_count' => 2]);
        QuestAttempt::factory()->for(Quest::factory()->create(['type' => 'daily']))->for($high)->create(['total_score' => 150, 'correct_count' => 1]);
        QuestAttempt::factory()->for($quest)->for($mid)->create(['total_score' => 300, 'correct_count' => 3]);
        QuestAttempt::factory()->for($quest)->for($low)->create(['total_score' => 100, 'correct_count' => 1]);

        $response = $this->get(route('leaderboard', ['periode' => 'daily']))->assertOk();

        $this->assertSame(350, QuestAttempt::where('user_id', $high->id)->sum('total_score'));

        $body = $response->getContent();
        // Bandingkan urutan di dalam tabel (podium sengaja berurutan 2–1–3).
        $table = substr($body, strpos($body, '<tbody'));
        $this->assertLessThan(strpos($table, 'Peringkat Dua'), strpos($table, 'Peringkat Satu'));
        $this->assertLessThan(strpos($table, 'Peringkat Tiga'), strpos($table, 'Peringkat Dua'));
    }

    public function test_unfinished_attempts_are_not_ranked(): void
    {
        $quest = Quest::factory()->create(['type' => 'daily']);
        $user = User::factory()->create(['name' => 'Belum Selesai']);

        QuestAttempt::factory()->for($quest)->for($user)->create([
            'status' => 0,
            'total_score' => 0,
            'finished_at' => null,
        ]);

        $this->get(route('leaderboard', ['periode' => 'daily']))
            ->assertOk()
            ->assertDontSee('Belum Selesai');
    }

    public function test_logged_in_user_sees_their_own_position(): void
    {
        $quest = Quest::factory()->create(['type' => 'daily']);

        $me = User::factory()->create(['name' => 'Pengguna Aktif']);
        $other = User::factory()->create(['name' => 'Pengguna Lain']);

        QuestAttempt::factory()->for($quest)->for($other)->create(['total_score' => 500, 'correct_count' => 5]);
        QuestAttempt::factory()->for($quest)->for($me)->create(['total_score' => 250, 'correct_count' => 2]);

        $this->actingAs($me)
            ->get(route('leaderboard', ['periode' => 'daily']))
            ->assertOk()
            ->assertSee('Posisi kamu peringkat 2')
            ->assertSee('Kamu');
    }

    public function test_logged_in_user_without_attempt_is_invited_to_play(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('leaderboard', ['periode' => 'monthly']))
            ->assertOk()
            ->assertSee('Kamu belum menyelesaikan kuis bulanan pada periode ini.');
    }

    public function test_user_who_only_played_daily_quiz_is_not_listed_in_weekly_tab(): void
    {
        $daily = Quest::factory()->create(['type' => 'daily']);
        $user = User::factory()->create(['name' => 'Pemain Harian Saja']);

        QuestAttempt::factory()->for($daily)->for($user)->create([
            'total_score' => 400,
            'finished_at' => Carbon::parse('2026-10-29 09:00:00'),
        ]);

        $response = $this->actingAs($user)
            ->get(route('leaderboard', ['periode' => 'weekly']))
            ->assertOk()
            ->assertSee('Kamu belum menyelesaikan kuis mingguan pada periode ini.');

        // Nama pemilik akun tetap muncul di navigasi, jadi cek area konten saja.
        $this->assertStringNotContainsString('Pemain Harian Saja', $this->mainContent($response->getContent()));
    }

    private function mainContent(string $html): string
    {
        $start = strpos($html, '<main');

        return $start === false ? '' : substr($html, $start);
    }
}
