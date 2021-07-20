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
        Schema::create('team_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('weekendID')->index('feesforweekendid');
            $table->unsignedBigInteger('memberID')->nullable()->index('feesformember');
            $table->double('total_paid', 10, 2)->nullable();
            $table->date('date_paid')->nullable();
            $table->integer('complete')->nullable()->index('bycomplete');
            $table->text('comments')->nullable();
            $table->string('recorded_by', 60);
            $table->timestamps();

            $table->unique(['weekendID', 'memberID'], 'unique_payment');

            $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('memberID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_fees');
    }
};
