<?php

namespace App\Http\Controllers;

use App\Notifications\PrayerWheelChangeNotification;
use App\PrayerWheel;
use App\PrayerWheelSignup;
use App\User;
use App\Weekend;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class PrayerWheelController extends Controller
{
    protected $wheels;
    protected $users;
    protected $times = [];

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => 'index']);

        // get all prayer wheels of weekends having future dates
        $this->wheels = PrayerWheel::with(['weekend' => function ($query) {
            $query->where('end_date', '>', Carbon::now());
        }])->get()
            // filter to remove "completed"/other weekends:
            ->reject(function ($w, $key) {
                return null === $w->weekend;
            });


        // build list for pulldowns
        $this->users   = User::active()->orderBy('first')->orderBy('last')->get();
        $select        = new User;
        $select->id    = 0;
        $select->first = ' Please ';
        $select->last  = ' select';
        $this->users->prepend($select);

        $this->loadTimeslots();
    }

    /**
     * display a list of all upcoming Prayer Wheel dates
     */
    public function index()
    {
        return view('prayerwheel.index')->withWheels($this->wheels);
    }

    /**
     * Display a list of participants for a given weekend prayer times
     * Also allows for editing or managing own participation
     *
     * @param int $wheel
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(int $wheel, Request $request)
    {
        $member = Auth::check() ? $request->user() : null;

        if (! $member) {
            return redirect()->route('prayerwheels');
        }

        $wheel = PrayerWheel::where('id', $wheel)
            ->with(['weekend', 'signups'])
            ->first();
        if (!$wheel) {
            return redirect(url('/prayerwheel'));
        }

        $gate = new Gate;
        $gate::define('see prayer wheel names', function ($ability) use ($wheel, $member) {
            return $wheel->weekend->team->whereIn('roleID', [1, 2, 3, 42, 43])->where('memberID', $member->id)->first() !== null;
        });

        $canSeePrayerWheelNames = config('site.prayerwheel_names_visible_to_all') || $member->can('see prayer wheel names');
        $canEditPrayerWheel = $member->can('edit prayer wheel');
        $canSeeAllNames = $canEditPrayerWheel || $canSeePrayerWheelNames;

        $countPositionsFilled = $wheel->signups->groupBy('timeslot')->count();
        $countPositionsRemaining = $this->times->count() - $countPositionsFilled;
        $allowDoublingUp = ($countPositionsRemaining <= (int)config('site.prayerwheel_empty_before_doubles', 6));


        $csvData = null;
        if ($canSeeAllNames) {
            foreach ($this->times as $spot) {
                $signups = $wheel->signups->where('timeslot', $spot['position'])->pluck('memberID');
                if ($signups->count() === 0) {
                    $signups->push(0);
                }
                $names = [];
                foreach ($signups as $person_id) {
                    $names[] = $person_id ? $this->csvClean(\App\User::firstOrNew(['id' => $person_id])->name) : ' ';
                }
                $csvData[] = $spot + ['names' => '"' . implode(', ', $names) . '"'];
            }
            $csvData = collect($csvData);
        }

        return view('prayerwheel.show', compact('csvData'))
            ->withWheels($this->wheels->reverse())
            ->withWheel($wheel)
            ->withWeekend($wheel->weekend)
            ->withHours($this->times)
            ->withCountPositionsFilled($countPositionsFilled)
            ->withCountPositionsRemaining($countPositionsRemaining)
            ->withAllowDoublingUp($allowDoublingUp)
            ->withMember($member)
            ->withCanSeePrayerWheelNames($canSeePrayerWheelNames)
            ->withCanEditPrayerWheel($canEditPrayerWheel)
            ->withCanSeeAllNames($canSeeAllNames)
            ->withUsers($this->users);
    }

    public function csvClean($val)
    {
        $val = str_replace('"', ' ', $val);
        return $val;
    }

    /**
     * Lookup weekend by slug, create wheel record if necessary, and use pass to show()
     * @param string $slug weekend slug with name,number,gender
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function slug(string $slug, Request $request)
    {
        preg_match('/([a-zA-Z]+)([\d]+)([mw])/', $slug, $matches);
        [, $community, $number, $gender] = $matches;

        // @TODO - should include $community in this lookup:
        $weekend = Weekend::numberAndGender($number, $gender)->first();

        if (! $weekend) {
            return redirect(route('prayerwheels'));
        }

        if ($weekend->prayerwheel) {
            return $this->show($weekend->prayerwheel->id, $request);
        }

        // if we get here, there isn't a wheel record yet, so create one
        $wheel = PrayerWheel::create(['weekendID' => $weekend->id]);
        return $this->show($wheel->id, $request);
    }

    /**
     * store a person to a timeslot
     * @param Request $request
     * @param PrayerWheel $wheel
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, PrayerWheel $wheel)
    {
        $this->validate($request, [
            'memberID' => 'required|numeric|exists:users,id',
            'hour'     => 'numeric',
        ]);

        if ($wheel->weekend->has_ended && ! $request->user()->can('edit prayer wheel')) {
            abort('403', 'Unauthorized edit to closed Prayer Wheel. Activity logging has been enabled.');
        }

        $timeslot = $request->input('hour');
        $oldMember = $request->input('old');
        $member = $request->input('memberID');

        // for messaging
        $timedetails = $this->times->where('position', $timeslot)->first();

        // can only edit my own, unless am admin
        if (! $request->user()->can('edit prayer wheel') && $request->user()->id != $member) {
            flash('Not authorized to sign up other people.', 'danger');
            return redirect('/prayerwheel/' . $wheel->id);
        }

        try {
            PrayerWheelSignup::updateOrCreate([
                'timeslot' => $timeslot,
                'memberID' => $oldMember,
                'wheel_id' => $wheel->id,
            ], [
                'timeslot' => $timeslot,
                'memberID' => $member,
                'wheel_id' => $wheel->id,
            ])->save();
            flash('Signed up ' . User::find($member)->name . ' for ' . $timedetails['day'] . ' ' . $timedetails['hour_to'], 'success');

            Notification::route('mail', config('site.notify_PrayerWheelChanges'))
                ->notify(new PrayerWheelChangeNotification('Assigned ', $timeslot . ': ' . $timedetails['day'] . ' ' . $timedetails['hour_to'], $wheel, User::find($member), $request->user()));
        } catch (QueryException $exception) {
            flash('Error: Could not store the requested selection', 'danger');
        }

        return redirect('/prayerwheel/' . $wheel->id);
    }

    /**
     * Delete an assigned timeslot
     *
     * @param Request $request
     * @param PrayerWheel $wheel
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy(Request $request, PrayerWheel $wheel)
    {
        $this->validate($request, [
            'memberID' => 'required|numeric|exists:users,id',
            'hour'     => 'numeric',
        ]);

        if ($wheel->weekend->has_ended && ! $request->user()->can('edit prayer wheel')) {
            abort('403', 'Unauthorized edit to closed Prayer Wheel. Activity logging has been enabled.');
        }

        $timeslot = $request->input('hour');
        $member = $request->input('memberID');
        $timedetails = $this->times->where('position', $timeslot)->first();

        if (! $request->user()->can('edit prayer wheel') && $request->user()->id != $member) {
            flash('Not authorized to edit signups of others.', 'danger');
            return redirect('/prayerwheel/' . $wheel->id);
        }

        $signup = PrayerWheelSignup::where('wheel_id', $wheel->id)
            ->where('memberID', $member)
            ->where('timeslot', $timeslot)
            ->first();

        if ($signup) {
            $signup->delete();
            flash('Removed ' . User::find($member)->name . ' from ' . $timedetails['day'] . ' ' . $timedetails['hour_to'], 'success');

            Notification::route('mail', config('site.notify_PrayerWheelChanges'))
                ->notify(new PrayerWheelChangeNotification('Deleted', $timeslot . ': ' . $timedetails['day'] . ' ' . $timedetails['hour_to'], $wheel, User::find($member), $request->user()));
        } else {
            flash('Error: Could not store the requested change', 'danger');
        }

        return redirect('/prayerwheel/' . $wheel->id);
    }



    /**
     * preload the collection of timeslots
     */
    protected function loadTimeslots()
    {
        $this->times = PrayerWheel::getTimeSlots();
    }
}
