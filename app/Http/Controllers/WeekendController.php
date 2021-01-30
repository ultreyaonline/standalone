<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeekendRequest;
use App\Models\Location;
use App\Models\User;
use App\Models\Weekend;
use App\Models\WeekendAssignments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;

class WeekendController extends Controller
{
    use LogsActivity;

    protected $weekend;
    protected $users;

    public function __construct(Weekend $weekend)
    {
        $this->middleware('auth');
        $this->middleware('role:Member');
        $this->weekend = $weekend;

        // build list for pulldowns
        $this->users = User::active()->orderBy('first')->orderBy('last')->get();
        $select = new User;
        $select->id=0;
        $select->first=' Please ';
        $select->last=' select';
        $this->users->prepend($select);
    }

    /**
     * Display a listing of Weekends.
     *
     */
    public function index(Request $request)
    {
        // defaults to first non-finished weekend (rolls over one day after the weekend finishes)
        $weekend = Weekend::nextWeekend()
            ->where('weekend_MF', $request->user()->gender)
            ->first();
        // fallback to most recent in case no future weekends defined yet
        if (!$weekend) {
            $weekend = Weekend::activeDescending()
            ->where('weekend_MF', $request->user()->gender)
            ->first();
        }

        return $this->show($request, null, $weekend);
    }

    /**
     * Display the specified weekend.
     *
     * @param Request $request
     * @param  int $id
     * @param  Weekend $weekend
     */
    public function show(Request $request, $id, $weekend = null)
    {
        if (null === $weekend) {
            if ($request->user()->can('create a weekend') || $request->user()->can('edit weekends')) {
                // show even non-visible
                $weekend = Weekend::find($id);
            } else {
                $weekend = Weekend::active($request->user()->id)->findOrFail($id);
            }
        }

        if (null === $weekend) {
            return view('weekend.none');
        }

        $weekends = Weekend::activeDescending($request->user()->id)->get();

        // date for displaying "candidate info as of X date"
        $stats_date = Carbon::now();
        if ($weekend->end_date !== null && $weekend->end_date < Carbon::now()) {
            $stats_date = $weekend->end_date;
        }

        // fix null dates
        if ($weekend->candidate_arrival_time === null) {
            $weekend->candidate_arrival_time = $weekend->start_date;
        }
        if ($weekend->sendoff_start_time === null) {
            $weekend->sendoff_start_time = $weekend->start_date;
        }
        if ($weekend->serenade_arrival_time === null) {
            $weekend->serenade_arrival_time = $weekend->start_date->addHours(44);
        }
        if ($weekend->serenade_scheduled_start_time === null) {
            $weekend->serenade_scheduled_start_time = $weekend->start_date->addHours(46);
        }
        if ($weekend->closing_arrival_time === null) {
            $weekend->closing_arrival_time = $weekend->start_date->addHours(70);
        }
        if ($weekend->closing_scheduled_start_time === null) {
            $weekend->closing_scheduled_start_time = $weekend->start_date->addHours(71);
        }

        $auth_user = $request->user();
        $user_can_do_sendoff =
            $auth_user->id == $weekend->sendoff_couple_id1 ||
            $auth_user->id == $weekend->sendoff_couple_id2 ||
            $auth_user->hasAnyRole('Pre-Weekend', 'Admin') ||
            $auth_user->id == $weekend->rectorID ||
            $weekend->head_cha->contains($auth_user->id);

        $can_see_sendoff_point_of_contact_report =
            $auth_user->hasAnyRole('Pre-Weekend', 'Admin', 'Secretariat') ||
            $auth_user->id == $weekend->rectorID ||
            $weekend->head_cha->contains($auth_user->id);

        $location = Location::where('location_name', strip_tags($weekend->weekend_location))->firstOrNew([]);
        $stats = $weekend->feePaymentStatistics();

        return view('weekend.show', [
            'weekend' => $weekend,
            'weekends' => $weekends,
            'id' => $id,
            'location' => $location,
            'stats_date' => $stats_date->toFormattedDateString(),
            'user_can_do_sendoff' => $user_can_do_sendoff,
            'can_see_sendoff_point_of_contact_report' => $can_see_sendoff_point_of_contact_report,
            'stats' => $stats,
            'user' => $auth_user,
        ]);
    }

    /**
     * Show the form for creating a new weekend.
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        if (! $request->user()->can('create a weekend')) {
            abort(404);
        }

        $weekend = new Weekend;

        return view('weekend.create', ['weekend' => $weekend, 'users' => $this->users]);
    }

    /**
     * Store a newly created weekend to database
     *
     * @param WeekendRequest $request
     */
    public function store(WeekendRequest $request)
    {
        $attributes = $request->all();

        if (isset($attributes['rectorID']) && $attributes['rectorID'] == 0) $attributes['rectorID'] = null;
        if (isset($attributes['emergency_poc_id']) && $attributes['emergency_poc_id'] == 0) $attributes['emergency_poc_id'] = null;

        $weekend = Weekend::create($attributes);

        flash()->success('Weekend: ' . $weekend->fullname. ' added.');
        event('WeekendAdded', ['weekend'=> $weekend, 'by'=> $request->user()]);

        // assign the Rector role
        if ($weekend->rectorID) {
            $assignment = WeekendAssignments::create([
                'weekendID' => $weekend->id,
                'roleID' => 1,
                'memberID' => $weekend->rectorID,
                'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
            ]);
        }

        return redirect('/weekend/' . $weekend->id);
    }
//    $weekend = Weekend::create($request->all());  // create, without a User ID (weekends don't need a creator userid)
//		$request->user()->weekends()->create($request->all()); // add the userid and save it

