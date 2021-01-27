<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Events\CandidateAdded;
use App\Events\CandidateDeleted;
use App\Mail\InternalCandidateRegistrationNotice;
use App\Mail\SponsorAcknowledgeCandidate;
use App\Mail\SponsorAcknowledgeCandidateReminder;
use App\Models\User;
use App\Models\Weekend;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CandidateController extends Controller
{
    protected $user;
    protected $users;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->middleware('auth')->except(['confirm']);
        $this->user = $user;

        // build list for pulldowns
        $this->users = User::select('*')
            ->selectRaw('(community=?) as weight', [config('site.community_acronym')]) // weighted by community
            ->active()
//            ->onlyLocal()
            ->orderBy('weight', 'DESC') // weighted by community
            ->orderBy('first')
            ->orderBy('last')
            ->get();

        $select        = new User;
        $select->id    = 0;
        $select->first = ' Please ';
        $select->last  = ' select';
        $this->users->prepend($select);
    }

    public function index($slug = null)
    {
        $weekend = null;

        if ($slug) {
            $community = substr($slug, 0, 4);
            $number    = substr($slug, 4);
            $weekend   = Weekend::where('tresdias_community', strtoupper($community))
                ->where('weekend_number', $number)
                ->first();
        }
        if (empty($weekend)) {
            $weekend = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();
        }

        $candidates = Candidate::where('weekend', $weekend->shortname ?? '')->get();

        $men = $candidates->where('m_user_id', '>', 0)->count();
        $women = $candidates->where('w_user_id', '>', 0)->count();
        $couples = $candidates->where('m_user_id', '>', 0)->where('w_user_id', '>', 0)->count();
        $total = $men + $women;

        $date = Carbon::now()->format('Y-m-d h:ia');

        $weekends = Weekend::activeDescending()->where('weekend_MF', 'M')->get();

        return view('candidates.indexlist', compact('candidates', 'men', 'women', 'couples', 'total', 'date', 'weekends', 'weekend'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $weekend   = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();
        $weekends  = Weekend::activeDescending()->where('weekend_MF', 'M')->get();
        $candidate = new Candidate;
        $candidate->weekend = $weekend->shortname ?? '';
        $users     = $this->users;

        return view('candidates.create', compact('candidate', 'weekends', 'weekend', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $rules_m = [
            'm_email'    => 'nullable|email|max:60',
            'm_first'    => 'required|string|max:45',
            'm_last'     => 'required|string|max:45',
            'm_age'      => 'nullable|string|max:10',
            'm_gender'   => 'required|in:M,W,m,w,f,F',
            'm_username' => 'required|unique:users,username|max:255',
            'm_special_dorm'   => 'nullable|string|max:255',
            'm_special_diet'   => 'nullable|string|max:255',
            'm_special_prayer' => 'nullable|string|max:5000',
            'm_special_medications' => 'nullable|string|max:255',
            'm_special_notes'  => 'nullable|string|max:5000',
            'm_cellphone' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:60',
            'address2' => 'nullable|string|max:60',
            'city' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:60',
            'postalcode' => 'nullable|string|max:10',
            'homephone' => 'nullable|string|max:20',
            'church' => 'nullable|string|max:60',
            'm_emergency_name' => 'nullable|string|max:255',
            'm_emergency_phone' => 'nullable|string|max:255',
        ];
        $rules_w = [
            'w_email'    => 'nullable|email|max:60',
            'w_first'    => 'required|string|max:45',
            'w_last'     => 'required|string|max:45',
            'w_age'      => 'nullable|string|max:10',
            'w_gender'   => 'required|in:M,W,m,w,f,F',
            'w_username' => 'required|unique:users,username|max:255',
            'w_special_dorm'   => 'nullable|string|max:255',
            'w_special_diet'   => 'nullable|string|max:255',
            'w_special_prayer' => 'nullable|string|max:5000',
            'w_special_medications' => 'nullable|string|max:255',
            'w_special_notes'  => 'nullable|string|max:5000',
            'w_cellphone' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:60',
            'address2' => 'nullable|string|max:60',
            'city' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:60',
            'postalcode' => 'nullable|string|max:10',
            'homephone' => 'nullable|string|max:20',
            'church' => 'nullable|string|max:60',
            'w_emergency_name' => 'nullable|string|max:255',
            'w_emergency_phone' => 'nullable|string|max:255',
        ];
        $rules   = [];
        if ($request->filled('m_last')) {
            $rules = \array_merge($rules, $rules_m);
        }
        if ($request->filled('w_last')) {
            $rules = \array_merge($rules, $rules_w);
        }
        if ($rules == []) {
            $rules = ['m_last' => 'required'];
        }
        $this->validate($request, $rules);

        $man = null;
        $woman = null;
        $flash_message = [];

        DB::beginTransaction();

        try {
            if ($request->filled('m_last')) {
                $data = [
                    'first' => $request->input('m_first'),
                    'last' => $request->input('m_last'),
                    'gender' => strtoupper($request->input('m_gender')),
                    'cellphone' => $request->input('m_cellphone'),
                    'email' => $request->input('m_email'),
                    'username' => $request->input('m_username'),
                    'address1' => $request->input('address1'),
                    'address2' => $request->input('address2'),
                    'city' => $request->input('city'),
                    'state' => $request->input('state'),
                    'postalcode' => $request->input('postalcode'),
                    'homephone' => $request->input('homephone'),
                    'church' => $request->input('church'),
                    'weekend' => $request->input('weekend'),
                    'sponsorID' => $request->input('m_sponsorID'),
                    'emergency_contact_details' => $request->input('m_emergency_name') . ' ' . $request->input('m_emergency_phone'),
                    'community' => config('site.community_acronym'),
                ];

                if (isset($data['spouseID']) && $data['spouseID'] == 0) $data['spouseID'] = null;
                if (isset($data['sponsorID']) && $data['sponsorID'] == 0) $data['sponsorID'] = null;

                $man = new User($data);

                $man->created_by = $request->user()->name;

                $man->okay_to_send_serenade_and_palanca_details = false;
                $man->interested_in_serving = false;
                $man->active = false;
                $man->allow_address_share = false;
                $man->receive_prayer_wheel_invites = false;
                $man->receive_email_reunion = false;
                $man->receive_email_sequela = false;
                $man->receive_email_community_news = false;
                $man->receive_email_weekend_general = false;

                $man->save();
                $request->merge(['m_user_id' => $man->id]);

                $flash_message[] = $man->name . ' added.';
                event(CandidateAdded::class, ['who' => $man->name, 'by' => $request->user()]);
            }

            if ($request->filled('w_last')) {
                $data = [
                    'first' => $request->input('w_first'),
                    'last' => $request->input('w_last'),
                    'gender' => strtoupper($request->input('w_gender')),
                    'cellphone' => $request->input('w_cellphone'),
                    'email' => $request->input('w_email'),
                    'username' => $request->input('w_username'),
                    'address1' => $request->input('address1'),
                    'address2' => $request->input('address2'),
                    'city' => $request->input('city'),
                    'state' => $request->input('state'),
                    'postalcode' => $request->input('postalcode'),
                    'homephone' => $request->input('homephone'),
                    'church' => $request->input('church'),
                    'weekend' => $request->input('weekend'),
                    'sponsorID' => $request->input('w_sponsorID'),
                    'emergency_contact_details' => $request->input('w_emergency_name') . ' ' . $request->input('w_emergency_phone'),
                    'community' => config('site.community_acronym'),
                ];

                if (isset($data['spouseID']) && $data['spouseID'] == 0) $data['spouseID'] = null;
                if (isset($data['sponsorID']) && $data['sponsorID'] == 0) $data['sponsorID'] = null;

                $woman = new User($data);
                if ($woman->gender == 'F') {
                    $woman->gender = 'W';
                }
                $woman->created_by = $request->user()->name;
                if ($man) {
                    $woman->spouseID = $man->id;
                }
                $woman->okay_to_send_serenade_and_palanca_details = false;
                $woman->interested_in_serving = false;
                $woman->active = false;
                $woman->allow_address_share = false;
                $woman->receive_prayer_wheel_invites = false;
                $woman->receive_email_reunion = false;
                $woman->receive_email_sequela = false;
                $woman->receive_email_community_news = false;
                $woman->receive_email_weekend_general = false;

                $woman->save();
                $request->merge(['w_user_id' => $woman->id]);

                $flash_message[] = $woman->name . ' added.';
                event(CandidateAdded::class, ['who' => $woman->name, 'by' => $request->user()]);
            }

            if ($woman && $man) {
                $man->spouseID = $woman->id;
                $man->save();
            }

            flash()->success(implode('<br>', $flash_message));


            $fields = [
                'm_user_id',
                'w_user_id',
                'm_age',
                'w_age',
                'm_emergency_name',
                'm_emergency_phone',
                'w_emergency_name',
                'w_emergency_phone',
                'm_pronunciation',
                'w_pronunciation',
                'm_married',
                'm_vocational_minister',
                'w_married',
                'w_vocational_minister',
                'sponsor_confirmed_details',
                'fees_paid',
                'ready_to_mail',
                'invitation_mailed',
                'm_response_card_returned',
                'm_special_dorm',
                'm_special_diet',
                'm_special_prayer',
                'm_special_medications',
                'm_smoker',
                'w_response_card_returned',
                'w_special_dorm',
                'w_special_diet',
                'w_special_prayer',
                'w_special_medications',
                'w_smoker',
                'payment_details',
                'm_arrival_poc_person',
                'm_arrival_poc_phone',
                'w_arrival_poc_person',
                'w_arrival_poc_phone',
                'm_special_notes',
                'w_special_notes',
                'weekend',
                //            'completed',
            ];
            $candidate = new Candidate($request->only($fields));

            if (!$candidate->hash_sponsor_confirm) {
                $candidate->hash_sponsor_confirm = Str::random(12);
            }

            $candidate->save();

            DB::commit();

            if (config('site.notify_PreWeekend_of_NewCandidate_When') === 'initial_data_entry') {
                $reply_to_email = config('site.email-preweekend-mailbox', config('site.email_general'));
                if (!empty($reply_to_email)) {
                    Mail::to(new User(['first' => 'Preweekend', 'last' => 'Committee', 'email' => config('site.email-preweekend-mailbox')]))
                        ->send(new InternalCandidateRegistrationNotice($candidate, $reply_to_email));
                }
            }
            return redirect('/candidates/' . preg_replace('/[^a-z0-9]/', '', strtolower($request->input('weekend'))));

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Display the form for editing the resource.
     *
     * @param \App\Models\Candidate $candidate
     * @param string|null $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(Candidate $candidate, $slug = null)
    {
        $weekend = null;
        // retrieve Candidate record using route-model-binding
//        if ($candidate === null) {
//            $candidate = new Candidate();
//        }
        // retrieve persons and populate data for views
        if ($candidate->man && $candidate->man->id) {
            $candidate->m_first     = $candidate->man->first;
            $candidate->m_last      = $candidate->man->last;
            $candidate->m_gender    = $candidate->man->gender;
            $candidate->m_cellphone = $candidate->man->cellphone;
            $candidate->m_email     = $candidate->man->email;
            $candidate->m_username  = $candidate->man->username;
            $candidate->m_sponsorID = $candidate->man->sponsorID;

            $candidate->address1   = $candidate->man->address1;
            $candidate->address2   = $candidate->man->address2;
            $candidate->city       = $candidate->man->city;
            $candidate->state      = $candidate->man->state;
            $candidate->postalcode = $candidate->man->postalcode;
            $candidate->homephone  = $candidate->man->homephone;
            $candidate->church     = $candidate->man->church;
            $candidate->weekend    = $candidate->man->weekend;
        }
        if ($candidate->woman && $candidate->woman->id) {
            $candidate->w_first     = $candidate->woman->first;
            $candidate->w_last      = $candidate->woman->last;
            $candidate->w_gender    = $candidate->woman->gender;
            $candidate->w_cellphone = $candidate->woman->cellphone;
            $candidate->w_email     = $candidate->woman->email;
            $candidate->w_username  = $candidate->woman->username;
            $candidate->w_sponsorID = $candidate->woman->sponsorID;

            $candidate->address1   = $candidate->woman->address1;
            $candidate->address2   = $candidate->woman->address2;
            $candidate->city       = $candidate->woman->city;
            $candidate->state      = $candidate->woman->state;
            $candidate->postalcode = $candidate->woman->postalcode;
            $candidate->homephone  = $candidate->woman->homephone;
            $candidate->church     = $candidate->woman->church;
            $candidate->weekend    = $candidate->woman->weekend;
        }

        $weekends = Weekend::activeDescending()->where('weekend_MF', 'M')->get();

        if ($slug) {
            $community = substr($slug, 0, 4);
            $number    = substr($slug, 4);
            $weekend   = Weekend::where('tresdias_community', strtoupper($community))
                ->where('weekend_number', $number)
                ->first();
        }
        if (!$weekend) {
            $weekend = Weekend::nextWeekend()->first() ?? $weekends->first();
        }

        $users = $this->users;

        return view('candidates.edit', compact('candidate', 'weekends', 'weekend', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Candidate $candidate
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Candidate $candidate)
    {
        $rules_m = [
            'm_email'    => 'nullable|email|max:60',
            'm_first'    => 'required',
            'm_last'     => 'required',
            'm_age'      => 'nullable|string|max:10',
            'm_gender'   => 'required|in:M,W,m,w,f,F',
            'm_username' => 'required|unique:users,username' . ($candidate->man ? ',' . $candidate->man->id : '') . '|max:255',
            'm_special_dorm'   => 'nullable|string|max:255',
            'm_special_diet'   => 'nullable|string|max:255',
            'm_special_prayer' => 'nullable|string|max:5000',
            'm_special_medications' => 'nullable|string|max:255',
            'm_special_notes'  => 'nullable|string|max:5000',
            'm_cellphone' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:60',
            'address2' => 'nullable|string|max:60',
            'city' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:60',
            'postalcode' => 'nullable|string|max:10',
            'homephone' => 'nullable|string|max:20',
            'church' => 'nullable|string|max:60',
            'm_emergency_name' => 'nullable|string|max:255',
            'm_emergency_phone' => 'nullable|string|max:255',
        ];
        $rules_w = [
            'w_email'    => 'nullable|email|max:60',
            'w_first'    => 'required',
            'w_last'     => 'required',
            'w_age'      => 'nullable|string|max:10',
            'w_gender'   => 'required|in:M,W,m,w,f,F',
            'w_username' => 'required|unique:users,username' . ($candidate->woman ? ',' . $candidate->woman->id : '') . '|max:255',
            'w_special_dorm'   => 'nullable|string|max:255',
            'w_special_diet'   => 'nullable|string|max:255',
            'w_special_prayer' => 'nullable|string|max:5000',
            'w_special_medications' => 'nullable|string|max:255',
            'w_special_notes'  => 'nullable|string|max:5000',
            'w_cellphone' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:60',
            'address2' => 'nullable|string|max:60',
            'city' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:60',
            'postalcode' => 'nullable|string|max:10',
            'homephone' => 'nullable|string|max:20',
            'church' => 'nullable|string|max:60',
            'w_emergency_name' => 'nullable|string|max:255',
            'w_emergency_phone' => 'nullable|string|max:255',
        ];

        $rules = [];
        if ($candidate->man || $request->filled('m_last')) {
            $rules = \array_merge($rules, $rules_m);
        }
        if ($candidate->woman || $request->filled('w_last')) {
            $rules = \array_merge($rules, $rules_w);
        }
        if ($rules == []) {
            $rules = ['m_last' => 'required'];
        }

        if ($request->user()->can('record candidate fee payments')) {
            $rules['payment_details'] = 'nullable|string|max:255';
        }

        $this->validate($request, $rules);


        $flash_message = [];

        $fields = [
            'm_age',
            'w_age',
            'm_emergency_name',
            'm_emergency_phone',
            'w_emergency_name',
            'w_emergency_phone',
            'm_pronunciation',
            'w_pronunciation',
            'm_special_dorm',
            'm_special_diet',
            'm_special_prayer',
            'm_special_medications',
            'w_special_dorm',
            'w_special_diet',
            'w_special_prayer',
            'w_special_medications',
            'm_arrival_poc_person',
            'm_arrival_poc_phone',
            'w_arrival_poc_person',
            'w_arrival_poc_phone',
            'm_special_notes',
            'w_special_notes',
            'weekend',
            //            'completed',
        ];

        if ($request->user()->can('record candidate fee payments')) {
            $fields[] = 'payment_details';
        }

        $updates = $request->only($fields);

        // NOTE: checkboxes require special on/off treatment
        $checkboxes = [
            'm_married',
            'm_vocational_minister',
            'w_married',
            'w_vocational_minister',
            'sponsor_confirmed_details',
            'ready_to_mail',
            'invitation_mailed',
            'm_response_card_returned',
            'm_smoker',
            'w_response_card_returned',
            'w_smoker',
        ];
        if ($request->user()->can('record candidate fee payments')) {
            $checkboxes[] = 'fees_paid';
        }
        foreach ($checkboxes as $field) {
            $request->merge([$field => $request->has($field)]);
            $updates[$field] = $request->input($field, false);
        }

        DB::beginTransaction();

        try {
            $candidate->update($updates);

            $man = null;
            $woman = null;

            if ($request->filled('m_last')) {
                $data = [
                    'first' => $request->input('m_first'),
                    'last' => $request->input('m_last'),
                    'gender' => strtoupper($request->input('m_gender')),
                    'cellphone' => $request->input('m_cellphone'),
                    'email' => $request->input('m_email'),
                    'username' => $request->input('m_username'),
                    'address1' => $request->input('address1'),
                    'address2' => $request->input('address2'),
                    'city' => $request->input('city'),
                    'state' => $request->input('state'),
                    'postalcode' => $request->input('postalcode'),
                    'homephone' => $request->input('homephone'),
                    'church' => $request->input('church'),
                    'weekend' => $request->input('weekend'),
                    'sponsorID' => $request->input('m_sponsorID'),
                ];

                if (isset($data['spouseID']) && $data['spouseID'] == 0) $data['spouseID'] = null;
                if (isset($data['sponsorID']) && $data['sponsorID'] == 0) $data['sponsorID'] = null;

                if ($candidate->man) {
                    $candidate->man->update($data);
                    $flash_message[] = $candidate->man->name . ' updated.';
                    event('UserUpdated', ['user' => $candidate->man, 'by' => $request->user()]);
                } else {
                    $man = new User($data);

                    $man->created_by = $request->user()->name;
                    if ($candidate->woman) {
                        $man->spouseID = $candidate->woman->id;
                    }
                    $man->save();
                    $flash_message[] = $man->name . ' added.';

                    $candidate->m_user_id = $man->id;
                    $candidate->save();
                }
            }

            if ($request->filled('w_last')) {
                $data = [
                    'first' => $request->input('w_first'),
                    'last' => $request->input('w_last'),
                    'gender' => strtoupper($request->input('w_gender')),
                    'cellphone' => $request->input('w_cellphone'),
                    'email' => $request->input('w_email'),
                    'username' => $request->input('w_username'),
                    'address1' => $request->input('address1'),
                    'address2' => $request->input('address2'),
                    'city' => $request->input('city'),
                    'state' => $request->input('state'),
                    'postalcode' => $request->input('postalcode'),
                    'homephone' => $request->input('homephone'),
                    'church' => $request->input('church'),
                    'weekend' => $request->input('weekend'),
                    'sponsorID' => $request->input('w_sponsorID'),
                ];
                if ($data['gender'] === 'F') {
                    $data['gender'] = 'W';
                }

                if (isset($data['spouseID']) && $data['spouseID'] == 0) $data['spouseID'] = null;
                if (isset($data['sponsorID']) && $data['sponsorID'] == 0) $data['sponsorID'] = null;

                if ($candidate->woman) {
                    $candidate->woman->update($data);
                    $flash_message[] = $candidate->woman->name . ' updated.';
                    event('UserUpdated', ['user' => $candidate->woman, 'by' => $request->user()]);
                } else {
                    $woman = new User($data);

                    $woman->created_by = $request->user()->name;
                    if ($candidate->man) {
                        $woman->spouseID = $candidate->man->id;
                    }
                    $woman->save();
                    $flash_message[] = $woman->name . ' added.';

                    $candidate->w_user_id = $woman->id;
                    $candidate->save();
                }
            }

            DB::commit();

            flash()->success(implode('<br>', $flash_message));

            return redirect('/candidates/' . preg_replace('/[^a-z0-9]/', '', strtolower($request->input('weekend'))));

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Candidate $candidate
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request, Candidate $candidate)
    {
        if ($request->user()->cannot('delete candidates')) {
            flash()->error('ERROR: Not authorized to delete candidates.');
            return redirect('/candidates');
//            abort(403, 'Not authorized to delete candidates.');
        }

        if (!$candidate->id) {
            flash()->error('Unable to determine which record to delete.');
            return redirect('/candidates');
        }

        DB::beginTransaction();

        try {
            $messages = [];
            if ($candidate->man) {
                $man = User::find($candidate->man->id);
                $messages[] = $man->name . ' deleted.';
                event(CandidateDeleted::class, ['who' => $man->name, 'by' => $request->user()]);
                $man->delete();
            }
            if ($candidate->woman) {
                $woman = User::find($candidate->woman->id);
                $messages[] = $woman->name . ' deleted.';
                event(CandidateDeleted::class, ['who' => $woman->name, 'by' => $request->user()]);
                $woman->delete();
            }
            $candidate->delete();

            DB::commit();

            flash(implode('<br>', $messages), 'success');

            return redirect('/candidates');

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * Send email to Sponsor asking them to verify the entered candidate information, and click to confirm.
     *
     * @param Candidate $candidate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendSponsorAcknowledgement($slug, Candidate $candidate)
    {
        $reply_to_email = config('site.email-preweekend-mailbox', config('site.email_general'));
        if (empty($reply_to_email)) {
            abort('400', 'ERROR: Contact the Administrator: you have no Pre-Weekend email address configured, and no General mailbox configured.');
        }

        $sponsorID = $candidate->man && $candidate->man->sponsorID ? $candidate->man->sponsorID : $candidate->woman->sponsorID;

        $sponsor = User::find($sponsorID);
        if (!$sponsor) {
            abort('400', 'ERROR: No sponsor found. Could not send email. Press the Back button to go back and edit.');
        }

        $spouse = $sponsor->spouse;

        $mail = Mail::to($sponsor);
        $recipients = $sponsor->email;
        if ($spouse && !empty($spouse->email) && $spouse->email !== $sponsor->email && $spouse->isMember()) {
            $mail->cc($spouse);
            $recipients .= ', ' . $spouse->email;
        }

        $mail->send(new SponsorAcknowledgeCandidate($candidate, $reply_to_email));

        $candidate = Candidate::find($candidate->id);
        $candidate->sponsor_acknowledgement_sent = 1;
        $candidate->update();
        flash('Acknowledgement sent to: ' . $recipients, 'success');

        if (config('site.notify_PreWeekend_of_NewCandidate_When') === 'sponsor_acknowledgement_sent') {
            Mail::to(new User(['first' => 'Preweekend', 'last'=> 'Committee', 'email' => $reply_to_email ]))
                ->send(new InternalCandidateRegistrationNotice($candidate, $reply_to_email));
            flash('Acknowledgement sent to PreWeekend.', 'success');
        }

        return back();
    }

    /**
     * Send email to Sponsor asking them to verify the entered candidate information, and click to confirm.
     *
     * @param Candidate $candidate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendSponsorAcknowledgementReminder($slug, Candidate $candidate)
    {
        $reply_to_email = config('site.email-preweekend-mailbox', config('site.email_general'));
        if (empty($reply_to_email)) {
            abort('400', 'ERROR: Contact the Administrator: you have no Pre-Weekend email address configured, and no General mailbox configured.');
        }

        $sponsorID = $candidate->man ? $candidate->man->sponsorID : $candidate->woman->sponsorID;

        $sponsor = User::find($sponsorID);
        $spouse = $sponsor->spouse;

        $mail = Mail::to($sponsor);
        $recipients = $sponsor->email;

        if ($spouse && !empty($spouse->email) && $spouse->email !== $sponsor->email && $spouse->isMember()) {
            $mail->cc($spouse);
            $recipients .= ', ' . $spouse->email;
        }
        $mail->send(new SponsorAcknowledgeCandidateReminder($candidate, $reply_to_email));

        $candidate = Candidate::find($candidate->id);
        $candidate->sponsor_acknowledgement_sent = 1;
        $candidate->update();
        flash('Reminder sent to: ' . $recipients, 'success');

        return back();
    }

    /**
     * Record click from sponsor which confirms candidate information.
     *
     * @param Candidate $candidate
     * @param $hash
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirm(Candidate $candidate, $hash)
    {
        if (empty($hash)) {
            return redirect('/home');
        }

        if ($candidate->hash_sponsor_confirm == $hash) {
            $candidate->sponsor_confirmed_details = 1;
            $candidate->save();
            flash($candidate->names . ' confirmed. Thank you!', 'success');
        } else {
            flash('Sorry, could not find match for that confirmation code. Please email your confirmation instead by replying to the same email.', 'error');
        }

        return redirect(auth()->guest() ? '/login' : '/home');
    }
}
