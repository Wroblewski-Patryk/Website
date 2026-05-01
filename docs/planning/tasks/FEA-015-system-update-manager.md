# Task

## Header
- ID: FEA-015
- Title: Design and implement environment-adaptive System Update Manager
- Task Type: feature
- Current Stage: verification
- Status: IN_PROGRESS
- Owner: Backend Builder
- Depends on: `docs/architecture/system-update-manager-contract.md`
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
Featherly needs WordPress-like update behavior for both maintainer-controlled
deployments, such as Coolify on a VPS, and third-party self-hosted
installations where the maintainer has no server access.

The architecture decision is recorded in
`docs/architecture/system-update-manager-contract.md`: the app owns update
discovery, settings, status, and safety checks; update application is delegated
to a safe environment-specific driver.

## Goal
Implement a System Update Manager that checks for trusted Featherly releases,
shows update state in the admin panel, allows automatic updates to be enabled
or disabled, and applies updates only through a supported driver that passes
preflight checks.

## Scope
- Update settings and status persistence.
- Admin settings/status surface under the existing admin boundary.
- Laravel scheduler command for daily update checks.
- Release manifest client and version comparison.
- Update driver interface and first driver implementations:
  - `coolify`
  - `archive`
  - `manual`
- Fake or test driver for automated tests.
- Audit logging for checks and attempts.
- Deployment, rollback, smoke, and security documentation updates.

Out of scope for the first implementation slice:

- marketplace/plugin updates
- self-modifying code without staging/verification
- automatic application of high-risk/manual-review releases
- bypassing hosting-level deployment controls

## Implementation Plan
1. Inspect current settings storage, scheduler commands, audit logging, admin
   settings UI, deployment docs, and security docs.
2. Add update configuration and status storage using the existing settings or a
   narrowly scoped update status model if audit/history requirements require it.
3. Add a release manifest client that reads a stable trusted manifest and
   validates version, channel, checksum metadata, migration risk, and manual
   review flags.
4. Add `updates:check` command and schedule it daily without blocking normal
   runtime when the manifest source is unavailable.
5. Add admin UI controls for update checks, auto-apply, release channel,
   preferred driver, current version, latest version, last check, last attempt,
   and failure details.
6. Add update driver contract with `manual`, fake/test, `coolify`, and
   `archive` drivers.
7. Add `updates:apply` guarded by permissions, settings, environment kill
   switch, driver support, release integrity, preflight checks, and release
   risk classification.
8. Add tests for settings authorization, manifest handling, scheduler command,
   driver selection, fail-closed behavior, and fake driver application.
9. Update operations docs with driver setup, smoke, rollback, and recovery
   evidence.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: Featherly has Coolify deployment notes but no approved runtime update
  contract.
- Gaps: no update settings, scheduler checks, release manifest, driver
  interface, shared-hosting strategy, or rollback model for automatic updates.
- Inconsistencies: the desired WordPress-like update behavior spans runtime,
  deployment, security, and shared-hosting constraints that are not yet modeled.
- Architecture constraints: preserve admin/public split, existing settings and
  permission patterns, Laravel scheduler usage, deployment gate, rollback docs,
  and secure secret handling.

### 2. Select One Priority Task
- Selected task: FEA-015 System Update Manager.
- Priority rationale: deployment and third-party installation strategy affects
  how releases are packaged, shipped, and operated.
- Why other candidates were deferred: feature-specific CMS tasks do not answer
  the installation/update lifecycle requirement.

### 3. Plan Implementation
- Files or surfaces to modify: settings/admin UI, scheduler command files,
  update services, tests, translations, deployment docs, rollback docs,
  architecture references.
- Logic: check release manifest daily, store update status, expose admin
  controls, select a driver, apply only when safe, and degrade to manual mode
  when unsupported.
- Edge cases: no shell access, missing write permissions, manifest unavailable,
  checksum mismatch, high-risk migrations, disabled auto-update, missing
  Coolify secret, failed archive staging, post-update smoke failure.

### 4. Execute Implementation
- Implementation notes: implemented the first safe slice using the existing
  settings surface, a new `updates:check` command, manifest parsing/status
  storage in `settings`, and a manual-mode admin status UI. Automatic
  application and environment-specific apply drivers remain deferred.
- Implementation notes: continued the admin/manual slice by adding an
  authenticated "check now" action that forces an immediate manifest refresh,
  keeps auto-apply fail-closed, and surfaces release notes when available.
- Implementation notes: continued the apply-contract slice by adding the
  `UpdateDriver` interface, manual instructions driver, config-gated fake
  driver, guarded `updates:apply`, and admin apply action while keeping real
  code replacement out of scope.
