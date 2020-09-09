<?php

use App\Http\Controllers\Admin\ActivitylogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RolesAndPermissionsController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateEmailsController;
use App\Http\Controllers\CandidatePaymentsController;
use App\Http\Controllers\CandidateReportsController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MailchimpSubscriptionController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\PagesStaticController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrayerWheelController;
use App\Http\Controllers\PrayerWheelNotificationsController;
use App\Http\Controllers\PreWeekendController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SpiritualAdvisorController;
use App\Http\Controllers\TeamAssignmentController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamFeePaymentsController;
use App\Http\Controllers\Webhooks\MailchimpWebhooksController;
use App\Http\Controllers\Webhooks\StripeWebhooksController;
use App\Http\Controllers\WeekendController;
use App\Http\Controllers\WeekendExternalController;
use App\Http\Controllers\WeekendStatsController;
use Illuminate\Support\Facades\Route;

// Authentication Web Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('logout', [LoginController::class, 'logout']);

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
// route for quick access to set a password, mainly for new pescadores
Route::get('pescador', [ForgotPasswordController::class, 'showLinkRequestForm']);
Route::permanentRedirect('pescadore', 'pescador'); // alias in case of misspelling

// ===================================================

/* Visitor (unauthenticated) Pages */
Route::get('/', [GuestController::class, 'index']);
Route::get('/about', [GuestController::class, 'about']);
Route::get('calendar', [EventController::class, 'index'])->name('calendar');
Route::get('/believe', [GuestController::class, 'believe']); // Statement of Faith
Route::get('/history', [GuestController::class, 'history']);

// ===================================================

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/', [HomeController::class, 'index']);
Route::permanentRedirect('/dashboard', '/home');

Route::get('/profile', [MembersController::class, 'myProfile'])->name('myprofile');
Route::permanentRedirect('/myprofile', '/profile');
Route::permanentRedirect('/me', '/profile');

Route::patch('/members/{id}/updateavatar', [MembersController::class, 'updateAvatar']);

Route::get('/members/{user}/mailchimp/check', [MailchimpSubscriptionController::class, 'checkStatus']);
Route::put('/members/{user}/mailchimp/add', [MailchimpSubscriptionController::class, 'addToMailchimp']);
Route::delete('/members/{user}/mailchimp/unsubscribe', [MailchimpSubscriptionController::class, 'unsubscribe']);
Route::delete('/members/{user}/mailchimp/archive', [MailchimpSubscriptionController::class, 'archive']);
Route::delete('/members/{user}/mailchimp/deletePermanently', [MailchimpSubscriptionController::class, 'deletePermanently']);

Route::resource('/members', MembersController::class);
Route::get('/directory', [MembersController::class, 'index'])->name('directory'); // alias
Route::permanentRedirect('/community', '/directory');

Route::get('/palanca', [PagesStaticController::class, 'palanca']);
Route::get('/preweekend', [PagesStaticController::class, 'preweekend']);
Route::get('/postweekend', [PagesStaticController::class, 'postweekend']);
Route::get('/reuniongroups', [PagesStaticController::class, 'reuniongroups']);
Route::get('/secretariat', [PagesStaticController::class, 'secretariat']);
Route::get('/secuelas', [PagesStaticController::class, 'secuelas']);
Route::permanentRedirect('/secuela', '/secuelas'); // alias
Route::get('/sponsoring', [PagesStaticController::class, 'sponsoring']);
Route::get('/weekendcommittee', [PagesStaticController::class, 'weekendcommittee']);
Route::get('/vocabulary', [PagesStaticController::class, 'vocabulary']);

// team guide
Route::get('/teamguide', [PagesStaticController::class, 'teamguide']);
Route::get('/teambook', [PagesStaticController::class, 'teamguide']); // alias
Route::get('/teamguide/cha_general_instructions', [PagesStaticController::class, 'cha_general_instructions']);
Route::get('/teamguide/packinglist', [PagesStaticController::class, 'packinglist']);
Route::get('/palanca-sample-general-letter', [PagesStaticController::class, 'palancaSampleGeneralLetter']);
Route::get('/palanca-sample-individual-letter', [PagesStaticController::class, 'palancaSampleIndividualLetter']);
Route::get('/teamguide/table_rollo_discussion', [PagesStaticController::class, 'table_rollo_discussion']);
Route::get('/teamguide/relationship_of_talks', [PagesStaticController::class, 'relationship_of_talks']);
Route::get('/teamguide/meditation', [PagesStaticController::class, 'meditation']);
Route::get('/teamguide/decolores', [PagesStaticController::class, 'decolores']);


