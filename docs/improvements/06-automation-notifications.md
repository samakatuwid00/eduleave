# 06 — Automation and Notifications

## 1. Objective and administrative benefit

Automate recurring monitoring and reporting without sending duplicate messages or hiding failures. Automation should surface work and assist admins, not silently change leave balances or approval decisions.

## 2. Current project fit and reusable components

The application already queues verification, reset, approval, and rejection mail; status transitions are transactionally guarded so duplicate requests do not queue duplicate mail. `routes/console.php` already schedules database mail-queue draining with overlap protection. New automation should follow these idempotent and bounded-retry patterns.

## 3. Functional requirements

- Daily evaluation of approval aging, missing profiles/cards, low balances, and missing expected-period updates.
- Daily admin digest by default; optional immediate critical alerts.
- Weekly summary of open/resolved Action Center items.
- Terminal mail/report job failures surfaced as actionable items.
- Employee notification after a material leave-card create/update/import, grouped into a digest where bulk imports would otherwise spam.
- Monthly report generation on a configured day/time with recipient list and retained artifact metadata.
- Admin settings for enabled rules, thresholds, digest cadence, recipients, and report schedule.
- Automation never auto-approves, auto-rejects, or alters balances.

## 4. Data and interface changes

Add versioned automation settings with validated defaults and an `automation_runs` ledger containing rule, window, status, counts, started/finished timestamps, and error summary. Use idempotency key `rule_code + subject/audience + evaluation_window + payload_version` for notifications and reports. Enforce uniqueness in storage, not only in job code.

Create small commands that dispatch jobs; jobs consume Action Center and report services rather than reimplementing queries. Suggested schedules in Asia/Manila: alert evaluation 01:00 daily, digest 08:00 daily, weekly summary Monday 08:00, monthly reports first day 02:00. Make schedules configurable and use `withoutOverlapping` plus one-server protection where supported.

## 5. Workstream task delegation

**Automation:** commands/jobs, scheduling, idempotency ledger, retries/backoff, run visibility, recipient routing, and cleanup.

**Backend/Data:** settings validation, Action Center/report contracts, material-change event definition, and failure adapter.

**Admin UI:** settings page, run history, failed-run detail, retry control, and clear “last successful run” state.

**QA:** frozen-time schedule tests, duplicate dispatch, retry/terminal failure, digest grouping, disabled rule, and recipient tests.

## 6. Dependencies and execution order

1. Action Center rules and audit storage.
2. Run/idempotency ledger and read-only dry-run commands.
3. Admin-only digests.
4. Failure visibility/retry.
5. Monthly reports.
6. Employee material-change notifications after noise review.

## 7. Edge cases and failure handling

- No recipients: fail configuration validation and show an Action Center item; do not discard silently.
- Repeated scheduler/worker execution is safe through unique idempotency keys.
- Partial digest send records recipient-level outcomes and retries only failed deliveries.
- A retry reuses the logical idempotency key while recording a new attempt.
- Queue outage leaves jobs pending and exposes queue age/last successful run.
- Bulk imports generate one employee summary after commit, never per row.

## 8. Security and authorization

Only privileged admins edit automation settings or retry terminal failures. Validate recipient domains/addresses, redact message bodies and tokens from logs, and use signed/authenticated application links. Audit configuration and manual retry changes.

## 9. Test scenarios

- Each schedule in application timezone and daylight-independent Philippine time.
- Overlap and duplicate dispatch attempts.
- Disabled/enabled rules and threshold changes.
- Bounded retries/backoff and terminal failure alert.
- Import digest grouping and transaction-after-commit dispatch.
- No-recipient/misconfiguration and recovery.

## 10. Acceptance criteria

- Re-running any command for the same window sends no duplicate logical notification/report.
- Admins can see last success, current failure, counts, and next action.
- Thresholds and schedules change through validated configuration, not controller edits.
- Jobs have bounded retry/backoff and structured, privacy-safe logs.
- Automation performs no autonomous employee decision or balance mutation.

## 11. Rollout and rollback notes

Run evaluators in dry-run/log-only mode, enable admin digests for a pilot recipient, then widen recipients. Enable employee notifications last. Every job/rule has a kill switch; disabling schedules leaves interactive features intact and preserves run history.

## 12. Future enhancements excluded

- SMS/push channels.
- AI-generated policy advice.
- Automatic approval or leave-balance correction.

