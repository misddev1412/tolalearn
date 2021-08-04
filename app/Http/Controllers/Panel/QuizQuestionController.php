<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\QuizzesQuestion;
use App\Models\QuizzesQuestionsAnswer;
use Illuminate\Http\Request;
use App\Models\Quiz;
use Illuminate\Support\Facades\Validator;

class QuizQuestionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->get('ajax');

        $rules = [
            'quiz_id' => 'required|exists:quizzes,id',
            'title' => 'required|max:255',
            'grade' => 'required|integer',
            'type' => 'required',
        ];

        $validate = Validator::make($data, $rules);

        if ($validate->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validate->errors()
            ], 422);
        }

        $user = auth()->user();

        if ($data['type'] == QuizzesQuestion::$multiple and !empty($data['answers'])) {
            $answers = $data['answers'];

            $hasCorrect = false;
            foreach ($answers as $answer) {
                if (isset($answer['correct'])) {
                    $hasCorrect = true;
                }
            }

            if (!$hasCorrect) {
                return response([
                    'code' => 422,
                    'errors' => [
                        'current_answer' => [trans('quiz.current_answer_required')]
                    ],
                ], 422);
            }
        }

        $quiz = Quiz::where('id', $data['quiz_id'])
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($quiz)) {
            $quizQuestion = QuizzesQuestion::create([
                'quiz_id' => $data['quiz_id'],
                'creator_id' => $user->id,
                'title' => $data['title'],
                'grade' => $data['grade'],
                'type' => $data['type'],
                'correct' => $data['correct'] ?? null,
                'created_at' => time()
            ]);

            $quiz->increaseTotalMark($quizQuestion->grade);

            if ($quizQuestion->type == QuizzesQuestion::$multiple and !empty($data['answers'])) {

                foreach ($answers as $answer) {
                    if (!empty($answer['title']) or !empty($answer['file'])) {
                        QuizzesQuestionsAnswer::create([
                            'question_id' => $quizQuestion->id,
                            'creator_id' => $user->id,
                            'title' => $answer['title'],
                            'image' => $answer['file'],
                            'correct' => isset($answer['correct']) ? true : false,
                            'created_at' => time()
                        ]);
                    }
                }
            }

            return response()->json([
                'code' => 200
            ], 200);
        }

        return response()->json([
            'code' => 422
        ], 422);
    }

    public function edit($question_id)
    {
        $user = auth()->user();

        $question = QuizzesQuestion::where('id', $question_id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($question)) {
            $quiz = Quiz::find($question->quiz_id);
            if (!empty($quiz)) {

                $data = [
                    'quiz' => $quiz,
                    'question_edit' => $question
                ];

                if ($question->type == 'multiple') {
                    $html = (string)\View::make(getTemplate() . '.panel.quizzes.modals.multiple_question', $data);
                } else {
                    $html = (string)\View::make(getTemplate() . '.panel.quizzes.modals.descriptive_question', $data);
                }

                return response()->json([
                    'html' => $html
                ], 200);
            }
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $data = $request->get('ajax');

        $rules = [
            'quiz_id' => 'required|exists:quizzes,id',
            'title' => 'required',
            'grade' => 'required',
            'type' => 'required',
        ];

        $validate = Validator::make($data, $rules);

        if ($validate->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validate->errors()
            ], 422);
        }


        $user = auth()->user();

        $quiz = Quiz::where('id', $data['quiz_id'])
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($quiz)) {
            $quizQuestion = QuizzesQuestion::where('id', $id)
                ->where('creator_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->first();

            if (!empty($quizQuestion)) {
                $quiz_total_grade = $quiz->total_mark - $quizQuestion->grade;

                $quizQuestion->update([
                    'quiz_id' => $data['quiz_id'],
                    'creator_id' => $user->id,
                    'title' => $data['title'],
                    'grade' => $data['grade'],
                    'type' => $data['type'],
                    'correct' => $data['correct'] ?? '',
                    'updated_at' => time()
                ]);

                $quiz_total_grade = ($quiz_total_grade > 0 ? $quiz_total_grade : 0) + $data['grade'];
                $quiz->update(['total_mark' => $quiz_total_grade]);;

                if ($data['type'] == QuizzesQuestion::$multiple and !empty($data['answers'])) {
                    $answers = $data['answers'];

                    if ($quizQuestion->type == QuizzesQuestion::$multiple and $answers) {
                        foreach ($answers as $key => $answer) {
                            if (!empty($answer['title']) or !empty($answer['file'])) {
                                $quizQuestionsAnswer = QuizzesQuestionsAnswer::where('id', $key)->first();

                                if (!empty($quizQuestionsAnswer)) {
                                    $quizQuestionsAnswer->update([
                                        'question_id' => $quizQuestion->id,
                                        'creator_id' => $user->id,
                                        'title' => $answer['title'],
                                        'image' => $answer['file'],
                                        'correct' => isset($answer['correct']) ? true : false,
                                        'created_at' => time()
                                    ]);
                                } else {
                                    QuizzesQuestionsAnswer::create([
                                        'question_id' => $quizQuestion->id,
                                        'creator_id' => $user->id,
                                        'title' => $answer['title'],
                                        'image' => $answer['file'],
                                        'correct' => isset($answer['correct']) ? true : false,
                                        'created_at' => time()
                                    ]);
                                }
                            }
                        }
                    }
                }

                return response()->json([
                    'code' => 200
                ], 200);
            }
        }

        return response()->json([
            'code' => 422
        ], 422);
    }

    public function destroy(Request $request, $id)
    {
        QuizzesQuestion::where('id', $id)
            ->where('creator_id', auth()->user()->id)
            ->delete();

        return response()->json([
            'code' => 200
        ], 200);
    }

}
