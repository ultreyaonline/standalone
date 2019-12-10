<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MessageToCommunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('role:Admin|Pre-Weekend|Super-Admin');
    }

    /**
     * Show the Admin dashboard.
     *
     * @TODO - Note: Pre-Weekend currently can see this in order to access their button panel
     */
    public function index()
    {
        return view('admin.main');
    }

    public function members_edit()
    {
        return view('admin.members_audit');
    }


    /**
     * Send test message to all Admins
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function emailAllAdmins(Request $request)
    {
        // only admins can use this
        abort_unless(auth()->user()->hasRole('Admin'), 403, 'Only administrators can access this action.');

//        $this->validate($request, [
//            'subject' => 'required',
//            'message' => 'required',
//        ]);

        $subject = $request->input('subject', 'Admin Email Test');
        $message = $request->input('message', 'This is just a simple test of the email subsystem. Things to check: to-address, from-address, replyTo-address, and whether it went to spam folder.');

        $recipients = \App\User::active()
            ->where('email', '!=', '')// skip blank email addresses
            ->role('Admin')
            ->notunsubscribed();

        $recipients = $recipients->get();

        $recipients->each(function ($recipient, $key) use ($subject, $message) {
            $sender = auth()->user();
            Mail::to($recipient)->queue(new MessageToCommunity($subject, $message, $sender, null));
        });

        flash()->success('Message queued for delivery to ' . $recipients->count() . ' recipients.' . '<br>If it does not arrive in a couple minutes then check the server queue system and the application logs.')->important();

        return redirect('/admin');
    }

}
