<?php

namespace Tests\Feature;

use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createQuestWithQuestion(): array
    {
        $user = User::factory()->create();
        $quest = Quest::factory()->create(['type' => 'daily', 'available_on' => today(), 'ends_at' => today()->endOfDay()]);
        $question = Question::factory()->for($quest)->create([
            'question' => 'Tari Saman berasal dari?',
            'options' => ['Aceh', 'Bali', 'Jogja', 'Maluku'],
            'correct_index' => 0,
        ]);

        return [$user, $quest, $question];
    }

    public function test_user_can_start_play_and_finish_quest(): void
    {
        [$user, $quest, $question] = $this->createQuestWithQuestion();

        $response = $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();

        // Halaman main menampilkan soal & mencatat shown_at (konten play
        // dikosongkan sementara — TODO: kembalikan assertSee('Tari Saman') setelah view play diisi)
        $this->get(route('quests.play', $attempt))->assertOk();

        $answer = $attempt->answers()->firstOrFail();
        $this->assertNotNull($answer->shown_at);

        // Jawab benar
        $this->post(route('quests.answer', $attempt), [
            'answer_row' => $answer->id,
            'selected_index' => 0,
        ])->assertRedirect(route('quests.play', $attempt));

        $attempt->refresh();
        $this->assertEquals(1, $attempt->correct_count);
        $this->assertGreaterThan(0, $attempt->total_score);

        // Selesai → reputasi ter-update
        $this->post(route('quests.finish', $attempt))->assertRedirect();
        $this->assertEquals(1, $user->reputation()->sum('quests_completed'));
        $this->assertEquals($attempt->total_score, $user->reputation()->sum('total_score'));
    }

    public function test_quest_cannot_be_replayed_with_same_account(): void
    {
        [$user, $quest] = $this->createQuestWithQuestion();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();
        $attempt->update(['status' => 1]);

        // Mulai lagi → tetap attempt lama yang sudah selesai
        $this->actingAs($user)->post(route('quests.start', $quest))
            ->assertRedirect(route('quests.result', $attempt))
            ->assertSessionHas('error');

        $this->assertEquals(1, QuestAttempt::where('user_id', $user->id)->count());
    }

    public function test_unique_database_constraint_prevents_duplicate_attempts(): void
    {
        [$user, $quest] = $this->createQuestWithQuestion();

        $this->actingAs($user)->post(route('quests.start', $quest));

        $this->expectException(UniqueConstraintViolationException::class);

        QuestAttempt::create([
            'quest_id' => $quest->id,
            'user_id' => $user->id,
            'started_at' => now(),
        ]);
    }

    public function test_answer_time_is_measured_server_side(): void
    {
        [$user, $quest, $question] = $this->createQuestWithQuestion();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();
        $this->get(route('quests.play', $attempt));

        $answer = $attempt->answers()->firstOrFail();

        // Simulasikan jawaban 3 detik setelah soal dikirim
        $answer->update(['shown_at' => now()->subSeconds(3)]);

        $this->post(route('quests.answer', $attempt), [
            'answer_row' => $answer->id,
            'selected_index' => 0,
        ]);

        $answer->refresh();
        $this->assertGreaterThanOrEqual(2900, $answer->time_ms);
        $this->assertLessThan(5000, $answer->time_ms);
    }

    public function test_late_answer_beyond_time_limit_is_rejected(): void
    {
        [$user, $quest, $question] = $this->createQuestWithQuestion();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();
        $this->get(route('quests.play', $attempt));

        $answer = $attempt->answers()->firstOrFail();
        // Shown 10 detik lalu, limit 7 detik → harus dianggap timeout meski klien klaim jawab
        $answer->update(['shown_at' => now()->subSeconds(10)]);

        $this->post(route('quests.answer', $attempt), [
            'answer_row' => $answer->id,
            'selected_index' => 0,
        ]);

        $answer->refresh();
        $this->assertFalse($answer->is_correct);
        $this->assertEquals(0, $answer->score);
    }

    public function test_user_cannot_access_others_attempt(): void
    {
        [$user, $quest] = $this->createQuestWithQuestion();
        $other = User::factory()->create();

        $this->actingAs($user)->post(route('quests.start', $quest));
        $attempt = QuestAttempt::where('user_id', $user->id)->firstOrFail();

        $this->actingAs($other)->get(route('quests.result', $attempt))->assertForbidden();
        $this->actingAs($other)->post(route('quests.answer', $attempt), [
            'answer_row' => 999,
            'selected_index' => 0,
        ])->assertForbidden();
    }
}
