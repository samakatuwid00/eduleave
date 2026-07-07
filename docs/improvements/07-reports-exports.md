# 07 — Reports and Exports

## 1. Objective and administrative benefit

Provide reproducible, filterable administrative reports whose totals match the dashboard and analytics pages. Excel is the first delivery format; print-ready PDF follows after data contracts stabilize.

## 2. Current project fit and reusable components

SimpleXLSXGen already generates workbook templates, so the first export format needs no new spreadsheet dependency. Shared analytics filters/queries prevent the common failure where dashboards and reports implement different totals. Existing employee/card routes provide source-record links in interactive views.

## 3. Functional requirements

Reports:

- Monthly leave summary by personnel type and leave type.
- Individual employee leave ledger.
- Low/zero/negative/unavailable balance list.
- Leave-without-pay detail.
- Teaching/non-teaching comparison with unit-safe separate measures.
- Approval-aging report.
- Employees missing profiles/types/cards.
- Import and adjustment history.

Interactive report pages show selected filters, generated time, definitions, excluded-data counts, preview rows, and export action. Excel workbooks contain a title/metadata sheet or header, frozen/filterable column headings, typed dates/numbers, data rows, and reconciliation totals. PDF uses a fixed official header/footer, page numbering, repeated table headers, generation timestamp, and filter/definition summary.

## 4. Data and interface changes

Create a report registry: code, label, required permission, allowed filters, provider, columns, and supported formats. Providers consume the same filter object and query services as analytics. Small Excel reports may stream synchronously; PDF and reports above a configured row threshold are queued and tracked as report runs with status, requester, parameters, artifact path, checksum, expiry, and failure.

Named routes cover report index, preview, request export, run status, and authorized download. Downloads use opaque run IDs and authorization checks, not public storage URLs.

## 5. Workstream task delegation

**Backend/Data:** registry/providers, shared filters, workbook writer, report runs, private artifact storage, queued generation, reconciliation metadata.

**Admin UI:** report catalog, filter forms, preview, generation status, download/history, and print-friendly PDF specification.

**Automation:** monthly scheduled requests and expiration cleanup.

**QA:** cross-surface reconciliation, spreadsheet typing/formulas, PDF visual verification, access/expiry, large report, and failure cases.

## 6. Dependencies and execution order

1. Shared analytics services and stable definitions.
2. Report registry plus preview.
3. Excel for monthly summary, ledger, and low balance.
4. Remaining Excel reports.
5. Queued generation/history.
6. PDF layouts and scheduled reports.

## 7. Edge cases and failure handling

- Zero-row reports remain valid and state “No records matched.”
- Excel cells beginning with `=`, `+`, `-`, or `@` from user-controlled text are escaped to prevent formula injection.
- Very large reports are queued and row-limited/paginated internally.
- Expired/missing artifacts can be regenerated from stored validated parameters.
- Unknown legacy values remain blank/raw with excluded counts, never coerced.
- A failed run retains sanitized error detail and offers retry.

## 8. Security and authorization

All reports are admin-only initially. Private artifacts have configurable retention (default 30 days), authorization on every download, unguessable identifiers, and audit events for generation/download. Export only fields required by the selected report and avoid unnecessary birth/contact data.

## 9. Test scenarios

- Each provider with both personnel types, filters, and no rows.
- KPI/chart/detail/export totals match.
- Correct numeric/date cell types and formula-injection protection.
- Queued success/failure/retry/idempotency and expired artifacts.
- PDF page overflow, repeated headers, long names, and visual snapshot/render review.
- Unauthorized/cross-admin artifact access.

## 10. Acceptance criteria

- Every report states filters, definitions, generated timestamp, and excluded-data count.
- Excel totals reconcile to preview and analytics for identical filters.
- Private downloads are authorized and audited.
- Large generation does not block normal web requests.
- PDF is released only after render-and-verify QA on representative data.

## 11. Rollout and rollback notes

Release previews first, then Excel formats incrementally. Validate official users' expected columns before PDF work. Disable a report through the registry if defective; retain run/audit history and remove expired artifacts through scheduled cleanup.

## 12. Future enhancements excluded

- Public/shareable report links.
- Editable spreadsheet round-tripping.
- External BI or data-warehouse integration.

