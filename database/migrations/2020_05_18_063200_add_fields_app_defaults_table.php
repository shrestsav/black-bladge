<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsAppDefaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_defaults', function (Blueprint $table) {
            $table->longText('copyrights')->nullable()->after('TACS');
            $table->longText('privacy_policies')->nullable()->after('copyrights');
            $table->longText('data_providers')->nullable()->after('privacy_policies');
            $table->longText('software_licences')->nullable()->after('data_providers');
            $table->longText('local_informations')->nullable()->after('software_licences');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_defaults', function (Blueprint $table) {
            $table->dropColumn('copyrights');
            $table->dropColumn('privacy_policies');
            $table->dropColumn('data_providers');
            $table->dropColumn('software_licences');
            $table->dropColumn('local_informations');
        });
    }
}
