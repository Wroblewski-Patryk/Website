# Task

## Header
- ID: FEA-015H
- Title: Add archive release integrity metadata preflight gate
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-015G
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
The archive driver had staging and release path preflight, but did not yet
require trusted release archive metadata from the manifest.

## Goal
Make archive apply fail closed unless a trusted manifest records both the
archive URL and a valid SHA-256 checksum.

## Scope
- Persist archive metadata from the release manifest.
- Normalize archive metadata in update status.
- Require archive URL and SHA-256 in archive driver preflight.
- Add regression coverage for missing and present metadata.
- Synchronize architecture, planning, and context docs.

## Implementation Plan
1. Inspect current manifest parsing, update status normalization, archive
   driver preflight, and update command tests.
2. Store `release_archive_url` and `release_archive_sha256` from the manifest.
3. Normalize those fields in `system_update_status`.
4. Require non-empty archive URL and 64-character SHA-256 before archive driver
   preflight can pass.
5. Keep `supports_apply=false` until download, verification, staging, and
   switch execution are implemented.
6. Add tests and update docs.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: archive path readiness could pass without release integrity metadata.
- Gaps: manifest archive URL and checksum were not persisted.
- Inconsistencies: architecture required checksum enforcement, while code only
  checked writable paths.
- Architecture constraints: archive apply must fail closed until integrity and
  staging are proven.

### 2. Select One Priority Task
- Selected task: FEA-015H archive integrity metadata gate.
- Priority rationale: integrity metadata is the first prerequisite before safe
  archive download or staging work.
- Why other candidates were deferred: archive file switching is higher risk and
  should wait for metadata gating.

### 3. Plan Implementation
- Files or surfaces to modify: update manager, archive driver, update command
  tests, architecture and planning docs.
- Logic: archive preflight fails if URL or checksum metadata is missing or the
  checksum is not a 64-character hexadecimal SHA-256 value.
- Edge cases: configured writable paths without metadata, metadata present but
  apply execution still disabled.

### 4. Execute Implementation
- Implementation notes: persisted manifest metadata and added archive preflight
  validation while keeping archive `supports_apply=false`.

### 5. Verify and Test
- Validation performed: targeted update command tests and settings tests.
- Result: archive mode fails closed without metadata and passes preflight with
  valid metadata while still blocking automatic apply.

### 6. Self-Review
- Simpler option considered: wait until archive download implementation.
- Technical debt introduced: no.
- Scalability assessment: metadata fields align with release manifest
  requirements and can be reused by the later downloader/verifier.
- Refinements made: checksum format is validated before apply support can be
  considered.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update manager contract, MVP plan, next
  commits, task board, project state, and FEA-015 evidence.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Release manifest archive URL is persisted in update status.
- [x] Release manifest archive SHA-256 is persisted in update status.
- [x] Archive driver preflight fails closed without valid metadata.
- [x] Archive driver remains preflight-only even with valid metadata.

## Success Signal
- User or operator problem: shared-hosting archive updates must not proceed
  without release integrity metadata.
- Expected product or reliability outcome: archive apply has an explicit
  integrity gate before download/staging work starts.
- How success will be observed: archive driver preflight fails without
  `release_archive_url` and `release_archive_sha256`.
- Post-launch learning needed: yes.

## Deliverable For This Stage
Verified archive integrity metadata preflight gate.

## Constraints
- Do not download or switch files in this slice.
- Do not mark archive apply production-ready.
- Preserve fail-closed behavior.
- Keep secrets out of status payloads.

## Definition of Done
- [x] Code builds without errors.
- [x] Feature works through the existing CLI/status path.
- [x] No mock, placeholder, fake, or temporary path remains in production
  behavior.
- [x] Full data flow works across manifest parsing, status storage, driver
  preflight, and tests.
- [x] Backend error handling exists where applicable.
- [x] No existing functionality is broken.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from validation evidence.
- [x] Reliability and security evidence are recorded.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: `php artisan test --filter=SystemUpdateCheckCommandTest`;
  `php artisan test --filter=SettingsManagementTest`.
- Manual checks: reviewed archive driver preflight status path.
- Screenshots/logs: PHPUnit command output.
- High-risk checks: archive mode remains `supports_apply=false` with valid
  metadata.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes.
- Real API/service path used: not applicable.
- Endpoint and client contract match: not applicable.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: yes.
- Refresh/restart behavior verified: status metadata persists through settings.
- Regression check performed: targeted command tests.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes.
- Critical user journey: archive update is not considered safe without trusted
  release integrity metadata.
- SLI: archive preflight requires URL and valid SHA-256.
- SLO: preflight remains local and fast.
- Error budget posture: healthy.
- Health/readiness check: not applicable for metadata gate.
- Logs, dashboard, or alert route: admin update status preflight message.
- Smoke command or manual smoke: `php artisan updates:check`.
- Rollback or disable path: archive apply remains disabled.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: public release URL and checksum metadata.
- Trust boundaries: trusted manifest to application status storage.
- Permission or ownership checks: update status remains admin-visible.
- Abuse cases: malicious manifest without checksum, malformed checksum,
  premature archive apply.
- Secret handling: no secrets added.
- Security tests or scans: targeted regression tests.
- Fail-closed behavior: missing or invalid checksum blocks preflight.
- Residual risk: actual download checksum verification and staging switch are
  still pending.

## Architecture Evidence
- Architecture source reviewed:
  `docs/architecture/system-update-manager-contract.md`.
- Fits approved architecture: yes.
- Mismatch discovered: no.
- Decision required from user: no.
- Approval reference if architecture changed: not applicable.
- Follow-up architecture doc updates: none.

## Deployment / Ops Evidence
- Deploy impact: low.
- Env or secret changes: none.
- Health-check impact: none.
- Smoke steps updated: not needed for metadata gate.
- Rollback note: archive rollback still pending apply design.
- Observability or alerting impact: preflight message is visible in update
  status.
- Staged rollout or feature flag: archive apply remains disabled.
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
- Task summary: added archive release URL/checksum status persistence and
  preflight validation.
- Files changed: `app/Services/SystemUpdates/UpdateManager.php`,
  `app/Services/SystemUpdates/Drivers/ArchiveUpdateDriver.php`,
  `tests/Feature/SystemUpdateCheckCommandTest.php`, and synchronized docs.
- How tested: targeted PHPUnit feature tests.
- What is incomplete: archive download, checksum verification, staging,
  switching, and rollback execution.
- Next steps: add archive download/staging verification design or implement the
  first no-switch archive download verifier.
- Decisions made: archive preflight accepts only 64-character hexadecimal
  SHA-256 metadata and still does not support apply.

## Notes
This slice intentionally stops before file downloads or filesystem mutation.
