<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use App\Models\Weekend;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    protected $types;
    protected $users;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->middleware('password.confirm')->except(['index', 'show']);

        // build types list for pulldown
        $this->types = collect(Event::TYPES);
        $this->types->prepend('Please Select ...');

        // build contacts list for pulldown
        $this->users = User::active()->onlyLocal()->orderBy('first')->orderBy('last')->get();
        $select = new User;
        $select->id = 0;
        $select->first = ' Please ';
        $select->last = ' select';
        $this->users->prepend($select);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $weekends = Weekend::future()->get();
        $weekendEvents = $weekends->map(function ($weekend) { return $this->getWeekendAsEventObject($weekend); });
        $reuniongroups = collect();
        $user = $request->user();
        $guest = $user === null;

        if ($guest) {
            $events = Event::public();
        } elseif ($user->can('edit events')) {
            $events = Event::query();
            $reuniongroups = Event::query();
        } elseif ($user->hasRole('Member')) {
            $events = Event::active();
            $reuniongroups = Event::active();
        }

        $events = $events->withoutReunionGroups()->orderBy('start_datetime')->get();

        if ($reuniongroups->count()) {
            $reuniongroups = $reuniongroups->onlyReunionGroups()->orderBy('start_datetime')->get();
        }

        // merge and re-sort
        $weekendEvents->each(function ($weekend) use ($events) {
            $events->push($weekend);
        });
        $events = $events->sortBy('start_datetime');

        return view('events.index', compact('events', 'reuniongroups'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::active()->find($id);

        return view('events.show', compact('event'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $event = new Event(['is_enabled'=>true]);

        $event->type = $request->input('type', '');

        abort_if(!$request->user()->can('create events'), 403);

        return view('events.create', ['event' => $event, 'types' => $this->types, 'users' => $this->users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        abort_if(!$request->user()->can('create events'), 403);

        $this->validate($request, [
            'name' => 'required',
            'type' => Rule::in(collect(Event::TYPES)->keys()),
            'start_datetime' => 'required',
            'end_datetime' => 'required',
        ]);

        // get list of date fields
        $dummy = Event::firstOrNew([]);

        $dateFields = $dummy->getDates();

        // build new event in memory
        $event = new Event($request->except($dateFields));

        foreach ($event->getCasts() as $theField => $itsType) {
            // handle checkboxes
            if ($itsType === 'boolean') {
                $request->merge([$theField => $request->filled($theField)]);
                continue;
            }
            // avoid saving null dates
            if ($itsType === 'datetime') {
                if (Str::endsWith($theField, '_at')) {
                    continue;
                }
                if ($request->filled($theField)) {
                    $event->$theField = $request->input($theField) . (strlen($request->input($theField)) === 16 ? ':00' : '');
                }
            }
        }

        if ($event->location_url && !Str::startsWith($event->location_url, 'http')) {
            $event->location_url = 'http://' . $event->location_url;
        }
        if ($event->map_url_link && !Str::startsWith($event->map_url_link, 'http')) {
            $event->map_url_link = 'http://' . $event->map_url_link;
        }

        $event->posted_by = $request->user()->id;

        $event->save();

        flash()->success('Event: ' . $event->name . ' added.');
        event('EventCreated', ['event' => $event, 'by' => $request->user()]);

//        return view('events.show', compact('event'));
        return redirect('/events/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Event $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Event $event = null)
    {
        if (!$request->user()->can('edit events')) {
            return redirect('/events/' . $event->id, 403);
        }

        return view('events.edit', ['event' => $event, 'types' => $this->types, 'users' => $this->users]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Event $event
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Event $event)
    {
        if (!$request->user()->can('edit events')) {
            return redirect('/events/' . $event->id, 403);
        }

        $this->validate($request, [
            'name' => 'required',
            'type' => Rule::in(collect(Event::TYPES)->keys()),
            'start_datetime' => 'required',
            'end_datetime' => 'required',
        ]);

        $dateFields = [];

        foreach ($event->getCasts() as $theField => $itsType) {
            // handle checkboxes
            if ($itsType === 'boolean') {
                $request->merge([$theField => $request->filled($theField)]);
                continue;
            }

            // handle null dates
            if ($itsType === 'datetime') {
                $event->$theField = $request->filled($theField)
                    ? $request->input($theField) . (strlen($request->input($theField)) === 16 ? ':00' : '')
                    : null;
                $dateFields[] = $theField;
            }
        }

        // save by applying Request data (except date fields, handled above)
        $event->update($request->except($dateFields));

        flash()->success('Event: ' . $event->name . ' updated.');
        event('EventUpdated', ['event' => $event, 'by' => $request->user()]);

        return redirect('/events/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  Event $event
     * @return Response
     * @throws \Exception
     */
    public function destroy(Request $request, Event $event)
    {
        if (!$request->user()->can('delete events')) {
            return redirect('/events/' . $event->id, 403);
        }

        $event->delete();

        flash()->success('Event: ' . $event->name . ' deleted.');
        event('EventDeleted', ['event' => $event, 'by' => $request->user()]);

        return redirect('/events/');
    }

    public function download($event_id)
    {
        if (!Str::startsWith($event_id, 'w')) {
            $eventRecord = Event::find($event_id);

            if ($eventRecord !== null) {
                return $this->doCalendarEntryDownload($eventRecord);
            }
        }
        // if starts with w, then it's a Weekend
        $eventRecord = Weekend::find(substr($event_id, 1));
        if ($eventRecord !== null) {
            return $this->doCalendarEntryDownload($this->getWeekendAsEventObject($eventRecord));
        }

        abort('404', 'Sorry, could not find that calendar entry.');
    }

    protected function doCalendarEntryDownload(Event $event): Response
    {
        $vCalendar = new \Eluceo\iCal\Component\Calendar(config('app.url') . '/calendar');
        $vEvent = new \Eluceo\iCal\Component\Event();

        $vEvent
            ->setDtStart(new \DateTime($event->start_datetime->toDateTimeString()))
            ->setDtEnd(new \DateTime($event->end_datetime->toDateTimeString()))
            ->setLocation($event->location_name . ($event->address ? ' ' . $event->address : ''))
            ->setSummary($event->name)
            ->setDescription($event->description)
            ->setUrl($event->location_url ?: $event->map_link)
            ->setUseUtc(false);

        $vCalendar->addComponent($vEvent);

        return Response::create($vCalendar->render())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="cal-' . config('site.community_acronym') . '-' . $event->name . '.ics"');
    }

    /**
     * Get a Weekend's data back as an Event object suitable for Calendar display/download
     * @param Weekend $weekend
     * @return Event
     */
    protected function getWeekendAsEventObject(Weekend $weekend)
    {
        $location = Location::where('location_name', strip_tags($weekend->weekend_location))->firstOrNew([]);
        $event = new Event([
            'event_key' => ($weekend->tresdias_community === config('site.community_acronym')) ? $weekend->long_name_with_number : $weekend->weekend_full_name,
            'type' => 'weekend',
            'name' => ($weekend->tresdias_community === config('site.community_acronym')) ? $weekend->long_name_with_number : $weekend->weekend_full_name,
            'description' => null,
            'location_name' => $location->location_name,
            'location_url' => $location->location_url,
            'address_street' => $location->address_street,
            'address_city' => $location->address_city,
            'address_province' => $location->address_province,
            'address_postal' => $location->address_postal,
            'map_url_link' => $location->map_url_link,
            'start_datetime' => $weekend->start_date,
            'end_datetime' => $weekend->end_date,
            'is_enabled' => 1,
            'is_public' => 1,
            'is_recurring' => 0,
            'expiration_date' => $weekend->end_date->addDays(3),
        ]);
        // add special mutator/accessor field for template display of edit/download buttons
        $event->edit_id = 'w' . $weekend->id;
        return $event;
    }
}
