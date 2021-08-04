<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\MeetingTime;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\Models\ReserveMeeting;
use App\Models\Setting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function reserve(Request $request)
    {
        $user = auth()->user();
        $timeIds = $request->input('time');
        $day = $request->input('day');
        $day = dateTimeFormat($day, 'Y-m-d');

        if (!empty($timeIds)) {
            $meetingTimes = MeetingTime::whereIn('id', $timeIds)
                ->with('meeting')
                ->get();

            foreach ($meetingTimes as $meetingTime) {
                $reserveMeeting = ReserveMeeting::where('meeting_time_id', $meetingTime->id)
                    ->where('day', $day)
                    ->first();

                if (!empty($reserveMeeting) and $reserveMeeting->locked_at) {
                    $toastData = [
                        'title' => trans('public.request_failed'),
                        'msg' => trans('meeting.locked_time'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }

                if (!empty($reserveMeeting) and $reserveMeeting->reserved_at) {
                    $toastData = [
                        'title' => trans('public.request_failed'),
                        'msg' => trans('meeting.reserved_time'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }

                $hourlyAmount = $meetingTime->meeting->amount;
                $explodetime = explode('-', $meetingTime->time);
                $hours = (strtotime($explodetime[1]) - strtotime($explodetime[0])) / 3600;

                $reserveMeeting = ReserveMeeting::updateOrCreate([
                    'user_id' => $user->id,
                    'meeting_time_id' => $meetingTime->id,
                    'meeting_id' => $meetingTime->meeting_id,
                    'status' => ReserveMeeting::$pending,
                    'day' => $day,
                    'date' => strtotime($day),
                ], [
                    'paid_amount' => (!empty($hourlyAmount) and $hourlyAmount > 0) ? $hourlyAmount * $hours : 0,
                    'discount' => $meetingTime->meeting->discount,
                    'created_at' => time(),
                ]);

                $cart = Cart::where('creator_id', $user->id)
                    ->where('reserve_meeting_id', $reserveMeeting->id)
                    ->first();

                if (empty($cart)) {
                    Cart::create([
                        'creator_id' => $user->id,
                        'reserve_meeting_id' => $reserveMeeting->id,
                        'created_at' => time()
                    ]);
                }
            }

            return redirect('/cart');
        }

        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('meeting.select_time_to_reserve'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

}
