<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizzesQuestion;
use App\Models\QuizzesQuestionsAnswer;
use Illuminate\Http\Request;
use App\Models\Quiz;

class QuizQuestionController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'quiz_id' => 'required|exists:quizzes,id',
            'title' => 'required|max:255',
            'grade' => 'required|integer',
            'type' => 'required',
        ]);

        $data = $request->all();

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

        $quiz = Quiz::where('id', $data['quiz_id'])->first();

        if (!empty($quiz)) {
            $creator = $quiz->creator;

            $quizQuestion = QuizzesQuestion::create([
                'quiz_id' => $data['quiz_id'],
                'creator_id' => $creator->id,
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
                            'creator_id' => $creator->id,
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
        $question = QuizzesQuestion::where('id', $question_id)->first();

        if (!empty($question)) {
            $quiz = Quiz::find($question->quiz_id);

            if (!empty($quiz)) {

                $data = [
                    'quiz' => $quiz,
                    'question_edit' => $question
                ];

                if ($question->type == 'multiple') {
                    $html = (string)\View::make('admin.quizzes.modals.multiple_question', $data);
                } else {
                    $html = (string)\View::make('admin.quizzes.modals.descriptive_question', $data);
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
        $this->validate($request, [
            'quiz_id' => 'required|exists:quizzes,id',
            'title' => 'required',
            'grade' => 'required',
            'type' => 'required',
        ]);

        $data = $request->all();

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

        $quiz = Quiz::where('id', $request->input('quiz_id'))->first();

        if (!empty($quiz)) {
            $creator = $quiz->creator;

            $quizQuestion = QuizzesQuestion::where('id', $id)
                ->where('creator_id', $creator->id)
                ->where('quiz_id', $quiz->id)
                ->first();

            if (!empty($quizQuestion)) {
                $quiz->decreaseTotalMark($quizQuestion->grade);

                $quizQuestion->update([
                    'quiz_id' => $request->input('quiz_id'),
                    'creator_id' => $creator->id,
                    'title' => $request->input('title'),
                    'grade' => $request->input('grade'),
                    'type' => $request->input('type'),
                    'correct' => $request->input('correct'),
                    'updated_at' => time()
                ]);

                $quiz->increaseTotalMark($quizQuestion->grade);


                if ($quizQuestion->type == QuizzesQuestion::$multiple and $answers) {
                    foreach ($answers as $key => $answer) {
                        if (!empty($answer['title']) or !empty($answer['file'])) {
                            $quizQuestionsAnswer = QuizzesQuestionsAnswer::where('id', $key)->first();

                            if (!empty($quizQuestionsAnswer)) {
                                $quizQuestionsAnswer->update([
                                    'question_id' => $quizQuestion->id,
                                    'creator_id' => $creator->id,
                                    'title' => $answer['title'],
                                    'image' => $answer['file'],
                                    'correct' => isset($answer['correct']) ? true : false,
                                    'created_at' => time()
                                ]);
                            } else {
                                QuizzesQuestionsAnswer::create([
                                    'question_id' => $quizQuestion->id,
                                    'creator_id' => $creator->id,
                                    'title' => $answer['title'],
                                    'image' => $answer['file'],
                                    'correct' => isset($answer['correct']) ? true : false,
                                    'created_at' => time()
                                ]);
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
            ->delete();

        return redirect()->back();
    }

}
