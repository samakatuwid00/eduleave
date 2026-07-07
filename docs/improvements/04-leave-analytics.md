# 04 — Leave Analytics

## 1. Objective and administrative benefit

Give administrators defensible views of leave usage, balances, and personnel patterns for operational planning while keeping individual records available for verification.

## 2. Current project fit and reusable components

Separate teaching/non-teaching records reflect real differences and should remain authoritative. The common typed DTO from the data foundation supplies comparable dimensions while preserving unavailable measures as `null`. Existing admin leave-card pages are the drill-down destination.

## 3. Functional requirements

- Overview KPIs: employees represented, leave quantity in range, unpaid quantity, low-balance count, and excluded-row count.
- Monthly stacked usage chart by vacation/sick/other/unclassified.
- Paid versus unpaid stacked chart with explicit unit.
- Ranked leave-type/category chart using controlled types, with “unclassified” visible.
- Balance distribution: healthy `> 5`, low `> 0 and <= 5`, zero, negative, unavailable; thresholds configurable.
- Teaching/non-teaching comparison only for genuinely comparable measures; otherwise render separate panels.
- Drill-down table with employee number/name, personnel type, period, type, quantities, balance, parse state, and leave-card link.
- Shared filters and CSV/XLSX export handoff to the report module.

## 4. Data and interface changes

Extend the shared analytics service with aggregate and paginated detail methods using the common filter object. Define measure metadata (`days`, `service credits`, `employees`, `records`) and never stack unlike units. Cache only aggregate results under a normalized filter/version key; detail pages remain directly queried and paginated.

Time attribution uses canonical `period_start` by default. Multi-month date ranges may be allocated only by an explicit, tested rule; v1 assigns the record to its start month and labels that rule. “Current balance” uses the latest valid record per employee and balance family at the filter end date.

## 5. Workstream task delegation

**Backend/Data:** definitions, aggregation/detail queries, latest-balance selection, excluded counts, cache keys/invalidation, and query plans.

**Admin UI:** analytics navigation, filters, charts, definitions/tooltips, table alternative, drill-down, and export links.

**QA:** hand-calculated fixtures, unit integrity, current-balance ordering, filter/export reconciliation, performance, and accessibility.

## 6. Dependencies and execution order

1. Canonical values, leave types, and shared filters.
2. Metric dictionary reviewed by administrators.
3. Detail query first, then aggregates derived from identical predicates.
4. Tables and definitions, then charts.
5. Caching and export integration.

## 7. Edge cases and failure handling

- Unknown dates/types/quantities appear in excluded/unclassified totals.
- Missing balances are unavailable, never zero.
- Negative balances remain visible.
- Do not sum running balance snapshots; use the latest valid snapshot per employee.
- Avoid direct paid/unpaid comparisons when teaching service credits and non-teaching days are not equivalent.
- Partial current periods are labeled.

## 8. Security and authorization

All analytics are admin-only. Aggregate charts avoid unnecessary personal data. Detail queries are paginated, authorized, and audited when exported. Filter input is allow-listed and parameterized through the query builder.

## 9. Test scenarios

- Known monthly/category totals for both card formats.
- Latest-balance tie and missing-date behavior.
- Every risk-band boundary.
- Unclassified/unparseable/excluded metadata.
- Identical filter results across KPI, chart, detail, and export.
- Empty, large-range, partial-month, and unauthorized cases.

## 10. Acceptance criteria

- Metric definitions and units are displayed and documented.
- Summary values reconcile to paginated details and exports.
- No running balances are summed as usage.
- Unknown data remains visible as a quality count.
- Representative filtered requests meet the agreed performance budget.

## 11. Rollout and rollback notes

Release tables and metric definitions internally before charts, validate against sample leave cards, then enable for all admins. Aggregate caching can be disabled independently. Rollback removes analytics routes/sidebar entries without affecting source records.

## 12. Future enhancements excluded

- Forecasting future leave demand.
- Attendance or payroll integration.
- Comparing personnel groups using non-equivalent units.

