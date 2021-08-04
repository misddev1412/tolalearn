<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Webinar;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Validator;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })
                ->with('tickets')
                ->first();

            if (!empty($webinar)) {
                $sumTicketsCapacities = $webinar->tickets->sum('capacity');
                $capacity = $webinar->capacity - $sumTicketsCapacities;

                $validator = Validator::make($data, [
                    'webinar_id' => 'required',
                    'title' => 'required|max:64',
                    'sub_title' => 'nullable',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                    'discount' => 'required|integer|between:1,100',
                    'capacity' => $webinar->isWebinar() ? 'nullable|numeric|min:1|max:' . $capacity : 'nullable',
                ]);

                if ($validator->fails()) {
                    return response([
                        'code' => 422,
                        'errors' => $validator->errors(),
                    ], 422);
                }

                if (empty($data['capacity']) and $webinar->isWebinar()) {
                    $data['capacity'] = $capacity;
                }

                Ticket::create([
                    'creator_id' => $user->id,
                    'webinar_id' => $data['webinar_id'],
                    'title' => $data['title'],
                    'start_date' => strtotime($data['start_date']),
                    'end_date' => strtotime($data['end_date']),
                    'discount' => $data['discount'],
                    'capacity' => $data['capacity'],
                    'created_at' => time()
                ]);

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $data = $request->get('ajax')[$id];

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })
                ->with('tickets')
                ->first();

            if (!empty($webinar)) {
                $ticket = Ticket::where('id', $id)
                    ->where('webinar_id', $webinar->id)
                    ->where('creator_id', $user->id)
                    ->first();

                $sumTicketsCapacities = $webinar->tickets->sum('capacity') - $ticket->capacity;
                $capacity = $webinar->capacity - $sumTicketsCapacities;

                $validator = Validator::make($data, [
                    'webinar_id' => 'required',
                    'title' => 'required|max:64',
                    'sub_title' => 'nullable',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date',
                    'discount' => 'required|integer|between:1,100',
                    'capacity' => $webinar->isWebinar() ? 'nullable|numeric|min:1|max:' . $capacity : 'nullable',
                ]);

                if ($validator->fails()) {
                    return response([
                        'code' => 422,
                        'errors' => $validator->errors(),
                    ], 422);
                }

                if (empty($data['capacity']) and $webinar->isWebinar()) {
                    $data['capacity'] = $capacity;
                }

                if (!empty($ticket)) {
                    $ticket->update([
                        'title' => $data['title'],
                        'start_date' => strtotime($data['start_date']),
                        'end_date' => strtotime($data['end_date']),
                        'discount' => $data['discount'],
                        'capacity' => $data['capacity'],
                        'updated_at' => time()
                    ]);

                    return response()->json([
                        'code' => 200,
                    ], 200);
                }
            }
        }

        return response()->json([], 422);
    }

    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::where('id', $id)
            ->where('creator_id', auth()->id())
            ->first();

        if (!empty($ticket)) {
            $ticket->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
