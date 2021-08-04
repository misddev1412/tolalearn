<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Webinar;
use App\Sessions\Zoom;
use Illuminate\Http\Request;
use Validator;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required|max:64',
            'date' => 'required|date',
            'duration' => 'required|numeric',
            'link' => ($data['session_api'] == 'local') ? 'required|url' : 'nullable',
            'api_secret' => ($data['session_api'] != 'zoom') ? 'required' : 'nullable',
            'moderator_secret' => ($data['session_api'] == 'big_blue_button') ? 'required' : 'nullable',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])->first();

            if (!empty($webinar)) {
                $teacher = $webinar->creator;

                if (!empty($data['session_api']) and $data['session_api'] == 'zoom' and (empty($teacher->zoomApi) or empty($teacher->zoomApi->jwt_token))) {
                    $error = [
                        'zoom-not-complete-alert' => []
                    ];

                    return response([
                        'code' => 422,
                        'errors' => $error,
                    ], 422);
                }

                if (strtotime($data['date']) < $webinar->start_date) {
                    $error = [
                        'date' => [trans('webinars.session_date_must_larger_webinar_start_date', ['start_date' => dateTimeFormat($webinar->start_date, 'Y-m-d')])]
                    ];

                    return response([
                        'code' => 422,
                        'errors' => $error,
                    ], 422);
                }

                $session = Session::create([
                    'creator_id' => $teacher->id,
                    'webinar_id' => $data['webinar_id'],
                    'title' => $data['title'],
                    'date' => strtotime($data['date']),
                    'duration' => $data['duration'],
                    'link' => $data['link'] ?? '',
                    'session_api' => $data['session_api'],
                    'api_secret' => $data['api_secret'] ?? '',
                    'moderator_secret' => $data['moderator_secret'] ?? '',
                    'description' => $data['description'],
                    'created_at' => time()
                ]);

                if ($data['session_api'] == 'big_blue_button') {
                    $this->handleBigBlueButtonApi($session, $teacher);
                }

                if ($data['session_api'] == 'zoom') {
                    return $this->handleZoomApi($session, $teacher);
                }

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        return response()->json([], 422);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $session = Session::where('id', $id)->first();

        if (!empty($session)) {
            $session->link = $session->getJoinLink();

            return response()->json([
                'session' => $session
            ], 200);
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->all();
        $session = Session::where('id', $id)
            ->first();

        $session_api = !empty($data['session_api']) ? $data['session_api'] : $session->session_api;

        $validator = Validator::make($data, [
            'title' => 'required|max:64',
            'date' => ($session_api == 'local') ? 'required|date' : 'nullable',
            'duration' => ($session_api == 'local') ? 'required|numeric' : 'nullable',
            'link' => ($session_api == 'local') ? 'required|url' : 'nullable',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $webinar = Webinar::where('id', $data['webinar_id'])->first();

        if (!empty($webinar)) {
            if (!empty($session)) {
                $sessionDate = !empty($data['date']) ? strtotime($data['date']) : $session->date;

                if ($sessionDate < $webinar->start_date) {
                    $error = [
                        'date' => [trans('webinars.session_date_must_larger_webinar_start_date', ['start_date' => dateTimeFormat($webinar->start_date, 'Y-m-d')])]
                    ];

                    return response([
                        'code' => 422,
                        'errors' => $error,
                    ], 422);
                }

                $session->update([
                    'title' => $data['title'],
                    'date' => $sessionDate,
                    'duration' => $data['duration'] ?? $session->duration,
                    'link' => $data['link'] ?? $session->link,
                    'session_api' => $session_api,
                    'api_secret' => $data['api_secret'] ?? $session->api_secret,
                    'description' => $data['description'],
                    'updated_at' => time()
                ]);

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        return response()->json([], 422);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        Session::find($id)->delete();

        return redirect()->back();
    }

    private function handleZoomApi($session, $user)
    {
        $zoom = new Zoom();

        if (!empty($user->zoomApi) and !empty($user->zoomApi->jwt_token)) {
            $zoomUser = $zoom->getUserByJwt($user->zoomApi->jwt_token);

            if (!empty($zoomUser)) {
                $meeting = $zoom->storeUserMeeting($session, $zoomUser, $user->zoomApi->jwt_token);

                if (!empty($meeting)) {
                    $session->update([
                        'link' => $meeting['join_url'],
                        'zoom_start_link' => $meeting['start_url'],
                    ]);

                    return response()->json([
                        'code' => 200,
                    ], 200);
                }
            }
        }

        $session->delete();

        return response()->json([
            'code' => 422,
            'status' => 'zoom_jwt_token_invalid'
        ], 422);
    }

    private function handleBigBlueButtonApi($session, $user)
    {
        $createMeeting = \Bigbluebutton::initCreateMeeting([
            'meetingID' => $session->id,
            'meetingName' => $session->title,
            'attendeePW' => $session->api_secret,
            'moderatorPW' => $session->moderator_secret,
        ]);

        $createMeeting->setDuration($session->duration);
        \Bigbluebutton::create($createMeeting);

        return true;
    }
}
