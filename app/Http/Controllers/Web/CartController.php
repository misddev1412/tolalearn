<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Discount;
use App\Models\DiscountUser;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\Models\ReserveMeeting;
use App\Models\Setting;
use App\Models\Webinar;
use App\PaymentChannels\ChannelManager;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $carts = Cart::where('creator_id', $user->id)
            ->with([
                'user',
                'webinar',
                'reserveMeeting' => function ($query) {
                    $query->with([
                        'meeting',
                        'meetingTime'
                    ]);
                },
                'ticket'
            ])
            ->get();

        if (!empty($carts) and !$carts->isEmpty()) {
            $calculate = $this->calculatePrice($carts, $user);

            if (!empty($calculate)) {
                $data = [
                    'pageTitle' => trans('public.cart_page_title'),
                    'carts' => $carts,
                    'subTotal' => $calculate["sub_total"],
                    'totalDiscount' => $calculate["total_discount"],
                    'tax' => $calculate["tax"],
                    'taxPrice' => $calculate["tax_price"],
                    'total' => $calculate["total"],
                    'userGroup' => $user->userGroup ? $user->userGroup->group : null,
                ];

                return view('web.default.cart.cart', $data);
            }
        }

        return redirect('/');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'webinar_id' => 'required',
            'ticket_id' => 'nullable',
        ]);

        $webinar_id = $request->get('webinar_id');
        $ticket_id = $request->input('ticket_id');

        $webinar = Webinar::where('id', $webinar_id)
            ->where('private', false)
            ->where('status', 'active')
            ->first();

        if (!empty($webinar)) {
            $checkCourseForSale = checkCourseForSale($webinar, $user);

            if ($checkCourseForSale != 'ok') {
                return $checkCourseForSale;
            }

            $activeSpecialOffer = $webinar->activeSpecialOffer();

            if (!empty($webinar) and $webinar->canSale()) {
                Cart::updateOrCreate([
                    'creator_id' => $user->id,
                    'webinar_id' => $webinar_id,
                ], [
                    'ticket_id' => $ticket_id,
                    'special_offer_id' => !empty($activeSpecialOffer) ? $activeSpecialOffer->id : null,
                    'created_at' => time()
                ]);
            }

            $toastData = [
                'title' => trans('cart.cart_add_success_title'),
                'msg' => trans('cart.cart_add_success_msg'),
                'status' => 'success'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function destroy($id)
    {
        $user_id = auth()->id();
        $cart = Cart::where('id', $id)
            ->where('creator_id', $user_id)
            ->first();

        if (!empty($cart)) {
            if (!empty($cart->reserve_meeting_id)) {
                $reserve = ReserveMeeting::where('id', $cart->reserve_meeting_id)
                    ->where('user_id', $user_id)
                    ->first();

                if (!empty($reserve)) {
                    $reserve->delete();
                }
            }

            $cart->delete();

            return response()->json([
                'code' => 200
            ], 200);
        }

        return response()->json([], 422);
    }

    public function couponValidate(Request $request)
    {
        $user = auth()->user();
        $coupon = $request->get('coupon');

        $discountCoupon = Discount::where('code', $coupon)
            ->where('expired_at', '>', time())
            ->first();

        if (!empty($discountCoupon) and $discountCoupon->checkValidDiscount()) {
            $carts = Cart::where('creator_id', $user->id)
                ->get();

            if (!empty($carts) and !$carts->isEmpty()) {
                $calculate = $this->calculatePrice($carts, $user, $discountCoupon);

                if (!empty($calculate)) {
                    return response()->json([
                        'status' => 200,
                        'discount_id' => $discountCoupon->id,
                        'total_discount' => $calculate["total_discount"],
                        'total_tax' => $calculate["tax_price"],
                        'total_amount' => $calculate["total"],
                    ], 200);
                }
            }
        }

        return response()->json([
            'status' => 422,
        ], 200);
    }

    private function calculatePrice($carts, $user, $discountCoupon = null)
    {
        $totalDiscount = 0;

        $subTotal = $carts->sum(function ($cart) use ($user, &$totalDiscount) {
            $price = 0;

            if (!empty($cart->webinar_id)) {
                $price = $cart->webinar->price;
                $totalDiscount += $cart->webinar->getDiscount($cart->ticket, $user);
            } elseif (!empty($cart->reserve_meeting_id)) {
                $price = $cart->reserveMeeting->paid_amount;
                $totalDiscount += $price * $cart->reserveMeeting->discount / 100;
            }

            return $price;
        });


        if (!empty($discountCoupon)) {
            $couponAmount = $subTotal * $discountCoupon->percent / 100;

            if (!empty($discountCoupon->amount) and $couponAmount > $discountCoupon->amount) {
                $totalDiscount += $discountCoupon->amount;
            } else {
                $totalDiscount += $couponAmount;
            }
        }

        $subTotalWithDiscount = $subTotal - $totalDiscount;

        $financialSettings = getFinancialSettings();
        $commission = $user->getCommission();
        $tax = $financialSettings['tax'];

        $commissionPrice = $subTotalWithDiscount * $commission / 100;
        $taxPrice = $subTotalWithDiscount * $tax / 100;

        $total = $subTotalWithDiscount + $taxPrice;

        return [
            'sub_total' => round($subTotal, 2),
            'total_discount' => $totalDiscount,
            'tax' => $tax,
            'tax_price' => round($taxPrice, 2),
            'commission' => $commission,
            'commission_price' => round($commissionPrice, 2),
            'total' => round($total, 2),
        ];
    }

    public function checkout(Request $request)
    {
        $discountId = $request->input('discount_id');

        $paymentChannels = PaymentChannel::where('status', 'active')->get();

        $discountCoupon = Discount::where('id', $discountId)->first();

        if (empty($discountCoupon) or !$discountCoupon->checkValidDiscount()) {
            $discountCoupon = null;
        }

        $user = auth()->user();
        $carts = Cart::where('creator_id', $user->id)
            ->get();

        if (!empty($carts) and !$carts->isEmpty()) {
            $calculate = $this->calculatePrice($carts, $user, $discountCoupon);

            $order = $this->createOrderAndOrderItems($carts, $calculate, $user, $discountCoupon);

            $razorpay = false;
            foreach ($paymentChannels as $paymentChannel) {
                if ($paymentChannel->class_name == 'Razorpay') {
                    $razorpay = true;
                }
            }

            $data = [
                'pageTitle' => trans('public.checkout_page_title'),
                'paymentChannels' => $paymentChannels,
                'carts' => $carts,
                'subTotal' => $calculate["sub_total"],
                'totalDiscount' => $calculate["total_discount"],
                'tax' => $calculate["tax"],
                'taxPrice' => $calculate["tax_price"],
                'total' => $calculate["total"],
                'userGroup' => $user->userGroup ? $user->userGroup->group : null,
                'order' => $order,
                'count' => $carts->count(),
                'userCharge' => $user->getAccountingCharge(),
                'razorpay' => $razorpay
            ];

            return view(getTemplate() . '.cart.payment', $data);
        }

        return redirect('/cart');
    }

    public function createOrderAndOrderItems($carts, $calculate, $user, $discountCoupon = null)
    {
        $order = Order::create([
            'user_id' => $user->id,
            'status' => Order::$pending,
            'amount' => $calculate["sub_total"],
            'tax' => $calculate["tax_price"],
            'total_discount' => $calculate["total_discount"],
            'total_amount' => $calculate["total"],
            'created_at' => time(),
        ]);

        foreach ($carts as $cart) {
            $price = 0;
            $discount = 0;
            $discountCouponPrice = 0;
            $sellerUser = null;

            if (!empty($cart->webinar_id)) {
                $price = $cart->webinar->price;
                $discount = $cart->webinar->getDiscount($cart->ticket, $user);
                $sellerUser = $cart->webinar->creator;
            } elseif (!empty($cart->reserve_meeting_id)) {
                $price = $cart->reserveMeeting->paid_amount;
                $discount = $price * $cart->reserveMeeting->discount / 100;
                $sellerUser = $cart->reserveMeeting->meeting->creator;
            }

            if (!empty($discountCoupon)) {
                $couponAmount = $price * $discountCoupon->percent / 100;

                if (!empty($discountCoupon->amount) and $couponAmount > $discountCoupon->amount) {
                    $discountCouponPrice += $discountCoupon->amount;
                } else {
                    $discountCouponPrice += $couponAmount;
                }
            }

            $financialSettings = getFinancialSettings();
            $commission = $financialSettings['commission'];
            $tax = $financialSettings['tax'];

            if (!empty($sellerUser)) {
                $commission = $sellerUser->getCommission();
            }

            $subAmount = $price - ($discount + $discountCouponPrice);
            $taxPrice = $subAmount * $tax / 100;
            $commissionPrice = $subAmount * $commission / 100;
            $totalAmount = $subAmount + $taxPrice;

            $ticket = $cart->ticket;
            if (!empty($ticket) and !$ticket->isValid()) {
                $ticket = null;
            }

            OrderItem::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'webinar_id' => $cart->webinar_id ?? null,
                'reserve_meeting_id' => $cart->reserve_meeting_id ?? null,
                'subscribe_id' => $cart->subscribe_id ?? null,
                'promotion_id' => $cart->promotion_id ?? null,
                'ticket_id' => !empty($ticket) ? $ticket->id : null,
                'discount_id' => $discountCoupon ? $discountCoupon->id : null,
                'amount' => $price,
                'total_amount' => $totalAmount,
                'tax' => $tax,
                'tax_price' => $taxPrice,
                'commission' => $commission,
                'commission_price' => $commissionPrice,
                'discount' => $discount + $discountCouponPrice,
                'created_at' => time(),
            ]);
        }

        return $order;
    }
}
