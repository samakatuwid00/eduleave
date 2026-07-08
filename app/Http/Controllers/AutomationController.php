<?php

namespace App\Http\Controllers;

use App\Models\AutomationRun;
use App\Models\AutomationSetting;
use App\Services\AuditService;
use App\Services\AutomationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AutomationController extends Controller
{
    public function index(): View
    {
        return view('admin.automation', [
            'settings' => AutomationSetting::current(),
            'runs' => AutomationRun::query()->latest()->limit(100)->get(),
            'failedJobs' => DB::table('failed_jobs')->latest('failed_at')->limit(20)->get(),
        ]);
    }

    public function update(Request $request, AuditService $audit): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_emails' => ['nullable', 'string', 'max:4000'],
            'change_reason' => ['required', 'string', 'max:500'],
        ]);
        $emails = collect(preg_split('/[\s,;]+/', $validated['recipient_emails'] ?? '', -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($email) => strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();
        Validator::make(['emails' => $emails], [
            'emails.*' => ['email:rfc'],
        ])->validate();

        $settings = AutomationSetting::current();
        DB::transaction(function () use ($settings, $request, $emails, $validated, $audit) {
            $before = $settings->getAttributes();
            $settings->update([
                'version' => $settings->version + 1,
                'automation_enabled' => $request->boolean('automation_enabled'),
                'daily_digest_enabled' => $request->boolean('daily_digest_enabled'),
                'weekly_summary_enabled' => $request->boolean('weekly_summary_enabled'),
                'employee_notifications_enabled' => $request->boolean('employee_notifications_enabled'),
                'recipient_emails' => $emails === [] ? null : $emails,
                'updated_by' => $request->user()->getKey(),
            ]);
            $diff = $audit->changedValues($before, $settings->fresh()->getAttributes());
            $audit->record(
                'automation.settings_updated',
                'AutomationSetting',
                $settings->getKey(),
                'Automation settings',
                $diff['before'],
                $diff['after'],
                $validated['change_reason'],
            );
        });

        return back()->with('success', 'Automation settings updated.');
    }

    public function run(Request $request, AutomationService $automation): RedirectResponse
    {
        $validated = $request->validate([
            'rule' => ['required', Rule::in([
                AutomationService::ACTION_CENTER_EVALUATION,
                AutomationService::DAILY_ADMIN_DIGEST,
                AutomationService::WEEKLY_ADMIN_SUMMARY,
            ])],
        ]);
        $run = $automation->run($validated['rule']);

        return back()->with(
            $run->status === 'failed' ? 'error' : 'success',
            "Automation run {$run->status}.",
        );
    }

    public function retry(
        Request $request,
        AutomationRun $run,
        AutomationService $automation,
        AuditService $audit,
    ): RedirectResponse {
        abort_unless($run->status === 'failed', 404);
        $reason = $request->validate(['audit_reason' => ['required', 'string', 'max:500']])['audit_reason'];
        $before = ['status' => $run->status, 'attempt' => $run->attempt];
        $run = $automation->retry($run);
        $audit->record(
            'automation.run_retried',
            'AutomationRun',
            $run->getKey(),
            $run->rule_code,
            $before,
            ['status' => $run->status, 'attempt' => $run->attempt],
            $reason,
        );

        return back()->with(
            $run->status === 'failed' ? 'error' : 'success',
            "Automation retry {$run->status}.",
        );
    }
}
