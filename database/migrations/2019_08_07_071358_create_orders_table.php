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
            $table->bigInteger('vehicle_id')->unsigned()->nullable()->comment('Only for drivers');
            $table->bigInteger('payment_id')->unsigned()->nullable();
            $table->string('promo_code')->nullable();
            $table->smallInteger('type')->comment('1:Instant, 2:Advanced');
            
            $table->text('pick_location');
            $table->datetime('pick_timestamp')->nullable()->comment('this timestamp is in respective timezone');
            
            $table->text('drop_location')->nullable();
            $table->datetime('drop_timestamp')->nullable()->comment('this timestamp is in respective timezone, only for advanced');
            
            $table->decimal('booked_hours', 8, 2)->nullable();
            $table->decimal('estimated_distance', 8, 2)->nullable();
            $table->decimal('estimated_price', 8, 2)->nullable();

            $table->smallInteger('payment')->default(0)->comment('0:Pending, 1:Paid');
            $table->smallInteger('status')->default(0)->comment('See Config');
            
            $table->text('cancellation_reason')->nullable();
            $table->softDeletes();
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
