<?php

return [
    'audit_retention_days' => (int) env('AUDIT_RETENTION_DAYS', 2555),
    'automation_retention_days' => (int) env('AUTOMATION_RETENTION_DAYS', 365),
];
