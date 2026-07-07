# 05 — Import Center

## 1. Objective and administrative benefit

Turn the current direct Excel upload into a previewable, attributable, and recoverable workflow that prevents accidental duplicate or wrong-format data.

## 2. Current project fit and reusable components

`ExcelUploadController` already validates file type/size, profile/card-type match, exact headers, row emptiness, numeric values, and transactional inserts. SimpleXLSX/SimpleXLSXGen already support parsing and templates. The new center extracts this logic into reusable services and wraps it in batches rather than replacing it.

## 3. Functional requirements

- Search/select employee; card type is derived from the employee profile and cannot be overridden inconsistently.
- Download the matching current template.
- Upload to a temporary private location and parse without inserting.
- Preview normalized rows with row number, warnings, errors, duplicate status, and proposed action.
- Block confirmation on errors; permit warnings only after explicit acknowledgement.
- Confirm with an opaque preview token tied to admin, file hash, employee, parser/template version, and expiry.
- Insert all accepted rows transactionally and record a completed import batch.
- History shows actor, employee, format, filename, hash, counts, status, timestamps, and errors.
- Rollback a completed batch only when its rows have not subsequently changed or been referenced; require reason and confirmation.

## 4. Data and interface changes

Add `import_batches` and row lineage (`import_batch_id`, source row number) on both card tables. Batch statuses: `uploaded`, `validated`, `committing`, `completed`, `failed`, `rolled_back`, `expired`. Store original filename and SHA-256 hash, never trust or expose a client path. Keep temporary files on a private disk with expiry cleanup.

Extract workbook headers, parsing, canonical normalization, and validation into an import service used by preview and commit. Commit reparses or verifies the immutable staged file/hash; never trust browser-submitted preview rows.

Duplicate levels: exact existing match blocks by default; likely match warns and requires acknowledgement; duplicate within workbook blocks. The fingerprint uses employee, card type, normalized period/date, normalized type/particular, and principal quantities.

## 5. Workstream task delegation

**Backend/Data:** schema/lineage, parser service, preview tokens, duplicate rules, transactional commit, history, rollback guard, cleanup command.

**Admin UI:** employee selection, upload/preview table, error summaries, acknowledgement, progress, history/detail, and rollback confirmation.

**QA:** existing format regression, tampered/expired preview, duplicate/concurrency, partial-failure prevention, rollback eligibility, and private-file tests.

## 6. Dependencies and execution order

1. Canonical parser and audit foundation.
2. Extract current import behavior into covered service without UI change.
3. Add batch/lineage schema.
4. Add preview and confirmation.
5. Add history and guarded rollback.
6. Redirect existing employee-level upload entry to the new center with employee preselected.

## 7. Edge cases and failure handling

- Wrong personnel template remains a blocking error with zero inserts.
- Preview expires after 30 minutes by default; staged files are cleaned after expiry.
- Concurrent confirmation locks/atomically transitions the batch so only one commit runs.
- Database failure marks batch failed and inserts no rows.
- Changed rows make rollback ineligible; admin receives the specific conflict.
- Formula/macro content is not executed; file size and row count are capped.

## 8. Security and authorization

Admin-only, CSRF protected, private file storage, sanitized filenames, MIME/extension/size checks, randomized storage keys, and no spreadsheet HTML rendered unescaped. Preview tokens are unguessable and bound to the uploading admin. Import and rollback actions are audited.

## 9. Test scenarios

- Both templates, valid import, empty workbook, wrong headers/type, malformed numeric values.
- Preview creates no leave rows.
- Tampered token/hash, expired batch, double confirm, and concurrent confirm.
- Exact/likely/in-workbook duplicates.
- Transaction rollback and guarded batch rollback.
- Existing import route behavior and template downloads.

## 10. Acceptance criteria

- No leave row is inserted before explicit confirmation.
- Preview and commit use the same parser/version and immutable file.
- Batch history traces every inserted row to actor/file/source row.
- Duplicate handling is deterministic and explained.
- Eligible rollback is atomic, reasoned, and audited.

## 11. Rollout and rollback notes

Deploy the extracted service first and keep the current endpoint. Add preview behind a feature flag, then redirect the current form after parity testing. Disable new routes to roll back; existing direct service behavior remains available. Do not delete batch history on rollback.

## 12. Future enhancements excluded

- Cross-employee bulk workbooks.
- Background processing for very large files.
- Cloud-drive integrations and arbitrary spreadsheet layouts.