Route::resource('/events', EventController::class);
Route::get('/events/{event_id}/ical', [EventController::class, 'download'])->where(['event_id' => 'w?[\d]+']);

Route::get('weekend/{weekend}/team', [TeamController::class, 'show']); // alias
Route::get('weekend/{weekend}/roster', [TeamController::class, 'show']);
Route::get('weekend/{weekend}/roster/csv', [TeamController::class, 'show']);
Route::get('weekend/{weekend}/roster/csv-all', [TeamController::class, 'show']);
Route::get('weekend/{weekend}/roster/candidatecsv', [TeamController::class, 'show']);
Route::get('weekend/{weekend}/roster/candidatecsv-download', [TeamController::class, 'show']);
Route::get('weekend/{weekend}/roster/team-emails', [TeamController::class, 'show']);
Route::get('weekend/{weekend}/roster/team-email-positions', [TeamController::class, 'show']);

Route::resource('weekend', WeekendController::class)->name('index', 'weekend');
Route::patch('weekend/{weekend}/updatephoto', [WeekendController::class, 'updateTeamPhoto']);
Route::delete('weekend/{weekend}/deletephoto', [WeekendController::class, 'deleteTeamPhoto']);
Route::patch('weekend/{weekend}/updatebanner', [WeekendController::class, 'updateBannerPhoto']);
Route::delete('weekend/{weekend}/deletebanner', [WeekendController::class, 'deleteBannerPhoto']);

// Leaders-access only
Route::group(['middleware' => ['role:Member']], function () {
    // email
    Route::get('/team/{weekend}/email', [CommunicationController::class, 'composeTeamEmail']);
    Route::post('/team/{weekend}/email', [CommunicationController::class, 'emailTeamMembers']);
    Route::get('/email-everyone', [CommunicationController::class, 'composeCommunityEmail']);
    Route::post('/email-everyone', [CommunicationController::class, 'emailEntireCommunity']);
    Route::permanentRedirect('/communication', '/email-everyone'); // alias

    // Rector team editing
    Route::get('/team/{weekend}/edit', [TeamAssignmentController::class, 'show']);
    Route::get('/team/{weekend}/add', [TeamAssignmentController::class, 'create']);
    Route::post('/team/{weekend}/add', [TeamAssignmentController::class, 'store']);
    Route::get('/team/{weekend}/p/{position}/m/{member}/edit', [TeamAssignmentController::class, 'edit']);
    Route::patch('/team/{weekend}/p/{oldposition}/m/{member}/edit', [TeamAssignmentController::class, 'update']);
    Route::delete('/team/{weekend}/p/{position}/m/{member}/delete', [TeamAssignmentController::class, 'destroy']);

    // Candidate Reports
    Route::get('/reports/{weekend}/{gender}/dorm', [CandidateReportsController::class, 'dorm_needs']);
    Route::get('/reports/{weekend}/{gender}/diet', [CandidateReportsController::class, 'diet']);
    Route::get('/reports/{weekend}/{gender}/meds', [CandidateReportsController::class, 'meds']);
    Route::get('/reports/{weekend}/{gender}/notes', [CandidateReportsController::class, 'special_notes']);
    Route::get('/reports/{weekend}/{gender}/numbered', [CandidateReportsController::class, 'numbered_list']);
    Route::get('/reports/{weekend}/{gender}/palanca', [CandidateReportsController::class, 'palanca']);
    Route::get('/reports/{weekend}/{gender}/prayer', [CandidateReportsController::class, 'prayer_requests']);
    Route::get('/reports/{weekend}/{gender}/seating', [CandidateReportsController::class, 'seating_info']);
    Route::get('/reports/{weekend}/{gender}/sendoff_drivers', [CandidateReportsController::class, 'sendoff_drivers']);
    Route::get('/reports/{weekend}/{gender}/sendoff_script', [CandidateReportsController::class, 'sendoff_script']);

    Route::get('/leaders-help', [PagesStaticController::class, 'helpLeadersTools']);
    Route::get('/reports/stats', [WeekendStatsController::class, 'index']);
    Route::match(['get', 'post'], '/reports/leaders-worksheet', [ReportsController::class, 'leadersWorksheet']);
    Route::match(['get', 'post'], '/reports/interested_in_serving', [ReportsController::class, 'interestedInServing']);
    Route::match(['get', 'post'], '/reports/byposition', [ReportsController::class, 'serviceByPosition']);
    Route::get('/reports/sd-history', [SpiritualAdvisorController::class, 'serviceHistoryForSpiritualDirectors']);
    Route::get('/inactive-members', [ReportsController::class, 'inactive']);

    // Sponsor Emails
    Route::get('/sponsors/{weekend}/email', [CandidateEmailsController::class, 'composeSponsorsEmail']);
    Route::post('/sponsors/{weekend}/email', [CandidateEmailsController::class, 'sendEmailToSponsorsOfWeekend']);
});


