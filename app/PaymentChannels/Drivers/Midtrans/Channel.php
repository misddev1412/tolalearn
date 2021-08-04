<?php

namespace App\PaymentChannels\Drivers\Midtrans;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Midtrans\Config as MidtransConfig;

class Channel implements IChannel
{

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        MidtransConfig::$serverKey = 'SB-Mid-server-KoACw3e1xOwEA1meLy75FwDH';
        MidtransConfig::$clientKey = 'SB-Mid-client-OXN7n8JlCBzkIxif';
        MidtransConfig::$isProduction = false; //(env('APP_ENV') == 'production');
        MidtransConfig::$isSanitized = false;
        MidtransConfig::$is3ds = false;
    }

    public function paymentRequest(Order $order)
    {
        $user = $order->user;
        $price = $order->total_amount;
        $generalSettings = getGeneralSettings();


        $transaction_details = array(
            'order_id' => $order->id,
            'gross_amount' => (int)$price,
        );

        $customer_details = array(
            'first_name' => $user->full_name,
            'email' => $user->email ?? $generalSettings['site_email'],
            'phone' => $user->mobile,
        );

        $params = array(
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
        );

        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        return $paymentUrl;
    }

    private function makeCallbackUrl($order, $status)
    {

    }

    public function verify(Request $request)
    {
        dd(2);
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
