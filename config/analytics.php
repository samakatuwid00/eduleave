<?php

return [
    'low_balance_threshold' => (float) env('ANALYTICS_LOW_BALANCE_THRESHOLD', 5),
    'action_center' => [
        'pending_high_days' => (int) env('ACTION_CENTER_PENDING_HIGH_DAYS', 3),
        'pending_critical_days' => (int) env('ACTION_CENTER_PENDING_CRITICAL_DAYS', 7),
        'missing_card_grace_days' => (int) env('ACTION_CENTER_MISSING_CARD_GRACE_DAYS', 7),
    ],
];
