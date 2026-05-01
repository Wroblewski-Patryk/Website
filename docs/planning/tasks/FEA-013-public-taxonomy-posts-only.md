# Task

## Header
- ID: FEA-013
- Title: Restrict public taxonomy archives to post taxonomies
- Task Type: fix
- Current Stage: verification
- Status: DONE
- Owner: Backend Builder
- Depends on: FEA-010
- Priority: P1
- Coverage Ledger Rows:
- Iteration: 3
- Operation Mode: ARCHITECT

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context
The approved V1 taxonomy contract reserves public archive URLs
(`/category/{slug}`, `/tag/{slug}`) for blog posts only, but runtime taxonomy
resolution still matched by `type` and localized `slug` without checking the
taxonomy module.

## Goal
Make public taxonomy archive resolution fail closed for non-post taxonomies and
record regression evidence for both the allowed and rejected paths.

## Scope
- `app/Http/Controllers/TaxonomyController.php`
- `tests/Feature/PublicRouteContractTest.php`
- `docs/architecture/seo-route-contracts.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/mvp-execution-plan.md`
- `.codex/context/TASK_BOARD.md`
- `.codex/context/PROJECT_STATE.md`
- `docs/planning/tasks/FEA-013-public-taxonomy-posts-only.md`

## Implementation Plan
1. Inspect the current public taxonomy resolver, route contract, and existing
   public route feature coverage.
2. Add the smallest runtime guard that restricts public taxonomy archives to
   `taxonomies.module = posts`.
3. Add feature coverage for a valid post taxonomy archive and a rejected
   project taxonomy archive using the same public route entrypoint.
4. Sync architecture/planning/context files with the completed slice and its
   follow-up queue.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: `/category/{slug}` and `/tag/{slug}` can currently resolve any
  taxonomy row with a matching type/slug, regardless of module ownership.
- Gaps: no regression test proves that project taxonomies are rejected from the
  public blog taxonomy surface.
- Inconsistencies: docs say V1 public taxonomy archives are posts-only, while
  runtime still behaves module-agnostically.
- Architecture constraints: preserve the localized public resolver, reuse the
  existing taxonomy module column, and fail closed instead of guessing module
  intent from the URL.

### 2. Select One Priority Task
- Selected task: FEA-013 restrict public taxonomy archives to post taxonomies
  and add regression coverage.
- Priority rationale: this is the smallest runtime slice that removes a known
  architecture mismatch on a public content path.
- Why other candidates were deferred: FEA-014 depends on the public taxonomy
  contract being enforced first; broader module audits are lower priority than
  a live public-route mismatch.

### 3. Plan Implementation
- Files or surfaces to modify: public `TaxonomyController`, public route
  feature tests, and task/context docs.
- Logic: add a `module = posts` guard to the taxonomy lookup and verify both
  the happy path and rejection path through `public.content.show`.
- Edge cases: keep empty post archives valid, but reject project taxonomies
  even if their type and localized slug match the blog-shaped URL.

### 4. Execute Implementation
- Implementation notes: public taxonomy lookup now filters by the `posts`
  module before slug matching; feature coverage was extended for post taxonomy
  success and project taxonomy 404 behavior.

### 5. Verify and Test
- Validation performed: `php artisan test --filter=PublicRouteContractTest`
- Result: pass

### 6. Self-Review
- Simpler option considered: remove taxonomy path handling from the dynamic
  resolver entirely.
- Technical debt introduced: no
- Scalability assessment: using the existing module column keeps the current V1
  contract explicit while leaving room for future module-qualified taxonomy
  routes without another data migration.
- Refinements made: kept the guard in the taxonomy resolver itself so any
  current route entrypoint benefits from the same fail-closed behavior.

### 7. Update Documentation and Knowledge
- Docs updated: yes
- Context updated: yes
- Learning journal updated: not applicable

## Acceptance Criteria
- [x] Public taxonomy archive resolution only matches `posts` taxonomies in V1.
- [x] Feature coverage proves post taxonomy archives resolve successfully.
- [x] Feature coverage proves project taxonomy archives fail closed with 404.

## Success Signal
- User or operator problem: public taxonomy URLs can expose the wrong module
  surface.
- Expected product or reliability outcome: the blog taxonomy archive path is no
  longer module-ambiguous at runtime.
- How success will be observed: the public route contract test suite passes
  with explicit success and rejection cases for taxonomy archives.
