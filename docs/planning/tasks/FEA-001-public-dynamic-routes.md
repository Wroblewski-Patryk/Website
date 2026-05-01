# Task

## Header
- ID: FEA-001
- Title: Finalize public dynamic routes for page/post/project
- Task Type: feature
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: none
- Priority: P1
- Coverage Ledger Rows:

## Context
Public content routing is locale-prefixed and must preserve a single resolver
contract where archive pages configured in settings remain the source of truth
for post and project detail paths.

## Goal
Make the public routing entrypoints explicit, keep page/post/project resolution
aligned with the architecture contract, and attach regression evidence.

## Scope
- `routes/public.php`
- `tests/Feature/PublicRouteContractTest.php`
- `docs/architecture/seo-route-contracts.md`
- `.codex/context/TASK_BOARD.md`
- `.codex/context/PROJECT_STATE.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/mvp-execution-plan.md`

## Implementation Plan
1. Inspect the public route topology, resolver behavior, and locale contract.
2. Expose the localized public entrypoints explicitly in routing without
   hardcoding archive slugs.
3. Add feature coverage for page, post, and project resolution through the
   named public resolver route.
4. Synchronize architecture and planning/context files with the chosen
   contract and evidence.

## Acceptance Criteria
- [x] Public localized entrypoints are explicit in `routes/public.php`.
- [x] Feature tests prove page, post, and project public paths resolve through
  the locale-aware contract.
- [x] Architecture/planning/context files describe the chosen resolver
  contract and completion evidence.

## Success Signal
- User or operator problem: public runtime route ownership is ambiguous.
- Expected product or reliability outcome: one documented locale-aware
  entrypoint for public content with stable regression coverage.
- How success will be observed: named route tests for home/page/post/project
  all pass.
- Post-launch learning needed: no

## Deliverable For This Stage
Verified routing contract, regression evidence, and synchronized docs/context.

## Constraints
- use existing systems and approved mechanisms
- preserve locale-aware route boundaries and the admin/public split
- preserve block-builder content contracts and shared admin form primitives
- keep translation and i18n scanning implications explicit
- do not introduce new structures without approval
- do not implement workarounds
- do not duplicate logic
- stay within the declared current stage unless explicit approval changes it
- no placeholders, mock-only paths, or temporary solutions in delivered
  behavior
- implement features as a vertical slice across UI, logic, API, DB, validation,
  error handling, and tests when the task affects runtime behavior

## Definition of Done
- [x] Code builds without errors.
- [x] Feature works manually through the real UI, API, CLI, or operator path.
- [x] No mock, placeholder, fake, or temporary data/path remains.
- [x] Full data flow works across all relevant layers.
- [x] Backend and UI/client error handling exists where applicable.
- [x] No existing functionality is broken.
- [x] Feature works after restart, reload, or navigation refresh where
  applicable.
- [x] Changes are documented in the relevant source of truth.
- [x] Behavior is reproducible from the evidence recorded below.
- [x] Success signal, reliability, security, and rollback evidence are recorded
  when applicable.
- [x] `DEFINITION_OF_DONE.md` was checked before status changed to `DONE`.

## Stage Exit Criteria
- [x] The output matches the declared `Current Stage`.
- [x] Work from later stages was not mixed in without explicit approval.
- [x] Risks and assumptions for this stage are stated clearly.

## Forbidden
- new systems without approval
- duplicated logic or parallel implementations of the same contract
- temporary bypasses, hacks, or workaround-only paths
- architecture changes without explicit approval
- implicit stage skipping

## Validation Evidence
- Tests: `php artisan test --filter=PublicRouteContractTest`
- Manual checks: route contract reviewed against `bootstrap/app.php` and
  `PageController`.
- Screenshots/logs: not applicable
- High-risk checks: post/project archive delegation still uses settings-backed
  archive pages instead of hardcoded slugs.
- Coverage ledger updated: not applicable
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes
- Real API/service path used: yes
- Endpoint and client contract match: yes
- DB schema and migrations verified: not applicable
- Loading state verified: not applicable
- Error state verified: yes
- Refresh/restart behavior verified: yes
- Regression check performed: localized home/page/post/project request matrix

## Product / Discovery Evidence
- Problem validated: yes
- User or operator affected: operators maintaining public SEO/runtime contracts
- Existing workaround or pain: routing ownership was implicit and easy to
  regress.
