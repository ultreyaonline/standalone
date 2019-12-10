<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrayerWheelsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prayer_wheels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('weekendID')->nullable();

            $table->timestamps();

            $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
        });

        Schema::create('prayer_wheel_signups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wheel_id');
            $table->unsignedTinyInteger('timeslot');

            $table->unsignedBigInteger('memberID')->nullable();

            $table->dateTime('acknowledged_at')->nullable();
            $table->dateTime('reminded_at')->nullable();

            $table->timestamps();

            $table->unique(['wheel_id', 'timeslot', 'memberID'], 'pwsignups');
            $table->index('wheel_id');
            $table->index('memberID');

            $table->foreign('wheel_id')->references('id')->on('prayer_wheels')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('memberID')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prayer_wheel_signups');
        Schema::dropIfExists('prayer_wheels');
    }
}