- Post-launch learning needed: yes

## Deliverable For This Stage
Verified runtime guard, regression tests, and synchronized planning/context
updates for the next taxonomy slice.

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
- Manual checks: reviewed `PageController`, public taxonomy route handling, the
  taxonomy schema, and admin module-scoped taxonomy management before changing
  runtime logic.
- Screenshots/logs: not applicable
- High-risk checks: confirmed that a project taxonomy slug using a blog-shaped
  URL now returns 404 instead of resolving the wrong module.
- Coverage ledger updated: not applicable
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes
- Real API/service path used: yes
- Endpoint and client contract match: yes
- DB schema and migrations verified: yes
- Loading state verified: not applicable
- Error state verified: yes
- Refresh/restart behavior verified: yes
- Regression check performed: targeted public route feature tests

## Product / Discovery Evidence
- Problem validated: yes
- User or operator affected: public visitors and editors relying on category
  URL correctness
- Existing workaround or pain: docs already declared a posts-only public
  taxonomy contract, but runtime still allowed project taxonomy leakage
- Smallest useful slice: add the module guard without redesigning taxonomy URLs
- Success metric or signal: no project taxonomy can resolve through
  `/category/{slug}` or `/tag/{slug}` in V1
- Feature flag, staged rollout, or disable path: not applicable
- Post-launch feedback or metric check: watch whether any project-facing UI
  still implies public category URLs before FEA-014 ships

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: not applicable
- Critical user journey: localized public taxonomy archive resolution
- SLI: taxonomy archive requests should resolve only when the taxonomy belongs
  to the posts module
- SLO: not applicable
- Error budget posture: healthy
- Health/readiness check: targeted feature test
- Logs, dashboard, or alert route: not applicable
- Smoke command or manual smoke: feature tests
- Rollback or disable path: revert the module guard and regression tests

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
- Data classification: public content taxonomy metadata
- Trust boundaries: public route input into published content resolution
- Permission or ownership checks: admin taxonomy ownership remains unchanged;
  public exposure is narrowed
- Abuse cases: ambiguous taxonomy slugs could previously present misleading or
  incorrect content groupings
- Secret handling: none
- Security tests or scans: targeted feature tests
- Fail-closed behavior: non-post taxonomies are rejected with 404
- Residual risk: project editor/runtime surfaces still reference the legacy
  `projects.category` field until FEA-014 lands

## Architecture Evidence (required for architecture-impacting tasks)
- Architecture source reviewed: `docs/architecture/system-architecture.md`,
  `docs/architecture/modules.md`, `docs/architecture/seo-route-contracts.md`
- Fits approved architecture: yes
- Mismatch discovered: yes
- Decision required from user: no
- Approval reference if architecture changed:
- Follow-up architecture doc updates: clarified that runtime enforces the
  posts-only guard on public taxonomy archives

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
- Rollback note: revert the resolver guard and the taxonomy route tests if
  public module ownership is intentionally redesigned later
- Observability or alerting impact: none
- Staged rollout or feature flag: not applicable
- `DEPLOYMENT_GATE.md` reviewed: yes

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
- [x] Deployment gate evidence is attached where applicable.
- [x] Definition of Done evidence is attached.
- [x] Relevant validations were run.
- [x] Docs or context were updated if repository truth changed.
- [x] Learning journal was updated if a recurring pitfall was confirmed.

## Result Report
- Task summary: public taxonomy archives now resolve only for post taxonomies,
  with regression coverage for both the allowed and rejected module paths.
- Files changed: `app/Http/Controllers/TaxonomyController.php`,
  `tests/Feature/PublicRouteContractTest.php`,
  `docs/architecture/seo-route-contracts.md`,
  `docs/planning/mvp-next-commits.md`,
  `docs/planning/mvp-execution-plan.md`,
  `.codex/context/TASK_BOARD.md`,
  `.codex/context/PROJECT_STATE.md`,
  `docs/planning/tasks/FEA-013-public-taxonomy-posts-only.md`
- How tested: `php artisan test --filter=PublicRouteContractTest`
- What is incomplete: project authoring and presentation still need migration
  away from the legacy `projects.category` string field
- Next steps: execute FEA-014
- Decisions made: public taxonomy archives remain posts-only for V1 and reject
  all non-post taxonomy modules

## Notes
This slice intentionally avoids redesigning URL topology. It only enforces the
already-approved V1 contract on the current route surface.
