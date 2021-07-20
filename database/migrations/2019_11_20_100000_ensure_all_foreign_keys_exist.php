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
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();

        $doctrineTable = $dbSchemaManager->listTableDetails('candidates');
        if (! $doctrineTable->hasForeignKey('candidates_m_user_id_foreign')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->foreign('m_user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('candidates');
        if (! $doctrineTable->hasForeignKey('candidates_w_user_id_foreign')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->foreign('w_user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('events');
        if (! $doctrineTable->hasForeignKey('events_contact_id_foreign')) {
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
        }
        $doctrineTable = $dbSchemaManager->listTableDetails('events');
        if (! $doctrineTable->hasForeignKey('events_posted_by_foreign')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreign('posted_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('prayer_wheels');
        if (! $doctrineTable->hasForeignKey('prayer_wheels_weekendid_foreign')) {
            Schema::table('prayer_wheels', function (Blueprint $table) {
                $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('prayer_wheel_signups');
        if (! $doctrineTable->hasForeignKey('prayer_wheel_signups_wheel_id_foreign')) {
            Schema::table('prayer_wheel_signups', function (Blueprint $table) {
                $table->foreign('wheel_id')->references('id')->on('prayer_wheels')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('prayer_wheel_signups');
        if (! $doctrineTable->hasForeignKey('prayer_wheel_signups_memberid_foreign')) {
            Schema::table('prayer_wheel_signups', function (Blueprint $table) {
                $table->foreign('memberID')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('secretariat');
        if (! $doctrineTable->hasForeignKey('secretariat_president_foreign')) {
            Schema::table('secretariat', function (Blueprint $table) {
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

        $doctrineTable = $dbSchemaManager->listTableDetails('team_fees');
        if (! $doctrineTable->hasForeignKey('team_fees_weekendid_foreign')) {
            Schema::table('team_fees', function (Blueprint $table) {
                $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('team_fees');
        if (! $doctrineTable->hasForeignKey('team_fees_memberid_foreign')) {
            Schema::table('team_fees', function (Blueprint $table) {
                $table->foreign('memberID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('weekends');
        if (! $doctrineTable->hasForeignKey('weekends_sendoff_couple_id1_foreign')) {
            Schema::table('weekends', function (Blueprint $table) {
                $table->foreign('sendoff_couple_id1')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('weekends');
        if (! $doctrineTable->hasForeignKey('weekends_sendoff_couple_id2_foreign')) {
            Schema::table('weekends', function (Blueprint $table) {
                $table->foreign('sendoff_couple_id2')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('weekends');
        if (! $doctrineTable->hasForeignKey('weekends_rectorid_foreign')) {
            Schema::table('weekends', function (Blueprint $table) {
                $table->foreign('rectorID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('weekends');
        if (! $doctrineTable->hasForeignKey('weekends_emergency_poc_id_foreign')) {
            Schema::table('weekends', function (Blueprint $table) {
                $table->foreign('emergency_poc_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('weekend_assignments');
        if (! $doctrineTable->hasForeignKey('weekend_assignments_weekendid_foreign')) {
            Schema::table('weekend_assignments', function (Blueprint $table) {
                $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('weekend_assignments');
        if (! $doctrineTable->hasForeignKey('weekend_assignments_memberid_foreign')) {
            Schema::table('weekend_assignments', function (Blueprint $table) {
                $table->foreign('memberID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('weekend_assignments');
        if (! $doctrineTable->hasForeignKey('weekend_assignments_roleid_foreign')) {
            Schema::table('weekend_assignments', function (Blueprint $table) {
                $table->foreign('roleID')->references('id')->on('weekend_roles')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('weekend_assignments_external');
        if (! $doctrineTable->hasForeignKey('weekend_assignments_external_memberid_foreign')) {
            Schema::table('weekend_assignments_external', function (Blueprint $table) {
                $table->foreign('memberID')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('users');
        if (! $doctrineTable->hasForeignKey('users_spouseid_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('spouseID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('users');
        if (! $doctrineTable->hasForeignKey('users_sponsorid_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('sponsorID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $doctrineTable = $dbSchemaManager->listTableDetails('users');
        if (! $doctrineTable->hasForeignKey('users_updated_by_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }
    }

};
