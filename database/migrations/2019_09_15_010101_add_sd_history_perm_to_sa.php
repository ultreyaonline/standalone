<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

class AddSdHistoryPermToSa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            $role = Role::findByName('Spiritual Advisor');
            Permission::create(['name' => 'view SD history']);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $role->givePermissionTo('view SD history');
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            return;
        }
    }
}
