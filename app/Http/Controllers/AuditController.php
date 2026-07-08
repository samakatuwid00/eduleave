<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\User;
use App\Services\AuditService;
use App\Services\ReportExcelService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\HeaderUtils;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);

        return view('admin.audit', [
            'events' => $this->query($filters)->paginate(50)->withQueryString(),
            'filters' => $filters,
            'actions' => AuditEvent::query()->distinct()->orderBy('action')->pluck('action'),
            'targetTypes' => AuditEvent::query()->distinct()->orderBy('target_type')->pluck('target_type'),
            'admins' => User::query()->where('usertype', 'admin')->orderBy('name')->get(),
        ]);
    }

    public function export(
        Request $request,
        ReportExcelService $excel,
        AuditService $audit,
    ): Response {
        $filters = $this->validatedFilters($request);
        $events = $this->query($filters)->limit(5000)->get();
        $report = [
            'label' => 'Audit Log',
            'definition' => 'Append-only administrative events with redacted before and after values.',
            'generated_at' => now(config('automation.timezone', 'Asia/Manila')),
            'row_count' => $events->count(),
            'excluded_count' => 0,
            'truncated_count' => 0,
            'filters_applied' => collect($filters)->filter()->all(),
            'totals' => ['Events exported' => $events->count()],
            'columns' => [
                ['key' => 'created_at', 'label' => 'Timestamp', 'type' => 'date'],
                ['key' => 'actor', 'label' => 'Actor', 'type' => 'text'],
                ['key' => 'action', 'label' => 'Action', 'type' => 'text'],
                ['key' => 'target_type', 'label' => 'Target Type', 'type' => 'text'],
                ['key' => 'target_id', 'label' => 'Target ID', 'type' => 'text'],
                ['key' => 'employee_number', 'label' => 'Employee Number', 'type' => 'text'],
                ['key' => 'reason', 'label' => 'Reason', 'type' => 'text'],
                ['key' => 'before', 'label' => 'Previous Values', 'type' => 'text'],
                ['key' => 'after', 'label' => 'New Values', 'type' => 'text'],
                ['key' => 'correlation_id', 'label' => 'Correlation ID', 'type' => 'text'],
            ],
            'rows' => $events->map(fn (AuditEvent $event) => [
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                'actor' => $event->actor?->name ?? $event->actor_label ?? 'System',
                'action' => $event->action,
                'target_type' => $event->target_type,
                'target_id' => $event->target_id,
                'employee_number' => $event->employee_number,
                'reason' => $event->reason,
                'before' => json_encode($event->previous_values, JSON_UNESCAPED_UNICODE),
                'after' => json_encode($event->new_values, JSON_UNESCAPED_UNICODE),
                'correlation_id' => $event->correlation_id,
            ]),
        ];
        $filename = 'audit-log-'.now()->format('Y-m-d-His').'.xlsx';
        $content = $excel->generate($report);
        $audit->record(
            'audit.exported',
            'audit_log',
            null,
            'Audit log export',
            after: ['row_count' => $events->count(), 'format' => 'xlsx'],
            metadata: ['filters' => $report['filters_applied'], 'file_name' => $filename],
        );

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $filename),
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    public function updateRole(Request $request, User $user, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);
        abort_unless($user->isAdmin(), 404);
        $validated = $request->validate([
            'admin_role' => ['required', Rule::in([
                User::ROLE_SUPER_ADMIN,
                User::ROLE_RECORDS_ADMIN,
                User::ROLE_AUDITOR,
            ])],
            'change_reason' => ['required', 'string', 'max:500'],
        ]);
        $currentRole = $user->effectiveAdminRole();

        if ($currentRole === User::ROLE_SUPER_ADMIN && $validated['admin_role'] !== User::ROLE_SUPER_ADMIN) {
            $superAdmins = User::query()->where('usertype', 'admin')->where(function ($query) {
                $query->where('admin_role', User::ROLE_SUPER_ADMIN)->orWhereNull('admin_role');
            })->count();

            if ($superAdmins <= 1) {
                throw ValidationException::withMessages(['admin_role' => 'The final full administrator cannot be demoted.']);
            }
        }

        DB::transaction(function () use ($user, $validated, $currentRole, $audit) {
            $user->forceFill(['admin_role' => $validated['admin_role']])->save();
            $audit->record(
                'admin.role_updated',
                'user',
                $user->getKey(),
                $user->name,
                ['admin_role' => $currentRole],
                ['admin_role' => $validated['admin_role']],
                $validated['change_reason'],
            );
        });

        return back()->with('success', 'Administrator role updated.');
    }

    public function hold(Request $request, AuditEvent $event, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);
        $validated = $request->validate([
            'is_held' => ['required', 'boolean'],
            'change_reason' => ['required', 'string', 'max:500'],
        ]);
        $held = (bool) $validated['is_held'];

        DB::transaction(function () use ($event, $held, $validated, $audit) {
            DB::table('audit_events')->where('id', $event->getKey())->update(['is_held' => $held]);
            $audit->record(
                'audit.hold_updated',
                'AuditEvent',
                $event->getKey(),
                $event->action,
                ['is_held' => $event->is_held],
                ['is_held' => $held],
                $validated['change_reason'],
            );
        });

        return back()->with('success', $held ? 'Audit event placed on hold.' : 'Audit hold removed.');
    }

    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'actor' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:80'],
            'target_type' => ['nullable', 'string', 'max:100'],
            'target_id' => ['nullable', 'string', 'max:100'],
            'employee_number' => ['nullable', 'string', 'max:255'],
            'correlation_id' => ['nullable', 'string', 'max:64'],
        ]);

        return [
            'from' => $validated['from'] ?? now()->subDays(30)->toDateString(),
            'to' => $validated['to'] ?? now()->toDateString(),
            'actor' => $validated['actor'] ?? null,
            'action' => $validated['action'] ?? null,
            'target_type' => $validated['target_type'] ?? null,
            'target_id' => $validated['target_id'] ?? null,
            'employee_number' => $validated['employee_number'] ?? null,
            'correlation_id' => $validated['correlation_id'] ?? null,
        ];
    }

    private function query(array $filters): Builder
    {
        return AuditEvent::query()
            ->with('actor')
            ->whereBetween('created_at', [$filters['from'].' 00:00:00', $filters['to'].' 23:59:59'])
            ->when($filters['actor'], fn (Builder $query, $actor) => $query->where('actor_user_id', $actor))
            ->when($filters['action'], fn (Builder $query, $action) => $query->where('action', $action))
            ->when($filters['target_type'], fn (Builder $query, $type) => $query->where('target_type', $type))
            ->when($filters['target_id'], fn (Builder $query, $id) => $query->where('target_id', $id))
            ->when($filters['employee_number'], fn (Builder $query, $number) => $query->where('employee_number', $number))
            ->when($filters['correlation_id'], fn (Builder $query, $id) => $query->where('correlation_id', $id))
            ->latest();
    }
}
