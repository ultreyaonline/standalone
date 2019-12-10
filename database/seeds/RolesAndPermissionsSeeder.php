<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // create permissions used by the app
        Permission::create(['name' => 'view members']);
        Permission::create(['name' => 'view reunion groups']);
        Permission::create(['name' => 'request reunion group']);
        Permission::create(['name' => 'create reunion groups']);
        Permission::create(['name' => 'edit reunion groups']);
        Permission::create(['name' => 'delete reunion groups']);
        Permission::create(['name' => 'send email to reunion groups']);
        Permission::create(['name' => 'view candidate applications']);
        Permission::create(['name' => 'email secretariat members']);
        Permission::create(['name' => 'email entire community']);
        Permission::create(['name' => 'edit community roster']);
        Permission::create(['name' => 'add community member']);
        Permission::create(['name' => 'create a weekend']);
        Permission::create(['name' => 'see hidden weekends']);
        Permission::create(['name' => 'assign rectors']);
        Permission::create(['name' => 'edit rector assignments']);
        Permission::create(['name' => 'use rector tools']);
        Permission::create(['name' => 'use leaders worksheet']);
        Permission::create(['name' => 'view past community service']);
        Permission::create(['name' => 'record team fees paid']);
        Permission::create(['name' => 'reports about team fees']);
        Permission::create(['name' => 'edit weekends']);
        Permission::create(['name' => 'delete weekends']);
        Permission::create(['name' => 'add candidates']);
        Permission::create(['name' => 'edit candidates']);
        Permission::create(['name' => 'delete candidates']);
        Permission::create(['name' => 'record candidate fee payments']);
        Permission::create(['name' => 'email candidates']);
        Permission::create(['name' => 'email sponsors']);
        Permission::create(['name' => 'email community about candidates']);
        Permission::create(['name' => 'view events']);
        Permission::create(['name' => 'create events']);
        Permission::create(['name' => 'edit events']);
        Permission::create(['name' => 'delete events']);
        Permission::create(['name' => 'send email about events']);
        Permission::create(['name' => 'edit members']);
        Permission::create(['name' => 'delete members']);
        Permission::create(['name' => 'edit member permissions']);
        Permission::create(['name' => 'edit weekend photo']);
        Permission::create(['name' => 'edit team member assignments']);
        Permission::create(['name' => 'edit prayer wheel']);
        Permission::create(['name' => 'see prayer wheel names']);
        Permission::create(['name' => 'send prayer wheel invites']);
        Permission::create(['name' => 'assign SD to teams']);
        Permission::create(['name' => 'view SD history']);
        Permission::create(['name' => 'menu-see-admin-pane']);
        Permission::create(['name' => 'webmaster-email-how-to-login-msg']);
        Permission::create(['name' => 'manage queues']);


        // assign to roles
        $role = Role::create(['name' => 'Member']);
        $role->givePermissionTo('view members');
        $role->givePermissionTo('view events');
        $role->givePermissionTo('view reunion groups');
        $role->givePermissionTo('request reunion group');

        $role = Role::create(['name' => 'Secretariat']);
        $role->givePermissionTo('view candidate applications');
        $role->givePermissionTo('email secretariat members');
        $role->givePermissionTo('email entire community');
        $role->givePermissionTo('edit community roster');
        $role->givePermissionTo('add community member');
        $role->givePermissionTo('view past community service');
        $role->givePermissionTo('view SD history');

        $role = Role::create(['name' => 'President']);
        $role->givePermissionTo('edit rector assignments');
        $role->givePermissionTo('edit team member assignments');
        $role->givePermissionTo('see prayer wheel names');
        $role->givePermissionTo('create a weekend');
        $role->givePermissionTo('see hidden weekends');
        $role->givePermissionTo('assign rectors');
        $role->givePermissionTo('view past community service');
        $role->givePermissionTo('use leaders worksheet');

        $role = Role::create(['name' => 'Financial Secretary']);
        $role->givePermissionTo('record team fees paid');
        $role->givePermissionTo('reports about team fees');
        $role->givePermissionTo('record candidate fee payments');

        $role = Role::create(['name' => 'Treasurer']);
        $role->givePermissionTo('record team fees paid');
        $role->givePermissionTo('reports about team fees');
        $role->givePermissionTo('record candidate fee payments');

        $role = Role::create(['name' => 'Pre-Weekend']);
        $role->givePermissionTo('add candidates');
        $role->givePermissionTo('edit candidates');
        $role->givePermissionTo('email candidates');
        $role->givePermissionTo('email sponsors');
        $role->givePermissionTo('email community about candidates');
        $role->givePermissionTo('menu-see-admin-pane');
