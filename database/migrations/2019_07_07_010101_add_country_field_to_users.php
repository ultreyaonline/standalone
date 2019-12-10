<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryFieldToUsers extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('users', function (Blueprint $table) {
// @ALERT: This is commented out because it has been moved into the users table migration
//            $table->string('country', 32)->nullable()->index('bycountry')->after('postalcode');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    //     Schema::table('users', function (Blueprint $table) {
    //         $table->dropColumn([
    //            'country',
    //         ]);
    //     });
    }
}
