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
        $foreignKeys = collect(Schema::getForeignKeys('candidates'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'candidates_m_user_id_foreign')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->foreign('m_user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'candidates_w_user_id_foreign')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->foreign('w_user_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('events'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'events_contact_id_foreign')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreign('contact_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'events_posted_by_foreign')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreign('posted_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('prayer_wheels'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'prayer_wheels_weekendid_foreign')) {
            Schema::table('prayer_wheels', function (Blueprint $table) {
                $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('prayer_wheel_signups'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'prayer_wheel_signups_wheel_id_foreign')) {
            Schema::table('prayer_wheel_signups', function (Blueprint $table) {
                $table->foreign('wheel_id')->references('id')->on('prayer_wheels')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('prayer_wheel_signups'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'prayer_wheel_signups_memberid_foreign')) {
            Schema::table('prayer_wheel_signups', function (Blueprint $table) {
                $table->foreign('memberID')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('secretariat'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'secretariat_president_foreign')) {
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

        $foreignKeys = collect(Schema::getForeignKeys('team_fees'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'team_fees_weekendid_foreign')) {
            Schema::table('team_fees', function (Blueprint $table) {
                $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('team_fees'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'team_fees_memberid_foreign')) {
            Schema::table('team_fees', function (Blueprint $table) {
                $table->foreign('memberID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('weekends'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'weekends_sendoff_couple_id1_foreign')) {
            Schema::table('weekends', function (Blueprint $table) {
                $table->foreign('sendoff_couple_id1')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('weekends'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'weekends_sendoff_couple_id2_foreign')) {
            Schema::table('weekends', function (Blueprint $table) {
                $table->foreign('sendoff_couple_id2')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('weekends'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'weekends_rectorid_foreign')) {
            Schema::table('weekends', function (Blueprint $table) {
                $table->foreign('rectorID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('weekends'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'weekends_emergency_poc_id_foreign')) {
            Schema::table('weekends', function (Blueprint $table) {
                $table->foreign('emergency_poc_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('weekend_assignments'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'weekend_assignments_weekendid_foreign')) {
            Schema::table('weekend_assignments', function (Blueprint $table) {
                $table->foreign('weekendID')->references('id')->on('weekends')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('weekend_assignments'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'weekend_assignments_memberid_foreign')) {
            Schema::table('weekend_assignments', function (Blueprint $table) {
                $table->foreign('memberID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('weekend_assignments'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'weekend_assignments_roleid_foreign')) {
            Schema::table('weekend_assignments', function (Blueprint $table) {
                $table->foreign('roleID')->references('id')->on('weekend_roles')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('weekend_assignments_external'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'weekend_assignments_external_memberid_foreign')) {
            Schema::table('weekend_assignments_external', function (Blueprint $table) {
                $table->foreign('memberID')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('users'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'users_spouseid_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('spouseID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('users'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'users_sponsorid_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('sponsorID')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }

        $foreignKeys = collect(Schema::getForeignKeys('users'));
        if (! $foreignKeys->contains(fn ($fk) => ($fk['name'] ?? null) === 'users_updated_by_foreign')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('CASCADE');
            });
        }
    }

};
