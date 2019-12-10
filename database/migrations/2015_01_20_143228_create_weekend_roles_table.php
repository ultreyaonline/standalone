<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeekendRolesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekend_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('RoleName');
            $table->string('ReportName')->nullable();
            $table->integer('sortorder')->index('bysortorder');
            $table->unsignedInteger('head_id')->nullable();
            $table->unsignedInteger('section_id')->nullable();
            $table->boolean('isCoreTalk')->nullable();
            $table->boolean('isBasicTalk')->nullable();
            $table->boolean('excludeAsFormerRector')->nullable();
            $table->boolean('requiredForRector')->nullable();
            $table->boolean('isDeptHead')->nullable();
            $table->boolean('canEmailEntireTeam')->nullable();
            $table->timestamps();
        });

        $roles = [
            [1, 1, 1, 'Rector', 'Rector'],
            [2, 1, 1, 'Head Cha', 'Head Cha'],
            [3, 1, 1, 'Assistant Head Cha', 'AH Cha'],
            [4, 1, 1, 'Backup Rector', 'BUR'],
            [5, 1, 1, 'Rover', 'Rover'],
            [6, 6, 13, 'Head Spiritual Director', 'H.SD'],
            [7, 6, 13, 'Spiritual Director', 'SD'],
            [8, 8, 2, 'Ideal', 'Ideal'],
            [9, 9, 2, 'Church', 'Church'],
            [10, 10, 2, 'Piety', 'Piety'],
            [11, 11, 2, 'Study', 'Study'],
            [12, 12, 2, 'Action', 'Action'],
            [13, 13, 2, 'Leaders', 'Leaders'],
            [14, 14, 2, 'Environment', 'Envir'],
            [15, 15, 2, 'CCIA', 'CCIA'],
            [16, 16, 2, 'Reunion Group', 'ReunGrp'],
            [17, 17, 2, 'Fourth Day', '4thDay'],
            [18, 18, 2, 'Silent Professor', 'TA'],
            [19, 19, 2, 'Table Assistant', 'TA'],
            [20, 20, 2, 'Table Leader', 'TA'],
            [21, 21, 3, 'Head Table Cha', 'Head Table Cha'],
            [22, 21, 3, 'Assistant Head Table Cha', 'Asst Hd Tbl'],
            [23, 21, 3, 'Table Cha', 'Table'],
            [24, 24, 4, 'Head Music/Worship Cha', 'Hd Music'],
            [25, 24, 4, 'Assist.Head Music/Worship Cha', 'AsstHd Music'],
            [26, 24, 4, 'Music/Worship Cha', 'Music'],
            [27, 27, 5, 'Head Chapel Cha', 'Head Chapel'],
            [28, 27, 5, 'Assistant Head Chapel Cha', 'Asst Head Chapel'],
            [29, 27, 5, 'Chapel Cha', 'Chapel'],
            [30, 30, 6, 'Head Dorm Cha', 'Head Dorm'],
            [31, 30, 6, 'Assistant Head Dorm Cha', 'Asst Head Dorm'],
            [32, 30, 6, 'Dorm Cha', 'Dorm Cha'],
            [33, 33, 7, 'Head Storeroom Cha', 'Head Storeroom'],
            [34, 33, 7, 'Assistant Head Storeroom Cha', 'Asst Head Storeroom'],
            [35, 33, 7, 'Storeroom Cha', 'Storeroom'],
            [36, 33, 7, 'Floater/Supply Cha', 'Floater'],
            [37, 37, 8, 'Head Gopher', 'Head Gopher'],
            [38, 37, 8, 'Gopher', 'Gopher'],
            [39, 39, 9, 'Head Palanca Cha', 'Head Palanca'],
            [40, 39, 9, 'Assistant Head Palanca Cha', 'Asst Head Palanca'],
            [41, 39, 9, 'Palanca Cha', 'Palanca'],
            [42, 42, 10, 'Head Prayer Cha', 'Head Prayer'],
            [43, 42, 10, 'Assistant Head Prayer Cha', 'Asst Head Prayer'],
            [44, 42, 10, 'Prayer Cha', 'Prayer'],
            [45, 45, 11, 'Head Dining Cha', 'Head Dining'],
            [46, 45, 11, 'Assistant Head Dining Cha', 'Asst Head Dining'],
            [47, 45, 11, 'Dining Cha', 'Dining'],
            [48, 48, 12, 'Head Audio/Visual Cha', 'Head AV'],
            [49, 48, 12, 'Audio/Visual Cha', 'AV'],
            [50, 50, 14, 'Special Cha', 'Special'],
//            [51, 51, 0, 'Candidate', 'Candidate'],
        ];

        $date = Illuminate\Support\Carbon::now();
        foreach ($roles as $key => $val) {
            DB::table('weekend_roles')->insert(
                [
                    'sortorder'  => $val[0],
                    'section_id'   => $val[2],
                    'head_id' => $val[1],
                    'RoleName'   => $val[3],
                    'ReportName'   => $val[4],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );
        }
        DB::update('update weekend_roles set isCoreTalk=1 where id in (10,12,13,14,15)');
        DB::update('update weekend_roles set isBasicTalk=1 where id in (8,9,11,16,17)');
        DB::update('update weekend_roles set excludeAsFormerRector=1 where id in (1,4,5,50)');
        DB::update('update weekend_roles set isDeptHead=1 where id in (1,2,3,4,5,6,21,24,27,30,33,37,39,42,45,48)');
        DB::update('update weekend_roles set canEmailEntireTeam=1 where id in (1,2,3,4,5,6,21,24,27,30,33,37,39,42,45,48)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('weekend_roles');
    }
}
