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
        Schema::create('weekend_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('weekendID')->index('byweekendid');
            $table->unsignedBigInteger('memberID')->nullable()->index('bymember');
            $table->unsignedInteger('roleID')->index('byrole');
            $table->integer('confirmed')->default(0)->index('byconfirmed');
            $table->string('comments')->nullable();
            $table->timestamps();

            $table->unique(['weekendID', 'memberID', 'roleID'], 'unique_assignment');

            $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('memberID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('roleID')->references('id')->on('weekend_roles')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('weekend_assignments');
    }
};
