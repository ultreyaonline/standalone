<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeekendsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekends', function (Blueprint $table) {
            $table->increments('id');
            $table->string('weekend_full_name',80)->index('byname');
            $table->unsignedInteger('weekend_number')->index('bynumber');
            $table->string('weekend_MF', 1)->index('bytype');
            $table->string('tresdias_community',20)->index('bycommunity');
            $table->datetime('start_date')->index('bydate');
            $table->datetime('end_date')->nullable();
            $table->string('sendoff_location',100)->nullable();
            $table->string('sendoff_couple_name', 80)->nullable();
            $table->string('sendoff_couple_email', 80)->nullable();
            $table->unsignedBigInteger('sendoff_couple_id1')->nullable();
            $table->unsignedBigInteger('sendoff_couple_id2')->nullable();
            $table->string('weekend_location', 100)->nullable();
            $table->string('candidate_arrival_time')->nullable();
            $table->string('sendoff_start_time')->nullable();
            $table->integer('maximum_candidates')->nullable();
            $table->double('candidate_cost', 6, 2)->nullable();
            $table->double('team_fees', 6, 2)->nullable();
            $table->unsignedBigInteger('rectorID')->nullable();
            $table->text('weekend_verse_text')->nullable();
            $table->string('weekend_verse_reference', 255)->nullable();
            $table->string('weekend_theme')->nullable();
            $table->string('banner_url')->nullable(); // or a media object separately?
            $table->string('serenade_arrival_time')->nullable();
            $table->string('serenade_practice_location', 255)->nullable();
            $table->string('serenade_scheduled_start_time')->nullable();
            $table->string('serenade_lead_contact', 100)->nullable(); // usually rector spouse
            $table->string('serenade_coordinator', 100)->nullable();
            $table->string('serenade_musician', 100)->nullable();
            $table->string('serenade_songbook_maker', 100)->nullable();
            $table->string('closing_arrival_time')->nullable();
            $table->string('closing_scheduled_start_time')->nullable();
            $table->string('emergency_poc_name', 100)->nullable();
            $table->string('emergency_poc_email', 100)->nullable();
            $table->string('emergency_poc_phone', 100)->nullable();
            $table->unsignedBigInteger('emergency_poc_id')->nullable();
            $table->integer('visibility_flag')->index('byvisibility')->default(0);
            $table->string('teamphoto')->nullable();
            $table->text('team_meetings')->nullable();
            $table->string('table_palanca_guideline_text',255)->nullable();
            $table->timestamps();

            $table->foreign('sendoff_couple_id1')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('sendoff_couple_id2')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('rectorID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('emergency_poc_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('weekends');
    }
}
