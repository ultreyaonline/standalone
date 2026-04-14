# Ultreya Developer Guide

**Application:** Tres Dias Community Management Platform

**Stack:** Laravel 13 · MySQL · Redis · Bootstrap 4 · Laravel Horizon

**Last documented:** March 2026 

---

## Table of Contents

1. [What this application does](#1-what-this-application-does)
2. [Domain vocabulary — read this first](#2-domain-vocabulary--read-this-first)
3. [Architecture overview](#3-architecture-overview)
4. [Data model](#4-data-model)
5. [Authentication & authorization](#5-authentication--authorization)
6. [Queue system & scheduled jobs](#6-queue-system--scheduled-jobs)
7. [Email system](#7-email-system)
8. [Key packages and why they're here](#8-key-packages-and-why-theyre-here)
9. [Configuration — `config/site.php`](#9-configuration--configsitephp)
10. [Laravel upgrade path notes](#10-laravel-upgrade-path-notes)
11. [Known TODOs and rough edges](#11-known-todos-and-rough-edges)
12. [AI-assisted development guidance](#12-ai-assisted-development-guidance)

---

## 1. What this application does

Ultreya is a private membership management system for a Christian retreat community called "Tres Dias". It is not a public-facing e-commerce or SaaS product — it is an internal organizational tool.

**Core functions:**

- **Member directory** — stores minimal necessary personal info, contact details, community role, and service history for all community members ("pescadores")
- **Weekend management** — tracks retreat weekends (called simply "weekends"); each weekend has a Rector (leader), a team of members serving in specific roles, and a list of candidates (attendees)
- **Candidate management** — tracks the intake and preparation process for new retreat candidates, including sponsor relationships, fees, dietary needs, and the invitation/confirmation email workflow
- **Team assignment management** — allows Rectors and leaders to build the team roster, track whether members have accepted/declined, and record fee payments
- **Prayer Wheel** — a sign-up system where community members volunteer to pray during specific one-hour slots across the weekend; automated emails acknowledge sign-ups and send daily reminders
- **Communications** — allows authorized users to compose and send targeted emails (to the whole community, to a weekend's team, to candidates' sponsors, etc.)
- **Reports** — generates printable and CSV reports for weekend logistics: dorm assignments, meal/diet needs, medications, palanca preparation, seating charts, etc.
- **Events calendar** — community events, including iCal download support
- **Finance** — records candidate fee payments and team fee payments, with basic reporting
- **Stripe integration** — online donation/payment page (used for candidate fees). Also supports PayPal.
- **Mailchimp integration** — community newsletter subscription management (keeps the Mailchimp database current with new/removed members, so Mailchimp mailings can be sent without a lot of manual list-maintenance).

---

## 2. Domain vocabulary — read this first

This codebase uses terminology specific to the Tres Dias retreat movement. Without this context, variable names will be confusing.

| Term | Meaning in this codebase |
|---|---|
| **Weekend** | A 3-day retreat event (Thu–Sun). Men's and Women's weekends are tracked separately. Identified by number + gender (e.g. "ANYTD #47M"). |
| **Candidate** | A person attending a weekend for the first time. Stored in the `candidates` table; also has a `User` record. The `Candidate` model represents a couple (man + woman fields), even for single attendees. |
| **Pescador / Pescadores** | A community member who has already attended a weekend. The normal `User` model represents pescadores. The term comes from the Spanish word for "fisherman." |
| **Rector** | The leader (head) of a weekend team. Stored as `rectorID` on the `weekends` table. |
| **Cha / Head-Cha / AH-Cha** | "Cha" is short for a spanish word meaning talk/presentation, but effectively here refers to "a member serving on a weekend's team". Head Cha manages the talk schedule; AH Cha is Assistant Head Cha. These are specific team roles stored in `weekend_roles`. |
| **SD / Spiritual Director** | Clergy who provide spiritual guidance for the weekend. There is special business logic around them (e.g., fee exemptions). |
| **Sponsor** | The pescador who invited and is accompanying a candidate. Stored on the `users` table (`sponsorID` column). |
| **Palanca** | Letters and prayers sent to candidates during the weekend from the community. Reports and email workflows exist to coordinate this. |
| **Prayer Wheel** | A schedule of one-hour prayer slots covering the entire weekend (Thursday 6pm to Sunday 5pm). Members sign up to pray during specific slots. See `PrayerWheel`, `PrayerWheelSignup`. |
| **Sendoff** | The ceremony before the weekend where candidates are sent off by their families. Has its own logistics (drivers, location, timing). |
| **Serenade** | A musical gathering that happens during the closing of the weekend. Tracked with timing and role fields on the `Weekend` model. |
| **Secuelas / Reunion Groups** | Post-weekend small group gatherings for pescadores. Referenced in the routes and static pages. |
| **Secretariat** | The governing body of the community. Stored in its own `secretariat` table. |
| **Community** | The overall group of pescadores. `community_acronym`/name are set in `config/site.php`. Multi-community support exists (members from other Tres Dias communities can serve). |
| **Visibility flag** | An integer on `weekends` controlling who can see team details. Defined in `App\Enums\WeekendVisibleTo`. |

---

## 3. Architecture overview

```
Browser (Bootstrap 4 + vanilla JS + jQuery)
    │
    ▼
Laravel
    ├── routes/web.php          ← all main routes
    ├── routes/webhooks.php     ← Stripe & Mailgun webhook routes
    ├── routes/api.php          ← minimal JSON API (DataTables)
    ├── app/Http/Controllers/   ← standard MVC controllers
    ├── app/Http/Middleware/    ← auth, permission, webhook verification
    ├── resources/views/        ← Blade templates
    │
    ├── app/ (models)
    │   ├── User.php            ← community members + candidates
    │   ├── Weekend.php         ← retreat weekends
    │   ├── WeekendAssignments.php ← team roles per weekend
    │   ├── WeekendRoles.php    ← role definitions (Rector, Cha, SD, etc.)
    │   ├── Candidate.php       ← pre-weekend couple/individual record
    │   ├── PrayerWheel.php     ← prayer slot schedule per weekend
    │   ├── PrayerWheelSignup.php ← individual prayer slot sign-ups
    │   ├── Event.php           ← community events calendar
    │   ├── TeamFeePayments.php ← fee payment tracking (accountant aid)
    │   └── ...
    │
    ├── app/Jobs/               ← queued jobs (prayer wheel emails)
    ├── app/Mail/               ← Mailable classes (email templates)
    ├── app/Notifications/      ← Laravel Notifications (Slack + DB)
    ├── app/Listeners/          ← Event listeners (auth logging, cleanup)
    ├── app/Events/             ← Custom domain events
    ├── app/Policies/           ← Authorization policies
    ├── app/Enums/              ← Type-safe integer enums
    ├── app/Reports/            ← Report-generation classes
    └── bootstrap/app.php       ← Scheduled task definitions
    │
    ▼
Redis (phpredis) (queue driver + Horizon monitoring)
MySQL (primary database)
AWS S3 (file storage — avatars, photos)
Mailgun (transactional email delivery)
Mailchimp (newsletter list management)
Stripe/PayPal (offsite payment processing)
```

### Request lifecycle notes

- All web routes pass through the `web` middleware group, which includes `LogLastUserActivity` (updates `users.last_login_at`).
- Route-level authorization uses two middleware: `role:RoleName` (checks Spatie roles) and `permission:permission-name` (checks Spatie permissions). Both are in `app/Http/Middleware/`.
- There is a global scope on `WeekendAssignments` that filters to only `Community`-visible weekends by default. Many places in the code use `->withoutGlobalScope('visibleWeekendsOnly')` to bypass this — pay attention to this when working with team data queries.

---

## 4. Data model

### Core tables and their models

| Table | Model | Notes |
|---|---|---|
| `users` | `App\User` | All people: pescadores, candidates, admins |
| `weekends` | `App\Weekend` | Retreat events |
| `weekend_assignments` | `App\WeekendAssignments` | Many-to-many: users ↔ weekends via roles |
| `weekend_roles` | `App\WeekendRoles` | Role definitions (Rector=1, HeadCha=2, etc.) |
| `candidates` | `App\Candidate` | Pre-weekend candidate records (couple-centric) |
| `prayer_wheels` | `App\PrayerWheel` | One prayer wheel per weekend |
| `prayer_wheel_signups` | `App\PrayerWheelSignup` | Individual hour-slot commitments |
| `events` | `App\Event` | Community calendar events |
| `team_fee_payments` | `App\TeamFeePayments` | Team member fee records |
| `secretariat` | `App\Secretariat` | Governing body members |
| `sections` | `App\Section` | Weekend sections/departments |
| `locations` | `App\Location` | Retreat venue records |

### Notable design decisions

**Candidates are couples, not individuals.** The `candidates` table has `m_user_id` and `w_user_id` columns. A single person attending alone will only have one of these populated. Both the man and woman have separate `User` records. The `Candidate->getNamesAttribute()` method handles formatting the combined name.

**Candidates are also Users.** A candidate is registered as a `User` first, then a `Candidate` record is created linking to that user. When they complete their weekend they "become a pescador" — their `User` record remains; the `Candidate` record is no longer the primary way to find them, but it is retained for lookup purposes.

**`users.weekend` is a string, not a foreign key.** The `weekend` column on `users` stores the weekend short name (e.g. `"ANYTD #47"`) as a plain string, not an integer FK. This is how the system knows which inaugural weekend a member attended. Queries like `User::where('weekend', $short_name)` are common throughout the codebase.

**Prayer Wheel timeslots are integers.** The `prayer_wheel_signups.timeslot` column stores an integer offset from Thursday 5pm. Slot 1 = Thu 6pm, slot 2 = Thu 7pm, etc. through to slot 72 = Sun 5pm. The full slot list is defined as a static collection in `PrayerWheel::getTimeSlots()`.

**`visibility_flag` is an enum-like integer.** See `App\Enums\WeekendVisibleTo` for the full scale (0 = AdminOnly through 6 = Community). This controls both what appears in calendars and what team information is shown.

**`confirmed` on weekend_assignments is also an enum.** See `App\Enums\TeamAssignmentStatus` (0 = Pending through 8 = DonatedFees). The value `4` (Accepted) is the key threshold — most queries filter `>= Accepted`.

---

## 5. Authentication & authorization

### Authentication

Standard Laravel Auth with username/password. Registration is disabled (`Auth::routes(['register' => false])`). New members are created by authorized users through the `/member/add` route.

Username is a string field, not an email address. The system does not enforce email uniqueness, so multiple users can share the same email (e.g., spouses). Authentication is done via username.

New pescadores who have never set a password use the "pescador" route (`/pescador`) which shows the forgot-password form — this is the standard onboarding flow for new members.

### Authorization layers

There are two parallel systems, both from the `spatie/laravel-permission` package:

**Roles** (coarse-grained): `Super-Admin`, `Admin`, `President`, `Mens Leader`, `Womens Leader`, `Rector Selection`, `Member`. Applied in routes using the `role:` middleware.

**Permissions** (fine-grained): Strings like `"add candidates"`, `"delete members"`, `"email candidates"`, `"record candidate fee payments"`, etc. Applied in routes using the `permission:` middleware, or in controller/view logic with `$user->can('...')`.

The full list of roles and permissions is seeded in `database/seeds/RolesAndPermissionsSeeder.php` — read this file to understand the full permission matrix.

**Impersonation:** The `lab404/laravel-impersonate` package is installed. Admins can impersonate other users via the UI. Impersonation login/logout events are also fed into the audit log.

**Horizon access:** Defined in `App\Providers\HorizonServiceProvider::gate()`. Only users with the `"manage queues"` permission can access the `/horizon` dashboard.

### Policy class

`App\Policies\TeamAssignmentsPolicy` controls who can view/edit weekend team rosters. Note the class comment: this policy is **not** bound to a model in the normal way — it is registered manually in `TeamAssignmentController::__construct()` via `Gate::policy()`.

---

## 6. Queue system & scheduled jobs

### Infrastructure

- **Queue driver:** Redis (configured in `.env` as `QUEUE_CONNECTION=redis`)
- **Queue monitoring:** Laravel Horizon at `/horizon` (requires `manage queues` permission)
- **Horizon config:** `config/horizon.php` — 3 worker processes in production, 1 locally; `memory_limit` is 64MB per worker
- **Redis connection:** `phpredis`

> ⚠️ In local/test environments, `QUEUE_CONNECTION` defaults to `sync` (jobs run immediately, inline). Only in production does Redis/Horizon actually queue jobs asynchronously.

### Scheduled tasks (`bootstrap/app.php`)

| Schedule | Task | Notes |
|---|---|---|
| Every 10 minutes | `SendPrayerWheelAcknowledgements::dispatch()` | Emails members acknowledging their prayer sign-ups |
| Daily @ 16:00 | `SendPrayerWheelReminderEmails::dispatch()` | Emails members reminding them of upcoming prayer slots |
| Daily @ 03:40 | `backup:clean` | Removes old backups (spatie/laravel-backup) |
| Daily @ 03:50 | `backup:run` | Creates DB + file backup |
| Daily | `activitylog:clean` | Prunes old activity log records |
(Horizon metrics can also be enabled as a scheduled task.)

### Job: `SendPrayerWheelAcknowledgements`

**File:** `app/Jobs/SendPrayerWheelAcknowledgements.php`

**Purpose:** For every member who has unacknowledged prayer sign-ups, send one consolidated email listing all their upcoming slots.

**Logic flow:**
1. Find all `PrayerWheelSignup` records where `acknowledged_at IS NULL`
2. Get all sign-ups for those members (not just the unacknowledged ones — sends the full picture)
3. Filter to: future timeslots only, active weekends only (not ended > 1 month ago), non-empty email
4. Group by member, send one `PrayerWheelAcknowledgementEmail` per member
5. On success, set `acknowledged_at = NOW()` on each slot

**Important:** The constructor captures `Carbon::now()` as `$cutoffTime` at dispatch time, not at job-execution time. This means if the job sits in the queue for a while, it uses the original dispatch timestamp for "future slot" filtering. Under normal conditions (every-10-minute schedule) this is irrelevant, but it's worth knowing.

### Job: `SendPrayerWheelReminderEmails`

**File:** `app/Jobs/SendPrayerWheelReminderEmails.php`

**Purpose:** Daily reminder emails to members who have upcoming prayer slots and have opted in to reminders.

**Logic flow:**
1. Find members with unreminded sign-ups (`reminded_at IS NULL`)
2. Filter to: future timeslots, slots during an active weekend, non-ended weekends, non-empty email, `user->receive_prayer_wheel_reminders === true`
3. Group by member, send one `PrayerWheelReminderEmail` per member

**Known issue (see @TODO in source):** The `reminded_at` flag is **never actually set** — the code that would stamp it is commented out. This means the reminder is sent every day until the slot passes. This is intentional behaviour (daily reminders) but the variable name `reminded_at` and the `whereNull('reminded_at')` filter are misleading. A future developer should either: (a) remove the `reminded_at` filtering logic entirely and query by upcoming slot date instead, or (b) decide on a proper "one reminder only" vs "daily reminder" policy and implement it.
The current behaviour is intentional.

---

## 7. Email system

### Transport

Mailgun (configured in `.env` and `config/mail.php`). The `MAIL_MAILER` env key should be `mailgun`.

### Mailable classes (`app/Mail/`)

Each email type has its own Mailable class. All emails queue themselves (use `Mail::to(...)->queue(...)` rather than `send()`).

| Mailable | Trigger | Recipient |
|---|---|---|
| `CandidateConfirmationEmail` | Manually sent by pre-weekend team | Candidate |
| `CandidateReminderEmail` | Manually sent by pre-weekend team | Candidate |
| `CandidateBecomesPescador` | When candidate is "converted" | New pescador |
| `InternalCandidateRegistrationNotice` | On new candidate registration | Pre-weekend team inbox |
| `SponsorAcknowledgeCandidate` | On candidate registration | Sponsor |
| `SponsorAcknowledgeCandidateReminder` | Manually sent | Sponsor |
| `SponsorFollowup` | Manually sent | Sponsor |
| `MessageToSponsors` | Bulk: compose UI in app | All sponsors for a weekend |
| `MessageToCommunity` | Bulk: compose UI in app | All community members |
| `MessageToTeamMembers` | Bulk: compose UI in app | Weekend team members |
| `PrayerWheelInviteEmail` | Manually triggered | Community members |
| `PrayerWheelAcknowledgementEmail` | Scheduled job (every 10 min) | Prayer wheel sign-up member |
| `PrayerWheelReminderEmail` | Scheduled job (daily 4pm) | Prayer wheel sign-up member |
| `HowToSponsor` | Admin bulk send | Community members |
| `PaymentOnline_Confirmation` | After Stripe payment | Payer |
| `WebsiteLoginInstructions` | Manually triggered | Member |

### Mailgun webhook

`routes/webhooks.php` and `app/Http/Middleware/MailgunWebhookMiddleware.php` handle inbound Mailgun event callbacks (e.g., email bounces/failures). The middleware verifies the Mailgun webhook signature before processing.

### Mailchimp integration

`spatie/laravel-newsletter` is used for community newsletter subscription management. The `MailchimpSubscriptionController` handles subscribe/unsubscribe/delete. There is also an audit view at `/admin/mailchimpaudit` that compares active members against Mailchimp subscribers.

---

## 8. Key packages and why they're here

| Package | Purpose | Notes |
|---|---|---|
| `spatie/laravel-permission` | Roles & permissions | 
| `spatie/laravel-activitylog` | Audit log of model changes | Enabled on User, Weekend, Candidate, PrayerWheel, WeekendAssignments via `LogsActivity` trait |
| `spatie/laravel-backup` | DB + file backups to S3 | Scheduled daily at 3:50am |
| `spatie/laravel-newsletter` | Mailchimp API wrapper | |
| `laravel/horizon` | Redis queue monitoring dashboard | Access at `/horizon`, requires `manage queues` permission |
| `bensampo/laravel-enum` | Type-safe PHP enums | Used for `WeekendVisibleTo` and `TeamAssignmentStatus` (Could go to native enums) |
| `lab404/laravel-impersonate` | Admin user impersonation | |
| `intervention/image` | Image resizing | Used for avatar upload processing |
| `league/flysystem-aws-s3-v3` | AWS S3 filesystem driver | For avatar and photo storage |
| `eluceo/ical` | iCal file generation | For events calendar download |
| `illuminatech/config` | DB-stored config overrides | Allows runtime config changes |

---

## 9. Configuration — `config/site.php`

This is the most important application-specific config file. It contains the community's identity and feature flags.

```php
'community_acronym'             => 'ANYTD',        // used in queries and weekend slugs
'community_long_name'           => 'Any Tres Dias',
'local_community_filter'        => 'ANYTD',        // distinguishes local vs extended community members
'email_general'                 => 'news@example.org',
'email-preweekend-mailbox'      => 'registration@example.org',
// ... other contact emails

'team_fees_spiritual_directors_exempt' => true,    // SDs don't pay team fees
'notify_PreWeekend_of_NewCandidate_When' => 'sponsor_acknowledgement_sent',
    // controls WHEN the pre-weekend team gets notified of new candidates:
    // 'initial_data_entry' = immediately on data entry
    // 'sponsor_acknowledgement_sent' = only after the sponsor ack email is sent

'prayerwheel_empty_before_doubles' => 4,    // how many empty slots before allowing double sign-ups
'prayerwheel_names_visible_to_all' => 0,    // 0 = only admin sees who signed up for each slot
'members_may_edit_own_sponsor'  => 0,       // self-service sponsor update
'members_may_edit_own_spouse'   => 0,       // self-service spouse update
'admin_old_weekend_teams_editable' => false,  // prevent editing past weekend teams
'community_SA_may_assign_SDs'   => false,   // Community Spiritual Advisors can assign SDs
'preweekend_does_physical_mailing' => true, // show/hide mailing-related UI in candidate workflow
'features-emailtypes-reunion'   => true,    // enable reunion group email type
'preweekend_sponsor_confirmations_enabled' => true,

// ... and more...
```

**Notification recipient config:** The `notify_*` keys (e.g., `notify_UserAdded1`, `notify_TeamfeePayments1`) accept email addresses or `null`. `null` means that notification type is disabled. Up to two recipients per notification type are supported.

---

## 10. Laravel upgrade path notes

When upgrading, here are some key breaking-change areas to watch:

### Pagination — Bootstrap 3 override
- `AppServiceProvider::boot()` calls `Paginator::useBootstrapThree()`. In Laravel 9+, this method was renamed to `useBootstrapThree()` (stays the same) but Bootstrap 4/5 themes became default. If Bootstrap 4 is kept, this line is still needed; if you upgrade to Bootstrap 5, remove it and update the pagination views.
- **Note:** The comment in the code says "Bootstrap 3" but the front-end actually uses Bootstrap 4. The method call still works fine; it just means pagination uses the B3 markup which is compatible with B4 styling.

### `bensampo/laravel-enum`
- The custom enum classes (`WeekendVisibleTo`, `TeamAssignmentStatus`) extend this package's `Enum` base class. In PHP 8.1+, native enums exist. One can consider migrating to native PHP enums and removing this package.

### `Collection::macro('toInlineCsv', ...)`
- This custom macro is registered in `AppServiceProvider`. It will survive upgrades unchanged but is worth noting for developers unfamiliar with Collection macros.

---

## 11. Known TODOs and rough edges

These are flagged with `@TODO` in the source or are patterns a future developer should be aware of:

**`SendPrayerWheelReminderEmails`:** The `reminded_at` flag is never stamped. The job runs daily but relies on `whereNull('reminded_at')` — since this is never set, the filter doesn't work as the variable name implies. See [Section 6](#6-queue-system--scheduled-jobs) for full details.

**`Weekend::scopeActive()`:** Contains a comment noting the admin override for visibility could be improved. The `@can('create weekends')` check is used but a more elegant approach was planned.

**`WeekendAssignments` global scope:** There is a `visibleWeekendsOnly` global scope that restricts queries to `Community`-visible weekends. Many places use `->withoutGlobalScope('visibleWeekendsOnly')` to bypass it. This is intentional but creates subtle bugs if forgotten — always check whether a query that seems to return no results is being caught by this scope.

**`TeamAssignmentsPolicy::delete()`:** Has a `@TODO` noting it should receive a `$assignment` model binding instead of `$weekend`. Deleting "self" (removing yourself from a team) is also flagged as something that should be prevented but isn't yet.

**`users.weekend` is a string:** The design choice to store the weekend name as a string rather than a foreign key creates de-normalization. It cannot be changed without a data migration and careful refactoring of the many places that query by this string.

**Location routes:** `routes/web.php` has a comment `// @TODO - register routes from Locations model`. 

**`candidate_age`:** The migration `2019_10_02_010101_change_candidate_age_to_string.php` changed the age column to a string, suggesting age is entered as free text (e.g., "35-40" or "40+") rather than a strict integer. Keep this in mind when filtering or sorting by age.

**`config/app.php` timezone:** Verify this matches the server timezone, especially for Prayer Wheel slot calculations which depend on `Carbon::now()` matching local time.

### Javascript
- Vite is used for asset bundling, but the front-end is mostly vanilla JS and jQuery. There are 2 Livewire pages. 

---

## 12. AI-assisted development guidance

This section is guidance for developers using AI coding assistants (like Claude, GitHub Copilot, etc.) when working on this codebase.

### Things AI assistants handle well here

- **Reading and explaining existing logic.** The business logic (especially `Weekend.php`, `PrayerWheelSignup.php`, and the job classes) is dense. Ask AI to explain what a method does before modifying it.
- **Adding PHPDoc blocks.** This codebase has minimal inline documentation. AI can generate accurate PHPDoc for methods after reading the implementation.
- **Writing new Mailables.** The pattern is consistent across `app/Mail/`. Ask AI to generate a new Mailable following the existing class structure.
- **Writing Blade templates.** The Bootstrap 4 patterns are consistent. AI can scaffold new views following existing templates in `resources/views/`.
- **Writing migrations.** The migration naming convention and column patterns are clear from existing files.
- **Understanding the permission matrix.** Ask AI to read `RolesAndPermissionsSeeder.php` and explain who can do what.

### Where to be careful with AI assistance

**Domain vocabulary confusion.** AI will not know that "cha" means "a serving team member" or that "pescador" means "completed a retreat." Always confirm that AI-generated code uses these terms correctly and doesn't rename them to generic alternatives.

**The `visibleWeekendsOnly` global scope.** AI will not spontaneously add `withoutGlobalScope('visibleWeekendsOnly')` when it's needed. If a query unexpectedly returns no results, always ask: "Does this query need to bypass the global scope on `WeekendAssignments`?"

**The string-based weekend reference on `users`.** AI may suggest adding a foreign key constraint to `users.weekend`. Do not do this without a full migration — the column intentionally stores a string short name, and changing it is a large refactor.

**Queue vs. sync execution.** AI may generate code calling `Mail::send()` rather than `Mail::queue()`. In production, all emails should use `->queue()` to go through Redis/Horizon. Check any AI-generated mail-sending code.

**Bootstrap 4 vs. Bootstrap 5.** The front-end uses Bootstrap 4. If asking AI to write Blade/HTML, specify "Bootstrap 4" explicitly, as AI defaults to generating Bootstrap 5 class names (e.g., `me-2` vs `mr-2`, `ms-3` vs `ml-3`).


### Recommended AI workflow for new features

1. **Understand before writing.** Paste the relevant existing model/controller into the AI context. Ask it to explain the current behavior before writing new code.
2. **Check for existing patterns.** There is almost always a similar existing feature. If adding a new report, look at `CandidateReportsController.php`. If adding a new email, look at an existing Mailable. Ask AI to "follow the same pattern as X."
3. **Test the permission boundaries.** After writing new routes, verify they're wrapped in the correct `role:` or `permission:` middleware group. Ask AI to review the `routes/web.php` file and confirm the new route has appropriate access control.
4. **Watch for N+1 queries.** The codebase has some pre-existing N+1 issues (particularly in views that loop over collections and call model methods). When adding loops, use Eloquent's `with()` eager loading.
5. **Run the test suite.** The project has a meaningful feature test suite in `tests/Feature/`. Run `php artisan test` after AI-generated changes.

---

*End of DEVELOPER_GUIDE.md*
