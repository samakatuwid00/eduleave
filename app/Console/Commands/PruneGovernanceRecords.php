<?php

namespace App\Console\Commands;

use App\Models\AuditEvent;
use App\Models\AutomationRun;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneGovernanceRecords extends Command
{
    protected $signature = 'governance:prune {--dry-run}';

    protected $description = 'Prune expired audit and automation history while honoring audit holds';

    public function handle(AuditService $audit): int
    {
        $auditCutoff = now()->subDays(config('governance.audit_retention_days', 2555));
        $automationCutoff = now()->subDays(config('governance.automation_retention_days', 365));
        $auditQuery = AuditEvent::query()->where('is_held', false)->where('created_at', '<', $auditCutoff);
        $automationQuery = AutomationRun::query()->where('created_at', '<', $automationCutoff);
        $auditCount = (clone $auditQuery)->count();
        $automationCount = (clone $automationQuery)->count();

        $this->info("Audit events eligible: {$auditCount}; automation runs eligible: {$automationCount}.");

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($auditQuery, $automationQuery, $audit, $auditCount, $automationCount) {
            $auditQuery->toBase()->delete();
            $automationQuery->delete();
            $audit->record(
                'governance.retention_pruned',
                'governance',
                null,
                'Retention cleanup',
                metadata: [
                    'audit_events_deleted' => $auditCount,
                    'automation_runs_deleted' => $automationCount,
                ],
                source: 'system',
            );
        });

        return self::SUCCESS;
    }
}
