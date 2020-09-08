<?php

namespace App\Http\Controllers;

use App\Reports\LeadersWorksheet;
use App\Models\User;
use App\Models\Weekend;
use App\Models\WeekendAssignments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportsController extends Controller
{
    protected $paginationThreshold = 25;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->paginationThreshold = (int)config('site.pagination_threshold', $user->getPerPage());
    }

    public function interestedInServing(Request $request)
    {
        $gender = strtoupper($request->input('mw', $request->user()->gender));

        $members = User::select('*')
            ->selectRaw('(community=?) as weight', [config('site.community_acronym')]) // weighted
            ->active()
            ->with(['weekendAssignments', 'weekendAssignmentsExternal'])
            ->withCount('weekendAssignments')
            ->withCount('weekendAssignmentsExternal')
            ->where('interested_in_serving', 1)
            ->where('gender', $gender)
            ->orderBy('weight', 'DESC') // weighted
            ->orderBy('last')
            ->orderBy('first')
            ->get();

        // checkboxes
        $candidate_filter = $request->has('c') && $request->input('c') == ['yes'];
        $served_filter    = $request->has('s') && $request->input('s') == ['yes'];
        // if both are unchecked, the report is pointless, so re-check both if unchecked
        if (!$candidate_filter && !$served_filter) {
            $candidate_filter = $served_filter = true;
        }

        // limit to minimum NUMBER of served weekends in current database
        $recent_filter = (int)request()->input('l', 3);

        // if the limit is set to 0, we show all service and interested-in-serving results
        // if the limit is 1-20 then we filter down the results based on that limitation
        if ($recent_filter > 0) {
            $recent_weekends = Weekend::ended()->where('weekend_MF', $gender)->get()->take(-1 * $recent_filter);

            //  filter down to only where member's service records are related to $recent_weekends, OR they were a candidate on one of those weekends
            $members = $members->filter(function ($member) use ($recent_weekends, $candidate_filter, $served_filter) {
                $served = $served_filter && $recent_weekends->pluck('id')->intersect($member->weekendAssignments->pluck('weekendID'))->count();
                $candidate = $candidate_filter && $recent_weekends->contains($member->weekend_record);

                return $served || $candidate;
            });
        }

        if ($request->has('csv')) {
            return response(view('reports.interested_in_serving_CSV', compact('members')), 200, [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . config('site.community_acronym') . '-' . $gender . '-interested_in_serving-' . date('Y-m-d') . '.csv"',
            ]);
        }

        return view('reports.interested_in_serving', compact('members', 'gender', 'recent_filter', 'candidate_filter', 'served_filter'));
    }

    /**
     * List of all members who have served X times and which roles
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function leadersWorksheet(Request $request)
    {
        if (! $request->user()->can('use leaders worksheet')
            && ! $request->user()->isAnActiveRector('heads')
            && ! $request->user()->isAnActiveRover()
            &&  $request->user()->cannot('use rectors tools')
        ) {
            abort(403);
        }

        $gender = strtoupper(request()->input('mw', auth()->user()->gender));

        $min_times_served = (int)request()->input('t', 3);
        $recent_filter = (int)request()->input('p', 0);
        $section_filter   = (int)request()->input('s', 0);

        $include_rectors = (bool)request()->input('r', true);
        $showAssistants = (bool)request()->input('a', true);

        $communities = request()->has('c') ? request()->input('c') : ['Local'];

        $worksheet = new LeadersWorksheet;
        [
            $roles,
            $members,
            $coreTalks,
            $basicTalks,
            $allTalks,
            $taRoles,
            $taRoleNames,
        ] = $worksheet->generate($gender, $communities, $include_rectors, $recent_filter, $section_filter, $showAssistants);

        // filter by minimum number of times served
        $members = $members->filter(function ($user) use ($min_times_served) {
            return $user->times_served >= $min_times_served;
        });

        if (request()->input('sort') === 'times') {
            $members = $members->sortByDesc('times_served');
        }
        if (request()->input('sort') === 'core') {
            $members = $members->sortByDesc('core_talks_count');
        }
        if (request()->input('sort') === 'talks') {
            $members = $members->sortByDesc('all_talks_count');
        }

        $sections = $worksheet->getSections();



        $titles = ['Name','Weekend','Community','Total Service'];
        $roles->each(function($role) use (&$titles) {
            $titles[] = $role->ReportName;
        });
        $titles[] = 'Core Talks';
        $titles[] = 'Basic Talks';
        $titles[] = 'Talks Given';

        $reportData = [];
        $reportData[] = $titles;

        foreach($members as $member) {
            $row = [];
            $row['Name'] = $member->name;
            $row['Weekend'] = $member->weekend;
            $row['Community'] = $member->community;
            $row['Total Service'] = $member->times_served;

            foreach ($roles as $role) {
                if ($taRoles->contains('id', $role->id)) {
                    $number = $member->weekendAssignments->filter(function ($position) use ($taRoles) {
                            return $taRoles->contains('id', $position->roleID);
                        })->count() +
                        $member->weekendAssignmentsExternal->filter(function ($position) use ($taRoleNames) {
                            return $taRoleNames->contains($position->RoleName);
                        })->count();
                } else {
                    $number = $member->weekendAssignments->filter(function ($position) use ($role) {
                            return $position->roleID === $role->id;
                        })->count() +
                        $member->weekendAssignmentsExternal->filter(function ($position) use ($role) {
                            return $position->RoleName === $role->RoleName;
                        })->count();
                }
                $row[$role->ReportName] = $number;
            }

            $row['Core Talks'] = $member->core_talks_count;
            $row['Basic Talks'] = $member->basic_talks_count;
            $row['Talks Given'] = implode(', ', \array_merge($member->talks_local->toArray(), $member->talks_external->toArray()));
            if (!empty($row['Talks Given'])) {
                $row['Talks Given'] = '"' . $row['Talks Given'] . '"';
            }

            $reportData[] = $row;
        }
        $reportData = collect($reportData);

        return view(
            'reports.leaders_worksheet',
            compact(
                'roles',
                'members',
                'coreTalks',
                'basicTalks',
                'allTalks',
                'taRoles',
                'taRoleNames',
                'gender',
                'sections',
                'reportData',
            )
        )->withToday(Carbon::now());
    }

    /**
     * "Rector List" is a list of Pescadores and the preferred positions they need to further their qualifications for rector.
     */
    public function rectorsList(string $gender): void
    {
        /**
         * For example, in our community a man needs to serve as a Gopher (among other positions) to be qualified for rector.
         * So, I might get a list that says Al, Bob and Chuck need to serve as a Gopher;
         * or that Dan, Ed and Fred need to give a rollo;
         * or Guy, Hal or Irving need to be a head of a service area.
         *
         * Your Secretariat may have special extra qualifications
         *
         * A good starting point for rectors is a list of people that have served 3 or more times in the community
         * and what position they can serve in to advance their qualifications, or what positions you think they'd excel at.
         */
    }


    /**
     * Build the report of Inactive members
     *
     * @param Request $request
     * @param int $perPage
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function inactive(Request $request, $perPage = 0)
    {
        $user = $request->user();
        // if not allowed to see other members, just show self
        if ($user->cannot('use rectors tools') && !$user->hasAnyRole(['Secretariat', 'Mens Leader', 'Womens Leader', 'Rector Selection'])) {
            return redirect('/members');
        }

        // filter out candidates of future weekends
        $w = Weekend::futureAnyStatus()->get();

        $future_weekends = collect();
        $w->each(function ($weekend, $key) use ($future_weekends) {
            $future_weekends->push($weekend->shortname);
        });

        $users = User::inactive()
//            ->onlyLocal()
            ->whereNotIn('weekend', $future_weekends->unique())
            ->orderBy('last', 'asc')->orderBy('first', 'asc');


        $scope_prefix = 'Inactive/Unsubscribed ';
        $scope_title = 'Inactive/Unsubscribed Members';

        // check pagination defaults
        if ($perPage === 0) {
            $perPage = $this->paginationThreshold;
        }

        return view('members.community_directory_basic', ['users' => $users->paginate($perPage), 'scope_prefix' => $scope_prefix, 'scope_title' => $scope_title]);
    }

    /**
     * Members without Avatar Image
     *
     * @param Request $request
     * @param int $perPage
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function membersWithoutAvatar(Request $request, $perPage = 0)
    {
        // filter out candidates of future weekends
        $w = Weekend::futureAnyStatus()->get();

        $future_weekends = collect();
        $w->each(function ($weekend, $key) use ($future_weekends) {
            $future_weekends->push($weekend->shortname);
        });

        $users = User::query()
            ->role('Member')
            ->whereIn('avatar', [null, ''])
            ->onlyLocal()
            ->whereNotIn('weekend', $future_weekends->unique())
            ->orderBy('last', 'asc')->orderBy('first', 'asc');

        $scope_prefix = 'No Image Present for ';
        $scope_title = 'Members With No Image';

        // check pagination defaults
        if ($perPage === 0) {
            $perPage = $this->paginationThreshold;
        }

        return view('members.community_directory_basic', ['users' => $users->paginate($perPage), 'scope_prefix' => $scope_prefix, 'scope_title' => $scope_title]);
    }


    public function serviceByPosition(Request $request)
    {
        $gender = strtoupper($request->input('g', $request->user()->gender));

        $data = WeekendAssignments::select('weekend_assignments.*', 'weekend_roles.RoleName', 'weekend_roles.sortorder', 'weekends.*')
            ->join('weekend_roles', 'weekend_assignments.roleID', '=', 'weekend_roles.id')
            ->join('weekends', 'weekend_assignments.weekendID', '=', 'weekends.id')
            ->where('weekend_MF', $gender)
            ->orderBy('weekend_roles.sortorder', 'asc')
            ->orderBy('weekend_roles.id', 'asc')
            ->orderBy('weekendID', 'asc')
            ->with(['user', 'role'])
            ->get();

        $assignments = $data->groupBy('RoleName');

        return view('reports.service_by_position', compact('assignments', 'gender'));
    }
}
