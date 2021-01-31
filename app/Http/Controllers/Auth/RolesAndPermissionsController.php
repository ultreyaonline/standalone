<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsController extends Controller
{

    public function __construct() {
        parent::__construct();
        $this->middleware('password.confirm')->except(['index']);

        Gate::define('can delete admins', function ($user) {
            return $user->hasAnyRole('Super-Admin', 'Admin');
        });
        Gate::define('can delete super-admins', function ($user) {
            return $user->hasAnyRole('Super-Admin');
        });
    }

    public function index()
    {
        $select = new User;
        $select->id = 0;
        $select->first = ' Please ';
        $select->last = ' select';

        $excludeRoles = ['Member'];
        if (!auth()->user()->can('can delete super-admins')) $excludeRoles[] = 'Super-Admin';

        $roles = Role::whereNotIn('name', $excludeRoles) // ALERT: agnostic of guard_name here!
            ->with('users')
            ->get();
        $users = User::query()
            ->active()
//            ->onlyLocal()
            ->orderBy('first')->orderBy('last')
            ->with('roles')
            ->get();
        $nonmembers = $users->reject(function ($user, $key) {
            return $user->hasRole('Member') || $user->unsubscribe == true;
        });
        $members = $users->reject(function ($user, $key) {
            return !$user->hasRole('Member') || $user->unsubscribe == true;
        });

        return view('admin.roles_editor',
            [
                'roles' => $roles,
                'members' => $members->prepend($select),
                'nonmembers' => $nonmembers,
                'canEdit' => auth()->user()->can('assignRoles'),
                'canDeleteAdmins' => auth()->user()->can('can delete admins'),
                'canDeleteSuperAdmins' => auth()->user()->can('can delete super-admins'),
            ]);
    }

    /**
     * Assign a role to a member
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        \abort_unless($request->user()->can('assignRoles'), '403', 'You do not have permission to make Role assignments.');

        $rules = [
            'member_id' => 'exists:users,id',
            'role_id'   => 'exists:roles,id',
        ];
        $this->validate($request, $rules);

        $member = User::find($request->input('member_id'));
        $role = Role::findById($request->input('role_id'));

        // if member already has the role, flash message and return
        if ($member->hasRole($role)) {
            flash()->warning('Note: Member already has the selected role. No action taken.');

            return redirect(route('showAssignedRoles'));
        }

        // do the assignment
        $member->assignRole($role);

        flash()->success($role->name . ' role assigned to ' . $member->name . '.');

        return redirect(route('showAssignedRoles'));
    }

    /**
     * Delete the role assignment
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function destroy(Request $request)
    {
        \abort_unless($request->user()->can('assignRoles'), '403', 'You do not have permission to change Role assignments.');

        $rules = [
            'member_id' => 'exists:users,id',
            'role_id'   => 'exists:roles,id',
        ];
        $this->validate($request, $rules);

        $member = User::find($request->input('member_id'));
        $role = Role::findById($request->input('role_id'));

        // cannot remove if doesn't already have it
        if (!$member->hasRole($role)) {
            flash()->warning('Note: Member does not have the selected role. No action taken.');

            return redirect(route('showAssignedRoles'));
        }

        // Prevent tampering with admins
        if ($role->name === 'Admin' && $request->user()->cannot('can delete admins')) {
            flash()->warning('Action could not be taken.');

            return redirect(route('showAssignedRoles'));
        }
        if ($role->name === 'Super-Admin' && $request->user()->cannot('can delete super-admins')) {
            flash()->warning('Action could not be taken.');

            return redirect(route('showAssignedRoles'));
        }

        // do the actual removal.
        $member->removeRole($role);

        flash()->success($role->name . ' role removed from ' . $member->name . '.');

        return redirect(route('showAssignedRoles'));
    }
}
