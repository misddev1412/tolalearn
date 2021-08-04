<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Session;
use App\Models\SessionRemind;
use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function index(Request $request, $methodName)
    {
        return $this->$methodName($request);
    }

    public function sendSessionsReminder($request)
    {
        $buyersCount = 0;
        $hour = getGeneralSettings('webinar_reminder_schedule') ?? 1;
        $time = time();
        $hoursLater = $time + ($hour * 60 * 60);

        $sessions = Session::where('date', '>=', $time)
            ->where('date', '<=', $hoursLater)
            ->with([
                'webinar' => function ($query) {
                    $query->select('id', 'title');
                }
            ])
            ->get();


        foreach ($sessions as $session) {
            $webinar = $session->webinar;

            $buyers = Sale::whereNull('refund_at')
                ->where('webinar_id', $session->webinar_id)
                ->pluck('buyer_id')
                ->toArray();

            $notifyOptions = [
                '[c.title]' => $webinar->title,
                '[time.date]' => dateTimeFormat($session->date, 'd F Y , H:i'),
            ];

            $buyersCount = count($buyers);

            if (count($buyers)) {
                foreach ($buyers as $buyer) {
                    $check = SessionRemind::where('session_id', $session->id)
                        ->where('user_id', $buyer)
                        ->first();

                    if (empty($check)) {
                        sendNotification('webinar_reminder', $notifyOptions, $buyer); // consultant

                        SessionRemind::create([
                            'session_id' => $session->id,
                            'user_id' => $buyer,
                            'created_at' => time()
                        ]);
                    }
                }
            }

            $check = SessionRemind::where('session_id', $session->id)
                ->where('user_id', $session->creator_id)
                ->first();

            if (empty($check)) {
                sendNotification('webinar_reminder', $notifyOptions, $session->creator_id); // consultant

                SessionRemind::create([
                    'session_id' => $session->id,
                    'user_id' => $session->creator_id,
                    'created_at' => time()
                ]);
            }
        }

        return response()->json([
            'sessions_count' => count($sessions),
            'buyers' => $buyersCount,
            'message' => "Notifications were sent for sessions starting from (" . dateTimeFormat($time, 'd F Y, H:i') . ')  to  (' . dateTimeFormat($hoursLater, 'd F Y, H:i') . ')'
        ]);
    }
}
