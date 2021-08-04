<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscribeTypeAccountInAccountingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting', function (Blueprint $table) {
            DB::statement("ALTER TABLE `accounting` MODIFY COLUMN `type_account` ENUM('income', 'asset', 'subscribe')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounting', function (Blueprint $table) {
            DB::statement("ALTER TABLE `accounting` MODIFY COLUMN `type_account` ENUM('income', 'asset', 'subscribe')");
        });
    }
}
