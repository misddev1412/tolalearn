<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Session;
use App\Models\Webinar;
use Illuminate\Http\Request;
use App\Sessions\Zoom;
use Validator;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

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

        if (!empty($data['session_api']) and $data['session_api'] == 'zoom' and (empty($user->zoomApi) or empty($user->zoomApi->jwt_token))) {
            $error = [
                'zoom-not-complete-alert' => []
            ];

            return response([
                'code' => 422,
                'errors' => $error,
            ], 422);
        }

        $webinar = Webinar::where('id', $data['webinar_id'])
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

        if (!empty($webinar)) {

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
                'creator_id' => $user->id,
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
                $this->handleBigBlueButtonApi($session, $user);
            }

            if ($data['session_api'] == 'zoom') {
                return $this->handleZoomApi($session, $user);
            }

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

        $session = Session::where('id', $id)
            ->where('creator_id', $user->id)
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

        $webinar = Webinar::where('id', $data['webinar_id'])
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->first();

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
        $session = Session::where('id', $id)
            ->where('creator_id', auth()->id())
            ->first();

        if (!empty($session)) {
            $session->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
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

    public function joinToBigBlueButton($id)
    {
        $session = Session::where('id', $id)->first();

        if (!empty($session)) {
            $user = auth()->user();

            if ($user->id == $session->creator_id) {
                $url = \Bigbluebutton::join([
                    'meetingID' => $session->id,
                    'userName' => $user->full_name,
                    'password' => $session->moderator_secret
                ]);

                if ($url) {
                    return redirect($url);
                }
            } else {
                $checkSale = Sale::where('buyer_id', $user->id)
                    ->where('webinar_id', $session->webinar_id)
                    ->where('type', 'webinar')
                    ->whereNull('refund_at')
                    ->first();

                if (!empty($checkSale)) {

                    $url = \Bigbluebutton::join([
                        'meetingID' => $session->id,
                        'userName' => $user->full_name,
                        'password' => $session->api_secret
                    ]);

                    if ($url) {
                        return redirect($url);
                    }
                }
            }
        }

        abort(404);
    }
}