    /**
     * Show the form for editing the specified weekend.
     *
     * @param  Weekend $weekend
     * @param Request $request
     */
    public function edit(Weekend $weekend, Request $request)
    {
        if (! $request->user()->can('edit weekends') && ! $request->user()->can('edit weekend photo') && $weekend->rectorID != $request->user()->id) {
            abort(404);
        }

        return view('weekend.edit', ['weekend' => $weekend, 'users' => $this->users]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Weekend $weekend
     * @param  WeekendRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function update(Weekend $weekend, WeekendRequest $request)
    {
        if (! $request->user()->can('edit weekends') && $weekend->rectorID != $request->user()->id) {
            abort(404);
        }

        $orig_rectorID = $weekend->rectorID;

        $attributes = $request->all();

        if (isset($attributes['rectorID']) && $attributes['rectorID'] == 0) $attributes['rectorID'] = null;
        if (isset($attributes['emergency_poc_id']) && $attributes['emergency_poc_id'] == 0) $attributes['emergency_poc_id'] = null;

        $weekend->update($attributes);
        flash()->success('Weekend: ' . $weekend->name . ' updated.');
        event('WeekendUpdated', ['weekend'=> $weekend, 'by'=> $request->user()]);

        // if Rector has changed, add/update the WeekendAssignments record too.
        if (empty($orig_rectorID) && !empty($weekend->rectorID)) {
            $assignment = WeekendAssignments::create([
                'weekendID' => $weekend->id,
                'roleID' => 1,
                'memberID' => $weekend->rectorID,
                'confirmed' => \App\Enums\TeamAssignmentStatus::Accepted,
            ]);
        } elseif ($orig_rectorID != $weekend->rectorID && $weekend->rectorID > 0) {
            $assignment = WeekendAssignments:: where('weekendID', $weekend->id)
                ->where('roleID', 1)
                ->withoutGlobalScope('visibleWeekendsOnly')
                ->first();
            $assignment->update(['memberID' => $weekend->rectorID]);
        } elseif ((int)$weekend->rectorID === 0) {
            $assignment = WeekendAssignments:: where('weekendID', $weekend->id)
                ->where('roleID', 1)
                ->withoutGlobalScope('visibleWeekendsOnly')
                ->first();
            if ($assignment) {
                $assignment->delete();
            }
        }

        return redirect('/weekend/' . $weekend->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  Weekend $weekend
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy(Request $request, Weekend $weekend)
    {
        if (! $request->user()->can('delete weekends')) {
            abort(404);
        }

        $weekend->delete();

        flash()->success('Weekend: ' . $weekend->name . ' deleted.');
        event('WeekendDeleted', ['weekend'=> $weekend, 'by'=> $request->user()]);

        return redirect('/weekend/');
    }


    /**
     * @param Request $request
     * @param Weekend $weekend
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateBannerPhoto(Request $request, Weekend $weekend)
    {
        if (!$weekend) {
            return redirect('/weekend/'. $weekend);
        }

        if (! $request->user()->can('edit weekends') && ! $weekend->rectorID == $request->user()->id) {
            abort(404);
        }

        $this->validate($request, [
            'banner_url' => 'mimes:jpg,jpeg,png,gif,bmp'
        ], [
            'image' => 'The photo must be a valid image file (PNG JPG GIF formats allowed)',
        ]);

        $file = request()->file('banner_url');
        if ($file) {
            $weekend->addMedia($file)->toMediaCollection('banner');
            flash()->success('Photo updated.');
        }
        return redirect('/weekend/'. $weekend->id);
    }

    public function deleteBannerPhoto(Request $request, Weekend $weekend)
    {
        if (!$weekend) {
            return redirect('/weekend/'. $weekend);
        }

        if (! $request->user()->can('edit weekends') && ! $weekend->rectorID == $request->user()->id) {
            abort(404);
        }

        $weekend->clearMediaCollection('banner');

        flash()->success('Photo deleted.');

        return redirect('/weekend/'. $weekend->id);
    }

    /**
     * @param Request $request
     * @param Weekend $weekend
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateTeamPhoto(Request $request, Weekend $weekend)
    {
        if (! $request->user()->can('edit weekends') && ! $request->user()->can('edit weekend photo') && ! $weekend->rectorID == $request->user()->id) {
            abort(404);
        }

        if (!$weekend) {
            return redirect('/weekend/'. $weekend);
        }

        $this->validate($request, [
            'teamphoto' => 'mimes:jpg,jpeg,png,gif,bmp'
        ], [
            'image' => 'The photo must be a valid image file (PNG JPG GIF formats allowed)',
        ]);

        $file = request()->file('teamphoto');
        if ($file) {
            $weekend->addMedia($file)->toMediaCollection('teamphoto');
            flash()->success('Photo updated.');
        }
        return redirect('/weekend/'. $weekend->id);
    }

    public function deleteteamPhoto(Request $request, Weekend $weekend)
    {
        if (!$weekend) {
            return redirect('/weekend/'. $weekend);
        }

        if (! $request->user()->can('edit weekends') && ! $request->user()->can('edit weekend photo') && ! $weekend->rectorID == $request->user()->id) {
            abort(404);
        }

        $weekend->clearMediaCollection('teamphoto');

        flash()->success('Photo deleted.');

        return redirect('/weekend/'. $weekend->id);
    }
}
