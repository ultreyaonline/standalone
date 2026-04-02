<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Candidate
 *
 * Represents a prospective retreat attendee in the pre-weekend (registration) process.
 *
 * The `candidates` table is designed around couples attending together.
 * Both the man and woman each have a separate User record, and the Candidate record
 * links to both via `m_user_id` and `w_user_id`.
 *
 * For an individual (not attending as a couple), only ONE of these fields is populated.
 *
 * All candidate data fields are prefixed m_ (man) or w_ (woman):
 *   m_age, m_special_diet, m_smoker, m_special_notes, m_arrival_poc_person, etc.
 *
 * ## Candidate lifecycle
 * 1. Sponsor registers the candidate (creates User + Candidate records)
 * 2. Sponsor confirmation email sent → sponsor confirms details
 * 3. Pre-weekend team reviews, mails invitation (snail-mail)
 * 4. Candidate responds (response card returned by postal mail)
 * 5. Fees paid
 * 6. Weekend happens
 * 7. Candidate "becomes a Pescador" → convertCandidateToPescador() in MembersController
 *    (The User record persists and is promoted; the Candidate record is effectively retired, retained for historical reporting.)
 *
 * ## Relationship to Users and Weekends
 * - Candidate → User via m_user_id / w_user_id (BelongsTo)
 * - User → Weekend via users.weekend (a STRING matching Weekend::shortname)
 *   There is NO FK from Candidate directly to Weekend, because sometimes we just want to unlink a candidate, leaving them for future attendance consideration.
 *
 * ## Weekend identification
 * Within the Tres Dias nomenclature one of the things a person identifies with is what "weekend" they attended, referring to the community name and the weekend "number". They may "serve" by attending subsequent weekends, but their first weekend as a "candidate" is "their weekend".
 * Thus a "user's weekend" assignment is stored as a plain string on users.weekend (e.g. "ANYTD #47").
 *
 * To find the Weekend model from a Candidate, use getWeekendDatesTextMAttribute()
 * or getWeekendDatesTextWAttribute() which parse the string and query the weekends table.
 *
 * @property int         $id
 * @property int|null    $m_user_id     FK → users.id (man)
 * @property int|null    $w_user_id     FK → users.id (woman)
 * @property string|null $m_age
 * @property string|null $w_age
 * @property bool        $married
 * @property bool        $fees_paid
 * @property bool        $ready_to_mail
 * @property bool        $invitation_mailed
 * @property bool        $sponsor_confirmed_details
 * @property bool        $completed
 * @property string|null $weekend       Short name string, e.g. "ANYTD #47"
 *
 * @package App
 */
