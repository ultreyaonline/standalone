<?php

namespace App\Http\Controllers;

use App\User;
use App\Weekend;
use App\Mail\SponsorFollowup;
use App\Mail\CandidateBecomesPescador;
use App\Mail\WebsiteLoginInstructions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class MembersController extends Controller
{
    protected $paginationThreshold = 0;
    protected $user;
    protected $users, $men, $women, $active;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->user = $user;

        // build lists for pulldowns
        $select = new User;
        $select->id=0;
        $select->first=' Please ';
        $select->last=' select';

        $this->users = User::select('*')
            ->selectRaw('(community=?) as weight', [config('site.community_acronym')]) // weighted by community
            ->orderBy('weight', 'DESC') // weighted by community
            ->orderBy('first')
            ->orderBy('last')
            ->get();

        $this->men = $this->users->where('gender', 'M');
        $this->women = $this->users->where('gender', 'W');
        $this->active = $this->users->where('active', 1);

        $this->users->prepend($select);
        $this->men->prepend($select);
        $this->women->prepend($select);
        $this->active->prepend($select);

        $this->paginationThreshold = config('site.pagination_threshold', $user->getPerPage());
    }

    /**
     * For accessing the user's own profile
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function myProfile(Request $request)
    {
        $member = $request->user();
        return redirect('/members/' . $member->id);
    }

    public function index(Request $request, $perPage = null)
    {
        // if not allowed to see other members, just show self
        if (!$request->user()->can('view members')) {
            return redirect()->route('myprofile');
        }

        // check pagination defaults
        if (! $perPage) {
            $perPage = $request->get('perPage', $this->paginationThreshold);
        }

        // check for search query
        $query = $request->get('q');

        // apply search filter, or get all
        if ($query) {
            $users = $this->user->search($query, $active=false);
        } else {
            $users = $this->user->onlyLocal()->orderBy('last', 'asc')->orderBy('first', 'asc');
//            $users = $this->user->active()->orderBy('last', 'asc')->orderBy('first', 'asc');
        }

        // display
        return view('members.community_directory', ['users' => $users->paginate($perPage), 'scope_prefix'=> '', 'scope_title' => '']);
    }

    public function show($id, Request $request)
    {
        $member = $this->user->find($id);
//        $member = $this->user->active()->find($id);

        // if not allowed to see other members, just show self
        if (!$request->user()->canViewUser($id)) {
            $member = $request->user();
        }
        if (!$member) {
            flash()->error('Could not find the specified member.');
            return redirect('/members');
        }
// @TODO - add additional calculated values, such as weekends served, etc
        return view('members.show', ['member' => $member]);
    }


    public function create(Request $request)
    {
        $weekend = Weekend::nextWeekend()->first() ?? Weekend::activeDescending()->where('weekend_MF', 'M')->first();
        $weekends = Weekend::activeDescending()->get();
        $member = new User;
        $users = $this->active;
        if ($request->user()->hasRole('Admin')) {
            $users = $this->users;
        }
        $men = $this->men;
        $women = $this->women;
        return view('members.create', compact('member', 'weekends', 'weekend', 'users', 'men', 'women'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'nullable|email|max:60',
            'username' => 'required|unique:users,username|max:255',
            'gender' => 'required|in:M,W,m,w,f',
            'first' => 'required|string|max:45',
            'last'  => 'required|string|max:45',
            'address1' => 'nullable|string|max:60',
            'address2' => 'nullable|string|max:60',
            'city' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:60',
            'postalcode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:32',
            'homephone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'workphone' => 'nullable|string|max:25',
            'church' => 'nullable|string|max:60',
            'weekend' => 'string|max:10',
            'sponsor' => 'nullable|string|max:60',
            'community' => 'nullable|string|max:60',
            'inactive_comments' => 'nullable|string|max:191',
            'skills' => 'nullable|string|max:191',
            'spouseID' => 'nullable|integer',
            'sponsorID' => 'nullable|integer',
        ]);

        $data = $request->all();

        if (isset($data['spouseID']) && $data['spouseID'] == 0) $data['spouseID'] = null;
        if (isset($data['sponsorID']) && $data['sponsorID'] == 0) $data['sponsorID'] = null;

        $member = new User($data);

        // force M/W
        $member->gender = strtoupper($member->gender);
        if ($member->gender === 'F') {
            $member->gender = 'W';
        }

        $member->created_by = $request->user()->name;

        $member->save();

        if (empty(config('site.admin_must_approve_new_members'))) {
            $this->convertCandidateToPescador($member);
        } else {
            // @TODO - send alert to Admin/moderator to approve
        }

        flash()->success($member->name . ' Added.');

        return redirect('/members/' . $member->id);
    }


    public function edit($id, Request $request)
    {
        $member = $this->user->find($id);

        // if not allowed to see other members, just show self
        if (!$request->user()->canEditUser($member->id)) {
            $member = $request->user();
        }

        if (!$member) {
            flash()->error('Requested member not found.');
            return redirect('/members');
        }

        $users = $this->active;
        if ($request->user()->hasRole('Admin')) {
            $users = $this->users;
        }

        return view('members.edit', ['member' => $member, 'users' => $users, 'men' => $this->men, 'women' => $this->women]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'email'     => 'nullable|email|max:60',
            'username'  => 'required|unique:users,username,'.$id .'|max:255',
            'gender' => 'in:M,W,m,w,f',
            'first' => 'required|string|max:45',
            'last'  => 'required|string|max:45',
            'address1' => 'nullable|string|max:60',
            'address2' => 'nullable|string|max:60',
            'city' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:60',
            'postalcode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:32',
            'homephone' => 'nullable|string|max:20',
            'cellphone' => 'nullable|string|max:20',
            'workphone' => 'nullable|string|max:25',
            'church' => 'nullable|string|max:60',
            'weekend' => 'string|max:10',
            'sponsor' => 'nullable|string|max:60',
            'community' => 'nullable|string|max:60',
            'inactive_comments' => 'nullable|string|max:191',
            'skills' => 'nullable|string|max:191',
            'spouseID' => 'nullable|integer',
            'sponsorID' => 'nullable|integer',
        ];

        if ((int)$id === $request->user()->id && !config('site.members_may_edit_own_name')) {
            unset($rules['first'], $rules['last']);
            $request->offsetUnset('first');
            $request->offsetUnset('last');
        }

        $this->validate($request, $rules);

        $member = $this->user->find($id);

        if (!$member) {
            return redirect('/home');
        }

        if (! $request->user()->canEditUser($member->id)) {
            abort(403, 'Unauthorized edit');
        }

        $fields_checkboxes = [];
        $fields_checkboxes[] = 'interested_in_serving';

        $fields_checkboxes[] = 'allow_address_share';
        $fields_checkboxes[] = 'receive_email_weekend_general';
        $fields_checkboxes[] = 'receive_email_sequela';
        $fields_checkboxes[] = 'receive_email_reunion';
        $fields_checkboxes[] = 'receive_prayer_wheel_invites';
        $fields_checkboxes[] = 'receive_email_community_news';

        if ($request->user()->can('edit members')) {
            $fields_checkboxes[] = 'active';
        }

        if ($request->user()->can('assign SDs to teams')) {
            $fields_checkboxes[] = 'qualified_sd';
        }

        if ($request->user()->id === $member->id || $request->user()->can('edit members')) {
            $fields_checkboxes[] = 'unsubscribe';
        }

        $flag_pre_unsubscribe = $member->unsubscribe;


        $excluded_fields = $fields_checkboxes;

        if (! $request->user()->can('edit members')) {
            if (!config('site.members_may_edit_own_spouse') && $request->user()->id === $member->id) {
                $excluded_fields [] = 'spouseID';
            }
            if (!config('site.members_may_edit_own_sponsor') && $request->user()->id === $member->id) {
                $excluded_fields [] = 'sponsorID';
                $excluded_fields [] = 'sponsor';
            }
        }

        $updates = $request->except($excluded_fields);

        if (isset($updates['spouseID']) && $updates['spouseID'] == 0) $updates['spouseID'] = null;
        if (isset($updates['sponsorID']) && $updates['sponsorID'] == 0) $updates['sponsorID'] = null;

        $member->update($updates);

        // NOTE: checkboxes require special on/off treatment
        foreach ($fields_checkboxes as $field) {
            $request->merge([$field => $request->has($field)]);
            $member->$field = $request->input($field, false);
        }

        // set unsubscribe date
        if ($flag_pre_unsubscribe !== $member->unsubscribe) {
            // no longer unsubscribed
            if ($flag_pre_unsubscribe) {
                $member->unsubscribe_date = null;
            }
            // newly unsubscribed
            if (! $flag_pre_unsubscribe) {
                $member->unsubscribe_date = Carbon::now();
                activity()->log('Member unsubscribed: ' . $member->name);
            }
        }

        // force M/W
        if ($request->filled('gender')) {
            $member->gender = strtoupper($member->gender);
            if ($member->gender === 'F') {
                $member->gender = 'W';
            }
        }

        $member->updated_by = $request->user()->id;
        $member->save();

        flash()->success('Updated.');

//        return redirect(route('listings.edit',$id));
        return redirect('/members/'. $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        if ($request->user()->cannot('delete members')) {
            flash()->error('ERROR: Not authorized to delete members.');
            return redirect()->route('home');
//            abort(403, 'Not authorized to delete members.');
        }

        $this->validate($request, [
            'memberID' => 'required|numeric|exists:users,id',
        ]);

        $user = User::find($request->input('memberID'));

        if (! $user) {
            flash()->error('Unable to determine which record to delete.');
            return redirect()->route('home');
        }

        $message = 'Member deleted: ' . $user->name;

        $user->delete();

        flash()->success($message);

        return redirect()->route('home');
    }

    public function updateAvatar(Request $request, $id)
    {
        $member = $this->user->find($id);

        if (!$member) {
            return redirect('/members/'. $id);
        }

        $this->validate($request, [
            'avatar' => 'mimes:jpg,jpeg,png,gif,bmp'
        ], [
            'image' => 'The photo must be a valid image file (PNG JPG GIF formats allowed)',
        ]);

        $file = request()->file('avatar');
        if ($file) {
            $member->addMedia($file)->toMediaCollection('avatar');
            $member->updated_by = $request->user()->id;
            $member->save();

            flash()->success('Avatar updated.');
        }

        return redirect('/members/'. $id);
    }

    public function convertCandidateToPescador(User $member)
    {
        // if not allowed to see other members
        if (! auth()->user()->can('edit candidates') && ! auth()->user()->can('add candidates') && ! auth()->user()->can('add community member')) {
            flash()->error('Not authorized.');
            return back();
        }

        if (!$member) {
            flash()->error('Requested member not found.');
            return back();
        }

        if (! $member->hasRole('Member')) {
            $result = $member->assignRole('Member');
            if (!$result) {
                flash()->error('Error granting permission.');
                return back();
            }
        }

        if (empty($member->community)) {
            $member->community = config('site.community_acronym');
        }

        $member->interested_in_serving = true;
        $member->active                = true;
        $member->allow_address_share   = true;
        $member->save();

        // lookup spouse
        // skip the rest of the notifications if their spouse's weekend has not "ended" yet
        if ($member->spouse &&
            $member->spouse_weekend_has_ended === false) {
            flash()->warning('Notifications for: ' . $member->name . ' not enabled because their Spouse is registered for an upcoming weekend, and we want to avoid sending communications that could spoil surprises. Please manually update email notifications settings after the spouse completes their Weekeend.');
            return back();
        }

        $member->okay_to_send_serenade_and_palanca_details = true;
        $member->receive_prayer_wheel_invites              = true;
        $member->receive_email_reunion                     = true;
        $member->receive_email_sequela                     = true;
        $member->receive_email_community_news              = true;
        $member->receive_email_weekend_general             = true;
        $member->save();

        flash()->success('Success: ' . $member->name . ' converted to Member.');
        return back();
    }

    public function demotePescadoreToRestrictedMember(User $member)
    {
        // if not allowed to see other members
        if (! auth()->user()->can('demote members')) {
            flash()->error('Not authorized.');
            return back();
        }

        if (!$member) {
            flash()->error('Requested member not found.');
            return back();
        }

        if ($member->hasRole('Member')) {
            $result = $member->removeRole('Member');
            if ($member->fresh()->hasRole('Member')) {
                flash()->error('Error changing permission.');
                return back();
            }
        }

        $member->active = false;
        $member->save();

        flash()->warning('Success: ' . $member->name . ' demoted to Pending Approval status.');
        return back();
    }

    public function convertWeekendsCandidatesToPescadores(Weekend $weekend)
    {
        // restrict to authorized administrators
        if (! auth()->user()->can('edit candidates') && ! auth()->user()->can('add candidates') && ! auth()->user()->can('add community member')) {
            flash()->error('Not authorized.');
            return redirect('/');
        }

        if (!$weekend) {
            flash()->error('Invalid weekend specified');
            return redirect()->back();
        }

        $candidates = $weekend->candidates;

        $converted = $candidates->filter(function ($recipient, $key) {

            // skip if already a Member
            if (! $recipient->hasRole('Member')) {
                $recipient->assignRole('Member');
            }

            // lookup spouse
            // skip them if their spouse's weekend has not "ended" yet and spouse email matches!
            if ($recipient->spouse &&
                $recipient->spouse_weekend_has_ended === false &&
                $recipient->spouse->email === $recipient->email) {
                return false;
            }

            if (empty($recipient->community)) {
                $recipient->community = config('site.community_acronym');
            }

            $recipient->okay_to_send_serenade_and_palanca_details = true;
            $recipient->active                        = true;
            $recipient->interested_in_serving         = true;
            $recipient->allow_address_share           = true;
            $recipient->receive_prayer_wheel_invites  = true;
            $recipient->receive_email_reunion         = true;
            $recipient->receive_email_sequela         = true;
            $recipient->receive_email_community_news  = true;
            $recipient->receive_email_weekend_general = true;
            $recipient->save();

            flash()->info('Candidate ' . $recipient->name . ' converted to Member');

            return true;
        });

        event('CandidatesConvertedToMembers', ['recipients' => $candidates]);
        flash()->success('Converted ' . $converted->count() . ' candidates to members for ' . $weekend->weekend_full_name);
        return back();
    }

    public function threeDayFollowupToPescadore(Request $request, $id)
    {
        $recipient = $this->user->find($id);

        if (!$request->user()->hasRole('Admin')) {
            return redirect('/members');
        }
        if (!$recipient) {
            flash()->error('Could not find the specified member.');
            return redirect('/members');
        }

        Mail::to($recipient)->queue(new CandidateBecomesPescador($recipient));
        return redirect('/members');
    }

    public function sponsorFollowup(Request $request, $id, $w)
    {
        $recipient = $this->user->find($id);
        $weekend = Weekend::find($id);

        if (!$request->user()->hasRole('Admin')) {
            return redirect('/members');
        }
        if (!$recipient) {
            flash()->error('Could not find the specified member.');
            return redirect('/members');
        }

        Mail::to($recipient)->queue(new SponsorFollowup($recipient, $weekend));
        return redirect('/members');
    }

    public function sendWebsiteLoginInstructionsEmail(Request $request, $id)
    {
        if (!$request->user()->can('webmaster-email-how-to-login-msg') && !$request->user()->can('email candidates')) {
            return redirect('/');
        }

        $recipient = $this->user->find($id);

        if (!$recipient) {
            flash()->error('Could not find the specified member.');
            return redirect()->back();
        }

        Mail::to($recipient)->queue(new WebsiteLoginInstructions($recipient));
        flash()->success('Website welcome email sent to ' . $recipient->name . ' (' . $recipient->weekend . ')');
        return back();
    }
}
