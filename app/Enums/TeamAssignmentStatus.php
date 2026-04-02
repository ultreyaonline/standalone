<?php

namespace App\Enums;

// This enum package was used before PHP added native enum support.
use BenSampo\Enum\Enum;

/**
 * Enum TeamAssignmentStatus
 *
 * Tracks the lifecycle of a team member's confirmation for a weekend assignment.
 * Stored as an integer in weekend_assignments.confirmed.
 *
 * ## Status flow (typical)
 *
 *   Pending → CalledMessage → CalledSpoke → Accepted
 *                                         ↘ NotServing
 *   Accepted → Dropped → RefundRequested → RefundPaid
 *                      ↘ DonatedFees
 *
 * ## Key threshold: Accepted (4)
 * Most queries filter by `confirmed >= Accepted` or `confirmed = Accepted`.
 * Only Accepted assignments are counted in rosters, fee statistics, and team displays.
 *
 * ## Finance statuses (5–8)
 * Dropped, RefundRequested, RefundPaid, and DonatedFees track the financial outcome
 * when a confirmed team member can no longer serve. These are used by TeamFeePaymentsController.
 *
 * ## Usage in Blade
 *   {{ \App\Enums\TeamAssignmentStatus::getDescription($assignment->confirmed) }}
 *
 * ## Usage in form validation
 *   use App\Enums\TeamAssignmentStatus;
 *   use BenSampo\Enum\Rules\EnumValue;
 *   $this->validate($request, [
 *       'confirmed' => ['required', new EnumValue(TeamAssignmentStatus::class)],
 *   ]);
 *
 * See documentation at: https://sampo.co.uk/blog/using-enums-in-laravel and repo at https://github.com/BenSampo/laravel-enum#methods
 * @see https://github.com/BenSampo/laravel-enum
 * @see App\Models\WeekendAssignments
 * @see App\Http\Controllers\TeamAssignmentController
 */
final class TeamAssignmentStatus extends Enum
{
    /** Initial state; invitation has been identified but no contact made yet. */
    public const Pending = 0;

    /** Contacted by phone/email; left a message or sent email; awaiting response. */
    public const CalledMessage = 1;

    /** Spoke with the person; they are praying/considering. */
    public const CalledSpoke = 2;

    /** Definitively declined; will not serve on this weekend. */
    public const NotServing = 3;

    /**
     * Confirmed; the person has accepted and will serve.
     *
     * This is the key threshold — assignments with this status (or higher) are included
     * in rosters, team counts, and fee tracking.
     */
    public const Accepted = 4;

    /** "Dropped from serving on the team". Was Accepted but can no longer serve; fee refund or donation outcome pending. */
    public const Dropped = 5;

    /** Dropped and has requested a fee refund. */
    public const RefundRequested = 6;

    /** Refund has been paid out. */
    public const RefundPaid = 7;

    /** Dropped but chose to donate their fees rather than receive a refund. */
    public const DonatedFees = 8;

    /**
     * Return a human-readable description for a given status value.
     *
     * Used in the team assignment UI and status dropdowns.
     *
     * @param int $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::Pending:
                return 'Pending/Praying';
            case self::CalledMessage:
                return 'Called-Left Msg / Emailed / No Reply';
            case self::CalledSpoke:
                return 'Called-Spoke / Replied, Waiting';
            case self::NotServing:
                return 'Not Serving this Weekend';
            case self::Accepted:
                return 'Accepted-Confirmed';
            case self::Dropped:
                return 'Dropped';
            case self::RefundRequested:
                return 'Refund Requested';
            case self::RefundPaid:
                return 'Refund Paid';
            case self::DonatedFees:
                return 'Donated Fees';
            default:
                return self::getKey($value);
        }
    }
}
