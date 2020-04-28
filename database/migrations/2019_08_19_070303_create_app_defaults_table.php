<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppDefaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_defaults', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('VAT')->unsigned()->nullable();
            $table->integer('OTP_expiry')->unsigned()->comment('Minute')->nullable();
            $table->longText('driver_notes')->nullable();
            $table->string('FAQ_link')->nullable();
            $table->longText('online_chat')->nullable();
            $table->string('hotline_contact')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_logo')->nullable();
            $table->longText('TACS')->comment('TERMS AND CONDITIONS')->nullable();
            $table->longText('FAQS')->comment('FREQUENTLY ASKED QUESTIONS')->nullable();
            $table->integer('app_rows')->nullable();
            $table->integer('sys_rows')->nullable();
            $table->integer('referral_grant')->nullable();

            $table->decimal('cost_per_km', 8, 2)->nullable();
            $table->decimal('cost_per_min', 8, 2)->nullable();
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
        Schema::dropIfExists('app_defaults');
    }
}
