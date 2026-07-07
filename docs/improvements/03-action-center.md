# 03 — Action Center

## 1. Objective and administrative benefit

Provide one prioritized, explainable queue for administrative exceptions. This reduces repeated visits to separate status and leave-card pages and makes unresolved work measurable.

## 2. Current project fit and reusable components

Existing approve/reject endpoints, user detail modal, leave-card routes, import validation, mail queue, and user-status lists already supply most resolution destinations. The Action Center adds detection, evidence, priority, and navigation rather than duplicating these operations.

## 3. Functional requirements

Alert categories and defaults:

- Verified user pending approval: medium at 1 day, high at 3 days, critical at 7 days.
- Employee missing profile/personnel type: high immediately after approval.
- Employee missing leave card: medium after approval plus a configurable grace period of 7 days.
- Low balance: medium at `<= 5`, high at `<= 0`; unavailable/unparseable is a separate data-quality alert.
- Incomplete leave row: required canonical date/type/quantity missing after normalization.
- Potential duplicate: same employee, personnel table, normalized period/date, leave type/particular, and principal quantity.
- Failed import or terminal notification job: high until retried successfully or dismissed with reason.

The page supports category, priority, age, personnel type, status, owner, and open/resolved filters; paginated results; evidence explaining why each item exists; direct inspect/approve/reject/retry/fix links; bulk assignment/dismissal only where it cannot change employee records; and resolution history.

## 4. Data and interface changes

Use deterministic alert evaluators with a common result: rule code, subject type/id, severity, detected/last-seen timestamps, evidence JSON, action URL, and fingerprint. Persist alert state only when ownership, dismissal, or resolution history is needed; use fingerprint uniqueness to upsert recurring evaluations.

Resolution modes are `automatic` (condition no longer true), `action_completed`, or `dismissed` with required reason. A later recurrence creates/reopens an alert according to the rule's documented policy. Approval/rejection still calls the existing transactional endpoint; the center never changes status independently.

## 5. Workstream task delegation

**Backend/Data:** alert contract/table, evaluators, severity rules, filtering, pagination, ownership/resolution, and existing-action links.

**Admin UI:** sidebar badge, queue page, evidence panel, filters, safe actions, and resolution feedback.

**Automation:** scheduled evaluation and failed-job ingestion with idempotent fingerprints.

**QA:** clock-controlled aging tests, permission tests, rule boundaries, deduplication/reopen behavior, and action regression.

## 6. Dependencies and execution order

1. Canonical data and audit foundation.
2. Alert contract plus approval/profile/missing-card rules.
3. Read-only queue UI and counts.
4. Safe resolution/actions.
5. Import, balance, duplicate, and job-failure rules.
6. Scheduled evaluation and sidebar badge.

## 7. Edge cases and failure handling

- Evaluation failure for one rule records a system alert/log and does not erase existing alerts.
- Concurrent evaluators rely on fingerprint uniqueness.
- A stale action reloads the subject and returns “already resolved/changed” without repeating mail.
- Deleted subjects retain audit/resolution history but are hidden from the open queue.
- Dismissal never modifies the underlying employee or leave record.

## 8. Security and authorization

All reads/actions require admin access. Assignment and dismissal may later require a supervisor permission. Evidence stores identifiers and concise facts, not passwords, tokens, or entire uploaded rows. Sensitive actions require CSRF-protected non-GET requests.

## 9. Test scenarios

- Aging at just below/on/above each threshold.
- Verified versus unverified pending accounts.
- Missing profile/type/card and grace period.
- Low, zero, negative, null, and unparseable balances.
- Duplicate fingerprints and concurrent evaluations.
- Automatic resolution and recurrence.
- Approval/rejection queues exactly one email using existing behavior.

## 10. Acceptance criteria

- Each alert explains its rule, evidence, age, priority, and next action.
- Repeated evaluation produces no duplicate open alerts.
- Queue counts reconcile with source queries.
- Common exceptions can be resolved from the center or one linked page.
- Resolution and dismissal are audited.

## 11. Rollout and rollback notes

Start with read-only approval/profile/missing-card alerts and no notifications. Validate counts for one week, enable actions, then add remaining rules and scheduled evaluation. Disable rules individually through configuration if noisy; retain alert history.

## 12. Future enhancements excluded

- Machine-learned prioritization.
- Automatic employee-record corrections.
- External ticket-system assignment.

