<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration {
    /**
     * Create an emerging community liaison role
     *
     * @return void
     */
    public function up()
    {
        try {
            // test for pre-seeded roles, and skip this migration if not found since we need the seeder run first.
            $role = Role::findByName('President');

            $role = Role::create(['name' => 'Emerging Community Liaison']);
            $role->givePermissionTo('use rector tools');
            $role->givePermissionTo('edit team member assignments');
            $role->givePermissionTo('see prayer wheel names');
            $role->givePermissionTo('see hidden weekends');
            $role->givePermissionTo('view past community service');
            $role->givePermissionTo('edit members');
            $role->givePermissionTo('use leaders worksheet');
            $role->givePermissionTo('view SD history');

            app(PermissionRegistrar::class)->forgetCachedPermissions();

        } catch (Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            return;
        }
    }
};
