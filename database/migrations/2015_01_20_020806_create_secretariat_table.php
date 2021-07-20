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
        Schema::create('secretariat', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedBigInteger('president')->nullable();
            $table->unsignedBigInteger('vicepresident')->nullable();
            $table->unsignedBigInteger('treasurer')->nullable();
            $table->unsignedBigInteger('secretary')->nullable();
            $table->unsignedBigInteger('finsecretary')->nullable();
            $table->unsignedBigInteger('preweekend')->nullable();
            $table->unsignedBigInteger('weekend')->nullable();
            $table->unsignedBigInteger('postweekend')->nullable();
            $table->unsignedBigInteger('palanca')->nullable();
            $table->unsignedBigInteger('mleader')->nullable();
            $table->unsignedBigInteger('wleader')->nullable();
            $table->unsignedBigInteger('pastpresident')->nullable();
            $table->unsignedBigInteger('sadvisor')->nullable();

            $table->foreign('president')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('vicepresident')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('treasurer')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('secretary')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('finsecretary')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('preweekend')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('weekend')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('postweekend')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('palanca')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('mleader')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('wleader')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('pastpresident')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('sadvisor')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('secretariat');
    }
};
