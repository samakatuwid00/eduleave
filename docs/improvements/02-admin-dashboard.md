# 02 — Admin Dashboard

## 1. Objective and administrative benefit

Replace the mostly navigational admin landing page with an operational overview that answers: what is happening, what needs attention, and where can the admin act next?

## 2. Current project fit and reusable components

The admin Blade layout/sidebar and dark/responsive controls already exist. User status pages provide natural drill-down destinations. `EmployeeProfile` and personnel types support composition metrics, normalized leave cards support leave metrics, and `public/admincss/assets/plugins/chartjs/chart.min.js` avoids a new chart dependency.

## 3. Functional requirements

- KPI cards: total non-admin employees, pending approvals, employees missing a leave card, and employees in a low/zero balance band.
- Approval pipeline: registered/unverified, verified-pending, approved, and rejected counts as a stacked horizontal bar (use a funnel only if accessible labels remain clear).
- Activity line chart: registrations, approvals, and rejections by day/week/month for the active range.
- Personnel doughnut: teaching, non-teaching, and missing classification.
- Monthly leave stacked bars: vacation, sick, other, and unpaid quantities; show excluded/unparseable count.
- Action tables: oldest pending approvals, lowest balances, and incomplete profiles/records.
- Shared filters: inclusive dates, personnel type, user status, and leave type; default current year/all.
- Every card/segment/series/table row links to a named filtered route. Filters survive navigation and browser back/forward.
- Provide skeleton/loading state only for asynchronously loaded sections; server-rendered baseline must remain useful without JavaScript.

## 4. Data and interface changes

Create an `AdminAnalyticsService` (or equivalent application service) with stable methods for KPIs, approval pipeline/activity, personnel composition, leave trend, and action previews. Provide a server-rendered dashboard payload; use a JSON endpoint only for filter refresh if needed. Response datasets include label, numeric value, unit, filter/drill-down URL, generated timestamp, and excluded-data metadata.

Do not count admins as employees. “Missing leave card” means an employee profile whose routed card query has zero rows. Default low balance is `<= 5.00` canonical days/credits and must be configuration-driven.

## 5. Workstream task delegation

**Backend/Data:** implement shared filters, aggregate queries, indexes, caching, drill-down counts, and excluded-data metadata.

**Admin UI:** reorganize dashboard sections, render Chart.js charts, accessible tables/summaries, filter form, cards, and action links.

**QA:** reconcile every visual value to its destination list; test access, empty data, invalid filters, mobile/sidebar, dark mode, and no-JavaScript baseline.

## 6. Dependencies and execution order

1. Data foundation fields and filter contract.
2. Query service and fixtures.
3. Server-rendered KPI/action tables.
4. Chart components and filter refresh.
5. Cache/performance tuning only after measured query plans.

## 7. Edge cases and failure handling

- Show zero-state guidance rather than empty axes.
- Present malformed legacy values as “excluded from calculation” with a Data Quality link.
- If one dataset fails, retain other panels and show a retryable panel error with a correlation ID.
- Avoid misleading partial current-month comparisons; clearly label incomplete periods.
- Large drill-downs are paginated server-side.

## 8. Security and authorization

Dashboard and JSON endpoints require existing `auth` and `admin` middleware. Escape labels, validate all filters, and do not expose employee names in aggregate endpoint payloads unless required by an authorized action table.

## 9. Test scenarios

- Exact KPI/pipeline counts for each status and verification combination.
- Teaching/non-teaching/missing-type composition.
- Missing-card and threshold boundary (`5.00`) cases.
- Date grouping across month/year boundaries and Asia/Manila display.
- Chart-to-list filter reconciliation.
- Empty, malformed, unauthorized, mobile, dark-mode, and keyboard cases.

## 10. Acceptance criteria

- Initial dashboard is useful server-rendered and loads within the agreed performance budget on representative data.
- Every displayed number has a deterministic definition and reconciled drill-down.
- No chart calculates authoritative totals in JavaScript.
- Charts have text/table alternatives and clear units.
- Existing admin pages remain reachable from the reorganized sidebar.

## 11. Rollout and rollback notes

Release behind an admin feature flag. Compare new metrics against direct queries in staging, enable for selected admins, then make it the default landing page. Rollback restores the prior dashboard route/view while leaving query services unused.

## 12. Future enhancements excluded

- Predictive absence forecasting.
- Cross-organization benchmarking.
- Real-time websocket updates; normal refresh/caching is sufficient initially.

