<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestionAdminController extends Controller
{
    public function index(Quest $quest): View
    {
        return view('admin.questions.index', [
            'quest' => $quest->load('questions'),
            // Slot yang perlu diisi sesuai jumlah soal yang ditentukan admin.
            'slots' => max(1, $quest->question_count),
        ]);
    }

    /**
     * Simpan SEMUA soal sekaligus sesuai jumlah soal yang ditentukan.
     * Form di halaman admin menampilkan N blok soal (N = question_count).
     */
    public function store(Request $request, Quest $quest)
    {
        $validated = $request->validate([
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question' => ['nullable', 'string', 'max:1000'],
            'questions.*.options' => ['nullable', 'array', 'max:4'],
            'questions.*.options.*' => ['nullable', 'string', 'max:255'],
            'questions.*.correct_index' => ['nullable', 'integer', 'between:0,3'],
        ]);

        $saved = 0;

        DB::transaction(function () use ($quest, $validated, &$saved) {
            foreach ($validated['questions'] as $index => $row) {
                $text = trim((string) ($row['question'] ?? ''));

                // Lewati blok yang belum diisi (diizinkan mengisi bertahap).
                if ($text === '') {
                    continue;
                }

                $options = array_values(array_map(
                    fn ($o) => trim((string) $o),
                    array_filter($row['options'] ?? [], fn ($o) => trim((string) $o) !== '')
                ));

                // Butuh minimal 2 pilihan jawaban.
                if (count($options) < 2) {
                    continue;
                }

                $quest->questions()->updateOrCreate(
                    ['sort_order' => $index],
                    [
                        'question' => $text,
                        'options' => $options,
                        'correct_index' => (int) ($row['correct_index'] ?? 0),
                        'points' => 100,
                    ]
                );

                $saved++;
            }
        });

        return redirect()
            ->route('admin.quests.questions', $quest)
            ->with('success', $saved > 0
                ? $saved.' soal berhasil disimpan.'
                : 'Tidak ada soal yang tersimpan — isi teks soal dan minimal 2 pilihan jawaban.');
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'between:0,3'],
            'points' => ['required', 'integer', 'between:10,1000'],
        ]);

        $question->update($validated);

        return back()->with('success', 'Soal diperbarui.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return back()->with('success', 'Soal dihapus.');
    }
}
