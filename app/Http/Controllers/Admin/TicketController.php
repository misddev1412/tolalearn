<?php

namespace App\Http\Controllers\Admin;

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
        $this->authorize('admin_webinars_edit');

        $this->validate($request, [
            'title' => 'required|max:64',
            'date' => 'required',
            'discount' => 'required',
            'capacity' => 'nullable',
        ]);


        $data = $request->all();

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::findOrFail($data['webinar_id']);

            $date = $data['date'];
            $date = explode(' - ', $date);

            Ticket::create([
                'creator_id' => $webinar->creator_id,
                'webinar_id' => $webinar->id,
                'title' => $data['title'],
                'start_date' => strtotime($date[0]),
                'end_date' => strtotime($date[1]),
                'discount' => $data['discount'],
                'capacity' => $data['capacity'],
                'created_at' => time()
            ]);

        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');


        $ticket = Ticket::select('capacity', 'discount', 'end_date', 'start_date', 'title')
            ->where('id', $id)
            ->first();

        if (!empty($ticket)) {
            return response()->json([
                'ticket' => $ticket
            ], 200);
        }


        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $this->validate($request, [
            'title' => 'required|max:64',
            'date' => 'required',
            'discount' => 'required|integer',
            'capacity' => 'nullable|integer',
        ]);
        $data = $request->all();

        $ticket = Ticket::find($id);

        $date = $data['date'];
        $date = explode(' - ', $date);

        $ticket->update([
            'title' => $data['title'],
            'start_date' => strtotime($date[0]),
            'end_date' => strtotime($date[1]),
            'discount' => $data['discount'],
            'capacity' => $data['capacity'],
            'updated_at' => time()
        ]);

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        Ticket::find($id)->delete();

        return back();
    }
}
