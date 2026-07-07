<?php

namespace App\Services;

use App\Models\LeaveType;
use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Str;

class LeaveCardNormalizer
{
    public const PARSED = 'parsed';

    public const PARTIAL = 'partial';

    public const UNPARSEABLE = 'unparseable';

    public const NOT_APPLICABLE = 'not_applicable';

    public function teaching(array $attributes): array
    {
        $period = $this->period($attributes['inclusive_period'] ?? null);
        $attributes['period_start'] = $period['start'];
        $attributes['period_end'] = $period['end'];
        $attributes['leave_type_id'] = $this->leaveTypeId($attributes['nature_of_leave'] ?? null);
        $attributes['parse_state'] = $period['state'];
        $attributes['parse_note'] = $period['note'];

        return $attributes;
    }

    public function nonTeaching(array $attributes): array
    {
        $period = $this->period($attributes['period'] ?? null);
        $quantities = [
            'vacation_leave_with_pay_value' => $this->quantity($attributes['vacation_leave_with_pay'] ?? null),
            'vacation_leave_balance_value' => $this->quantity($attributes['vacation_leave_balance'] ?? null),
            'sick_leave_balance_value' => $this->quantity($attributes['sick_leave_balance'] ?? null),
            'sick_leave_without_pay_value' => $this->quantity($attributes['sick_leave_without_pay'] ?? null),
        ];

        $attributes['period_start'] = $period['start'];
        $attributes['period_end'] = $period['end'];
        $attributes['leave_type_id'] = $this->leaveTypeId($attributes['particulars'] ?? null);

        foreach ($quantities as $column => $result) {
            $attributes[$column] = $result['value'];
        }

        $attributes['application_action_code'] = $this->applicationAction(
            $attributes['leave_application_action'] ?? null,
        );

        $results = [$period, ...array_values($quantities)];
        $attributes['parse_state'] = $this->combinedState($results);
        $notes = array_values(array_filter(array_column($results, 'note')));
        $attributes['parse_note'] = $notes === [] ? null : implode('; ', array_unique($notes));

        return $attributes;
    }

    /** @return array{start: ?string, end: ?string, state: string, note: ?string} */
    public function period(mixed $value): array
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return ['start' => null, 'end' => null, 'state' => self::NOT_APPLICABLE, 'note' => null];
        }

        foreach (['!Y-m-d', '!F Y', '!M Y', '!Y-m'] as $format) {
            try {
                $date = CarbonImmutable::createFromFormat($format, $value);
            } catch (InvalidFormatException) {
                continue;
            }
            $errors = CarbonImmutable::getLastErrors();

            if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                $isExactDate = $format === '!Y-m-d';

                return [
                    'start' => ($isExactDate ? $date : $date->startOfMonth())->toDateString(),
                    'end' => ($isExactDate ? $date : $date->endOfMonth())->toDateString(),
                    'state' => self::PARSED,
                    'note' => null,
                ];
            }
        }

        return [
            'start' => null,
            'end' => null,
            'state' => self::UNPARSEABLE,
            'note' => 'Reporting period could not be parsed.',
        ];
    }

    /** @return array{value: ?float, state: string, note: ?string} */
    public function quantity(mixed $value): array
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '' || preg_match('/^(?:n\/?a|not applicable|-)$/i', $value)) {
            return ['value' => null, 'state' => self::NOT_APPLICABLE, 'note' => null];
        }

        if (preg_match('/^(?:none|nil)$/i', $value)) {
            return ['value' => 0.0, 'state' => self::PARSED, 'note' => null];
        }

        if (preg_match('/^(-?\d+(?:\.\d+)?)\s*(?:days?|credits?)?$/i', $value, $matches)) {
            return ['value' => (float) $matches[1], 'state' => self::PARSED, 'note' => null];
        }

        return [
            'value' => null,
            'state' => self::UNPARSEABLE,
            'note' => "Quantity [{$value}] could not be parsed.",
        ];
    }

    private function leaveTypeId(mixed $value): ?int
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return null;
        }

        $normalized = Str::lower($value);

        return LeaveType::query()
            ->where('is_active', true)
            ->where(function ($query) use ($normalized) {
                $query->whereRaw('LOWER(code) = ?', [$normalized])
                    ->orWhereRaw('LOWER(name) = ?', [$normalized]);
            })
            ->value('id');
    }

    private function applicationAction(mixed $value): ?string
    {
        $value = Str::lower(trim((string) ($value ?? '')));

        foreach (['approved', 'rejected', 'pending', 'cancelled'] as $action) {
            if (str_contains($value, $action)) {
                return $action;
            }
        }

        return $value === '' ? 'not_applicable' : null;
    }

    private function combinedState(array $results): string
    {
        $states = array_column($results, 'state');

        if (in_array(self::UNPARSEABLE, $states, true)) {
            return self::UNPARSEABLE;
        }

        if (in_array(self::PARSED, $states, true)) {
            return in_array(self::NOT_APPLICABLE, $states, true) ? self::PARTIAL : self::PARSED;
        }

        return self::NOT_APPLICABLE;
    }
}