- Implementation notes: added post-deploy confirmation through
  `updates:confirm`, which compares the running `APP_VERSION` against the last
  target release before marking a Coolify-triggered deployment complete.
- Implementation notes: reused the operational health probes from
  `ops:health-check` in `updates:confirm`, so version-matching deployments
  still fail confirmation when database, cache, or queue readiness fails.
- Implementation notes: added the Coolify rollout runbook and linked it from
  deployment, rollback, smoke, and architecture docs so production readiness
  requires captured rollout evidence rather than only code-level tests.
- Implementation notes: added archive release integrity metadata persistence
  and preflight gating; archive mode now requires `release_archive_url` and a
  valid 64-character `release_archive_sha256` before apply can be considered.
- Implementation notes: added no-switch archive verification; archive apply can
  download a release archive to staging, verify SHA-256, record evidence, and
  remove mismatched downloads without extracting or switching live files.
- Implementation notes: added archive extraction capability evidence; verified
  archives now record whether PHP `ZipArchive` support is pending/available or
  unavailable before extraction validation can be attempted.
- Implementation notes: closed DEC-009 by deferring Docker and Git runtime
  drivers from v1 and routing Docker/Git deployment responsibility to
  Coolify/platform/operator workflows until dedicated contracts exist.

### 5. Verify and Test
- Validation performed: targeted backend feature tests for settings and update
  checks; admin i18n scan; frontend lint.
- Result: first slice verified, full task remains in progress.
- Result: second slice verified with targeted backend coverage and frontend
  lint; admin i18n scan remains blocked locally by unavailable MySQL.
- Result: third slice verified with targeted backend coverage and frontend
  lint; real production drivers remain pending.
- Result: fourth slice verified with targeted backend coverage; Coolify live
  health-check integration remains pending.
- Result: fifth slice verified with targeted backend coverage and a live
  `ops:health-check --json` smoke; Coolify live rollout evidence remains
  pending.
- Result: sixth slice verified as documentation-only with whitespace checks;
  actual staging/live Coolify evidence remains pending.
- Result: seventh slice verified with targeted backend coverage; archive apply
  remains preflight-only until download, checksum verification, staging, and
  switch execution are implemented.
- Result: eighth slice verified with targeted backend coverage; archive apply
  now performs download/checksum verification but still does not extract,
  migrate, switch live files, or mark the update applied.
- Result: ninth slice verified with targeted backend coverage; local runtime
  lacks ZIP support, so extraction is recorded as unavailable while the staged
  archive remains verified.
- Result: tenth slice verified as documentation/architecture decision; Docker
  and Git runtime drivers are out of v1 scope and no placeholder classes are
  introduced.

### 6. Self-Review
- Simpler option considered: Coolify-only auto-deploy.
- Technical debt introduced: no, task not implemented yet.
- Scalability assessment: driver-based design supports Coolify, VPS, Docker,
  archive/shared-hosting, and manual fallback without duplicating update logic.
- Refinements made: automatic update application is fail-closed and separated
  from update checks.

### 7. Update Documentation and Knowledge
- Docs updated: task evidence, task board, and progress log.
- Context updated: task board and automation memory.
- Learning journal updated: not applicable.

## Acceptance Criteria
- [ ] Update checks are enabled by default and run daily through Laravel
  scheduler.
- [ ] Admin users with the existing settings permission can view update status
  and change update preferences.
- [ ] Automatic update application can be disabled in admin settings and by an
  environment-level override.
- [ ] Unsupported environments degrade to manual update notification instead of
  attempting unsafe file or service changes.
- [ ] The Coolify driver triggers deployment only through server-side secrets.
- [ ] The archive driver verifies release integrity and preserves local runtime
  state before switching files.
- [ ] High-risk or manual-review releases do not auto-apply.
- [ ] Update attempts are audit logged without leaking secrets.
- [x] Tests cover manifest parsing, fail-closed behavior, driver selection,
  authorization, and at least one successful fake-driver update.

## Success Signal
- User or operator problem: Featherly installations can drift from the latest
  code and currently have no safe app-level update experience across hosting
  models.
- Expected product or reliability outcome: self-hosted installations can detect
  and, where supported, apply stable updates automatically.
- How success will be observed: admin update screen shows accurate status,
  scheduler records daily checks, and supported drivers can apply a test update
  path with rollback/recovery evidence.
- Post-launch learning needed: yes

## Deliverable For This Stage
Verified tenth implementation slice: Docker/Git runtime driver v1 deferral,
while keeping production code replacement fail-closed.

## Constraints
- use existing settings, scheduler, admin permission, and audit patterns first
- preserve locale-aware route boundaries and the admin/public split
- keep translation and i18n scanning implications explicit for admin copy
- do not expose deploy secrets to the browser
- do not implement direct unsafe self-mutation of the live app
- fail closed to notification/manual mode when support or integrity is uncertain
- document rollback and recovery before enabling production auto-apply

