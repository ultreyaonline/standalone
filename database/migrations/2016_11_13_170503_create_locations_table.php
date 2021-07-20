<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('location_name', 100);
            $table->string('slug', 80)->nullable();
            $table->string('location_url',250)->nullable();
            $table->string('address_street', 80)->nullable();
            $table->string('address_city', 80)->nullable();
            $table->string('address_province', 80)->nullable();
            $table->string('address_postal', 20)->nullable();
            $table->string('map_url_link', 250)->nullable();
            $table->string('contact_name', 80)->nullable();
            $table->string('contact_email', 80)->nullable();
            $table->string('contact_phone', 80)->nullable();
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
        Schema::drop('locations');
    }
};
