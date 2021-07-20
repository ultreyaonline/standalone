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
        Schema::create('candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('m_user_id')->nullable();
            $table->unsignedBigInteger('w_user_id')->nullable();
            $table->string('m_age', 10)->nullable();
            $table->string('w_age', 10)->nullable();
            $table->string('m_emergency_name')->nullable();
            $table->string('m_emergency_phone')->nullable();
            $table->string('w_emergency_name')->nullable();
            $table->string('w_emergency_phone')->nullable();
            $table->string('m_pronunciation')->nullable();
            $table->string('w_pronunciation')->nullable();
            $table->tinyInteger('m_married')->nullable();
            $table->tinyInteger('w_married')->nullable();
            $table->tinyInteger('m_vocational_minister')->default(0)->nullable();
            $table->tinyInteger('w_vocational_minister')->default(0)->nullable();
            $table->tinyInteger('sponsor_acknowledgement_sent')->default(0)->nullable();
            $table->tinyInteger('sponsor_confirmed_details')->default(0)->nullable();
            $table->tinyInteger('fees_paid')->default(0)->nullable();
            $table->tinyInteger('ready_to_mail')->default(0)->nullable();
            $table->tinyInteger('invitation_mailed')->default(0)->nullable();
            $table->tinyInteger('m_response_card_returned')->default(0)->nullable();
            $table->string('m_special_dorm')->nullable();
            $table->string('m_special_diet')->nullable();
            $table->text('m_special_prayer')->nullable();
            $table->string('m_special_medications')->nullable();
            $table->tinyInteger('m_smoker')->nullable();
            $table->tinyInteger('w_response_card_returned')->default(0)->nullable();
            $table->string('w_special_dorm')->nullable();
            $table->string('w_special_diet')->nullable();
            $table->text('w_special_prayer')->nullable();
            $table->string('w_special_medications')->nullable();
            $table->tinyInteger('w_smoker')->nullable();
            $table->string('payment_details')->nullable();
            $table->string('m_arrival_poc_person')->nullable();
            $table->string('m_arrival_poc_phone')->nullable();
            $table->string('w_arrival_poc_person')->nullable();
            $table->string('w_arrival_poc_phone')->nullable();
            $table->string('weekend')->nullable();
            $table->tinyInteger('completed')->default(0)->nullable();
            $table->string('hash_sponsor_confirm')->nullable();
            $table->timestamps();
            $table->dateTime('m_packing_list_email_sent')->nullable();
            $table->dateTime('w_packing_list_email_sent')->nullable();
            $table->dateTime('m_confirmation_email_sent')->nullable();
            $table->dateTime('w_confirmation_email_sent')->nullable();
            $table->text('m_special_notes')->nullable();
            $table->text('w_special_notes')->nullable();

            $table->foreign('m_user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('w_user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidates');
    }
};
