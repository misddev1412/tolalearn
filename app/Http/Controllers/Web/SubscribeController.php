<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Sale;
use App\Models\Subscribe;
use App\Models\SubscribeUse;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    public function apply(Request $request, $webinarSlug)
    {
        $user = auth()->user();

        $subscribe = Subscribe::getActiveSubscribe($user->id);

        if (!$subscribe) {
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('site.you_dont_have_active_subscribe'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }

        $webinar = Webinar::where('slug', $webinarSlug)
            ->where('status', 'active')
            ->where('subscribe', true)
            ->first();

        if (!empty($webinar)) {

            $checkCourseForSale = checkCourseForSale($webinar, $user);

            if ($checkCourseForSale != 'ok') {
                return $checkCourseForSale;
            }

            $financialSettings = getFinancialSettings();
            $commission = $financialSettings['commission'];

            $pricePerSubscribe = $subscribe->price / $subscribe->usable_count;
            $commissionPrice = $pricePerSubscribe * $commission / 100;

            $admin = User::getAdmin();

            $sale = Sale::create([
                'buyer_id' => $user->id,
                'seller_id' => $webinar->creator_id,
                'webinar_id' => $webinar->id,
                'type' => Sale::$webinar,
                'payment_method' => Sale::$subscribe,
                'amount' => 0,
                'total_amount' => 0,
                'created_at' => time(),
            ]);

            Accounting::create([
                'user_id' => $webinar->creator_id,
                'amount' => $pricePerSubscribe - $commissionPrice,
                'webinar_id' => $webinar->id,
                'type' => Accounting::$addiction,
                'type_account' => Accounting::$income,
                'description' => trans('public.paid_form_subscribe'),
                'created_at' => time()
            ]);

            Accounting::create([
                'system' => true,
                'user_id' => $admin->id,
                'amount' => $pricePerSubscribe - $commissionPrice,
                'webinar_id' => $webinar->id,
                'type' => Accounting::$deduction,
                'type_account' => Accounting::$asset,
                'description' => trans('public.paid_form_subscribe'),
                'created_at' => time()
            ]);

            SubscribeUse::create([
                'user_id' => $user->id,
                'subscribe_id' => $subscribe->id,
                'webinar_id' => $webinar->id,
                'sale_id' => $sale->id,
            ]);

            $toastData = [
                'title' => trans('cart.success_pay_title'),
                'msg' => trans('cart.success_pay_msg_subscribe'),
                'status' => 'success'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }
}
