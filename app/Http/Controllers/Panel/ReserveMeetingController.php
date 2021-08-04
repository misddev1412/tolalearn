<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingTime;
use App\Models\Quiz;
use App\Models\ReserveMeeting;
use App\Models\Role;
use App\User;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ReserveMeetingController extends Controller
{
    public function reservation(Request $request)
    {
        $user = auth()->user();
        $reserveMeetingsQuery = ReserveMeeting::where('user_id', $user->id)
            ->whereHas('sale');

        $openReserveCount = deepClone($reserveMeetingsQuery)->where('status', \App\models\ReserveMeeting::$open)->count();
        $totalReserveCount = deepClone($reserveMeetingsQuery)->count();

        $meetingIds = deepClone($reserveMeetingsQuery)->pluck('meeting_id')->toArray();
        $teacherIds = Meeting::whereIn('id', array_unique($meetingIds))
            ->pluck('creator_id')
            ->toArray();
        $instructors = User::select('id', 'full_name')
            ->whereIn('id', array_unique($teacherIds))
            ->get();


        $reserveMeetingsQuery = $this->filters($reserveMeetingsQuery, $request);
        $reserveMeetingsQuery = $reserveMeetingsQuery->with([
            'meetingTime',
            'meeting' => function ($query) {
                $query->with([
                    'creator' => function ($query) {
                        $query->select('id', 'full_name', 'avatar', 'email');
                    }
                ]);
            },
            'user' => function ($query) {
                $query->select('id', 'full_name', 'avatar', 'email');
            },
            'sale'
        ]);

        $reserveMeetings = $reserveMeetingsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $activeMeetingTimeIds = ReserveMeeting::where('user_id', $user->id)->where('status', ReserveMeeting::$open)->pluck('meeting_time_id');

        $activeMeetingTimes = MeetingTime::whereIn('id', $activeMeetingTimeIds)->get();

        $activeHoursCount = 0;
        foreach ($activeMeetingTimes as $time) {
            $explodetime = explode('-', $time->time);
            $activeHoursCount += strtotime($explodetime[1]) - strtotime($explodetime[0]);
        }

        $data = [
            'pageTitle' => trans('meeting.meeting_list_page_title'),
            'instructors' => $instructors,
            'reserveMeetings' => $reserveMeetings,
            'openReserveCount' => $openReserveCount,
            'totalReserveCount' => $totalReserveCount,
            'activeHoursCount' => round($activeHoursCount / 3600, 2),
        ];

        return view(getTemplate() . '.panel.meeting.reservation', $data);
    }

    public function requests(Request $request)
    {
        $meetingIds = Meeting::where('creator_id', auth()->user()->id)->pluck('id');

        $reserveMeetingsQuery = ReserveMeeting::whereIn('meeting_id', $meetingIds)
            ->whereHas('sale');

        $pendingReserveCount = deepClone($reserveMeetingsQuery)->where('status', \App\models\ReserveMeeting::$pending)->count();
        $totalReserveCount = deepClone($reserveMeetingsQuery)->count();
        $sumReservePaid = deepClone($reserveMeetingsQuery)->sum('paid_amount');

        $userIdsReservedTime = deepClone($reserveMeetingsQuery)->pluck('user_id')->toArray();
        $usersReservedTimes = User::select('id', 'full_name')
            ->whereIn('id', array_unique($userIdsReservedTime))
            ->get();

        $reserveMeetingsQuery = $this->filters(deepClone($reserveMeetingsQuery), $request);
        $reserveMeetingsQuery = $reserveMeetingsQuery->with(['meetingTime', 'meeting', 'user' => function ($query) {
            $query->select('id', 'full_name', 'avatar', 'email');
        }]);

        $reserveMeetings = $reserveMeetingsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $activeMeetingTimeIds = ReserveMeeting::whereIn('meeting_id', $meetingIds)
            ->where('status', ReserveMeeting::$pending)
            ->pluck('meeting_time_id')
            ->toArray();

        $meetingTimesCount = array_count_values($activeMeetingTimeIds);
        $activeMeetingTimes = MeetingTime::whereIn('id', $activeMeetingTimeIds)->get();

        $activeHoursCount = 0;
        foreach ($activeMeetingTimes as $time) {
            $explodetime = explode('-', $time->time);
            $hour = strtotime($explodetime[1]) - strtotime($explodetime[0]);

            if (!empty($meetingTimesCount) and is_array($meetingTimesCount) and !empty($meetingTimesCount[$time->id])) {
                $hour = $hour * $meetingTimesCount[$time->id];
            }

            $activeHoursCount += $hour;
        }

        $data = [
            'pageTitle' => trans('meeting.meeting_requests_page_title'),
            'reserveMeetings' => $reserveMeetings,
            'pendingReserveCount' => $pendingReserveCount,
            'totalReserveCount' => $totalReserveCount,
            'sumReservePaid' => $sumReservePaid,
            'activeHoursCount' => $activeHoursCount,
            'usersReservedTimes' => $usersReservedTimes,
        ];

        return view(getTemplate() . '.panel.meeting.requests', $data);
    }

    public function filters($query, $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $day = $request->get('day');
        $instructor_id = $request->get('instructor_id');
        $student_id = $request->get('student_id');
        $status = $request->get('status');
        $openMeetings = $request->get('open_meetings');

        if (!empty($from) and !empty($to)) {
            $from = strtotime($from);
            $to = strtotime($to);

            $query->whereBetween('created_at', [$from, $to]);
        } else {
            if (!empty($from)) {
                $from = strtotime($from);
                $query->where('created_at', '>=', $from);
            }

            if (!empty($to)) {
                $to = strtotime($to);

                $query->where('created_at', '<', $to);
            }
        }

        if (!empty($day) and $day != 'all') {
            $meetingTimeIds = $query->pluck('meeting_time_id');
            $meetingTimeIds = MeetingTime::whereIn('id', $meetingTimeIds)
                ->where('day_label', $day)
                ->pluck('id');

            $query->whereIn('meeting_time_id', $meetingTimeIds);
        }

        if (!empty($instructor_id) and $instructor_id != 'all') {

            $meetingsIds = Meeting::where('creator_id', $instructor_id)
                ->where('disabled', false)
                ->pluck('id')
                ->toArray();

            $query->whereIn('meeting_id', $meetingsIds);
        }

        if (!empty($student_id) and $student_id != 'all') {
            $query->where('user_id', $student_id);
        }


        if (!empty($status) and $status != 'All') {
            $query->where('status', strtolower($status));
        }

        if (!empty($openMeetings) and $openMeetings == 'on') {
            $query->where('status', 'open');
        }

        return $query;
    }

    public function finish($id)
    {
        $user = auth()->user();

        $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id');

        $ReserveMeeting = ReserveMeeting::where('id', $id)
            ->where(function ($query) use ($user, $meetingIds) {
                $query->where('user_id', $user->id)
                    ->orWhereIn('meeting_id', $meetingIds);
            })
            ->with(['meeting', 'user'])
            ->first();

        if (!empty($ReserveMeeting)) {
            $ReserveMeeting->update([
                'status' => ReserveMeeting::$finished
            ]);

            $notifyOptions = [
                '[student.name]' => $ReserveMeeting->user->full_name,
                '[instructor.name]' => $ReserveMeeting->meeting->creator->full_name,
                '[time.date]' => $ReserveMeeting->day,
            ];
            sendNotification('meeting_finished', $notifyOptions, $ReserveMeeting->user_id);
            sendNotification('meeting_finished', $notifyOptions, $ReserveMeeting->meeting->creator_id);

            return response()->json([
                'code' => 200
            ], 200);
        }

        return response()->json([], 422);
    }

    public function createLink(Request $request)
    {
        $this->validate($request, [
            'link' => 'required|url'
        ]);

        $link = $request->input('link');
        $ReserveMeeting = ReserveMeeting::where('id', $request->input('item_id'))->first();

        if (!empty($ReserveMeeting) and !empty($ReserveMeeting->meeting)) {
            $ReserveMeeting->update([
                'link' => $link,
                'password' => $request->input('password'),
                'status' => ReserveMeeting::$open,
            ]);

            $notifyOptions = [
                '[link]' => $link,
                '[instructor.name]' => $ReserveMeeting->meeting->creator->full_name,
                '[time.date]' => $ReserveMeeting->day,
            ];
            sendNotification('new_appointment_link', $notifyOptions, $ReserveMeeting->user_id);
        }

        return response()->json([
            'code' => 200
        ], 200);
    }

    public function join(Request $request, $id)
    {
        $ReserveMeeting = ReserveMeeting::where('id', $id)->first();

        return Redirect::away($ReserveMeeting->link);
    }
}
