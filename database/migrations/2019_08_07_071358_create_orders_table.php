<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->bigInteger('payment_id')->unsigned()->nullable();
            $table->string('promo_code')->nullable();
            $table->smallInteger('type')->comment('1:Instant, 2:Advanced');
            
            $table->text('pick_location');
            $table->datetime('pick_timestamp')->nullable();
            
            $table->text('drop_location')->nullable();
            $table->datetime('drop_timestamp')->nullable();
            
            $table->integer('booked_hours')->nullable();
            $table->integer('estimated_distance')->nullable();
            $table->integer('estimated_price')->nullable();

            $table->smallInteger('payment')->default(0)->comment('0:Pending, 1:Paid');
            
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
        Schema::dropIfExists('orders');
    }
}
