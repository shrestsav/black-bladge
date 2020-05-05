<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->integer('OTP')->unsigned()->nullable();
            $table->datetime('OTP_timestamp')->nullable();
            $table->smallInteger('OTP_verified')->nullable()->default(0);
            $table->string('password')->nullable();

            //Other Details
            $table->string('photo')->nullable();
            $table->string('gender')->nullable();
            $table->string('country')->nullable();
            $table->date('dob')->nullable();
            $table->string('license_no')->nullable()->comment('if user is driver');
            $table->bigInteger('vehicle_id')->nullable()->comment('Only for drivers');
            $table->datetime('last_login')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
