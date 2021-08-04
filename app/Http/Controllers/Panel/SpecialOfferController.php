<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUser;
use App\Models\SpecialOffer;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecialOfferController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $webinars = Webinar::select('id', 'title')
            ->where(function ($qu) use ($user) {
                $qu->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })
            ->where('status', 'active')
            ->get();

        $webinarIds = $webinars->pluck('id');

        $query = SpecialOffer::whereIn('webinar_id', $webinarIds);

        if ($request->get('active_discounts', '') == 'on') {
            $query->where('status', 'active');
        }

        $specialOffers = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('panel.special_offers'),
            'specialOffers' => $specialOffers,
            'webinars' => $webinars,
        ];

        return view(getTemplate() . '.panel.marketing.special_offers', $data);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'webinar_id' => 'required',
            'percent' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $activeSpecialOfferForWebinar = Webinar::findOrFail($data["webinar_id"])->activeSpecialOffer();

        if ($activeSpecialOfferForWebinar) {
            return back()->with('msg', 'This Course Has Active Special Offer');
        }

        SpecialOffer::create([
            'creator_id' => auth()->user()->id,
            'name' => $data["name"],
            'webinar_id' => $data["webinar_id"],
            'percent' => $data["percent"],
            'status' => SpecialOffer::$active,
            'created_at' => time(),
            'from_date' => strtotime($data["from_date"]),
            'to_date' => strtotime($data["to_date"]),
        ]);

        return response()->json([
            'code' => 200
        ], 200);
    }

    public function disable(Request $request, $id)
    {
        $user = auth()->user();

        $specialOffer = SpecialOffer::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($specialOffer)) {
            $specialOffer->update([
                'status' => SpecialOffer::$inactive
            ]);

            return response()->json([
                'code' => 200
            ], 200);
        }

        return response()->json([], 422);
    }
}
