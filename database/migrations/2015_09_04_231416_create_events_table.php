<?php

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
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event_key')->nullable()->index('byeventkey');
            $table->string('type')->index('byeventtype');
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->text('location_name')->nullable();
            $table->text('location_url')->nullable();
            $table->text('address_street')->nullable();
            $table->text('address_city')->nullable();
            $table->text('address_province')->nullable();
            $table->text('address_postal')->nullable();
            $table->text('map_url_link')->nullable();
            $table->text('contact_name')->nullable();
            $table->text('contact_email')->nullable();
            $table->text('contact_phone')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->dateTime('start_datetime')->index('byeventdate');
            $table->dateTime('end_datetime');
            $table->unsignedTinyInteger('is_enabled')->default(0)->index('byeventenabled');
            $table->unsignedTinyInteger('is_public')->default(0)->index('byeventpublic');
            $table->unsignedTinyInteger('is_recurring')->default(0)->index('byeventrecurring');
            $table->dateTime('recurring_end_datetime')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('events');
    }
};
