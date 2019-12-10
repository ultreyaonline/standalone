<?php

namespace App\Reports;

use App\Section;
use App\User;
use App\WeekendRoles;
use Illuminate\Support\Collection;

class LeadersWorksheet
{
    private $coreTalks;
    private $basicTalks;
    private $allTalks;
    private $taRoles;
    private $taRoleNames;
    private $rolesToExcludeFormerRectors;

    public function __construct()
    {
        $this->rolesToExcludeFormerRectors = WeekendRoles::rolesOfFormerRectors()->get();

        // Table Assistant roles go by multiple names; this collects them together:
        $this->taRoles = WeekendRoles::where('ReportName', 'TA')->get();
        $this->taRoleNames = $this->taRoles->pluck('RoleName');

        // talks -- used for filtering and for calculating number of talks given by qualifying type
        $this->coreTalks = WeekendRoles::where('isCoreTalk', 1)->get();
        $this->basicTalks = WeekendRoles::where('isBasicTalk', 1)->get();
        $this->allTalks   = WeekendRoles::where('isCoreTalk', 1)->orWhere('isBasicTalk', 1)->orderBy('sortorder')->get();
    }

    /**
     * List all members who have served X times and which roles
     *
     * @param string $gender
     * @param array $communities
     * @param bool $include_rectors
     * @param int $recent_filter
     * @param int $section_filter
     * @param bool $showAssistants
     * @return array
     */
    public function generate(string $gender = '', array $communities = null, bool $include_rectors = true, int $recent_filter = null, int $section_filter = null, bool $showAssistants = true): array
    {
        if ($gender === '') {
            $gender = strtoupper(request()->input('mw', auth()->user()->gender));
        }
        if (!\in_array($gender, ['M', 'W', 'F'], true)) {
            $gender = 'M';
        }

        if (null === $communities) {
            $communities = ['Local'];
        }

        $roles = $this->getRolesForReporting($include_rectors);



        // get everyone from the database
        $all_members = User::select('*')
            ->selectRaw('(community=?) as weight', [config('site.community_acronym')])
            ->orderBy('weight', 'DESC')
            ->active()
            ->where('gender', $gender)
            ->with('weekendAssignments')->withCount('weekendAssignments')
            ->with('weekendAssignmentsExternal')->withCount('weekendAssignmentsExternal');

        if ($communities === ['Local']) {
            $community   = config('site.local_community_filter', config('site.community_acronym'));
            $all_members = $all_members->where('community', $community);
        }
        if ($communities === ['Other']) {
            $community   = config('site.local_community_filter', config('site.community_acronym'));
            $all_members = $all_members->where('community', '!=', $community);
        }

        // retrieve sorted collection of members
        $members = $all_members->orderBy('last')->orderBy('first')->get();

        // Loop thru and add calculated values for times served and number of talks given
        $members = $this->calculateAndAddTimesServed($members, $include_rectors);

        // Collapse AH section roles into normal section role
        $members = $this->collapseAliasedRoles($members, $showAssistants);


        // Filter to include only a specific Section, if requested
        if ($section_filter) {
            $members = $this->filterToSpecificSection($members, $section_filter);
        }



        return [
            $roles,
            $members,
            $this->coreTalks,
            $this->basicTalks,
            $this->allTalks,
            $this->taRoles,
            $this->taRoleNames,
        ];
    }

    public function getSections()
    {
        return Section::all();
    }

    public function getRolesForReporting(bool $include_rectors = true)
    {
        // get list of all roles to be reported on
        $roles = WeekendRoles::whereNotIn('RoleName', ['Candidate'])
            ->whereNotIn('RoleName', ['Head Spiritual Director', 'Spiritual Director'])
            ->whereNotIn('RoleName', ['Ideal', 'Ideals', 'Church', 'Piety', 'Study', 'Action', 'Leaders', 'Environment', 'CCIA', 'Reunion Group', 'Fourth Day'])
            ->whereNotIn('ReportName', [''])
        ;
        // remove Rectors Roles if selected
        if ($include_rectors === false) {
            $roles = $roles->whereNotIn('RoleName', $this->rolesToExcludeFormerRectors->pluck('RoleName'));
        }
        // remove Table Assistant duplicate role titles
        $roles = $roles->whereNotIn('id', $this->taRoles->slice(1)->pluck('id'));

        return $roles->get();
    }

    /**
     * calculate times served for each member, excluding "rector" service if flagged
     *
     * @param Collection $all_members
     * @param bool $include_rectors
     * @return Collection
     */
    protected function calculateAndAddTimesServed($all_members, bool $include_rectors): Collection
    {
        $members = $all_members->each(function ($user, $iterator) use ($include_rectors) {

            if ($include_rectors === false) {
                $internal_positions = $user->weekendAssignments->filter(function ($position) {
                    return !$this->rolesToExcludeFormerRectors->contains('id', $position->roleID);
                });
                $external_positions = $user->weekendAssignmentsExternal->filter(function ($position) {
                    return !$this->rolesToExcludeFormerRectors->contains('RoleName', $position->RoleName);
                });
                $user->times_served = count($internal_positions) + count($external_positions);
            } else {
                $user->times_served = $user->weekend_assignments_count + $user->weekend_assignments_external_count;
            }

            $user->core_talks_count =
                $user->weekendAssignments->filter(function ($position) {
                    return $this->coreTalks->contains('id', $position->roleID);
                })->count()
                + $user->weekendAssignmentsExternal->filter(function ($position) {
                    return $this->coreTalks->contains('RoleName', $position->RoleName);
                })->count();

            $user->basic_talks_count =
                $user->weekendAssignments->filter(function ($position) {
                    return $this->basicTalks->contains('id', $position->roleID);
                })->count()
                + $user->weekendAssignmentsExternal->filter(function ($position) {
                    return $this->basicTalks->contains('RoleName', $position->RoleName);
                })->count();

            $user->all_talks_count = $user->core_talks_count + $user->basic_talks_count;

            $user->talks_local =
                $user->weekendAssignments->filter(function ($position) {
                    return $this->allTalks->contains('id', $position->roleID);
                })->map(function ($role, $key) {
                    return $this->allTalks->where('id', $role->roleID)->first()->toArray()['RoleName'];
                });

            $user->talks_external =
                $user->weekendAssignmentsExternal->filter(function ($position) {
                    return $this->allTalks->contains('RoleName', $position->RoleName);
                })->pluck('RoleName');
        });

        return $members;
    }

    /**
     * @param Collection $members
     * @param int $section_filter
     * @return Collection $members
     */
    protected function filterToSpecificSection(Collection $members, int $section_filter): Collection
    {
        return $members->filter(function ($member) use ($section_filter) {
            foreach ($member->weekendAssignments as $position) {
                if ($position->role->section_id === $section_filter) {
                    return true;
                }
            }
        });
    }

    /**
     * Communities may have multiple names for the same role.
     * And for reporting, sometimes "Assistant Head" of Sections may not be useful from a calculation perspective, so these can be collapsed too
     *
     * @param Collection $members
     * @param bool $showAssistants
     * @return Collection
     */
    protected function collapseAliasedRoles(Collection $members, bool $showAssistants = true): Collection
    {
        $members = $members->each(function ($member) use ($showAssistants) {
            // @TODO -- reduce (er, combine) by section_id
            // or maybe pull back the WeeekendAssignments by seciton_id instead of role_id ?
            // @TODO - is this a "map" or maybe map-reduce operation?
        });
        return $members;
    }
}
