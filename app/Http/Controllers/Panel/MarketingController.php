<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\Webinar;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function promotions()
    {
        $user = auth()->user();
        $webinars = Webinar::select('id', 'title', 'creator_id', 'teacher_id')
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $promotions = Promotion::orderBy('created_at', 'desc')->get();

        $promotionSales = Sale::where('buyer_id', $user->id)
            ->where('type', Sale::$promotion)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('panel.promotions'),
            'promotionSales' => $promotionSales,
            'promotions' => $promotions,
            'webinars' => $webinars
        ];

        return view(getTemplate() . '.panel.marketing.promotions', $data);
    }


    public function payPromotion(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();
        $paymentChannels = PaymentChannel::where('status', 'active')->get();

        $promotion = Promotion::where('id', $data['promotion_id'])->first();

        if (!empty($promotion)) {
            $webinar = Webinar::where('id', $data['webinar_id'])
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })
                ->where('status', 'active')
                ->first();

            if (!empty($webinar)) {
                $financialSettings = getFinancialSettings();
                //$commission = $financialSettings['commission'];
                $tax = $financialSettings['tax'];

                $amount = $promotion->price;

                $taxPrice = $amount * $tax / 100;
                //$commissionPrice = $amount * $commission / 100;

                $order = Order::create([
                    "user_id" => $user->id,
                    "status" => Order::$pending,
                    'tax' => $taxPrice,
                    'commission' => 0,
                    "amount" => $promotion->price,
                    "total_amount" => $amount + $taxPrice,
                    "created_at" => time(),
                ]);

                OrderItem::updateOrCreate([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'webinar_id' => $webinar->id,
                    'promotion_id' => $promotion->id,
                ], [
                    'amount' => $order->amount,
                    'total_amount' => $amount + $taxPrice,
                    'tax' => $tax,
                    'tax_price' => $taxPrice,
                    'commission' => 0,
                    'commission_price' => 0,
                    'created_at' => time(),
                ]);

                $razorpay = false;
                foreach ($paymentChannels as $paymentChannel) {
                    if ($paymentChannel->class_name == 'Razorpay') {
                        $razorpay = true;
                    }
                }

                $data = [
                    'pageTitle' => trans('public.checkout_page_title'),
                    'paymentChannels' => $paymentChannels,
                    'total' => $order->total_amount,
                    'order' => $order,
                    'count' => 1,
                    'userCharge' => $user->getAccountingCharge(),
                    'razorpay' => $razorpay
                ];

                return view(getTemplate() . '.cart.payment', $data);
            }
        }

        abort(404);
    }
}
