<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DecimalOrdersOrderItemsSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE `orders` MODIFY COLUMN  `tax` decimal(13,2) NULL ");
            DB::statement("ALTER TABLE `orders` MODIFY COLUMN  `total_amount` decimal(13,2)  NULL ");
            DB::statement("ALTER TABLE `orders` MODIFY COLUMN  `total_discount` decimal(13,2)  NULL ");
        });

        Schema::table('order_items', function (Blueprint $table) {
            DB::statement("ALTER TABLE `order_items` MODIFY COLUMN  `total_amount` decimal(13,2) NULL ");
            DB::statement("ALTER TABLE `order_items` MODIFY COLUMN  `tax_price`  decimal(13,2) NULL ");
            DB::statement("ALTER TABLE `order_items` MODIFY COLUMN  `commission_price`  decimal(13,2) NULL ");
            DB::statement("ALTER TABLE `order_items` MODIFY COLUMN  `discount`  decimal(13,2) NULL ");
        });
        Schema::table('sales', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN  `tax`  decimal(13,2) NULL ");
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN  `commission`   decimal(13,2) NULL ");
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN  `discount`  decimal(13,2) NULL ");
            DB::statement("ALTER TABLE `sales` MODIFY COLUMN  `total_amount`   decimal(13,2) NULL ");

        });
        Schema::table('accounting', function (Blueprint $table) {
            DB::statement("ALTER TABLE `accounting` MODIFY COLUMN  `amount`  decimal(13,2) NULL ");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
