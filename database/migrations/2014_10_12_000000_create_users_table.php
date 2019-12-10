<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first', 45);
            $table->string('last', 45);
            $table->string('email', 60)->nullable();
            $table->string('password')->nullable();
            $table->string('username')->unique();
            $table->string('gender', 1)->nullable()->index('bygender');
            $table->string('address1', 60)->nullable();
            $table->string('address2', 60)->nullable();
            $table->string('city', 60)->nullable()->index('bycity');
            $table->string('state', 60)->nullable()->index('bystate');
            $table->string('postalcode', 10)->nullable()->index('bypostalcode');
            $table->string('country', 32)->nullable()->index('bycountry');
            $table->string('homephone', 25)->nullable();
            $table->string('cellphone', 25)->nullable();
            $table->string('workphone', 25)->nullable();
            $table->unsignedBigInteger('spouseID')->nullable();
            $table->integer('okay_to_send_serenade_and_palanca_details')->default(0)->index('share_palanca_details');
            $table->string('church', 60)->nullable()->index('bychurch');
            $table->string('weekend', 30)->nullable()->index('byweekend');
            $table->string('sponsor', 60)->nullable();
            $table->unsignedBigInteger('sponsorID')->nullable()->index('bysponsorid');
            $table->string('community', 64)->nullable()->index('membercommunity');
            $table->integer('interested_in_serving')->default(1)->index('byinterestedinserving');
            $table->integer('active')->default(1)->index('byactive');
            $table->string('inactive_comments', 255)->nullable();
            $table->string('skills', 255)->nullable();
            $table->integer('qualified_sd')->default(0)->index('bysd');
            $table->string('avatar')->nullable();
            $table->string('created_by', 90)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->integer('allow_address_share')->length(1)->default(0);
            $table->integer('receive_email_weekend_general')->default(1);
            $table->integer('receive_email_community_news')->default(1);
            $table->integer('receive_email_sequela')->default(1);
            $table->integer('receive_email_reunion')->default(1);
            $table->integer('unsubscribe')->default(0)->index('unsubscribe');
            $table->dateTime('unsubscribe_date')->nullable();

            $table->tinyInteger('receive_prayer_wheel_invites')->default(0)->nullable();
            $table->tinyInteger('receive_prayer_wheel_reminders')->default(1)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->text('emergency_contact_details')->nullable();
            $table->string('uidhash', 64)->nullable();

            $table->unique(['email', 'last', 'first'], 'byEmailAndName');
            $table->unique('uidhash');

            $table->foreign('spouseID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('sponsorID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
