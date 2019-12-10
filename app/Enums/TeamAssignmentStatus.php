<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Enum TeamAssignmentStatus
 *
 * Blade, to display description of a given status:
 * <p>{{ \App\Enums\TeamAssignmentStatus::getDescription($assignment->confirmed) }}</p>
 *
 * Validation:
 *     use App\Enums\TeamAssignmentStatus;
 *     use BenSampo\Enum\Rules\EnumValue;
 *     $this->validate($request, [
 *          'confirmed' => ['required', new EnumValue(TeamAssignmentStatus::class)],
 *      ]);
 *
 * See documentation at: https://sampo.co.uk/blog/using-enums-in-laravel and repo at https://github.com/BenSampo/laravel-enum#methods
 */

final class TeamAssignmentStatus extends Enum
{
    public const Pending = 0;
    public const CalledMessage = 1;
    public const CalledSpoke = 2;
    public const NotServing = 3;
    public const Accepted = 4;
    public const Dropped = 5;
    public const RefundRequested = 6;
    public const RefundPaid = 7;
    public const DonatedFees = 8;

    /**
     * Get the description for an enum value
     *
     * @param  int  $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::Pending:
                return 'Pending/Praying';
            break;
            case self::CalledMessage:
                return 'Called-Left Msg / Emailed / No Reply';
            break;
            case self::CalledSpoke:
                return 'Called-Spoke / Replied, Waiting';
            break;
            case self::NotServing:
                return 'Not Serving this Weekend';
            break;
            case self::Accepted:
                return 'Accepted-Confirmed';
            break;
            case self::Dropped:
                return 'Dropped';
            break;
            case self::RefundRequested:
                return 'Refund Requested';
            break;
            case self::RefundPaid:
                return 'Refund Paid';
            break;
            case self::DonatedFees:
                return 'Donated Fees';
            break;
            default:
                return self::getKey($value);
        }
    }
}
