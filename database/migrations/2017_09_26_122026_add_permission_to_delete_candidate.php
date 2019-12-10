<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use App\Permission;

class AddPermissionToDeleteCandidate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     */
    public function up()
    {
        // this was temporary, and is now in another migration file
    }
}