## Definition of Done
- [ ] Code builds without errors.
- [ ] Feature works manually through the real UI, CLI, and driver path.
- [ ] No mock, placeholder, fake, or temporary path remains in production
  behavior.
- [ ] Full data flow works across admin UI, backend, scheduler, storage,
  drivers, validation, and audit logging.
- [ ] Backend and UI/client error handling exists.
- [ ] No existing functionality is broken.
- [ ] Feature works after restart, reload, and scheduler rerun.
- [ ] Changes are documented in architecture, operations, and planning docs.
- [ ] Behavior is reproducible from validation evidence.
- [ ] Security, reliability, deployment, and rollback evidence are recorded.
- [ ] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Validation Evidence
- Tests: `php artisan test --filter=SettingsManagementTest`,
  `php artisan test --filter=SystemUpdateCheckCommandTest`, `npm run lint`
- Manual checks: reviewed the admin settings route, forced-check redirect flow,
  and settings UI contract.
- Screenshots/logs: command output asserted in feature tests; admin trigger
  redirect + flash asserted in feature tests.
- High-risk checks: missing manifest URL or invalid manifest fail closed and
  store a failed status instead of attempting any apply path; manual checks can
  bypass disabled scheduler preference without enabling auto-apply.
- High-risk checks: high-risk/manual-review releases block apply attempts;
  manual driver records instructions without mutating files; fake driver is
  config-gated and disabled by default.
- High-risk checks: Coolify-triggered deployments are not marked complete until
  `updates:confirm` observes the expected runtime `APP_VERSION`.
- High-risk checks: matching versions still record
  `confirmation_health_failed` when DB/cache/queue health fails.
- High-risk checks: Coolify driver production readiness now requires captured
  rollout evidence and rollback review from the runbook.
- High-risk checks: archive driver fails closed without release archive URL and
  SHA-256 metadata and still does not support apply even when metadata exists.
- High-risk checks: checksum mismatch removes the staged archive and leaves live
  files untouched.
- High-risk checks: missing PHP `ZipArchive` support records extraction as
  unavailable and does not attempt extraction.
- High-risk checks: Docker/Git runtime self-updaters are not introduced without
  dedicated staging, secret, migration, health, and rollback contracts.
- Coverage ledger updated: not applicable.
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: not applicable for planning stage.
- Real API/service path used: not applicable.
- Endpoint and client contract match: not applicable.
- DB schema and migrations verified: not applicable.
- Loading state verified: not applicable.
- Error state verified: not applicable.
- Refresh/restart behavior verified: not applicable.
- Regression check performed: documentation-only review.

## Product / Discovery Evidence
- Problem validated: yes.
- User or operator affected: maintainers, self-hosting site owners, and
  third-party installers.
- Existing workaround or pain: manual deploys or platform-specific setup only.
- Smallest useful slice: update checks, admin status, settings, manifest
  parsing, and fake/manual driver before destructive apply paths.
- Success metric or signal: daily update status is visible and supported
  drivers can safely apply a trusted release.
- Feature flag, staged rollout, or disable path: yes.
- Post-launch feedback or metric check: monitor failed preflights and manual
  fallback frequency.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: yes.
- Critical user journey: site remains available and recoverable across update
  check/apply attempts.
- SLI: update attempts either complete with healthy post-update status or fail
  closed without corrupting the installation.
- SLO: define before production auto-apply rollout.
- Error budget posture: healthy.
- Health/readiness check: required before production auto-apply.
- Logs, dashboard, or alert route: audit log and operator-visible admin status.
- Smoke command or manual smoke: required per driver before release.
- Smoke command or manual smoke: `php artisan updates:confirm` is available
  after a triggered deployment restarts and runs DB/cache/queue readiness.
- Smoke command or manual smoke: Coolify rollout evidence must include
  `updates:apply --force`, `updates:confirm`, deployment history, and
  post-deploy smoke results.
- Rollback or disable path: admin setting, environment kill switch, driver
  rollback/recovery instructions.

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes.
- Data classification: deployment secrets, release metadata, installation
  status, admin settings.
- Trust boundaries: browser to admin backend, backend to release manifest,
  backend to deployment platform or filesystem.
- Permission or ownership checks: existing `manage-settings` permission or a
  stricter update-specific permission if introduced.
- Abuse cases: malicious manifest, checksum mismatch, secret leakage, forced
  downgrade, unauthorized update trigger, partial file replacement.
- Secret handling: env-only secrets, never included in Inertia props or logs.
- Security tests or scans: required during implementation.
- Fail-closed behavior: missing integrity proof, unsupported driver, or
  high-risk release prevents automatic application.
