# Task

## Header
- ID: FEA-015I
- Title: Add no-switch archive download and SHA-256 verifier
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-015H
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
Archive updates had metadata and path preflight but no runtime artifact
verification path. The next safe slice is to verify a staged archive without
extracting or switching files.

## Goal
Allow the archive driver to download a release archive into staging and verify
its SHA-256 checksum while keeping live application files untouched.

## Scope
- Archive driver download and checksum verification.
- Update status evidence fields for archive verification.
- Regression tests for success and checksum failure.
- Architecture, reliability, task board, and planning docs.

## Implementation Plan
1. Inspect archive preflight, update manager result persistence, and command
   tests.
2. Download the trusted archive URL into the configured staging path.
3. Verify the downloaded file against `release_archive_sha256`.
4. Record verification evidence without marking the update applied.
5. Delete mismatched downloads and return a fail-closed status.
6. Add tests and synchronize docs.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: archive driver could only report metadata/path readiness.
- Gaps: no staged artifact verification evidence existed.
- Inconsistencies: architecture required integrity verification before apply.
- Architecture constraints: no extraction, migration, file switch, or live
  mutation in this slice.

### 2. Select One Priority Task
- Selected task: FEA-015I no-switch archive verifier.
- Priority rationale: checksum verification is required before any staging
  extraction or switch design.
- Why other candidates were deferred: file switching and rollback are higher
  risk and need verified artifacts first.

### 3. Plan Implementation
- Files or surfaces to modify: archive driver, update manager status fields,
  update command tests, architecture and operations docs.
- Logic: download to staging, hash the downloaded file, record
  `archive_verified` on match, remove file and record
  `archive_verification_failed` on mismatch.
- Edge cases: failed HTTP download, unwritable staging directory, checksum
  mismatch, repeated verification.

### 4. Execute Implementation
- Implementation notes: archive apply now performs download/checksum
  verification only and stores filename, byte count, SHA-256, and timestamp.

### 5. Verify and Test
- Validation performed: targeted update command tests and settings tests.
- Result: successful verification keeps the archive staged without changing
  live files; checksum failure removes the staged file.

### 6. Self-Review
- Simpler option considered: only document the verifier without code.
- Technical debt introduced: no.
- Scalability assessment: evidence fields can feed later staging extraction and
  rollback gates.
- Refinements made: live update status remains open and current version is not
  advanced after verification.

### 7. Update Documentation and Knowledge
- Docs updated: architecture map, update manager contract, service reliability,
  MVP plan, next commits, task board, project state, and FEA-015 evidence.
- Context updated: yes.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [x] Archive apply downloads the configured archive into staging.
- [x] Matching SHA-256 records `archive_verified`.
- [x] Checksum mismatch records `archive_verification_failed`.
- [x] Checksum mismatch removes the downloaded staged archive.
- [x] Live files are not extracted, switched, or marked applied.

## Success Signal
- User or operator problem: archive updates need artifact integrity evidence
  before shared-hosting file changes can be considered.
- Expected product or reliability outcome: Featherly can prove a staged archive
  matches the trusted manifest without touching live files.
- How success will be observed: `system_update_status.apply_status` is
  `archive_verified` and current version remains unchanged.
- Post-launch learning needed: yes.

## Deliverable For This Stage
Verified no-switch archive download and checksum verifier.

## Constraints
- Do not extract archives.
- Do not switch live files.
- Do not run migrations.
- Do not mark archive update as applied.
- Preserve fail-closed behavior.

## Definition of Done
- [x] Code builds without errors.
- [x] Feature works through the existing CLI/operator path.
- [x] No mock, placeholder, fake, or temporary path remains in production
  behavior.
- [x] Full data flow works across manifest status, driver apply, staging file,
  status persistence, and tests.
- [x] Backend error handling exists where applicable.
- [x] No existing functionality is broken.
- [x] Feature works after retry because staged evidence is persisted.
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
  `php artisan test --filter=SettingsManagementTest`.
- Manual checks: reviewed archive verifier status fields and no-switch behavior.
- Screenshots/logs: PHPUnit command output.
- High-risk checks: checksum mismatch deletes the staged archive and keeps live
  version unchanged.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes.
- Real API/service path used: HTTP archive download is covered with HTTP fake.
- Endpoint and client contract match: not applicable.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: yes.
- Refresh/restart behavior verified: status evidence persists in settings.
- Regression check performed: targeted command tests.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes.
- Critical user journey: operator verifies archive integrity before file
  mutation.
- SLI: matching checksum records `archive_verified`; mismatch records
  `archive_verification_failed`.
- SLO: bounded operator command.
- Error budget posture: healthy.
- Health/readiness check: not applicable before file switch.
- Logs, dashboard, or alert route: admin update status and audit event for
  update attempt.
- Smoke command or manual smoke: `php artisan updates:apply --force` with
  archive driver selected.
- Rollback or disable path: no live files changed; remove staged archive or
  disable archive driver config.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: release archive URL, checksum, staged artifact metadata.
- Trust boundaries: trusted manifest to HTTP archive source to local staging.
- Permission or ownership checks: server-side CLI/admin apply path.
- Abuse cases: tampered archive, checksum mismatch, failed download, premature
  live switch.
- Secret handling: no secrets are introduced or logged.
- Security tests or scans: checksum mismatch regression test.
- Fail-closed behavior: mismatch removes the artifact and does not advance
  version.
- Residual risk: archive extraction, staging validation, and rollback are still
  pending.

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
- Env or secret changes: none.
- Health-check impact: none.
- Smoke steps updated: service reliability notes updated.
- Rollback note: no live files changed in this slice.
- Observability or alerting impact: archive verification evidence is persisted.
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
- Task summary: archive apply now downloads and verifies a staged archive
  without switching live files.
- Files changed: `app/Services/SystemUpdates/Drivers/ArchiveUpdateDriver.php`,
  `app/Services/SystemUpdates/UpdateManager.php`,
  `tests/Feature/SystemUpdateCheckCommandTest.php`, and synchronized docs.
- How tested: targeted PHPUnit feature tests.
- What is incomplete: archive extraction, staging validation, switch execution,
  migration handling, and rollback execution.
- Next steps: add archive extraction staging validation without live switch.
- Decisions made: verified archives are staged and recorded as evidence, but
  the update remains unapplied.

## Notes
This is the first runtime archive verifier. It deliberately stops before any
operation that could alter the live application tree.