Route::group(['prefix' => 'prayerwheel'], function () {
    Route::get('/', [PrayerWheelController::class, 'index'])->name('prayerwheels');
    Route::get('/{wheel}', [PrayerWheelController::class, 'show'])->where(['wheel' => '[0-9]+']);
    Route::get('/{weekend}', [PrayerWheelController::class, 'slug'])->where(['weekend' => '[a-zA-Z]+[0-9]+[mw]']);
    Route::get('/{fallback?}', [PrayerWheelController::class, 'index']);
    Route::patch('/{wheel}', [PrayerWheelController::class, 'update'])->where(['wheel' => '[0-9]+']);
    Route::delete('/{wheel}', [PrayerWheelController::class, 'destroy'])->where(['wheel' => '[0-9]+']);
    Route::get('/{wheel}/invite', [PrayerWheelNotificationsController::class, 'composeCommunityEmail'])->where(['wheel' => '[0-9]+']);
    Route::post('/{wheel}/invite', [PrayerWheelNotificationsController::class, 'emailEntireCommunity'])->where(['wheel' => '[0-9]+']);
});

// Pre-Weekend
Route::group(['middleware' => ['permission:add candidates|add community member']], function () {
    Route::get('member/add', [MembersController::class, 'create']);
    Route::post('member/add', [MembersController::class, 'store']);
    Route::get('candidates/{slug?}', [CandidateController::class, 'index'])->where(['slug' => '[a-z]{4}[0-9]+']);
    Route::get('candidates/add', [CandidateController::class, 'create']);
    Route::post('candidates/add', [CandidateController::class, 'store']);
    Route::get('candidates/{slug?}/{candidate}', [CandidateController::class, 'edit'])->where(['slug' => '[a-z]{4}[0-9]+', 'candidate' => '[0-9]+']);
    Route::patch('candidates/{candidate}', [CandidateController::class, 'update'])->where(['candidate' => '[0-9]+']);
    Route::get('candidates/{slug?}/{candidate}/ack', [CandidateController::class, 'sendSponsorAcknowledgement'])->where(['slug' => '[a-z]{4}[0-9]+', 'candidate' => '[0-9]+']);
    Route::get('candidates/{slug?}/{candidate}/remind', [CandidateController::class, 'sendSponsorAcknowledgementReminder'])->where(['slug' => '[a-z]{4}[0-9]+', 'candidate' => '[0-9]+']);
    Route::post('convert/c/p/{member}', [MembersController::class, 'convertCandidateToPescador']);
    Route::post('convert/c/w/{weekend}', [MembersController::class, 'convertWeekendsCandidatesToPescadores'])->where(['weekend' => '[0-9]+'])->name('convert_weekends_candidates_to_pescadores');
    Route::post('demote/{member}', [MembersController::class, 'demotePescadoreToRestrictedMember']);
    Route::get('reports/sendoff-couples', [PreWeekendController::class, 'sendOffCoupleHistoryReport']);
    Route::get('preweekend/invitations', [PreWeekendController::class, 'invitationPreparationWorksheet']);
    Route::post('/candidates/{candidate_user?}/invite', [CandidateEmailsController::class, 'sendCandidateConfirmationEmail'])->where(['candidate_user' => '\d+'])->name('candidate_welcome_confirmation_email');
});
Route::group(['middleware' => ['permission:email candidates']], function () {
    Route::get('reminders/c/{user}', [CandidateEmailsController::class, 'sendCandidateReminderToOnePerson']);
    Route::get('reminders/w/{weekend}', [CandidateEmailsController::class, 'sendCandidatePackingListToAllCandidatesForWeekend']);
});
Route::get('/confirm/candidate/{candidate}/{hash}', [CandidateController::class, 'confirm']);

