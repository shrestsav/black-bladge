<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('description');
            $table->integer('discount');
            $table->smallInteger('type')->comment('1: Percentage, 2: Amount');
            $table->smallInteger('coupon_type')->comment('1: Single Use, 2: Multiple Use, 3: Single Use for Single User');
            $table->smallInteger('user_id')->nullable()->comment('Only if coupon_type 3');
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
            $table->smallInteger('status')->default(0)->comment('0: Inactive, 1: Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
