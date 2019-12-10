<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class MailchimpWebhooksController
 *
 * @package App\Http\Controllers\Webhooks
 *
 * For more reading about how another package does Mailchimp stuff, see https://github.com/thinkshout/mailchimp-api-php
 */
class MailchimpWebhooksController extends Controller
{
    public function __construct()
    {
        // @TODO use middleware to validate authenticity of webhook call
    }

    /**
     * Handle the Webhook call.
     * http://eepurl.com/bs-j_T
     * https://mailchimp.com/developer/guides/about-webhooks/
     * https://mailchimp.com/developer/reference/lists/list-webhooks/
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $event = $request->get('type');
        $fired_at = $request->get('fired_at'); // UTC timestamp string eg: "2009-03-26 21:35:57",
        $data = $request->get('data');

        switch ($event) {
            case 'subscribe':
                //$data['id'] = "8a25ff1d98",
                //$data['list_id'] = "a6b5da1054",
                //$data['email'] = "api@mailchimp.com",
                //$data['email_type'] = "html",
                //$data['merges']['EMAIL]": "api@mailchimp.com",
                //$data['merges']['FNAME]": "Mailchimp",
                //$data['merges']['LNAME]": "API",
                //$data['merges']['INTERESTS]": "Group1,Group2",
                //$data['ip_opt'] = "10.20.10.30",
                //$data['ip_signup'] = "10.20.10.30"
                break;

            case 'unsub':
            case 'delete':
                //An unsubscribe event's action is either unsub or delete. The reason will be manual unless caused by a spam complaint, then it will be abuse.
                //$data['reason'] = "manual", // "abuse"
                //$data['id'] = "8a25ff1d98",
                //$data['list_id'] = "a6b5da1054",
                //$data['email'] = "api+unsub@mailchimp.com",
                //$data['email_type'] = "html",
                //$data['merges']['EMAIL]": "api+unsub@mailchimp.com",
                //$data['merges']['FNAME]": "Mailchimp",
                //$data['merges']['LNAME]": "API",
                //$data['merges']['INTERESTS]": "Group1,Group2",
                //$data['ip_opt'] = "10.20.10.30",
                //$data['campaign_id'] = "cb398d21d2",
                break;


            case 'profile':
                //Note that you will always receive a profile update at the same time as an email update.
                //$data['id'] = "8a25ff1d98",
                //$data['list_id'] = "a6b5da1054",
                //$data['email'] = "api@mailchimp.com",
                //$data['email_type'] = "html",
                //$data['merges']['EMAIL]": "api@mailchimp.com",
                //$data['merges']['FNAME]": "Mailchimp",
                //$data['merges']['LNAME]": "API",
                //$data['merges']['INTERESTS]": "Group1,Group2",
                //$data['ip_opt'] = "10.20.10.30"
                break;

            case 'upemail':
                //Note that you will always receive a profile update at the same time as an email update.
                //$data['list_id'] = "a6b5da1054",
                //$data['new_id'] = "51da8c3259",
                //$data['new_email'] = "api+new@mailchimp.com",
                //$data['old_email'] = "api+old@mailchimp.com"
                break;

            case 'cleaned':
                //For cleaned emails, the reason will be hard (for hard bounces) or abuse.
                //$data['list_id'] = "a6b5da1054",
                //$data['campaign_id'] = "4fjk2ma9xd",
                //$data['reason'] = "hard", // "abuse"
                //$data['email'] = "api+cleaned@mailchimp.com"
                break;

            case 'campaign':
                // Campaign-Sent Emails
                //$data['id'] = "5aa2102003",
                //$data['subject'] = "Test Campaign Subject",
                //$data['status'] = "sent",
                //$data['reason'] = "",
                //$data['list_id'] = "a6b5da1054"
                break;
        }


        // Send a response, to acknowledge receipt, so it doesn't keep re-sending.
        return response('Success', 200);
    }
}
