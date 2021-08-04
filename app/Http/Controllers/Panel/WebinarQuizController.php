<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\WebinarQuizzes;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Validator;

class WebinarQuizController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'quiz_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $quiz = Quiz::where('id', $data['quiz_id'])
            ->where('creator_id', $user->id)
            ->where('webinar_id', null)
            ->first();

        if (!empty($quiz)) {
            $quiz->webinar_id = $data['webinar_id'];
            $quiz->save();

            return response()->json([
                'code' => 200,
            ], 200);
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'quiz_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $quiz = Quiz::where('id', $data['quiz_id'])
            ->where('creator_id', $user->id)
            ->where('webinar_id', null)
            ->first();

        if (!empty($quiz)) {
            $quiz->webinar_id = $data['webinar_id'];
            $quiz->save();

            return response()->json([
                'code' => 200,
            ], 200);
        }

        return response()->json([], 422);
    }
}
