# 01 — Data Foundation

## 1. Objective and administrative benefit

Create trustworthy, typed inputs for analytics without breaking current teaching/non-teaching leave-card screens or Excel workflows. Admins gain totals that can be reconciled; developers gain a stable contract for charts, alerts, and reports.

## 2. Current project fit and reusable components

`EmployeeProfile::leaveCardModelClass()` and `leaveCardQuery()` already route an employee to the correct table. Teaching quantities are decimals, while non-teaching `vacation_leave_with_pay`, `vacation_leave_balance`, `sick_leave_balance`, and `sick_leave_without_pay` remain strings. Periods, inclusive leave dates, leave categories, and application actions are also free text. Existing model relationships, personnel codes, transactional imports, and normalized-schema tests should remain the compatibility anchor.

## 3. Functional requirements

- Add canonical reporting period fields (`period_start`, `period_end`) to both leave-card tables.
- Add nullable typed numeric companions for every quantity currently stored as text; retain original text as the source value during transition.
- Introduce `leave_types` with stable code, display name, active flag, and optional personnel applicability.
- Add nullable `leave_type_id` to both card tables while retaining `nature_of_leave`/`particulars` for historical display.
- Standardize application outcomes through a controlled code (`pending`, `approved`, `rejected`, `cancelled`, `not_applicable`) while retaining the raw action text.
- Store parse state (`parsed`, `partial`, `unparseable`, `not_applicable`) and optional parse note so unknown data is excluded visibly instead of counted as zero.
- Add processing metadata to user decisions: `processed_by`, `processed_at`, and `decision_reason`; preserve `users.status` for middleware compatibility.
- Index user status/verification timestamps, profile personnel type/employee number, leave-card employee/period/type, and audit/import foreign keys after query review.

## 4. Data and interface changes

Use additive migrations first. Do not alter or drop a legacy column in the initial rollout. A dedicated normalizer service converts known formats such as plain decimals, `1 day`, `None`, and recognized month/year periods. Ambiguous ranges or comments remain raw with `partial` or `unparseable` state.

Expose a common read DTO for both tables:

```text
employee_profile_id, personnel_type, period_start, period_end,
leave_type_code, vacation_earned, vacation_paid, vacation_balance,
sick_earned, sick_paid, sick_balance, total_unpaid, parse_state
```

Unavailable concepts are `null`. The DTO may be built from separate queries or a database union, but callers must not know table-specific column names.

Backfill in chunks by primary key, record counts and parse outcomes, and produce a reconciliation artifact: total rows, parsed/partial/unparseable counts, and numeric sums where both old and new values can be compared. Imports dual-write raw and canonical fields after the backfill code is proven.

## 5. Workstream task delegation

**Backend/Data**

1. Inventory real distinct formats and publish parsing rules.
2. Add lookup/typed/metadata columns and indexes using reversible migrations.
3. Implement normalizer, common DTO/query adapter, and chunked backfill command with dry-run.
4. Update manual CRUD and Excel mapping to dual-write canonical values.
5. Add reconciliation output and cache-invalidation hooks.

**QA**

1. Build fixtures for clean, unit-bearing, empty, malformed, and ambiguous legacy values.
2. Verify dry-run makes no writes and live backfill is restartable.
3. Compare old display output before/after migration.
4. Verify both personnel types and import templates.

## 6. Dependencies and execution order

1. Format inventory and approved controlled vocabularies.
2. Additive schema and models.
3. Parser plus dry-run reconciliation.
4. Reviewed backfill.
5. Dual-write imports/CRUD.
6. Shared DTO/query interface.
7. Consumers may adopt canonical values; removal of old fields is outside this program.

## 7. Edge cases and failure handling

- Open-ended or multi-range dates remain partial and are excluded from date-bound totals with an explicit excluded count.
- Negative balances are retained and classified as risk, not corrected automatically.
- A missing profile/personnel type produces a data-quality alert; it must not guess a target table.
- Duplicate leave-type labels map only through reviewed aliases.
- Backfill failures log row ID and reason and continue; reruns skip successfully parsed rows unless forced.
- Imports fail transactionally when a value violates a strict required rule, but optional ambiguous fields may import as raw/unparseable with a preview warning.

## 8. Security and authorization

Only admin-authorized commands/routes may backfill or edit canonical classifications. Do not include names, emails, or full raw rows in routine logs. Record actor and source (`manual`, `import`, `backfill`) for changes.

## 9. Test scenarios

- Parser unit cases for all approved formats and malformed inputs.
- Migration up/down and restartable chunk backfill.
- Teaching/non-teaching DTO parity with unavailable fields as `null`.
- Existing card display, CRUD, template, and import regression tests.
- Index-backed filter query checks on representative data.
- Reconciliation proves no row disappears and no unknown becomes zero.

## 10. Acceptance criteria

- Every record has an explicit canonical parse state.
- Analytics uses only typed canonical values and reports excluded-row counts.
- Existing leave-card pages and workbooks remain usable.
- Backfill is dry-runnable, resumable, auditable, and reversible at schema level.
- Shared DTO contract is documented and covered for both personnel types.

## 11. Rollout and rollback notes

Deploy additive schema, then dual-read code, run dry-run/backfill, enable dual-write, and finally switch analytics consumers. Rollback consumers to raw display and stop dual-write; keep additive data in place to avoid destructive rollback. Never drop legacy columns during these phases.

## 12. Future enhancements excluded

- Merging both card tables into one table.
- Automatic policy interpretation from arbitrary remarks.
- Retroactively correcting ambiguous source records without administrator review.

