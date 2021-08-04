<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingTime;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MeetingController extends Controller
{
    public function setting(Request $request)
    {
        $user = auth()->user();

        $meeting = Meeting::where('creator_id', $user->id)
            ->with([
                'meetingTimes'
            ])
            ->first();

        if (empty($meeting)) {
            $meeting = Meeting::create([
                'creator_id' => $user->id,
                'created_at' => time()
            ]);
        }

        $meetingTimes = [];
        foreach ($meeting->meetingTimes->groupBy('day_label') as $day => $meetingTime) {

            $times = 0;
            foreach ($meetingTime as $time) {

                $meetingTimes[$day]["times"][] = $time;

                $explodetime = explode('-', $time->time);
                $times += strtotime($explodetime[1]) - strtotime($explodetime[0]);
            }
            $meetingTimes[$day]["hours_available"] = round($times / 3600, 2);

        }

        $data = [
            'pageTitle' => trans('meeting.meeting_setting_page_title'),
            'meeting' => $meeting,
            'meetingTimes' => $meetingTimes,
        ];

        return view(getTemplate() . '.panel.meeting.settings', $data);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'amount' => 'nullable',
            'discount' => 'nullable',
            'disabled' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting = Meeting::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($meeting)) {
            $meeting->update([
                'amount' => $data['amount'],
                'discount' => $data['discount'],
                'disabled' => !empty($data['disabled']) ? 1 : 0,
            ]);

            return response()->json([
                'code' => 200
            ], 200);
        }

        return response()->json([], 422);
    }

    public function saveTime(Request $request)
    {
        $user = auth()->user();
        $meeting = Meeting::where('creator_id', $user->id)->first();
        $data = $request->all();

        if (!empty($meeting)) {
            $time = $data['time'];
            $day = $data['day'];

            $explodeTime = explode('-', $time);

            if (!empty($explodeTime[0]) and !empty($explodeTime[1])) {
                $start_time = date("H:i", strtotime($explodeTime[0]));
                $end_time = date("H:i", strtotime($explodeTime[1]));

                if (strtotime($end_time) >= strtotime($start_time)) {
                    $checkTime = MeetingTime::where('meeting_id', $meeting->id)
                        ->where('day_label', $data)
                        ->where('time', $time)
                        ->first();

                    if (empty($checkTime)) {
                        MeetingTime::create([
                            'meeting_id' => $meeting->id,
                            'day_label' => $day,
                            'time' => $time,
                            'created_at' => time(),
                        ]);

                        return response()->json([
                            'code' => 200
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'error' => 'contradiction'
                    ], 422);
                }
            }
        }

        return response()->json([], 422);
    }

    public function deleteTime(Request $request)
    {
        $user = auth()->user();
        $meeting = Meeting::where('creator_id', $user->id)->first();
        $data = $request->all();
        $timeIds = $data['time_id'];

        if (!empty($meeting) and !empty($timeIds) and is_array($timeIds)) {

            $meetingTimes = MeetingTime::whereIn('id', $timeIds)
                ->where('meeting_id', $meeting->id)
                ->get();

            if (!empty($meetingTimes)) {
                foreach ($meetingTimes as $meetingTime) {
                    $meetingTime->delete();
                }

                return response()->json([], 200);
            }
        }

        return response()->json([], 422);
    }

    public function temporaryDisableMeetings(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();

        $meeting = Meeting::where('creator_id', $user->id)
            ->first();

        if (!empty($meeting)) {
            $meeting->update([
                'disabled' => (!empty($data['disable']) and $data['disable'] == 'true') ? 1 : 0,
            ]);

            return response()->json([
                'code' => 200
            ], 200);
        }

        return response()->json([], 422);
    }
}
