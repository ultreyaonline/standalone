<?php

namespace App\Http\Controllers;

use App\Enums\TeamAssignmentStatus;
use App\Models\User;
use App\Models\Weekend;
use App\Models\WeekendAssignments;
use App\Models\WeekendRoles;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class TeamAssignmentController extends Controller
{
    protected $positions;
    protected $users;

    public function __construct(Weekend $weekend)
    {
        parent::__construct();

        $this->middleware('auth');
        $this->middleware('role:Member');

        Gate::define('teamassignment.view', 'App\Policies\TeamAssignmentsPolicy@view');
        Gate::define('teamassignment.create', 'App\Policies\TeamAssignmentsPolicy@create');
        Gate::define('teamassignment.update', 'App\Policies\TeamAssignmentsPolicy@update');
        Gate::define('teamassignment.delete', 'App\Policies\TeamAssignmentsPolicy@delete');
    }

    /**
     * List the team assignments, with edit buttons
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Weekend $weekend)
    {
        $this->getPositionsDropdown($weekend);

        $assignments = WeekendAssignments::select('weekend_assignments.*', 'weekend_roles.RoleName', 'weekend_roles.sortorder')
            ->join('weekend_roles', 'weekend_assignments.roleID', '=', 'weekend_roles.id')
            ->where('weekendID', $weekend->id)
            ->orderBy('weekend_roles.sortorder', 'asc')
            ->orderBy('weekend_roles.id', 'asc')
            ->withoutGlobalScope('visibleWeekendsOnly')
            ->with(['user', 'role']);

        // check for SD restrictions
        $user = auth()->user();
        if ($user->can('make SD assignments') && $weekend->rectorID !== $user->id && !$weekend->head_cha->contains($user->id)) {
            $assignments = $assignments->whereIn('roleID', $this->positions->pluck('id'));
        }

        $assignments = $assignments->get();

        return view('weekend.team_edit')
            ->withWeekend($weekend)
            ->withPositions($this->positions)
            ->withAssignments($assignments);
    }

    /**
     * Show the form for adding a new team member
     *
     * @param Weekend $weekend
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Weekend $weekend)
    {
        $this->authorize('teamassignment.create', $weekend);

        $users = $this->getMembersDropdown($weekend);
        $this->getPositionsDropdown($weekend);

        return view('weekend.team_addperson')
            ->withWeekend($weekend)
            ->withAssignment(new WeekendAssignments)
            ->withRoles($this->positions)
            ->withStatuses(TeamAssignmentStatus::toSelectArray())
            ->withUsers($users);
    }

    /**
     * Store a new team member assignment
     *
     * @param  \Illuminate\Http\Request $request
     * @param Weekend $weekend
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Weekend $weekend)
    {
        $this->authorize('teamassignment.create', $weekend);

        // populate the positions property (can't be done in constructor)
        // we use this as part of validation and flash messaging
        $this->getPositionsDropdown($weekend);

        $this->validate($request, [
            'roleID'   => 'required|numeric|in:' . implode(',', $this->positions->where('id', '>', 0)->pluck('id')->toArray()),
            'memberID' => 'required|numeric|in:' . implode(',', $this->getAvailableMembers($weekend)->pluck('id')->toArray()),
            'comments' => 'nullable|string',
            'confirmed' => ['required', 'numeric', new EnumValue(TeamAssignmentStatus::class, false)],
        ]);

        // @TODO -- verify that any SD assignments are qualified SDs -- including auto-updating the DropDown in the View.


        // test for duplicates
        $exists = WeekendAssignments::withoutGlobalScope('visibleWeekendsOnly')
            ->where('weekendID', $weekend->id)
            ->where('memberID', $request->input('memberID'))
            ->where('roleID', $request->input('roleID'))
            ->first();
        if ($exists) {
            flash()->error('Problem: You attempted to assign someone to a role they are already assigned to. &nbsp;' . User::find($request->input('memberID'))->name . ' is already assigned to role: [' . $this->positions->where('id', $request->input('roleID'))->first()->RoleName . ']. No changes made.');
            return redirect('/team/' . $weekend->id . '/edit');
        }

        // insert record to weekend_assignments table
        $assignment = WeekendAssignments::create([
            'weekendID' => $weekend->id,
            'roleID'    => $request->input('roleID'),
            'memberID'  => $request->input('memberID'),
            'confirmed' => $request->input('confirmed'),
            'comments' => $request->input('comments'),
        ]);

        event('WeekendAssignmentAdded', ['assignment' => $assignment, 'by' => $request->user()]);

        flash()->success('Added ' . User::find($request->input('memberID'))->name . ' as ' . $this->positions->where('id', $request->input('roleID'))->first()->RoleName);

        $returnUri = '/team/' . $weekend->id . '/edit';
        if ($request->get('buttonAction') == 'saveAndAdd') {
            $returnUri = '/team/' . $weekend->id . '/add';
        }

        return redirect($returnUri);
    }

    /**
     * Show the form for editing a team member assignment
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Weekend $weekend, WeekendRoles $position, User $member)
    {
        $this->authorize('teamassignment.update', $weekend);

        $this->getPositionsDropdown($weekend);

        if (!$this->positions->contains('id', $position->id) || !$this->getAvailableMembers($weekend)->contains($member)) {
            abort(403, 'Tampering is not allowed. Your IP address has been recorded. You should go back to watching Netflix.');
        }

        $assignment = WeekendAssignments::where('weekendID', $weekend->id)
            ->where('memberID', $member->id)
            ->where('roleID', $position->id)
            ->with(['user', 'role'])
            ->withoutGlobalScope('visibleWeekendsOnly')
            ->first();

        if (! $assignment) {
            flash()->error(
                'Error: It appears you were trying to edit the [' . $position->RoleName . '] assignment for [' . $member->name . '], but that assignment no longer exists.
                 This sometimes happens if you click the [Back] button but unexpectedly get an old list.
                 The latest list is now shown below.
                 Please try your edit again.'
            );
            return redirect('/team/' . $weekend->id . '/edit');
        }

        return view('weekend.team_changeperson')
            ->withWeekend($weekend)
            ->withRoles($this->positions)
            ->withStatuses(TeamAssignmentStatus::toSelectArray())
            ->withAssignment($assignment);
    }

    /**
     * Save updates to a given assignment
     *
     * @param  \Illuminate\Http\Request $request
     * @param Weekend $weekend
     * @param WeekendRoles $oldposition
     * @param User $member
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Weekend $weekend, WeekendRoles $oldposition, User $member)
    {
        $this->authorize('teamassignment.update', $weekend);

        // we use this as part of validation and flash messaging
        $this->getPositionsDropdown($weekend);

        $this->validate($request, [
            'roleID' => 'required|numeric|in:' . implode(',', $this->positions->where('id', '>', 0)->pluck('id')->toArray()),
            'comments' => 'nullable|string',
            'confirmed' => ['required', 'numeric', new EnumValue(TeamAssignmentStatus::class, false)],
        ]);

        // @TODO -- verify that any SD assignments are qualified SDs

        if (!$this->positions->contains('id', $oldposition->id) || !$this->getAvailableMembers($weekend)->contains($member)) {
            abort(403, 'Tampering is not allowed. Your IP address has been recorded. You should go back to watching Netflix.');
        }

        // check for duplicates
        $exists = WeekendAssignments::where('weekendID', $weekend->id)
            ->where('memberID', $member->id)
            ->where('roleID', $request->input('roleID'))
            ->withoutGlobalScope('visibleWeekendsOnly')
            ->first();
        if ($exists && ($request->input('roleID') != $oldposition->id)) {
            flash()->error('Problem: You attempted to assign someone to a role they are already assigned to. &nbsp;' . $member->name . ' is already assigned to role: [' . $this->positions->where('id', $request->input('roleID'))->first()->RoleName . ']. No changes made.');

            return redirect('/team/' . $weekend->id . '/edit');
        }

        $properties = [
            'roleID' => $request->input('roleID'),
            'confirmed' => $request->input('confirmed'),
            'comments' => $request->input('comments'),
            'updated_at' => Carbon::now()
        ];

        // do the update
        $assignment = WeekendAssignments::where('weekendID', $weekend->id)
            ->where('memberID', $member->id)
            ->where('roleID', $oldposition->id)
            ->withoutGlobalScope('visibleWeekendsOnly')
            ->first();

        if ($assignment) {
            $assignment->update($properties);

            flash()->success('Updated ' . User::find($member->id)->name . ' to ' . $this->positions->where('id', $request->input('roleID'))->first()->RoleName);

            event('WeekendAssignmentUpdated', [
                'name'        => User::find($member->id)->name,
                'oldposition' => $oldposition->RoleName,
                'newposition' => $this->positions->where('id', $request->input('roleID'))->first()->RoleName,
                'weekend'     => $weekend->weekend_number,
                'by'          => $request->user(),
            ]);
        } else {
            flash()->error('Problem: Your re-assignment failed. No changes made. Please re-check the team list below.');
        }

        return redirect('/team/' . $weekend->id . '/edit');
    }

    /**
     * Delete a given assignment
     *
     * @param Request $request
     * @param Weekend $weekend
     * @param WeekendRoles $position
     * @param User $member
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Request $request, Weekend $weekend, WeekendRoles $position, User $member)
    {

        $this->authorize('teamassignment.delete', $weekend);

        if ($request->user()->cannot('assign rectors')) {
            if ($position->id === 1) {
                abort(403, "You don't have permission to assign/unassign rectors.");
            }
        }

        $this->getPositionsDropdown($weekend);

        if (!$this->positions->contains('id', $position->id)) {
            abort(403, 'You do not have permission to delete that role assignment.');
        }

        $assignment = WeekendAssignments::where('weekendID', $weekend->id)
            ->where('memberID', $member->id)
            ->where('roleID', $position->id)
            ->with(['user', 'role'])
            ->withoutGlobalScope('visibleWeekendsOnly')
            ->first();

        if ($assignment) {
            $removal_role = $assignment->role->RoleName;
            $removal_name = $assignment->user->name;

            $assignment->delete();

            if ($position->id == 1) {
                $weekend->update(['rectorID' => 0]);
            }

            flash()->success('Deleted ' . User::find($member->id)->name . ' from ' . $removal_role);

            event('WeekendAssignmentRemoved', [
                'name'     => $removal_name,
                'position' => $removal_role,
                'weekend'  => $weekend->weekend_number,
                'by'       => $request->user(),
            ]);
        }

        return redirect()->back();
    }


    /**
     * Build dropdown list of authorized positions
     *
     * @param $weekend
     *
     * @return void
     */
    protected function getPositionsDropdown(Weekend $weekend): void
    {
        // get all available positions
        $this->positions = $this->getAllowedWeekendPositions($weekend);

        // prepare a dropdown select for positions/roles
        $select           = new WeekendRoles;
        $select->id       = 0;
        $select->RoleName = ' Please select';
        $this->positions->prepend($select);
    }

    /**
     * Get scoped list of authorized positions to be assigned
     *
     * @param $weekend
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getAllowedWeekendPositions(Weekend $weekend)
    {
        if (auth()->user()->hasRole('Admin')) {
            return WeekendRoles::all();
        }

        if ($weekend->rectorID == auth()->id()
            || $weekend->head_cha->contains(auth()->id())
        ) {
            return WeekendRoles::rolesRectorCanAssign()->get();
        }

        if (auth()->user()->can('make SD assignments')) {
            return WeekendRoles::rolesSpiritualAdvisorCanAssign()->get();
        }

        if (auth()->user()->can('edit team member assignments')) {
            return WeekendRoles::all();
        }

        if (auth()->user()->can('assign rectors')) {
            return WeekendRoles::assignRectors()->get();
        }

        return collect();
    }

    /**
     * build dropdown list of members
     *
     * @param $weekend
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getMembersDropdown(Weekend $weekend)
    {
        $users = $this->getAvailableMembers($weekend);

        $select = new User;
        $select->id = 0;
        $select->first = ' Please ';
        $select->last  = ' select';
        $users->prepend($select);

        return $users;
    }

    /**
     * Get list of members available to serve, for populating the drop-down dialog.
     * Based on what permissions the logged-in user has, and what gender weekend, etc
     *
     * @param $weekend
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getAvailableMembers(Weekend $weekend)
    {
        $query = User::active();

        // skip anyone who might accidentally get through as a candidate (ie: their weekend is the same as "this" weekend)
        $query = $query->where('weekend', '!=', $weekend->shortname);

        // limit to same gender as the weekend ... unless the logged in user is able to do Spiritual Advisor assignments
        if ($weekend->rectorID == auth()->id()
            || auth()->user()->hasRole('Admin')
            || auth()->user()->can('edit team member assignments')) {
            $query = $query->where('gender', $weekend->weekend_MF)
                ->orWhere('qualified_sd', 1);
        } else {
            if (auth()->user()->can('make SD assignments')) {
                $query = $query->where('qualified_sd', 1);
            } else {
                $query = $query->where('gender', $weekend->weekend_MF);
            }
        }

        return $query->orderBy('first')->orderBy('last')->get();
    }
}