- Smallest useful slice: explicit named entrypoints plus feature coverage.
- Success metric or signal: tests prove the public resolver contract.
- Feature flag, staged rollout, or disable path: not applicable
- Post-launch feedback or metric check:

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: not applicable
- Critical user journey: localized public page/detail resolution
- SLI: successful 200/404 resolution by locale-aware path
- SLO: not applicable
- Error budget posture: not applicable
- Health/readiness check: test suite for routing contract
- Logs, dashboard, or alert route: not applicable
- Smoke command or manual smoke: targeted feature test
- Rollback or disable path: revert `routes/public.php` contract to previous
  catch-all only shape

## AI Testing Evidence (required for AI features)
- `AI_TESTING_PROTOCOL.md` reviewed: not applicable
- Memory consistency scenarios:
- Multi-step context scenarios:
- Adversarial or role-break scenarios:
- Prompt injection checks:
- Data leakage and unauthorized access checks:
- Result:

## Security / Privacy Evidence
- `docs/security/secure-development-lifecycle.md` reviewed: yes
- Data classification: public content metadata and routing
- Trust boundaries: public localized routes into Laravel controller boundary
- Permission or ownership checks: preview route remains auth-protected; public
  resolver exposes published content only
- Abuse cases: no new bypass route for draft/admin content
- Secret handling: none
- Security tests or scans: targeted feature tests for published-path contract
- Fail-closed behavior: unresolved or non-public entities still return 404 via
  existing resolver behavior
- Residual risk: archive slug settings remain runtime-driven, so future changes
  still require regression tests

## Architecture Evidence (required for architecture-impacting tasks)
- Architecture source reviewed: `docs/architecture/architecture-source-of-truth.md`, `docs/architecture/system-architecture.md`, `docs/architecture/seo-route-contracts.md`
- Fits approved architecture: yes
- Mismatch discovered: no
- Decision required from user: no
- Approval reference if architecture changed:
- Follow-up architecture doc updates: clarified the single named public content
  resolver route

## UX/UI Evidence (required for UX tasks)
- Design source type:
- Design source reference:
- Canonical visual target:
- Fidelity target:
- Evidence-driven UX review used:
- Primary user question answered within 3 seconds:
- Next action visibility:
- Blocked-state visibility:
- Stitch used:
- Stitch artifact reference (if used):
- Experience-quality bar reviewed:
- Visual-direction brief reviewed:
- Existing shared pattern reused:
- New shared pattern introduced:
- Design-memory entry reused:
- Design-memory update required:
- Pattern-gallery reference:
- Visual gap audit completed:
- Background or decorative asset strategy:
- Canonical asset extraction required:
- Screenshot comparison pass completed:
- Remaining mismatches:
- Anti-patterns checked:
- Screen-quality checklist reviewed:
- UI scorecard used:
- Surface strategy checked:
- State checks:
- Feedback locality checked:
- Raw technical errors hidden from end users:
- Responsive checks:
- Input-mode checks:
- Accessibility checks:
- Parity evidence:

## Deployment / Ops Evidence (required for runtime or infra tasks)
- Deploy impact: low
- Env or secret changes: none
- Health-check impact: none
- Smoke steps updated: no
- Rollback note: restore previous catch-all shape if route naming causes an
  unexpected integration issue
- Observability or alerting impact: none
- Staged rollout or feature flag: not applicable
- `DEPLOYMENT_GATE.md` reviewed: yes

## Review Checklist (mandatory)
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
- [x] Relevant validations were run.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: explicit localized public entrypoints were named and covered by
  feature tests for home/page/post/project resolution.
- Files changed: `routes/public.php`, `tests/Feature/PublicRouteContractTest.php`,
  `docs/architecture/seo-route-contracts.md`, `docs/planning/tasks/FEA-001-public-dynamic-routes.md`,
  `.codex/context/TASK_BOARD.md`, `.codex/context/PROJECT_STATE.md`,
  `docs/planning/mvp-next-commits.md`, `docs/planning/mvp-execution-plan.md`
- How tested: `php artisan test --filter=PublicRouteContractTest`
- What is incomplete: taxonomy/module audit and category alignment remain after
  route finalization
- Next steps: move to FEA-010 or derive the next tiny backlog item from the
  scaling roadmap
- Decisions made: keep a single public content resolver route so archive page
  settings remain authoritative for post/project URL shapes

## Notes
No architecture deviation was required; the task documents the chosen explicit
resolver contract rather than hardcoding archive prefixes into route
definitions.