//        $role->givePermissionTo('record candidate fee payments');
//        $role->givePermissionTo('record team fees paid');
        $role->givePermissionTo('delete candidates');


        $role = Role::create(['name' => 'Post-Weekend']);
        $role->givePermissionTo('create reunion groups');
        $role->givePermissionTo('edit reunion groups');
        $role->givePermissionTo('delete reunion groups');
        $role->givePermissionTo('send email to reunion groups');
        $role->givePermissionTo('create events');
        $role->givePermissionTo('edit events');
        $role->givePermissionTo('delete events');
        $role->givePermissionTo('send email about events');


        $role = Role::create(['name' => 'Mens Leader']);
        $role->givePermissionTo('use rector tools');
        $role->givePermissionTo('edit team member assignments');
        $role->givePermissionTo('see prayer wheel names');
        $role->givePermissionTo('create a weekend');
        $role->givePermissionTo('see hidden weekends');
        $role->givePermissionTo('assign rectors');
        $role->givePermissionTo('view past community service');
        $role->givePermissionTo('edit members');
        $role->givePermissionTo('use leaders worksheet');

        $role = Role::create(['name' => 'Womens Leader']);
        $role->givePermissionTo('use rector tools');
        $role->givePermissionTo('edit team member assignments');
        $role->givePermissionTo('see prayer wheel names');
        $role->givePermissionTo('create a weekend');
        $role->givePermissionTo('see hidden weekends');
        $role->givePermissionTo('assign rectors');
        $role->givePermissionTo('view past community service');
        $role->givePermissionTo('edit members');
        $role->givePermissionTo('use leaders worksheet');


        $role = Role::create(['name' => 'Palanca']);
        $role->givePermissionTo('edit prayer wheel');
        $role->givePermissionTo('see prayer wheel names');
        $role->givePermissionTo('send prayer wheel invites');
        $role->givePermissionTo('edit weekend photo');


        $role = Role::create(['name' => 'Weekend Committee']);
        $role->givePermissionTo('edit weekend photo');


        $role = Role::create(['name' => 'PrayerWheel Coordinator']);
        $role->givePermissionTo('edit prayer wheel');
        $role->givePermissionTo('see prayer wheel names');
        $role->givePermissionTo('send prayer wheel invites');


        $role = Role::create(['name' => 'Spiritual Advisor']);
        $role->givePermissionTo('view SD history');
//        $role->givePermissionTo('assign SD to teams');


        $role = Role::create(['name' => 'Rector Selection']);
        $role->givePermissionTo('use leaders worksheet');
        $role->givePermissionTo('view past community service');


        $role = Role::create(['name' => 'Emerging Community Liaison']);
        $role->givePermissionTo('use rector tools');
        $role->givePermissionTo('edit team member assignments');
        $role->givePermissionTo('see prayer wheel names');
        $role->givePermissionTo('see hidden weekends');
        $role->givePermissionTo('view past community service');
        $role->givePermissionTo('edit members');
        $role->givePermissionTo('use leaders worksheet');
        $role->givePermissionTo('view SD history');


        // admin has all roles
        $role = Role::create(['name' => 'Super-Admin']);
        $role = Role::create(['name' => 'Admin']);
//        $role->givePermissionTo('view members');
//        $role->givePermissionTo('view events');
//        $role->givePermissionTo('view reunion groups');
//        $role->givePermissionTo('request reunion group');
//        $role->givePermissionTo('view candidate applications');
//        $role->givePermissionTo('email secretariat members');
//        $role->givePermissionTo('email entire community');
//        $role->givePermissionTo('edit community roster');
//        $role->givePermissionTo('add community member');
//        $role->givePermissionTo('create a weekend');
//        $role->givePermissionTo('see hidden weekends');
//        $role->givePermissionTo('assign rectors');
//        $role->givePermissionTo('edit rector assignments');
//        $role->givePermissionTo('use rector tools');
//        $role->givePermissionTo('use leaders worksheet');
//        $role->givePermissionTo('view past community service');
//        $role->givePermissionTo('edit team member assignments');
//        $role->givePermissionTo('reports about team fees');
//        $role->givePermissionTo('create reunion groups');
//        $role->givePermissionTo('edit reunion groups');
//        $role->givePermissionTo('delete reunion groups');
//        $role->givePermissionTo('send email to reunion groups');
//        $role->givePermissionTo('create events');
//        $role->givePermissionTo('edit events');
//        $role->givePermissionTo('delete events');
//        $role->givePermissionTo('send email about events');
//        $role->givePermissionTo('add candidates');
//        $role->givePermissionTo('edit candidates');
//        $role->givePermissionTo('record candidate fee payments');
//        $role->givePermissionTo('email candidates');
//        $role->givePermissionTo('email sponsors');
//        $role->givePermissionTo('email community about candidates');
//        $role->givePermissionTo('edit prayer wheel');
//        $role->givePermissionTo('see prayer wheel names');
//        // admin-only roles
//        $role->givePermissionTo('manage queues');
//        $role->givePermissionTo('edit weekends');
//        $role->givePermissionTo('delete weekends');
//        $role->givePermissionTo('edit weekend photo');
//        $role->givePermissionTo('edit members');
//        $role->givePermissionTo('delete members');
//        $role->givePermissionTo('edit member permissions');
    }
}
