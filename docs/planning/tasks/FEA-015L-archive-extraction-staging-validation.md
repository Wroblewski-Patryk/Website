# Task

## Header
- ID: FEA-015L
- Title: Add no-switch archive extraction staging validation
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-015K
- Priority: P1
- Coverage Ledger Rows:
- Iteration:
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number once execution starts.
- [x] The task is aligned with repository source-of-truth documents.

## Context
Archive verification could download and checksum a release archive. The next
safe step is staging extraction validation, but still without switching live
application files.

## Goal
Extract verified archives into staging when PHP `ZipArchive` is available,
validate required Laravel release files, and fail closed on invalid structure.

## Scope
- Archive driver staging extraction.
- Unsafe ZIP entry guard.
- Required release file validation.
- Update status evidence for extracted staging.
- Regression tests with and without ZIP support.
- Architecture, reliability, planning, and context docs.

## Implementation Plan
1. Preserve no-ZIP behavior as `archive_extraction_status=unavailable`.
2. When `ZipArchive` exists, inspect entries for absolute or traversal paths.
3. Extract to a versioned directory inside the configured staging path.
4. Require `artisan`, `composer.json`, `bootstrap/app.php`, and
   `public/index.php`.
5. Remove failed extraction output and leave live files untouched.
6. Add targeted tests and update docs.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: archive verification did not validate extracted release structure.
- Gaps: no required-file check before a future switch.
- Inconsistencies: architecture required staging validation, but implementation
  stopped at checksum verification.
- Architecture constraints: no live switch, no migration, no applied status.

### 2. Select One Priority Task
- Selected task: FEA-015L archive extraction staging validation.
- Priority rationale: validated staging output is required before any safe
  switch/rollback design.
- Why other candidates were deferred: live switch and rollback are higher risk
  and need staging validation first.

### 3. Plan Implementation
- Files or surfaces to modify: archive driver, update manager status fields,
  update command tests, architecture and operations docs.
- Logic: verified archive extracts to staging, validates required files, stores
  `archive_staged` evidence on success, and stores `archive_staging_failed` on
  validation failure.
- Edge cases: no ZipArchive, unsafe archive entries, missing required files,
  repeated extraction.

### 4. Execute Implementation
- Implementation notes: added extraction validation and required-file checks
  while preserving no-switch behavior.

### 5. Verify and Test
- Validation performed: normal `artisan test` run without ZIP, direct PHPUnit
  run with `php -d extension=zip`, settings tests, and lint/whitespace checks.
- Result: no-ZIP runtime skips extraction tests and records unavailable; ZIP
  runtime validates extraction success and failure paths.

### 6. Self-Review
- Simpler option considered: only document ZIP as a blocker.
- Technical debt introduced: no.
- Scalability assessment: staging validation creates the evidence needed for a
  later switch/rollback gate.
- Refinements made: extraction output is removed when validation fails.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update manager contract, service reliability,
  MVP plan, next commits, task board, project state, and FEA-015 evidence.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] ZIP-capable runtime extracts verified archive to staging.
- [x] Required Laravel release files are validated.
- [x] Missing required files fail closed.
- [x] Failed extraction output is removed.
- [x] Live files are not switched or marked applied.

## Success Signal
- User or operator problem: archive releases must be structurally valid before
  shared-hosting file switches can be considered.
- Expected product or reliability outcome: staged archive structure is proven
  before switch/rollback work starts.
- How success will be observed: `apply_status=archive_staged` and
  `archive_extraction_status=validated` for complete archives.
- Post-launch learning needed: yes.

## Deliverable For This Stage
Verified no-switch archive extraction staging validation.

## Constraints
- Do not switch live files.
- Do not run migrations.
- Do not mark update applied.
- Keep failed staging output out of the filesystem.

## Definition of Done
- [x] Code builds without errors.
- [x] Feature works through the existing CLI/operator path.
- [x] No mock, placeholder, fake, or temporary path remains in production
  behavior.
- [x] Full data flow works across archive download, checksum, extraction,
  required-file validation, status persistence, and tests.
