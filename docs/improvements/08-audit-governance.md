# 08 — Audit and Governance

## 1. Objective and administrative benefit

Make sensitive administrative changes attributable and reviewable, introduce reasons for destructive decisions, and prepare the single-admin-flag model for safer role separation.

## 2. Current project fit and reusable components

Approval/rejection, card CRUD, and imports already pass through identifiable controllers and authenticated admins, which are reliable audit boundaries. Existing `Admin` middleware and `User::isAdmin()` remain the initial gate. Transactional status changes provide a safe place to write audit events atomically with the business mutation.

## 3. Functional requirements

Audit actions include login-relevant admin events where available, user approve/reject, profile correction, leave-card create/update/delete, import preview/commit/rollback, alert dismissal/resolution, report generation/download, automation setting change, and manual retry.

Each event stores actor, action code, subject type/id, timestamp, request/correlation ID, source, reason, before/after JSON, and metadata. Update events store changed fields only. Sensitive mutations require a non-empty reason: rejection, leave-card deletion, import rollback, manual balance correction, alert dismissal, and automation-setting change.

The audit page filters by date, actor, action, subject type/ID, employee number, and correlation ID; provides a redacted before/after diff; paginates; and allows an authorized audit export. Audit events are append-only through application code.

## 4. Data and interface changes

Add `audit_events` with indexed actor/action/subject/time/correlation fields and JSON payloads. Implement an audit service invoked inside the same database transaction as the mutation. For deletes, record the necessary snapshot before removal. Define a redaction allow/deny policy that excludes password hashes, remember tokens, reset/verification tokens, raw uploaded files, and secrets.

Introduce permissions incrementally: `manage_users`, `manage_leave_cards`, `manage_imports`, `view_analytics`, `export_reports`, `manage_automation`, and `view_audit`. Initially seed current admins with all permissions and keep existing middleware as a coarse outer gate; add policy checks per capability. Avoid replacing authentication or current admin routing in one release.

Retention default: audit metadata and sensitive mutation history seven years pending organizational policy confirmation; report artifacts 30 days; automation runs one year. Retention jobs must never delete records under an active investigation/hold.

## 5. Workstream task delegation

**Backend/Data:** schema, audit/redaction service, transactional integration, policies/permissions, retention command, and immutable access pattern.

**Admin UI:** required-reason dialogs/forms, audit search/diff, permission-aware navigation/actions, settings screens.

**Automation:** retention schedule and audit coverage for runs/retries/settings.

**QA:** atomicity, redaction, permissions, reason validation, filters, retention/hold, and tamper-resistance tests.

## 6. Dependencies and execution order

1. Audit schema/service and redaction policy.
2. Instrument approval/rejection and card CRUD.
3. Require reasons for sensitive actions.
4. Instrument imports, alerts, reports, and automation as delivered.
5. Add searchable audit UI.
6. Introduce seeded permissions/policies, then retention automation.

## 7. Edge cases and failure handling

- If required audit persistence fails, the sensitive business transaction fails and rolls back.
- Background jobs use a system actor plus initiating admin ID in metadata.
- Deleted actors/subjects retain stable IDs and safe display snapshots.
- Large before/after payloads are field-limited; raw workbooks are never embedded.
- Concurrent updates record the values actually persisted in each transaction.
- Retention dry-run lists counts/date bounds before deletion and honors legal holds.

## 8. Security and authorization

Only `view_audit` may read/export events; only `manage_automation` may edit policies. Audit routes are admin-authenticated, paginated, and rate-conscious. Event payloads are escaped in Blade, exports are private, and application models expose no update/delete path for ordinary audit operations. Database-level immutability controls may be added where operationally supported.

## 9. Test scenarios

- Before/after/reason for every sensitive action.
- Business mutation and audit event commit or roll back together.
- Password/token/raw-file redaction.
- Each permission allowed/denied while existing admin behavior remains compatible.
- Background system actor attribution.
- Search/filter/export authorization and pagination.
- Retention dry-run, execution, and hold exclusion.

## 10. Acceptance criteria

- All listed sensitive mutations create one attributable event in the same transaction.
- Required reasons are validated server-side.
- Secrets and unnecessary personal data never enter audit payloads.
- Existing admins retain access after permission migration.
- Audit search can trace an employee/import/report workflow by correlation ID.

## 11. Rollout and rollback notes

Deploy audit writes before the audit UI and monitor failure rate. Add reason requirements one workflow at a time. Seed permissions before enforcing policies and provide a recovery command for assigning the full administrator role. Rollback policy enforcement without deleting audit records or reverting captured reasons.

## 12. Future enhancements excluded

- External SIEM forwarding.
- Cryptographic ledger chaining.
- Delegated departmental scopes until organizational boundaries are defined.
