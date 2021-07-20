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
        Schema::create('weekend_assignments_external', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('memberID')->index('bymemberextpos');
            $table->string('WeekendName')->default('');
            $table->string('RoleName')->default('')->nullable();
            $table->timestamps();

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
        Schema::drop('weekend_assignments_external');
    }
};
