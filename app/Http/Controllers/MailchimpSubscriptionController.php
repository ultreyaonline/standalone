<?php

namespace App\Http\Controllers;

use App\User;
use Spatie\Newsletter\NewsletterFacade as Mailchimp;

class MailchimpSubscriptionController extends Controller
{
    protected $mailchimp_master_list_name;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');

        $this->mailchimp_master_list_name = config('newsletter.defaultListName');
    }

    public function index()
    {
        return view('errors.notimplemented');
    }

    public function addToMailchimp(User $user)
    {
        if (!auth()->user()->hasAnyRole(['Pre-Weekend', 'Admin'])) {
            return redirect('/member/' . $user->id);
        }

        $options = [];

        // @TODO - this doesn't account for shared email addresses;
        $result = Mailchimp::subscribe(
            $user->email,
            [
                'FNAME' => $user->first,
                'LNAME' => $user->last,
            ],
            $this->mailchimp_master_list_name,
            $options
        );

        if ($result !== false) {
            Mailchimp::addTags([$user->weekend, $user->gender === 'M' ? 'Men' : 'Women'], $user->email);
            flash()->success('Subscriber: ' . $user->name . ' added.');
            event('MailchimpSubscriberAdded', ['user' => $user, 'by' => auth()->user()]);
        } else {
            flash()->error('Problem adding subscriber: ' . $user->name);
        }

        return redirect()->back();
    }

    public function unsubscribe(User $user)
    {
        if (!auth()->user()->hasAnyRole(['Pre-Weekend', 'Admin'])) {
            return redirect('/member/' . $user->id);
        }

        $result = Mailchimp::unsubscribe($user->email, $this->mailchimp_master_list_name);

        if ($result !== false) {
            flash()->success('Member: ' . $user->name . ' unsubscribed.');
            event('MailchimpSubscriberRemoved', ['user' => $user, 'by' => auth()->user()]);
        } else {
            flash()->error('Problem removing subscriber: ' . $user->name);
        }

        return redirect()->back();
    }

    public function archive(User $user)
    {
        if (!auth()->user()->hasAnyRole(['Pre-Weekend', 'Admin'])) {
            return redirect('/member/' . $user->id);
        }

        $result = Mailchimp::delete($user->email, $this->mailchimp_master_list_name);

        if ($result !== false) {
            flash()->success('Subscriber: ' . $user->name . ' archived.');
            event('MailchimpSubscriberDeleted', ['user' => $user, 'by' => auth()->user()]);
        } else {
            flash()->error('Possible problem deleting subscriber: ' . $user->name);
        }

        return redirect()->back();
    }

    public function deletePermanently(User $user)
    {
        if (!auth()->user()->hasAnyRole(['Pre-Weekend', 'Admin'])) {
            return redirect('/member/' . $user->id);
        }

        $result = Mailchimp::deletePermanently($user->email, $this->mailchimp_master_list_name);

        if ($result !== false) {
            flash()->success('Subscriber: ' . $user->name . ' deleted permanently, and cannot be re-added.');
            event('MailchimpSubscriberDeleted', ['user' => $user, 'by' => auth()->user()]);
        } else {
            flash()->error('Possible problem deleting subscriber: ' . $user->name);
        }

        return redirect()->back();
    }

    public function auditSubscribersMissing()
    {
        if (!auth()->user()->hasAnyRole(['Admin'])) {
            return redirect()->back();
        }

        $results =
        $members  = \App\User::onlyLocal()->get();
        return view('admin.mailchimp_audit')->withMembers($results);
    }

    public function checkStatus($id)
    {
        if (!auth()->user()->hasAnyRole(['Pre-Weekend', 'Admin'])) {
            abort(403);
        }

        if (!empty($id) && $member = User::find($id)) {
            if (Mailchimp::isSubscribed($member->email, $this->mailchimp_master_list_name)) {
                return response('Success', 200);
            } else {
                return response('Not found', 404);
            }
        }
        return response('Missing', 410);
    }

    public function subscribeEveryone(): void
    {
        $users = User::onlyLocal()->active()->notUnsubscribed()->where('receive_email_community_news', 1)->get();
        foreach($users as $user) {
            // @TODO -- combine spouse first-names if sharing an email address
            Mailchimp::subscribe($user->email, ['FNAME'=>$user->first, 'LNAME'=>$user->last]);
            Mailchimp::addTags([$user->weekend, $user->gender === 'M' ? 'Men' : 'Women'], $user->email);
        }

    }

    public function updateTagsForEveryone(): void
    {
        $users = User::onlyLocal()->active()->notUnsubscribed()->where('receive_email_community_news', 1)->get();
        foreach($users as $user) {
            if (Mailchimp::hasMember($user->email, $this->mailchimp_master_list_name)) {
                Mailchimp::addTags([$user->weekend, $user->gender === 'M' ? 'Men' : 'Women'], $user->email);
            }
        }
    }
}
