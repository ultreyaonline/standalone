<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeMediaTable extends Migration
{
    public function up()
    {
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails('media');

        // Upgrade Spatie MediaLibrary schema from v8 to v9

        if (! $doctrineTable->hasIndex('media_uuid_unique')) {
            Schema::table('media', function (Blueprint $table) {
                $table->unique('uuid');
            });
        }

        if (! $doctrineTable->hasColumn('generated_conversions')) {
            Schema::table('media', function (Blueprint $table) {
                $table->json('generated_conversions');
            });
        }

    }
}
