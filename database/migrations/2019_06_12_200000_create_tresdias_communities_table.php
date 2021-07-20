<?php

use Illuminate\Support\Carbon;
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

        Schema::create('tresdias_communities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('abbreviation',12)->nullable();
            $table->string('community_name', 64)->nullable();
            $table->timestamps();
        });

        $list = [
            'NVTD' => 'Northern Virginia Tres Dias',
            'NGTD' => 'North Georgia Tres Dias',
        ];

        $date = Carbon::now();

        foreach ($list as $key => $val) {
            DB::table('tresdias_communities')->insert([
                'abbreviation' => $key,
                'community_name' => $val,
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
        Schema::dropIfExists('tresdias_communities');
    }
};
