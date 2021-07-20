<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations to rename old logs to new categories, for easier maintenance
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DELETE FROM activity_log WHERE properties = '{\"attributes\":[],\"old\":[]}'");
        DB::statement("UPDATE activity_log SET log_name='login-success' WHERE description = 'Login successful.'");
        DB::statement("UPDATE activity_log SET log_name='login-failures' WHERE subject_type = 'App\\FailedLoginAttempt'");
        DB::statement("UPDATE activity_log SET log_name='logout' WHERE description = 'Logout'");
        DB::statement("UPDATE activity_log SET log_name='admin' WHERE description LIKE ('%Impersonation%')");
        DB::statement("UPDATE activity_log SET log_name='config' WHERE description LIKE ('%ed Setting%')");
        DB::statement("UPDATE activity_log SET log_name='passwords' WHERE description = 'Password changed.'");
        DB::statement("UPDATE activity_log SET log_name='login-failures' WHERE description LIKE 'Login failure%'");
        DB::statement("UPDATE activity_log SET log_name='team-assignment' WHERE subject_type IN('App\\WeekendAssignments', 'App\\WeekendAssignmentsExternal')");
        DB::statement("UPDATE activity_log SET log_name='weekends' WHERE log_name='default' AND subject_type ='App\\Weekend'");
        DB::statement("UPDATE activity_log SET log_name='calendar' WHERE log_name='default' AND subject_type ='App\\Event'");
        DB::statement("UPDATE activity_log SET log_name='pre-weekend' WHERE log_name='default' AND subject_type ='App\\Candidate'");
        DB::statement("UPDATE activity_log SET log_name='members' WHERE log_name='default' AND subject_type ='App\\User'");
        DB::statement("UPDATE activity_log SET log_name='prayer-wheels' WHERE log_name='default' AND subject_type ='App\\PrayerWheel'");
        DB::statement("UPDATE activity_log SET log_name='secretariat' WHERE log_name='default' AND subject_type ='App\\Secretariat'");
    }
};
