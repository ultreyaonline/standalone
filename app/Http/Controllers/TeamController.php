<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Weekend;
use App\Models\WeekendAssignments;
use App\Models\WeekendRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    protected $weekend;
    protected $weekends;
    protected $team;
    protected $positions;

    public function __construct(Weekend $weekend)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('role:Member');

        $this->weekend = $weekend;
        $this->positions = WeekendRoles::all();
        $this->weekends = Weekend::activeDescending()->get();
    }

    /**
     * Display the Team Roster, suitable for non-editor users
     *
     * @param  Weekend $weekend
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Weekend $weekend, Request $request)
    {
        $team = collect();

        if ($request->user()->can('see weekend team roster', $weekend)) {
            $team = $weekend->team_all_visibility;
        } else {
            $team = $weekend->team;
        }

        $view = 'weekend.roster';
        if (last($request->segments()) == 'csv') {
            $view = 'weekend.roster-CSV';
        }
        if (last($request->segments()) == 'candidatecsv') {
            $view = 'candidates.candidate-extract-CSV';
        }
        if (last($request->segments()) == 'team-emails') {
            $view = 'weekend.team-emails-CSV';
        }
        if (last($request->segments()) == 'team-email-positions') {
            $view = 'weekend.team-email-positions-CSV';
        }

        /**
         * Prepare an array of team+candidates for CSV export purposes
         */
        $people = $team->map(function ($position) {
            return [
                'role' => $position->role->RoleName,
                'name' => $position->user->name,
                'email' => $position->user->email,
                'weekend' => $position->user->weekend,
                'church' => $position->user->church,
                'phone' => $position->user->cellphone ?: $position->user->homephone,
                ];
        });
        if (last($request->segments()) == 'csv-all') {
            $weekend->candidates->each(function ($user) use ($people) {
                $people[] = [
                    'role'    => 'Candidate',
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'weekend' => $user->weekend,
                    'church' => $user->church,
                    'phone'   => $user->cellphone ?: $user->homephone,
                ];
            });
        }
        $weekend->team_and_candidates = $people;

        if (last($request->segments()) == 'csv-all') {
            return response(view('weekend.roster-CSV-download')->withWeekend($weekend), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $weekend->number_slug . strtolower($weekend->gender) . '-team-and-candidates.csv"',
            ]);
        }

        if (last($request->segments()) == 'candidatecsv-download') {
//            $weekend->candidates->transform(function ($c, $key) {
//                $c->first = $this->csvClean($c->first);
//                $c->last = $this->csvClean($c->last);
//                $c->address1 = $this->csvClean($c->address1);
//                $c->city = $this->csvClean($c->city);
//                $c->state = $this->csvClean($c->state);
//                $c->postalcode = $this->csvClean($c->postalcode);
//                $c->cellphone = $this->csvClean($c->cellphone);
//                $c->homephone = $this->csvClean($c->homephone);
//                $c->email = $this->csvClean($c->email);
//                $c->church = $this->csvClean($c->church);
//                $c->sponsor = $this->csvClean($c->sponsor);
//                $c->spousename = $this->csvClean($c->spousename);
//            });
            return response(view('candidates.candidate-extract-CSV-download')->withWeekend($weekend), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $weekend->number_slug . strtolower($weekend->gender) . '-candidate-extract.csv"',
            ]);
        }

        return view($view)
            ->withToday(Carbon::now())
            ->withWeekends($this->weekends)
            ->withWeekend($weekend)
            ->withPositions($this->positions)
            ->withTeam($team);
    }

    public function csvClean($val): string
    {
        $val = str_replace(',', '', $val); // or instead of removing commas, wrap field in double-quotes
        // but if we start enclosing fields in double-quotes, then we must also escape double-quotes with another double-quote, ie: ""
        return $val;
    }

    /**
     * Display form to import team data from scraped data
     * @return mixed
     */
    public function importview()
    {
        return view('admin.team_import')
            ->withWeekends($this->weekends)
            ->withWeekend($this->weekend);
    }

    /**
     * Store imported data from $_POST
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function import(Request $request)
    {
        // @TODO - instead of deleting everything and replacing with imported data, do individual updates/adds/deletes
        // @TODO -- this will help keep created/updated dates more in line with reality


        $users_added = $errors = $assigned = [];

        // determine which weekend this is importing into
        $weekend = Weekend::find($request->input('weekend'));

        // import CSV data from textarea field
        $imported_data = explode("\n", str_replace("\r\n", "\n", $request->input('import_data')));

        // purge existing assignments, so we can overwrite previous data for this team
        if (count($imported_data) > 7) {
            $deletedRows = WeekendAssignments::where('weekendID', $weekend->id)->delete();
            flash()->warning('Purged ' . $deletedRows . ' before importing.');
        }

        // read each row to parse
        foreach ($imported_data as $row) {
            if ($row == '') {
                continue;
            }
            // break into its parts
            $entry = new ImportMemberByRole($row);

            // check if member already exists (by email, or if email blank, use first+last name)
            $member = User::where(DB::raw('1'), 1) // 1=1 placeholder to allow the nested 'where or where' below
                            ->where(function ($query) use ($entry) {
                                $query->where('email', 'like', ($entry->email ?: 'EMAIL_IS_BLANK_SO_NO_MATCH') . '%')
                                    ->orWhere(function ($query) use ($entry) {
                                        $query->where('email', '=', '')
                                          ->where('first', $entry->first)
                                          ->where('last', $entry->last);
                                    });
                            });

                // restrict lookup to same gender of weekend, except for SDs
            if (!Str::contains($entry->rolename, 'Spiritual Dir')) {
                $member = $member->where('gender', $weekend->weekend_MF);
            }
            // do actual lookup
            $member = $member->first();

            // insert member if needed
            if (!$member) {
                $member = User::create([
                    'first' => $entry->first,
                    'last' => $entry->last,
                    'email' => $entry->email,
                    'username' => $entry->email,
                    'gender' => $weekend->weekend_MF,
                    'okay_to_send_serenade_and_palanca_details' => 1,
                    'interested_in_serving' => 0,
                    'active' => 0,
                    'community' => '(Unknown)',
                ]);
                $users_added[] = $member;
                flash()->success('Added member: ' . $member);
                event(\App\Events\UserAdded::class, ['user'=> $member, 'by'=> $request->user()]);
            }

            // read imported role and match with WeekendRoles table (fuzzy match due to different spellings)
            $role = WeekendRoles::where('RoleName', 'like', substr($entry->rolename, 0, 23) . '%')->first();

            if (!$role) {
                $errors[] = $row;
            }
            // insert record to weekend_assignments table
            $assigned[] = WeekendAssignments::create([
                'weekendID' => $weekend->id,
                'roleID' => $role->id,
                'memberID' => $member->id,
                'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
            ]);
            event('WeekendAssignmentAdded', ['imported'=> last($assigned), 'by'=> $request->user()]);
        }
        flash()->success('Imported ' . count($assigned). ' position assignments.');
        if (count($errors)) {
            flash()->error('Errors: ' . print_r($errors, true));
        }

        // when done, take to Roster View to review imported data
        return redirect('/weekend/' . $weekend->id . '/roster');
    }
}
