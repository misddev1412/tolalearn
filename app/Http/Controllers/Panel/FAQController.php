<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Validator;

class FAQController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'title' => 'required|max:64',
            'webinar_id' => 'required',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        FAQ::create([
            'webinar_id' => $data['webinar_id'],
            'creator_id' => $user->id,
            'title' => $data['title'],
            'answer' => $data['answer'],
            'created_at' => time()
        ]);

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'title' => 'required|max:64',
            'webinar_id' => 'required',
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $faq = FAQ::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($faq)) {
            $faq->update([
                'title' => $data['title'],
                'webinar_id' => $data['webinar_id'],
                'answer' => $data['answer'],
                'updated_at' => time()
            ]);

            return response()->json([
                'code' => 200,
            ], 200);
        }

        return response()->json([], 422);
    }

    public function destroy(Request $request, $id)
    {
        $faq = FAQ::where('id', $id)
            ->where('creator_id', auth()->id())
            ->first();

        if (!empty($faq)) {
            $faq->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
