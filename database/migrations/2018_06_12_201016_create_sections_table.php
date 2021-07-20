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
        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('sort_order')->nullable();
            $table->boolean('enabled')->default(1);
            $table->timestamps();
        });

        // @TODO - foreign key to WeekendRoles table?


        $sections = [
            [1, 1, 'Rector/Head/AH/Rover/BUR', 1],
            [2, 2, 'Rollista', 1],
            [3, 3, 'Table Cha', 1],
            [4, 4, 'Music/Worship', 1],
            [5, 5, 'Chapel', 1],
            [6, 6, 'Dorm', 1],
            [7, 7, 'Storeroom', 1],
            [8, 8, 'Gopher', 1],
            [9, 9, 'Palanca', 1],
            [10,10, 'Prayer', 1],
            [11,11, 'Dining/Kitchen', 1],
            [12,12, 'Tech/Audio/Visual', 1],
            [13,13, 'SD', 1],
            [14,14, 'Other', 1],
        ];

        $date = Illuminate\Support\Carbon::now();

        foreach ($sections as $key => $val) {
            DB::table('sections')->insert([
                'id'         => $val[0],
                'sort_order' => $val[1],
                'name'       => $val[2],
                'enabled'    => $val[3],
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
};
