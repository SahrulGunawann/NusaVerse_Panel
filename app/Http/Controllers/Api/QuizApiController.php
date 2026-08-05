<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with('questions');

        if ($request->filled('q')) {
            $search = strtolower($request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(category) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        $quizzes = $query->get()->map(function ($q) {
            return $this->formatQuiz($q);
        });

        return response()->json($quizzes);
    }

    public function show($slug)
    {
        $quiz = Quiz::with('questions')->where('heritage_slug', $slug)->orWhere('id', $slug)->first();

        if (!$quiz) {
            $heritage = \App\Models\Heritage::where('slug', $slug)->orWhere('id', $slug)->first();
            if ($heritage) {
                return response()->json([
                    'id' => 'quiz_' . $heritage->slug,
                    'heritageId' => $heritage->slug,
                    'heritageSlug' => $heritage->slug,
                    'category' => $heritage->category_name ?? 'Sejarah & Budaya',
                    'title' => 'Kuis ' . $heritage->name,
                    'description' => 'Uji pengetahuan dan wawasan Anda tentang ' . $heritage->name . '.',
                    'passingScore' => 70,
                    'questions' => [],
                ]);
            }
            return response()->json(['message' => 'Quiz not found'], 404);
        }

        return response()->json($this->formatQuiz($quiz));
    }

    private function formatQuiz(Quiz $quiz)
    {
        return [
            'id' => $quiz->id,
            'heritageId' => $quiz->heritage_slug,
            'heritageSlug' => $quiz->heritage_slug,
            'category' => $quiz->category,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'passingScore' => $quiz->passing_score,
            'questions' => $quiz->questions->map(function ($qn) {
                return [
                    'id' => $qn->id,
                    'question' => $qn->question,
                    'options' => $qn->options,
                    'correctIndex' => (int)($qn->correct_index ?? 0),
                    'correctAnswerIndex' => (int)($qn->correct_index ?? 0),
                    'explanation' => $qn->explanation ?? '',
                ];
            }),
        ];
    }
}
