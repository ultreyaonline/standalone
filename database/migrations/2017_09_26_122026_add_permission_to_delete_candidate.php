<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use App\Permission;

return new class extends Migration {
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
};
