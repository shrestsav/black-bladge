<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDropLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drop_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id');
            $table->text('drop_location');
            $table->decimal('distance', 8, 4)->nullable()->comment('Distance from previous location');
            $table->decimal('price', 8, 2)->nullable()->comment('Price of distance from previous location');
            $table->smallInteger('type')->comment('1: Additional, 2: Final');
            $table->bigInteger('added_by');
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
        Schema::dropIfExists('drop_locations');
    }
}