Route::group(['middleware' => ['permission:email candidates,webmaster-email-how-to-login-msg']], function () {
    Route::post('reminders/websiteaccess/{member}', [MembersController::class, 'sendWebsiteLoginInstructionsEmail'])->name('website_access_reminder');
    Route::post('candidates/websiteaccess/{weekend}', [CandidateEmailsController::class, 'sendWebsiteWelcomeToCandidatesForWeekend'])->name('website_access_to_candidates');
});

Route::group(['middleware' => ['permission:delete candidates']], function () {
    Route::delete('candidates/{candidate}', [CandidateController::class, 'destroy']);
});


Route::group(['middleware' => ['permission:delete members']], function () {
    Route::delete('members', [MembersController::class, 'destroy']);
});

/***********************************************/

// Accounting
Route::group(['middleware' => ['permission:record candidate fee payments']], function () {
    Route::get('finance/candidates/{slug?}', [CandidatePaymentsController::class, 'index'])->where(['slug' => '[a-z]{4}[0-9]+']);
    Route::get('finance/candidates/{slug?}/{candidate}', [CandidatePaymentsController::class, 'edit'])->where(['slug' => '[a-z]{4}[0-9]+', 'candidate' => '[0-9]+']);
    Route::patch('finance/candidates/{candidate}', [CandidatePaymentsController::class, 'update'])->where(['candidate' => '[0-9]+']);
});
Route::group(['middleware' => ['role:Member']], function () {
    Route::get('finance/team/{weekend?}', [TeamFeePaymentsController::class, 'index'])->where(['weekend' => '[0-9]+'])->name('list_team_fees');
    Route::post('finance/team/{weekend}', [TeamFeePaymentsController::class, 'store'])->where(['weekend' => '[0-9]+'])->name('record_team_fee_payment');
    Route::get('finance/team/unpaid/{weekend}', [TeamFeePaymentsController::class, 'unpaid_fees'])->where(['weekend' => '[0-9]+'])->name('unpaid_team_fees');
});


/***********************************************/
Route::get('/admin', [AdminController::class, 'index'])->name('admin');

Route::group(['prefix' => 'admin', 'middleware' => ['role:Admin|Super-Admin']], function () {
    Route::get('/permissions', [RolesAndPermissionsController::class, 'index'])->name('showAssignedRoles');
    Route::post('/assign_role', [RolesAndPermissionsController::class, 'store'])->name('assignRole');
    Route::delete('/revoke_role', [RolesAndPermissionsController::class, 'destroy'])->name('revokeRole');

    Route::get('/activitylog', [ActivitylogController::class, 'index'])->name('activitylog');

    Route::get('/service/create', [WeekendExternalController::class, 'create'])->name('service.create');
    Route::post('/service', [WeekendExternalController::class, 'store']);
    Route::get('/service/{id}/edit', [WeekendExternalController::class, 'edit'])->where(['id' => '\d+'])->name('service.edit');
    Route::patch('/service/{id}', [WeekendExternalController::class, 'update'])->where(['id' => '\d+']);
    Route::delete('/service/{id}', [WeekendExternalController::class, 'destroy'])->where(['id' => '\d+']);

    Route::get('/mailchimpaudit', [MailchimpSubscriptionController::class, 'auditSubscribersMissing']);

    Route::post('/testemail', [AdminController::class, 'emailAllAdmins']);

    Route::post('/email-how-to-sponsor', [CommunicationController::class, 'emailHowToSponsorToEveryone']);

    Route::get('/data/members', [AdminController::class, 'members_edit'])->name('admin.members_audit');

    Route::get('/members_without_avatar', [ReportsController::class, 'membersWithoutAvatar'])->name('missing_avatars');

    Route::get('/settings', [SettingsController::class, 'index'])->name('admin-settings-edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('admin-settings-update');
});
/***********************************************/
Route::get('/payment', [PaymentController::class, 'displayForm'])->name('stripe-payment-form');
Route::post('/payment', [PaymentController::class, 'create'])->name('stripe-payment');
Route::get('/donate', [PaymentController::class, 'displayForm'])->name('donate');
Route::get('/fees', [PaymentController::class, 'displayForm'])->name('fees');
/***********************************************/


Route::resource('location', LocationController::class);

Route::impersonate();

Route::fallback(function () {
    return response()->view('errors.404', request(), 404);
})->name('fallback');







/***********************************************/
Route::prefix('webhooks')
    ->group(function () {
        Route::match(['get', 'post'], 'mailchimp', [MailchimpWebhooksController::class]);
        Route::post('stripe', [StripeWebhooksController::class]);
});

/***********************************************/


