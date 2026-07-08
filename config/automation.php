<?php

return [
    'timezone' => env('AUTOMATION_TIMEZONE', 'Asia/Manila'),
    'evaluation_time' => env('AUTOMATION_EVALUATION_TIME', '01:00'),
    'daily_digest_time' => env('AUTOMATION_DAILY_DIGEST_TIME', '08:00'),
    'weekly_summary_time' => env('AUTOMATION_WEEKLY_SUMMARY_TIME', '08:00'),
    'payload_version' => 1,
];
