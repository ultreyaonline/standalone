<?php

namespace App\Policies;

use App\Enums\WeekendVisibleTo;
use App\User;
use App\Weekend;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TeamAssignmentsPolicy
 *
 * NOTE: This policy is NOT directly linked to a Model
 * Instead, its Gate definitions are specified in TeamAssignmentController::__construct
 *
 * @package App\Policies
 */
class TeamAssignmentsPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->hasRole('Super-Admin')) {
            return true;
        }
    }

    public function view(User $user, Weekend $weekend)
    {
        // "leaders" = rector, head cha, AH chas
//        if ($weekend->weekend_leaders->contains($user->id)) {
//            return true;
//        }

        // section heads
//        if ($user->rolesForWeekend($weekend->id)->first()->role->isDeptHead) {
//            return true;
//        }


        if ($weekend->visibility_flag >= WeekendVisibleTo::HeadChas && $weekend->rover === $user->id) {
            return true;
        }

        // for now you can view if you can create
        return $this->create($user, $weekend);
    }

    /**
     * Determine whether the user can make assignments
     *
     * @param  \App\User $user
     * @param $weekend
     * @return mixed
     */
    public function create(User $user, Weekend $weekend)
    {
        // for now, deferring to 'Update', since it's the same
        return $this->update($user, $weekend);
    }

    /**
     * Determine whether the user can update the Team
     * Restricted to Rector, Head Cha, Men/Women Leader
     *
     * @param  \App\User $user
     * @param Weekend $weekend
     * @return mixed
     */
    public function update(User $user, Weekend $weekend)
    {
        if ($weekend->rectorID == $user->id) {
            return true;
        }

        if ($weekend->visibility_flag >= WeekendVisibleTo::HeadChas && $weekend->head_cha->contains($user->id)) {
            return true;
        }

        if ($user->can('edit team member assignments')) {
            if ($user->gender === $weekend->weekend_MF || $user->hasRole('Admin') || $user->hasRole('President')) {
                return true;
            }
        }

        if ($user->can('make SD assignments')) {
            return true;
        }

        abort(403, 'Must be rector of this weekend, or an authorized Secretariat member');
    }

    /**
     * Determine whether the user can delete the assignment.
     *
     * @param  \App\User $user
     * @param  \App\WeekendAssignments $assignment
     *
     * @return mixed
     */
    public function delete(User $user, Weekend $weekend)
    {
        return $this->update($user, $weekend);

        // * @TODO -- This should be $assignment, not $weekend.
        // @TODO And should prevent deleting 'self'

    }
}
