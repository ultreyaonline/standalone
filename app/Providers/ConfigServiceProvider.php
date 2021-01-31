<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Illuminatech\Config\PersistentRepository;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Boot services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->extend('config', function (Repository $originConfig) {

            // use db for storage of config overrides
            $storage = new Settings(app()->make('db.connection'));

            $newConfig = (new PersistentRepository($originConfig, $storage))
                ->setItems([
//                    'doc.example.only' => [
//                        'id' => '',// string, item unique ID in the list, this value will be used in request fields and form inputs.
//                        'label' => '',// string, verbose label for the config value input.
//                        'hint' => '',// string, verbose description for the config value or input hint.
//                        'rules' => [],// array, value validation rules. Default: ['sometimes', 'required']
//                        'cast' => '',// string, native type for the value to be cast to.
//                        'encrypt' => false,// bool, whether to encrypt value for the storage or not.
//                    ],
                    'site.community_acronym' => [
                        'id' => 'community_acronym',
                        'label' => 'Community Acronym (ie: ANYTD)',
                        'hint' => 'This displays in dozens of places on your site. Also determines which base community members are part of. Best not to change once set.',
                        'rules' => ['string', 'required'],
                    ],
                    'site.community_long_name' => [
                        'id' => 'community_long_name',
                        'label' => 'Community Name',
                        'hint' => 'Spelled out in full.',
                        'rules' => ['string', 'required'],
                    ],
                    'site.local_community_filter' => [
                        'id' => 'local_community_filter',
                        'label' => 'Community Filter',
                        'hint' => 'This determines whether members are "in-community" or "extended family". Usually set to the same as your Community Acronym above.',
                        'rules' => ['string', 'required'],
                    ],
                    'site.retreat_name_for_email_subject' => [
                        'id' => 'retreat_name_for_email_subject',
                        'label' => 'Candidate Email Subject Line',
                        'hint' => 'Email Subject/Title for Candidate Reminder/PackingList Email',
                        'rules' => ['string', 'required'],
                    ],
                    'site.email_general' => [
                        'id' => 'email_general',
                        'label' => 'Email Address: general',
                        'hint' => 'This is the email address displayed for contacting the community, and is the Reply-To address for outgoing messages',
                        'rules' => ['required', 'email'],
                    ],
                    'site.email-preweekend-mailbox' => [
                        'id' => 'email_preweekend_mailbox',
                        'label' => 'Email Address: Pre-Weekend',
                        'hint' => 'This is the email address shown for contacting the Pre-Weekend committee',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.email-postweekend-mailbox' => [
                        'id' => 'email_postweekend_mailbox',
                        'label' => 'Email Address: Post-Weekend',
                        'hint' => 'This is the email address shown for contacting the Post-Weekend committee and for Secuela inquiries',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.email-palanca-mailbox' => [
                        'id' => 'email_palanca_mailbox',
                        'label' => 'Email Address: Palanca',
                        'hint' => 'This is the email address shown for communities to send Palanca Letters to',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.email-finance-mailbox' => [
                        'id' => 'email_finance_mailbox',
                        'label' => 'Email Address: Finance/Payments/Donations',
                        'hint' => 'This is the email address displayed for sending payments or donations',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.payments_accepts_donations' => [
                        'id' => 'payments_accepts_donations',
                        'label' => 'Finance: Do you accept donations?',
                        'hint' => 'On the Fees page, do you want it to talk about [Fees] or both [Fees and Donations]?',
                        'rules' => ['in:fees,fees and donations'],
                    ],
                    'site.facebook_page' => [
                        'id' => 'facebook_page',
                        'label' => 'Facebook Page URL',
                        'hint' => 'If you have a community Facebook Group, enter the page URL here. The link will show on the Members Dashboard. Usually starts with: https://www.facebook.com/groups/',
                        'rules' => ['url', 'nullable'],
                    ],
                    'site.newsletter_archive_url' => [
                        'id' => 'newsletter_archive_url',
                        'label' => 'Newsletter Archive URL',
                        'hint' => 'If your community has an online Newsletter, enter the URL where the Archive list of past issues can be viewed.',
                        'rules' => ['url', 'nullable'],
                    ],
                    'site.community_mailing_address' => [
                        'id' => 'community_mailing_address',
                        'label' => 'Mailing Address - Primary',
                        'hint' => 'The organization\'s mailing address is required to be displayed on all newsletters.',
                        'rules' => ['required'],
                        'cast' => 'string',
                    ],
                    'site.community_mailing_address_for_payments' => [
                        'id' => 'community_mailing_address_for_payments',
                        'label' => 'Mailing Address for Payments',
                        'hint' => 'The address to which team and candidate payments should be mailed',
                        'cast' => 'string',
                        'rules' => ['string', 'nullable'],
                    ],
                    'site.candidate_fee_text_for_emails' => [
                        'id' => 'candidate_fee_text_for_emails',
                        'label' => 'Candidate Fee to display on emails',
                        'hint' => 'eg: "$250 per person". This displays on Email reminders to Sponsors.',
                        'rules' => ['string'],
                    ],
                    'site.candidate_application_url' => [
                        'id' => 'candidate_application_url',
                        'label' => 'Candidate Application Form URL (.pdf)',
                        'hint' => 'Note: Please contact support to ensure this file is on the server.',
                        'rules' => ['string'],
                    ],
                    'site.file_letters_sample' => [
                        'id' => 'file_letters_sample',
                        'label' => 'Attachment (.docx): Candidate - Palanca Letters Request Sample filename',
                        'hint' => 'Note: Please contact support to ensure this file is on the server.',
                        'rules' => ['string'],
                    ],
                    'site.file_sponsor_responsibilities' => [
                        'id' => 'file_sponsor_responsibilities',
                        'label' => 'Attachment (.pdf): Sponsor Responsibilities filename',
                        'hint' => 'Note: Please contact support to ensure this file is on the server.',
                        'rules' => ['string'],
                    ],
                    'site.camp-weather-forecast' => [
                        'id' => 'camp_weather',
                        'label' => 'Camp Weather Forecast',
                        'hint' => 'The weather text to display on Candidate Packing List Reminder email. eg: "The weather is typically 65ºF, with possible rain."',
                        'rules' => ['string'],
                    ],
                    'site.emergency_contact_text' => [
                        'id' => 'emergency_contact_text',
                        'label' => 'Emergency Contact Name/Text',
                        'hint' => 'If no weekend-specific Emergency Contact person is assigned, provide a generic name here. Something like "Camp Office" or "Our On Call Person"',
                        'rules' => ['string', 'nullable'],
                    ],
                    'site.emergency_contact_number' => [
                        'id' => 'emergency_contact_number',
                        'label' => 'Emergency Contact Phone Number',
                        'hint' => 'The phone number to call if no weekend-specific person has been assigned.',
                        'rules' => ['string', 'nullable'],
                    ],
                    'site.admin_must_approve_new_members' => [
                        'id' => 'admin_must_approve_new_members',
                        'label' => 'Admin must approve/moderate new members?',
                        'hint' => 'When someone (Admin/Rector/Leaders-Person) adds a new member to the database, do they become an authorized Member/Pescador automatically? Or does an Admin need to click the Convert-To-Pescador button separately?',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.rectors_can_add_new_members' => [
                        'id' => 'rectors_can_add_new_members',
                        'label' => 'Rectors can add new members?',
                        'hint' => 'When a Rector is building their team, can they add new members to the database themselves? (If not, they must ask an Administrator to do the data-entry)',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.weekend_shows_finished_for_x_days' => [
                        'id' => 'weekend_shows_finished_for_x_days',
                        'label' => 'Finished weekend shows for X days',
                        'hint' => 'When a Weeekend is over, how long does it show as default weekend when clicking Weekends tab?',
                        'cast' => 'integer',
                        'rules' => ['numeric'],
                    ],
                    'site.notify_UserAdded1' => [
                        'id' => 'notify_UserAdded1',
                        'label' => 'Email Notification 1: Member Added',
                        'hint' => 'Email address to notify when a new Member is added to your database. Usually an Administrator.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_UserAdded2' => [
                        'id' => 'notify_UserAdded2',
                        'label' => 'Email Notification 2: Member Added',
                        'hint' => 'Additional email address to notify when a new Member is added to your database.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_UserDeleted1' => [
                        'id' => 'notify_UserDeleted1',
                        'label' => 'Email Notification 1: Member Deleted',
                        'hint' => 'Email address to notify when a Member is deleted from your database. Usually an Administrator.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_UserDeleted2' => [
                        'id' => 'notify_UserDeleted2',
                        'label' => 'Email Notification 2: Member Deleted',
                        'hint' => 'Additional email address to notify when a Member is deleted from your database.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_CandidateAdded1' => [
                        'id' => 'notify_CandidateAdded1',
                        'label' => 'Email Notification 1: Candidate Added',
                        'hint' => 'Email address to notify when a Candidate is added to your database. Usually an Administrator.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_CandidateAdded2' => [
                        'id' => 'notify_CandidateAdded2',
                        'label' => 'Email Notification 2: Candidate Added',
                        'hint' => 'Additional email address to notify when a Candidate is added to your database.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_CandidateDeleted1' => [
                        'id' => 'notify_CandidateDeleted1',
                        'label' => 'Email Notification 1: Candidate Deleted',
                        'hint' => 'Email address to notify when a Candidate is deleted from your database. Usually an Administrator.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_CandidateDeleted2' => [
                        'id' => 'notify_CandidateDeleted2',
                        'label' => 'Email Notification 2: Candidate Deleted',
                        'hint' => 'Additional email address to notify when a Candidate is deleted from your database.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_PrayerWheelChanges' => [
                        'id' => 'notify_PrayerWheelChanges',
                        'label' => 'Email Notification: Prayer Wheel Changes',
                        'hint' => 'Email address to notify when a change is made to a Prayer Wheel. Usually an Administrator.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_TeamfeePayments1' => [
                        'id' => 'notify_TeamfeePayments1',
                        'label' => 'Email Notification 1: when Team Fee paid',
                        'hint' => 'Email address to notify when a Team Fee payment is recorded. Usually an Administrator.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.notify_TeamfeePayments2' => [
                        'id' => 'notify_TeamfeePayments2',
                        'label' => 'Email Notification 2: when Team Fee paid',
                        'hint' => 'Additional Email address to notify when a Team Fee payment is recorded.',
                        'rules' => ['email', 'nullable'],
                    ],
                    'site.team_fees_spiritual_directors_pay' => [
                        'id' => 'team_fees_spiritual_directors_pay',
                        'label' => 'Team Fees: Spiritual Directors pay team fees?',
                        'hint' => 'Some communities exempt SDs from paying team fees, as expression of thanks for taking time from their normal duties.',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.notify_PreWeekend_of_NewCandidate_When' => [
                        'id' => 'notify_PreWeekend_of_NewCandidate_When',
                        'label' => 'Pre-Weekend Notification Stage',
                        'hint' => 'Emails are sent to Pre-Weekend when candidates are entered. Choose whether you want those emails immediately upon data-entry, or only after clicking the button to Notify the Sponsor of the entry',
                        'rules' => ['in:sponsor_acknowledgement_sent,initial_data_entry'],
                    ],
                    'site.preweekend_sponsor_confirmations_enabled' => [
                        'id' => 'preweekend_sponsor_confirmations_enabled',
                        'label' => 'Pre-Weekend: Ask Sponsors To Confirm Details?',
                        'hint' => 'After a Candidate is entered, you can (click a button to) notify the Sponsor of the entry, and ask the Sponsor to verify the entered data.',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.preweekend_does_physical_mailing' => [
                        'id' => 'preweekend_does_physical_mailing',
                        'label' => 'Pre-Weekend: Track Mailouts?',
                        'hint' => 'Do you send invitations in the mail to candidates? If yes, enable this to track those mailouts in the Candidate Admin screen.',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.prayerwheel_empty_before_doubles' => [
                        'id' => 'prayerwheel_empty_before_doubles',
                        'label' => 'Prayer Wheel - Enable Double Signups at X Remaining',
                        'hint' => 'When a Prayer Wheel has this many signups remaining, multiple people will be allowed to signup in already-taken spots.',
                        'cast' => 'integer',
                        'rules' => ['numeric'],
                    ],
                    'site.prayerwheel_names_visible_to_all' => [
                        'id' => 'prayerwheel_names_visible_to_all',
                        'label' => 'Prayer Wheel - Names Visible to Everyone?',
                        'hint' => 'Prayer Wheel names can be visible to Everyone, or just to HeadPrayerChas/Rectors/HeadCha/Admins and Prayer Wheel Coordinators.',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.members_username_default' => [
                        'id' => 'members_username_default',
                        'label' => 'Member Username Default Format',
                        'hint' => 'When adding new members, what default format should their username use?',
                        'rules' => ['in:Email Address,FirstLast'],
                    ],
                    'site.members_may_edit_own_name' => [
                        'id' => 'members_may_edit_own_name',
                        'label' => 'Members May Edit Their First/Last Name?',
                        'hint' => 'Do you want members to be able to change their Name? (Useful to let people update when married, etc without bothering an Administrator.)',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.members_may_edit_own_sponsor' => [
                        'id' => 'members_may_edit_own_sponsor',
                        'label' => 'Members May Edit Their Sponsor?',
                        'hint' => 'Do you want members to be able to change their own Sponsor?',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.members_may_edit_own_spouse' => [
                        'id' => 'members_may_edit_own_spouse',
                        'label' => 'Members May Edit Who Their Spouse Is?',
                        'hint' => 'Do you want members to be able to change who they are linked to as their spouse?',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'site.reunion_groups_google_sheet_url' => [
                        'id' => 'reunion_groups_google_sheet_url',
                        'label' => 'Google Sheet URL for Reunion Groups list',
                        'hint' => 'On the Reunion Groups page, we can display the contents of a Google Sheet where you can maintain a list of groups and details.',
                        'rules' => ['string', 'nullable'],
                    ],
                    'site.pagination_threshold' => [
                        'id' => 'pagination_threshold',
                        'label' => 'Pagination Default',
                        'hint' => 'On some pages (like the members directory) if there are lots of entries, this controls how many are shown on each page.',
                        'cast' => 'integer',
                        'rules' => ['numeric'],
                    ],
                    'site.admin_old_weekend_teams_editable' => [
                        'id' => 'admin_old_weekend_teams_editable',
                        'label' => 'Admin - Old Weekend Teams Are Editable?',
                        'hint' => 'After a weekend is finished, the Rector and Head Cha can update team assignments for 2 weeks. After that nobody can edit. Setting this to Yes allows Admins to make updates afterward. (Should be rare.)',
                        'cast' => 'boolean',
                        'rules' => ['boolean'],
                    ],
                    'app.locale' => [
                        'id' => 'app_locale',
                        'label' => 'System - Language Locale',
                        'hint' => 'Some words like State/Province may be handled differently in your country.',
                        'rules' => ['in:en,en_US,en_CA'],
                    ],
                    'site.payments_paypal_hosted_button_id' => [
                        'id' => 'paypal_button_id',
                        'label' => 'PayPal Button ID',
                        'hint' => '13-character Button ID of your PayPal Button for payments or donations',
                        'rules' => ['string', 'nullable'],
                    ],
                    'site.payments_stripe_currencies' => [
                        'id' => 'stripe_currencies',
                        'label' => 'Stripe - Currencies Allowed',
                        'hint' => 'Which currencies will you accept payments for when using Stripe',
                        'rules' => ['in:USD,CAD,USD+CAD'],
                    ],
                    'services.stripe.key' => [
                        'id' => 'stripe_public_key',
                        'label' => 'Stripe - Publishable Key',
                        'hint' => 'Publishable Key for Stripe payments',
                        'rules' => ['string', 'nullable'],
                    ],
                    'services.stripe.secret' => [
                        'id' => 'stripe_secret_key',
                        'label' => 'Stripe - Secret Key',
                        'hint' => 'Secret Key for Stripe payments',
                        'rules' => ['string', 'nullable'],
                        'encrypt' => true,
                    ],
//                    'mail.mailers.smtp.host' => [
//                        'id' => 'smtp_host',
//                        'label' => 'SMTP Host Server',
//                        'hint' => 'SMTP Relay Host domain name, eg: smtp-relay.gmail.com (We already use TLS and port 587)',
//                        'rules' => ['string', 'nullable'],
//                    ],
//                    'mail.mailers.smtp.username' => [
//                        'id' => 'smtp_username',
//                        'label' => 'SMTP Mailbox Username',
//                        'hint' => 'SMTP Username',
//                        'rules' => ['string', 'nullable'],
//                    ],
//                    'mail.mailers.smtp.password' => [
//                        'id' => 'smtp_password',
//                        'label' => 'SMTP Mailbox Password',
//                        'hint' => 'SMTP Password',
//                        'rules' => ['string', 'nullable'],
//                        'encrypt' => true,
//                    ],
//                    'mail.from.address' => [
//                        'id' => 'website_email_from_address',
//                        'label' => 'Website Email From Address',
//                        'hint' => 'This will be shown as the [From] (and sometimes the [Reply-To]) address of emails being sent from the website. (Usually this should be the same as the SMTP Mailbox Username above.)',
//                        'rules' => ['string', 'nullable'],
//                    ],
//                    'mail.from.name' => [
//                        'id' => 'website_email_from_name',
//                        'label' => 'Website Email From Name',
//                        'hint' => 'This will be shown as the Name of the sender when the website sends general emails. Usually you will set this to be the Community Abbreviation such as ANYTD.',
//                        'rules' => ['string', 'nullable'],
//                    ],
                ]);

            return $newConfig;
        });

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
