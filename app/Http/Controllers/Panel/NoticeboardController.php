<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Noticeboard;
use App\Models\NoticeboardStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticeboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isOrganization()) {
            $noticeboards = Noticeboard::where('organ_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $data = [
                'pageTitle' => trans('panel.noticeboards'),
                'noticeboards' => $noticeboards
            ];

            return view(getTemplate() . '.panel.noticeboard.index', $data);
        }

        abort(404);
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->isOrganization()) {
            $data = [
                'pageTitle' => trans('panel.new_noticeboard'),
            ];

            return view(getTemplate() . '.panel.noticeboard.create', $data);
        }

        abort(404);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->isOrganization()) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => 'required|string',
                'type' => 'required',
                'message' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            Noticeboard::create([
                'organ_id' => $user->id,
                'type' => $data['type'],
                'sender' => $user->full_name,
                'title' => $data['title'],
                'message' => $data['message'],
                'created_at' => time()
            ]);

            return response()->json([
                'code' => 200,
            ]);
        }

        abort(404);
    }

    public function edit($noticeboard_id)
    {
        $user = auth()->user();

        if ($user->isOrganization()) {
            $noticeboard = Noticeboard::where('organ_id', $user->id)
                ->where('id', $noticeboard_id)
                ->first();

            if (!empty($noticeboard)) {
                $data = [
                    'pageTitle' => trans('panel.noticeboards'),
                    'noticeboard' => $noticeboard
                ];

                return view(getTemplate() . '.panel.noticeboard.create', $data);
            }
        }

        abort(404);
    }

    public function update(Request $request, $noticeboard_id)
    {
        $user = auth()->user();

        if ($user->isOrganization()) {
            $noticeboard = Noticeboard::where('organ_id', $user->id)
                ->where('id', $noticeboard_id)
                ->first();

            if (!empty($noticeboard)) {
                $data = $request->all();

                $validator = Validator::make($data, [
                    'title' => 'required|string',
                    'type' => 'required',
                    'message' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'code' => 422,
                        'errors' => $validator->errors()
                    ], 422);
                }

                $noticeboard->update([
                    'type' => $data['type'],
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'created_at' => time()
                ]);

                NoticeboardStatus::where('noticeboard_id', $noticeboard->id)->delete();

                return response()->json([
                    'code' => 200,
                ]);
            }
        }

        return response()->json([], 422);
    }

    public function delete($noticeboard_id)
    {
        $user = auth()->user();

        if ($user->isOrganization()) {
            $noticeboard = Noticeboard::where('organ_id', $user->id)
                ->where('id', $noticeboard_id)
                ->first();

            if (!empty($noticeboard)) {
                $noticeboard->delete();

                return response()->json([
                    'code' => 200,
                ]);
            }
        }

        return response()->json([], 422);
    }

    public function saveStatus($noticeboard_id)
    {
        $user = auth()->user();

        $status = NoticeboardStatus::where('user_id', $user->id)
            ->where('noticeboard_id', $noticeboard_id)
            ->first();

        if (empty($status)) {
            NoticeboardStatus::create([
                'user_id' => $user->id,
                'noticeboard_id' => $noticeboard_id,
                'seen_at' => time()
            ]);
        }

        return response()->json([], 200);
    }

}
