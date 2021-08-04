<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_channels', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('title', 64);
            $table->enum('class_name', ['Paypal', 'Paystack', 'Paytm', 'Payu', 'Razorpay', 'Zarinpal', 'Stripe', 'Paysera', 'Fastpay', 'YandexCheckout', '2checkout', 'Bitpay', 'Midtrans', 'Adyen', 'Flutterwave', 'Payfort']);
            $table->string('image')->nullable();
            $table->text('settings')->nullable();
            $table->integer('created_at')->unsigned();
            $table->string('disabled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_channels');
    }
}