class Candidate extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $casts = [
        'married'                   => 'boolean',
        'vocational_minister'       => 'boolean',
        'sponsor_confirmed_details' => 'boolean',
        'fees_paid'                 => 'boolean',
        'ready_to_mail'             => 'boolean',
        'invitation_mailed'         => 'boolean',
        'm_response_card_returned'  => 'boolean',
        'w_response_card_returned'  => 'boolean',
        'm_smoker'                  => 'boolean',
        'w_smoker'                  => 'boolean',
        'completed'                 => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
        'completed',
    ];

    // -------------------------------------------------------------------------
    // Computed Attributes
    // -------------------------------------------------------------------------

    /**
     * Combined display name for the candidate couple (or individual).
     *
     * Logic:
     * - If only man: "First Last"
     * - If man with different surname than woman: "John Smith and Jane Jones"
     * - If couple with matching surname: "John and Jane Smith"
     * - If only woman: "Jane Smith"
     *
     * @return string
     */
    public function getNamesAttribute()
    {
        $name = $this->man ? $this->man->first : '';

        // include surname if no spouse
        if ($this->man && !$this->woman) {
            $name .= ' ' . $this->man->last;
        }

        // if spouse has non-matching surname, include man's surname
        if ($this->man && $this->woman && $this->man->last != $this->woman->last) {
            $name .= ' ' . $this->man->last;
        }

        if ($this->man && $this->woman) {
            $name .= ' and ';
        }
        $name .= $this->woman ? $this->woman->name : '';

        return $name;
    }

    /**
     * Mailing address for the candidate couple.
     *
     * Returns the man's address if available, otherwise the woman's.
     *
     * @return string
     */
    public function getAddressFormattedAttribute()
    {
        if (!$this->attributes['m_user_id'] && !$this->attributes['w_user_id']) {
            return '';
        }

        return $this->man->address_formatted ?? $this->woman->address_formatted;
    }

    /**
     * Name of the sponsor for this candidate couple.
     *
     * Returns the man's sponsor name if available, otherwise the woman's.
     *
     * @return string|null
     */
    public function getSponsorAttribute()
    {
        if (!$this->attributes['m_user_id'] && !$this->attributes['w_user_id']) {
            return '';
        }

        return $this->man->sponsor ?? $this->woman->sponsor ?? null;
    }

    /**
     * The weekend short name this candidate is registered for, e.g. "ANYTD #47".
     *
     * Read from the linked User record (man's first, woman's as fallback).
     *
     * @return string
     */
    public function getCandidateWeekendAttribute()
    {
        if (!$this->attributes['m_user_id'] && !$this->attributes['w_user_id']) {
            return '';
        }

        return $this->man->weekend ?? $this->woman->weekend;
    }

    /**
     * Formatted weekend dates text for the men's weekend, including name and date range.
     *
     * Parses the string weekend name from the User record to look up the actual Weekend model,
     * then formats the name and date range as HTML (two lines, escaped).
     *
     * Returns null if no man is linked or if the weekend cannot be found.
     *
     * @return string|null  HTML string, e.g. "Any Tres Dias Men's #47<br>\nApril 14-17, 2023"
     */
    public function getWeekendDatesTextMAttribute()
    {
        $w = $text = null;

        if (! empty($this->attributes['m_user_id'])) {
            $w = $this->man->weekend;
        }

        if ($w) {
            $number = substr($w, strpos($w, '#')+1);
            $weekend = Weekend::NumberAndGender($number, 'M')->first();
            $text = e($weekend->long_name_with_number) . "<br>\n" . e($weekend->short_date_range);
        }

        return $text;
    }

    /**
     * Formatted weekend dates text for the women's weekend, including name and date range.
     *
     * Same logic as getWeekendDatesTextMAttribute() but for the woman.
     *
     * @return string|null
     */
    public function getWeekendDatesTextWAttribute()
    {
        $w = $text = null;

        if (! empty($this->attributes['w_user_id'])) {
            $w = $this->woman->weekend;
        }

        if ($w) {
            $number = substr($w, strpos($w, '#')+1);
            $weekend = Weekend::NumberAndGender($number, 'W')->first();
            $text = e($weekend->long_name_with_number) . "<br>\n" . e($weekend->short_date_range);
        }

        return $text;
    }

    // -------------------------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------------------------

    /** @param Builder $query */
    public function scopeMarried(Builder $query)
    {
        return $query->where('married', 1);
    }

    /** Candidates whose sponsor has confirmed their details. */
    public function scopeSponsorConfirmed(Builder $query)
    {
        return $query->where('sponsor_confirmed_details', 1);
    }

    /** Candidates marked as ready for the invitation mailing. */
    public function scopeReadyToMail(Builder $query)
    {
        return $query->where('ready_to_mail', 1);
    }

    /** Candidates whose invitation has been mailed. */
    public function scopeInvitationMailed(Builder $query)
    {
        return $query->where('invitation_mailed', 1);
    }

    /** Candidates whose invitation has NOT yet been mailed. */
    public function scopeInvitationNotMailed(Builder $query)
    {
        return $query->where('invitation_mailed', '!=', 1);
    }

    /**
     * Filter by a specific User's ID (matches either man or woman).
     *
     * @param Builder $query
     * @param int $id
     */
    public function scopeByMemberId(Builder $query, $id)
    {
        return $query->where('m_user_id', $id)->orWhere('w_user_id', $id);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The man in this candidate couple.
     * May be null for a single (non-coupled) female candidate.
     *
     */
    public function man(): BelongsTo
    {
        return $this->belongsTo(User::class, 'm_user_id');
    }

    /**
     * The woman in this candidate couple.
     * May be null for a single (non-coupled) male candidate.
     *
     */
    public function woman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'w_user_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('pre-weekend')
            ->logAll()
            ->logOnlyDirty();
    }
}
