<?php

namespace App\PaymentChannels\Drivers\MercadoPago;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use MercadoPago\SDK as Mercado;

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
        $this->api_key = env('MERCADO_PAGO_KEY');
        $this->api_secret = env('MERCADO_PAGO_SECRET');

        Mercado::setClientId(env('MERCADO_PAGO_KEY'));
        Mercado::setClientSecret(env('MERCADO_PAGO_SECRET'));
    }

    public function paymentRequest(Order $order)
    {
        $generalSettings = getGeneralSettings();
        $user = $order->user;

        $payment = new \MercadoPago\Payment();

        $payment->transaction_amount = $order->total_amount;
        $payment->token = $order->id;
        $payment->description = $generalSettings['site_name'].' payment';
        $payment->installments = 1;
        $payment->payment_method_id = "visa";
        $payment->payer = array(
            "email" => $user->email ?? $generalSettings['site_email']
        );

        $payment->save();

        echo $payment->status;
    }

    private function makeCallbackUrl($order, $status)
    {
        return url("/payments/verify/Stripe?status=$status&order_id=$order->id");
    }

    public function verify(Request $request)
    {
        $data = $request->all();
        $order_id = $data['order_id'];

        $user = auth()->user();

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->first();

        // Setup payment gateway
        $gateway = Omnipay::create('Paysera');
        $gateway->setProjectId($this->api_key);
        $gateway->setPassword($this->api_secret);

        // Accept the notification
        $response = $gateway->acceptNotification()->send();

        if ($response->isSuccessful() and !empty($order)) {
            // Mark the order as paid

            $order->update([
                'status' => Order::$paying
            ]);

            return $order;
        }

        if (!empty($order)) {
            $order->update([
                'status' => Order::$fail
            ]);
        }

        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => trans('cart.gateway_error'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData])->withInput();
    }
}
