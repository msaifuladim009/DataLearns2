<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Question;

class QuizController extends Controller
{
    // Menampilkan daftar Materi
    public function index()
    {
        $quizzes = Quiz::all();
        return view('admin.quiz', compact('quizzes'));
    }

    //menampilkan halaman quiz
    public function show($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return view('admin.viewquiz', compact('quiz')); 
    }
    //menampilkan submit quiz
    public function submit(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $score = 0;

        // mengulangi setiap pertanyaan dari kuis di database
        foreach ($quiz->questions as $question) {
            // Ambil jawaban yang dipilih oleh siswa
            $selectedAnswer = $request->input('question_'.$question->id);
            
            // cek apakah jawaban yyang telah dipilih sesuai dengan jawaban yang benar
            $answer = $request->input('question_'.$question->id);
            if ($answer == $question->correct_answer) {
                $score++;
            }
        }
        // Simpan skor ke database
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'score' => $score,
        ]);
        return redirect()->route('quiz.show', $quiz->id)->with('success', "You scored $score out of 10.");
    }

    //menampilkan form quiz
    public function create()
    {
        return view('admin.createquiz');
    }
     // Menyimpan kuis baru ke database
     public function store(Request $request)
     {
         $validatedData = $request->validate([
             'title' => 'required|string|max:255',
             'questions.*.question' => 'required|string',
             'questions.*.option_a' => 'required|string',
             'questions.*.option_b' => 'required|string',
             'questions.*.option_c' => 'required|string',
             'questions.*.option_d' => 'required|string',
             'questions.*.correct_answer' => 'required|in:option_a,option_b,option_c,option_d',
         ]);
 
         $quiz = Quiz::create(['title' => $validatedData['title']]);
 
         foreach ($validatedData['questions'] as $question) {
             $quiz->questions()->create($question);
         }
 
         return redirect()->route('quiz.show', $quiz->id)->with('success', 'Quiz successfully created.');
     }
}