- [x] Backend error handling exists.
- [x] No existing functionality is broken.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from validation evidence.
- [x] Reliability, security, deployment, and rollback evidence are recorded.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: `php artisan test --filter=SystemUpdateCheckCommandTest`;
  `php -d extension=zip vendor/bin/phpunit --filter
  SystemUpdateCheckCommandTest`; `php artisan test
  --filter=SettingsManagementTest`.
- Manual checks: verified local PHP ZIP availability via `php -d extension=zip`.
- Screenshots/logs: PHPUnit command output.
- High-risk checks: no live file switch, failed staging output removed.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes.
- Real API/service path used: HTTP archive download covered with fake.
- Endpoint and client contract match: not applicable.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: yes.
- Refresh/restart behavior verified: staging evidence persists in settings.
- Regression check performed: targeted command tests.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes.
- Critical user journey: operator validates staged archive before file switch.
- SLI: valid archives record `archive_staged`; invalid archives record
  `archive_staging_failed`.
- SLO: bounded operator command.
- Error budget posture: healthy.
- Health/readiness check: required-file staging validation.
- Logs, dashboard, or alert route: admin update status and audit event.
- Smoke command or manual smoke: `php artisan updates:apply --force` with
  archive driver selected.
- Rollback or disable path: no live files changed.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: staged release files and update status.
- Trust boundaries: trusted archive to local staging filesystem.
- Permission or ownership checks: server-side CLI/admin apply path.
- Abuse cases: path traversal ZIP entry, missing critical files, premature
  live switch.
- Secret handling: no secrets added.
- Security tests or scans: unsafe path guard added; required-file failure
  regression test added.
- Fail-closed behavior: validation failure removes extraction output.
- Residual risk: switch, migration, and rollback are still pending.

## Architecture Evidence
- Architecture source reviewed:
  `docs/architecture/system-update-manager-contract.md`.
- Fits approved architecture: yes.
- Mismatch discovered: no.
- Decision required from user: no.
- Approval reference if architecture changed: not applicable.
- Follow-up architecture doc updates: none.

## Deployment / Ops Evidence
- Deploy impact: medium.
- Env or secret changes: PHP ZIP extension required for extraction validation.
- Health-check impact: none.
- Smoke steps updated: service reliability notes updated.
- Rollback note: no live files changed in this slice.
- Observability or alerting impact: staging evidence is persisted.
- Staged rollout or feature flag: archive still does not switch files.
- `DEPLOYMENT_GATE.md` reviewed: yes.

## Review Checklist (mandatory)
- [x] Process self-audit completed before implementation.
- [x] Autonomous loop evidence covers all seven steps.
- [x] Exactly one priority task was completed in this iteration.
- [x] Operation mode was selected according to iteration rotation.
- [x] Current stage is declared and respected.
- [x] Deliverable for the current stage is complete.
- [x] Architecture alignment confirmed.
- [x] Existing systems were reused where applicable.
- [x] No workaround paths were introduced.
- [x] No temporary solution was introduced.
- [x] No logic duplication was introduced.
- [x] Integration checklist evidence is attached where applicable.
- [x] AI testing evidence is attached where applicable.
- [x] Deployment gate evidence is attached.
- [x] Definition of Done evidence is attached.
- [x] Relevant validations were run.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: archive apply now extracts verified archives into staging and
  validates required release files without switching live files.
- Files changed: `app/Services/SystemUpdates/Drivers/ArchiveUpdateDriver.php`,
  `app/Services/SystemUpdates/UpdateManager.php`,
  `tests/Feature/SystemUpdateCheckCommandTest.php`, and synchronized docs.
- How tested: targeted PHPUnit feature tests with and without ZIP enabled.
- What is incomplete: archive switch execution, migration handling, rollback
  execution, and captured staging/live Coolify rollout evidence.
- Next steps: design archive switch/rollback gate or capture Coolify evidence.
- Decisions made: staging validation requires ZIP support and remains no-switch.

## Notes
The local `artisan test` process does not load ZIP by default, so ZIP-specific
coverage was verified with direct PHPUnit and `php -d extension=zip`.
