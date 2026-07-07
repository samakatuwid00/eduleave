<?php

namespace App\Data;

use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;

readonly class LeaveCardAnalyticsRow
{
    public function __construct(
        public int $employeeProfileId,
        public string $personnelType,
        public ?string $periodStart,
        public ?string $periodEnd,
        public ?string $leaveTypeCode,
        public ?float $vacationEarned,
        public ?float $vacationPaid,
        public ?float $vacationBalance,
        public ?float $sickEarned,
        public ?float $sickPaid,
        public ?float $sickBalance,
        public ?float $totalUnpaid,
        public string $parseState,
    ) {}

    public static function fromTeaching(TeachingLeaveCard $card): self
    {
        $card->loadMissing('leaveType');

        return new self(
            $card->employee_profile_id,
            PersonnelType::CODE_TEACHING,
            $card->period_start?->toDateString(),
            $card->period_end?->toDateString(),
            $card->leaveType?->code,
            null,
            null,
            null,
            null,
            null,
            null,
            $card->days_without_pay === null ? null : (float) $card->days_without_pay,
            $card->parse_state,
        );
    }

    public static function fromNonTeaching(NonTeachingLeaveCard $card): self
    {
        $card->loadMissing('leaveType');
        $unpaid = array_filter([
            $card->vacation_leave_without_pay,
            $card->sick_leave_without_pay_value,
        ], fn ($value) => $value !== null);

        return new self(
            $card->employee_profile_id,
            PersonnelType::CODE_NON_TEACHING,
            $card->period_start?->toDateString(),
            $card->period_end?->toDateString(),
            $card->leaveType?->code,
            $card->vacation_leave_earned === null ? null : (float) $card->vacation_leave_earned,
            $card->vacation_leave_with_pay_value === null ? null : (float) $card->vacation_leave_with_pay_value,
            $card->vacation_leave_balance_value === null ? null : (float) $card->vacation_leave_balance_value,
            $card->sick_leave_earned === null ? null : (float) $card->sick_leave_earned,
            $card->sick_leave_with_pay === null ? null : (float) $card->sick_leave_with_pay,
            $card->sick_leave_balance_value === null ? null : (float) $card->sick_leave_balance_value,
            $unpaid === [] ? null : array_sum(array_map('floatval', $unpaid)),
            $card->parse_state,
        );
    }
}
