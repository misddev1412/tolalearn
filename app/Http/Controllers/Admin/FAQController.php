<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\Webinar;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_webinars_edit');

        $this->validate($request, [
            'title' => 'required|max:64',
            'webinar_id' => 'required',
            'answer' => 'required',
        ]);

        $data = $request->all();

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::findOrFail($data['webinar_id']);

            FAQ::create([
                'creator_id' => $webinar->creator_id,
                'webinar_id' => $webinar->id,
                'title' => $request->input('title'),
                'answer' => $request->input('answer')
            ]);

        }
        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function description(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $faq = FAQ::select('answer')
            ->where('id', $id)
            ->first();

        if (!empty($faq)) {
            return response()->json([
                'data' => $faq
            ], 200);
        }

        return response()->json([], 422);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $faq = FAQ::select('title', 'answer')
            ->where('id', $id)
            ->first();

        if (!empty($faq)) {
            return response()->json([
                'faq' => $faq
            ], 200);
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $this->validate($request, [
            'title' => 'required|max:64',
            'webinar_id' => 'required',
            'answer' => 'required',
        ]);

        $faq = FAQ::find($id);
        $faq->update([
            'title' => $request->input('title'),
            'webinar_id' => $request->input('webinar_id'),
            'answer' => $request->input('answer')
        ]);

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        FAQ::find($id)->delete();

        return redirect()->back();
    }
}
