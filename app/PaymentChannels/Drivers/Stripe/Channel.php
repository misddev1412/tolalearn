<?php

namespace App\PaymentChannels\Drivers\Stripe;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class Channel implements IChannel
{
    protected $currency;
    protected $api_key;
    protected $api_secret;

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->api_key = env('STRIPE_KEY');
        $this->api_secret = env('STRIPE_SECRET');
    }

    public function paymentRequest(Order $order)
    {
        $price = $order->total_amount;
        $generalSettings = getGeneralSettings();
        $currency = currency();

        Stripe::setApiKey($this->api_secret);

        $checkout = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount_decimal' => $currency == 'USD' ? $price * 100 : $price,
                    'product_data' => [
                        'name' => $generalSettings['site_name'] . ' payment',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->makeCallbackUrl($order, 'success'),
            'cancel_url' => $this->makeCallbackUrl($order, 'cancel'),
        ]);

        /*$order->update([
            'reference_id' => $checkout->id,
        ]);*/

        $Html = '<script src="https://js.stripe.com/v3/"></script>';
        $Html .= '<script type="text/javascript">let stripe = Stripe("' . $this->api_key . '");';
        $Html .= 'stripe.redirectToCheckout({ sessionId: "' . $checkout->id . '" }); </script>';

        echo $Html;
    }

    private function makeCallbackUrl($order, $status)
    {
        return url("/payments/verify/Stripe?status=$status&order_id=$order->id&session_id={CHECKOUT_SESSION_ID}");
    }

    public function verify(Request $request)
    {
        $data = $request->all();
        $status = $data['status'];
        $order_id = $data['order_id'];

        $user = auth()->user();

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->first();

        if ($status == 'success' and !empty($request->session_id) and !empty($order)) {
            Stripe::setApiKey($this->api_secret);

            $session = Session::retrieve($request->session_id);

            if (!empty($session) and $session->payment_status == 'paid') {
                $order->update([
                    'status' => Order::$paying
                ]);

                return $order;
            }
        }

        // is fail

        if (!empty($order)) {
            $order->update(['status' => Order::$fail]);
        }

        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => trans('cart.gateway_error'),
            'status' => 'error'
        ];

        return back()->with(['toast' => $toastData])->withInput();
    }
}