- Residual risk: archive and Git drivers need careful staging and rollback
  implementation before production enablement.

## Architecture Evidence
- Architecture source reviewed: `docs/architecture/README.md`,
  `docs/architecture/system-architecture.md`,
  `docs/architecture/tech-stack.md`,
  `docs/architecture/system-update-manager-contract.md`
- Fits approved architecture: yes
- Mismatch discovered: yes, automatic updates were not previously documented
- Decision required from user: no, user requested this behavior be planned and
  recorded
- Approval reference if architecture changed: conversation on 2026-05-01
- Follow-up architecture doc updates: none pending for planning stage

## Deployment / Ops Evidence
- Deploy impact: high once implemented.
- Env or secret changes: update manifest URL, channel, auto-apply kill switch,
  Coolify webhook/API secret, optional driver configuration.
- Health-check impact: update apply requires health/readiness and post-deploy
  smoke evidence.
- Smoke steps updated: required during implementation.
- Rollback note: driver-specific rollback must be documented before production
  auto-apply.
- Observability or alerting impact: update status and audit logging required.
- Staged rollout or feature flag: auto-apply setting and environment kill
  switch.
- `DEPLOYMENT_GATE.md` reviewed: required during implementation.

## Review Checklist (mandatory)
- [x] Process self-audit completed before implementation.
- [x] Autonomous loop evidence covers all seven steps.
- [x] Exactly one priority task was completed in this planning iteration.
- [x] Operation mode was selected according to iteration rotation once
  execution starts.
- [x] Current stage is declared and respected.
- [x] Deliverable for the current stage is complete.
- [x] Architecture alignment confirmed.
- [x] Existing systems were reused where applicable.
- [x] No workaround paths were introduced.
- [x] No temporary solution was introduced.
- [x] No logic duplication was introduced.
- [x] Integration checklist evidence is attached where applicable.
- [x] AI testing evidence is attached where applicable.
- [x] Deployment gate evidence is attached where applicable.
- [x] Definition of Done evidence is attached.
- [x] Relevant validations were run or explicitly scoped out.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: continued the System Update Manager by closing the Docker/Git
  driver direction decision for v1.
- Files changed: `config/updates.php`,
  `app/Services/SystemUpdates/UpdateManager.php`,
  `app/Services/SystemUpdates/UpdateDriver.php`,
  `app/Services/SystemUpdates/Drivers/ManualUpdateDriver.php`,
  `app/Services/SystemUpdates/Drivers/FakeUpdateDriver.php`,
  `app/Console/Commands/CheckForApplicationUpdates.php`,
  `app/Console/Commands/ApplyApplicationUpdate.php`,
  `app/Console/Commands/ConfirmApplicationUpdate.php`,
  `app/Console/Commands/CheckOperationalHealth.php`,
  `app/Services/Operations/OperationalHealthChecker.php`,
  `app/Services/SystemUpdates/Drivers/ArchiveUpdateDriver.php`,
  `docs/operations/coolify-update-rollout-runbook.md`,
  `docs/operations/coolify-vps-deployment-contract.md`,
  `docs/operations/rollback-and-recovery.md`,
  `docs/operations/post-deploy-smoke.md`,
  `app/Http/Controllers/Admin/SettingController.php`,
  `resources/js/Pages/Admin/Settings/Index.vue`,
  `routes/admin.php`,
  `database/seeders/data/translations/settings.php`,
  `tests/Feature/Admin/SettingsManagementTest.php`,
  `tests/Feature/SystemUpdateCheckCommandTest.php`,
  `docs/planning/mvp-execution-plan.md`,
  `docs/planning/mvp-next-commits.md`,
  `docs/planning/tasks/FEA-015-system-update-manager.md`
- How tested: targeted PHPUnit feature tests for update commands and settings;
  `git diff --check`.
- What is incomplete: archive extraction, staging validation, switch execution,
  rollback execution, audit history UI, and captured staging/live Coolify
  rollout evidence.
- Next steps: enable/test PHP ZIP extraction staging validation without
  switching live files, or capture Coolify staging/live evidence using the
  runbook.
- Decisions made: first implementation slice remains manual-only for apply
  behavior and stores status in existing `settings` instead of creating a new
  model.
- Decisions made: Docker/Git runtime drivers are deferred from v1; Docker/Git
  deployments remain platform/operator-owned until dedicated contracts exist.

## Notes
The first production-ready auto-apply target should be Coolify because rollback
and deployment execution can be delegated to the platform. The archive driver is
required for shared hosting support but must be treated as higher risk until
staging, checksum verification, local-state preservation, and rollback are
proven.
