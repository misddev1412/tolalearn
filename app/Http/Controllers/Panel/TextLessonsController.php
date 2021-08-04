<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\TextLesson;
use App\Models\TextLessonAttachment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class TextLessonsController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'title' => 'required',
            'study_time' => 'required|numeric',
            'image' => 'required',
            'accessibility' => 'required|' . Rule::in(File::$accessibility),
            'summary' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $lessonsCount = TextLesson::where('creator_id', $user->id)
            ->where('webinar_id', $data['webinar_id'])
            ->count();

        $textLesson = TextLesson::create([
            'creator_id' => $user->id,
            'webinar_id' => $data['webinar_id'],
            'title' => $data['title'],
            'image' => $data['image'],
            'study_time' => $data['study_time'],
            'accessibility' => $data['accessibility'],
            'order' => $lessonsCount + 1,
            'summary' => $data['summary'],
            'content' => $data['content'],
            'created_at' => time(),
        ]);

        $attachments = $data['attachments'];
        $this->saveAttachments($textLesson, $attachments);

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'title' => 'required',
            'study_time' => 'required|numeric',
            'image' => 'required',
            'accessibility' => 'required|' . Rule::in(File::$accessibility),
            'summary' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $textLesson = TextLesson::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($textLesson)) {
            $textLesson->update([
                'title' => $data['title'],
                'image' => $data['image'],
                'study_time' => $data['study_time'],
                'accessibility' => $data['accessibility'],
                'summary' => $data['summary'],
                'content' => $data['content'],
                'updated_at' => time(),
            ]);

            $textLesson->attachments()->delete();

            $attachments = $data['attachments'];
            $this->saveAttachments($textLesson, $attachments);

            return response()->json([
                'code' => 200,
            ], 200);
        }

        return response()->json([], 422);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $textLesson = TextLesson::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($textLesson)) {
            $textLesson->delete();
        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    private function saveAttachments($textLesson, $attachments)
    {
        if (!empty($attachments)) {

            if (!is_array($attachments)) {
                $attachments = [$attachments];
            }

            foreach ($attachments as $attachment_id) {
                if (!empty($attachment_id)) {
                    TextLessonAttachment::create([
                        'text_lesson_id' => $textLesson->id,
                        'file_id' => $attachment_id,
                        'created_at' => time(),
                    ]);
                }
            }
        }
    }
}


