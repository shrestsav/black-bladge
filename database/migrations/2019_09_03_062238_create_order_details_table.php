<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id')->unsigned()->unique();
            $table->integer('PAB')->unsigned()->nullable()->comment('Pick Assigned By');
            $table->dateTime('PAT')->nullable()->comment('Pick Assigned Time / Accepted Time');
            $table->dateTime('STPL')->nullable()->comment('Start Trip to Pick Location');
            $table->dateTime('AAPL')->nullable()->comment('Arrived at Pickup Location');
            $table->dateTime('DC')->nullable()->comment('Dropped Customer at Destination');
            $table->smallInteger('payment_type')->nullable()->comment('1:Cash On Delivery, 2:Card, 3: Paypal');
            $table->string('payment_id')->nullable()->comment('Payment ID from Paypal or Payfort');
            $table->dateTime('PT')->nullable()->comment('Payment Time');
            $table->text('PDR')->nullable()->comment('Pickup Driver Remark');

            $table->foreign('order_id')->references('id')->on('orders')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
