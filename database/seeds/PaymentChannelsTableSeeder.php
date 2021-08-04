<?php

use Illuminate\Database\Seeder;

class PaymentChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\PaymentChannel::updateOrCreate(['id' => 1], ['title' => 'paypal', 'class_name' => 'Paypal', 'status' => 'active', 'image' => '/assets/default/img/charge/paypal.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 2], ['title' => 'paystack', 'class_name' => 'Paystack', 'status' => 'active', 'image' => '/assets/default/img/charge/stripe.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 3], ['title' => 'paytm', 'class_name' => 'Paytm', 'status' => 'active', 'image' => '/assets/default/img/charge/paytm.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 4], ['title' => 'payu', 'class_name' => 'Payu', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 5], ['title' => 'Razorpay', 'class_name' => 'Razorpay', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 6], ['title' => 'Zarinpal', 'class_name' => 'Zarinpal', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 7], ['title' => 'Stripe', 'class_name' => 'Stripe', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 8], ['title' => 'Paysera', 'class_name' => 'Paysera', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 9], ['title' => 'Cashu', 'class_name' => 'Cashu', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 10], ['title' => 'Yandex checkout', 'class_name' => 'YandexCheckout', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 11], ['title' => 'MercadoPago', 'class_name' => 'MercadoPago', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 12], ['title' => 'Bitpay', 'class_name' => 'Bitpay', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 13], ['title' => 'Midtrans', 'class_name' => 'Midtrans', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 14], ['title' => 'Iyzipay', 'class_name' => 'Iyzipay', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 15], ['title' => 'Flutterwave', 'class_name' => 'Flutterwave', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
        \App\Models\PaymentChannel::updateOrCreate(['id' => 16], ['title' => 'Payfort', 'class_name' => 'Payfort', 'status' => 'active', 'image' => '/assets/default/img/charge/paytu.png', 'settings' => '', 'created_at' => time()]);
    }
}
