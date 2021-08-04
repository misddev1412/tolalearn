<?php

namespace App\PaymentChannels\Drivers\Payfort;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;

class Channel implements IChannel
{

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {

    }

    public function paymentRequest(Order $order)
    {
        $user = $order->user;
        $price = $order->total_amount;
        $generalSettings = getGeneralSettings();
        $currency = currency();

        $requestParams = array(
            'command' => 'AUTHORIZATION',
            'access_code' => 'zx0IPmPy5jp1vAz8Kpg7',
            'merchant_identifier' => 'CycHZxVj',
            'merchant_reference' => 'XYZ9239-yu898',
            'amount' => $price,
            'currency' => $currency,
            'language' => 'en',
            'customer_email' => $user->email ?? $generalSettings['site_email'],
            'signature' => '7cad05f0212ed933c9a5d5dffa31661acf2c827a',
            'order_description' => $generalSettings['site_name'] . ' payment',
        );

        $redirectUrl = 'https://sbcheckout.payfort.com/FortAPI/paymentPage';
        echo "<html xmlns='https://www.w3.org/1999/xhtml'>\n<head></head>\n<body>\n";
        echo "<form action='$redirectUrl' method='post' name='frm'>\n";
        foreach ($requestParams as $a => $b) {
            echo "\t<input type='hidden' name='".htmlentities($a)."' value='".htmlentities($b)."'>\n";
        }
        echo "\t<script type='text/javascript'>\n";
        echo "\t\tdocument.frm.submit();\n";
        echo "\t</script>\n";
        echo "</form>\n</body>\n</html>";
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
