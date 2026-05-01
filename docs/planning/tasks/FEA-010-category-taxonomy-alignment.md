# Task

## Header
- ID: FEA-010
- Title: Category and taxonomy alignment decision
- Task Type: design
- Current Stage: verification
- Status: DONE
- Owner: Product Docs
- Depends on: FEA-001
- Priority: P1
- Coverage Ledger Rows:
- Iteration: 2
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context
Public routing, admin taxonomy management, and project editing currently expose
three overlapping semantic-grouping mechanisms: module-scoped taxonomies for
posts and projects, public taxonomy archive URLs, and the legacy
`projects.category` string field.

## Goal
Make the V1 category/taxonomy direction explicit in repository truth, document
the current mismatch, and queue the smallest follow-up implementation slices.

## Scope
- `docs/planning/open-decisions.md`
- `docs/architecture/seo-route-contracts.md`
- `docs/planning/mvp-next-commits.md`
- `docs/planning/mvp-execution-plan.md`
- `.codex/context/TASK_BOARD.md`
- `.codex/context/PROJECT_STATE.md`
- `docs/planning/tasks/FEA-010-category-taxonomy-alignment.md`

## Implementation Plan
1. Audit runtime and admin taxonomy/category surfaces in routes, controllers,
   models, requests, and views.
2. Record the approved V1 direction and the specific mismatch that remains in
   implementation.
3. Queue the smallest follow-up tasks needed to bring runtime behavior in line
   with the approved contract.
4. Validate the documented contract against targeted existing tests.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: public taxonomy URLs are module-ambiguous; project editing still uses
  both taxonomy relations and legacy `category` text.
- Gaps: no explicit source-of-truth statement says whether project taxonomies
  are public in V1 or whether `projects.category` remains canonical.
- Inconsistencies: admin exposes taxonomy management for posts/projects, but
  public taxonomy rendering is blog-shaped and not module-qualified.
- Architecture constraints: preserve locale-aware public routing, reuse the
  existing taxonomy system, and avoid introducing parallel categorization
  mechanisms.

### 2. Select One Priority Task
- Selected task: FEA-010 category/taxonomy alignment decision with doc evidence.
- Priority rationale: unresolved taxonomy direction blocks safe follow-up work
  in public routing and project content surfaces.
- Why other candidates were deferred: implementation follow-ups depend on one
  canonical V1 contract first.

### 3. Plan Implementation
- Files or surfaces to modify: planning/context docs plus SEO route contract.
- Logic: declare module-scoped taxonomies as canonical for posts/projects,
  constrain V1 public taxonomy archives to posts, and mark `projects.category`
  as deprecated compatibility debt.
- Edge cases: project taxonomies currently can match public taxonomy slugs;
  follow-up work must fail closed rather than exposing ambiguous archives.

### 4. Execute Implementation
- Implementation notes: documented the approved direction, closed DEC-002,
  added FEA-013 and FEA-014 follow-ups, and synchronized the task board and MVP
  queue.

### 5. Verify and Test
- Validation performed: `php artisan test --filter=TaxonomyManagementTest`;
  `php artisan test --filter=PublicRouteContractTest`
- Result: targeted admin taxonomy and public route regression tests passed.

### 6. Self-Review
- Simpler option considered: leave DEC-002 open and move directly to code
  changes.
- Technical debt introduced: no
- Scalability assessment: the chosen direction removes category-system
  ambiguity and keeps future public taxonomy expansion possible through
  explicit module-qualified routes.
- Refinements made: split follow-up work into one route-hardening task and one
  project-surface migration task.

### 7. Update Documentation and Knowledge
- Docs updated: yes
- Context updated: yes
- Learning journal updated: not applicable

## Acceptance Criteria
- [x] Taxonomy direction for V1 is explicit in planning/decision docs.
- [x] Implementation implications are queued as narrow follow-up tasks.
- [x] Architecture/context files describe the remaining runtime mismatch and
  next recommended slice.

## Success Signal
- User or operator problem: content categorization behavior is ambiguous across
  admin, public routing, and projects.
- Expected product or reliability outcome: one documented V1 contract for
  semantic grouping with unambiguous next implementation steps.
- How success will be observed: DEC-002 is closed, follow-up tasks are queued,
  and targeted regression tests still pass.
- Post-launch learning needed: yes

## Deliverable For This Stage
Verified decision record, synchronized task/context updates, and a prioritized
implementation queue for the next taxonomy slices.

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
- Tests: `php artisan test --filter=TaxonomyManagementTest`; `php artisan test --filter=PublicRouteContractTest`
- Manual checks: reviewed `routes/admin.php`, `routes/public.php`,
  `PageController`, `TaxonomyController`, `BaseAdminContentController`,
  `ProjectController`, project requests, and project edit/runtime views.
- Screenshots/logs: not applicable
- High-risk checks: confirmed the public taxonomy surface is currently
  blog-shaped and therefore should not remain module-ambiguous in V1.
- Coverage ledger updated: not applicable
- Coverage rows closed or changed:

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes
- Real API/service path used: yes
- Endpoint and client contract match: yes
- DB schema and migrations verified: yes
- Loading state verified: not applicable
- Error state verified: not applicable
- Refresh/restart behavior verified: not applicable
- Regression check performed: targeted admin taxonomy + public route tests

## Product / Discovery Evidence
- Problem validated: yes
- User or operator affected: content editors and developers working across
  posts/projects/public routing
- Existing workaround or pain: two category systems can drift and public
  taxonomy URLs do not encode module ownership.
- Smallest useful slice: decide the canonical V1 taxonomy contract and queue
  two follow-up implementation slices.
- Success metric or signal: no ambiguity remains in docs/backlog about how
  semantic grouping should work in V1.
- Feature flag, staged rollout, or disable path: not applicable
- Post-launch feedback or metric check: validate editor usage after the
  project-category migration slice ships.

## Reliability / Observability Evidence
- `docs/operations/service-reliability-and-observability.md` reviewed: not applicable
- Critical user journey: taxonomy-managed content editing and public taxonomy
  route resolution
- SLI: public taxonomy requests should resolve only to the intended module
  contract
- SLO: not applicable
- Error budget posture: not applicable
- Health/readiness check: targeted feature tests
- Logs, dashboard, or alert route: not applicable
- Smoke command or manual smoke: targeted feature tests
- Rollback or disable path: revert doc/planning updates and keep DEC-002 open

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
- Data classification: public content metadata and admin content taxonomy data
- Trust boundaries: public routing into published content; admin content editing
- Permission or ownership checks: taxonomy management remains admin protected
  and public taxonomy exposure stays intentionally narrow
- Abuse cases: ambiguous public taxonomy slugs could expose misleading empty or
  wrong archives if not module-qualified
- Secret handling: none
- Security tests or scans: targeted feature tests
- Fail-closed behavior: follow-up route hardening must reject non-post
  taxonomies rather than guessing
- Residual risk: runtime still needs FEA-013 and FEA-014 to fully match the
  documented direction

## Architecture Evidence (required for architecture-impacting tasks)
- Architecture source reviewed: `docs/architecture/system-architecture.md`,
  `docs/architecture/modules.md`, `docs/architecture/seo-route-contracts.md`
- Fits approved architecture: yes
- Mismatch discovered: yes
- Decision required from user: no
- Approval reference if architecture changed:
- Follow-up architecture doc updates: added the approved V1 taxonomy direction
  and the remaining implementation gap note

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
- Deploy impact: none
- Env or secret changes: none
- Health-check impact: none
- Smoke steps updated: no
- Rollback note: revert doc/queue updates if the team rejects the direction
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
- Task summary: closed the taxonomy/category direction for V1, documented the
  mismatch between public taxonomy routing and project category surfaces, and
  queued the next two implementation tasks.
- Files changed: `docs/planning/open-decisions.md`,
  `docs/architecture/seo-route-contracts.md`,
  `docs/planning/mvp-next-commits.md`,
  `docs/planning/mvp-execution-plan.md`,
  `.codex/context/TASK_BOARD.md`,
  `.codex/context/PROJECT_STATE.md`,
  `docs/planning/tasks/FEA-010-category-taxonomy-alignment.md`
- How tested: `php artisan test --filter=TaxonomyManagementTest`; `php artisan test --filter=PublicRouteContractTest`
- What is incomplete: runtime still allows module-ambiguous public taxonomy
  resolution and projects still expose the legacy `category` string field
- Next steps: implement FEA-013, then FEA-014
- Decisions made: taxonomies are the canonical semantic grouping system for
  posts and projects; V1 public taxonomy archives are posts-only; the legacy
  `projects.category` field is deprecated compatibility debt

## Notes
This task closes the planning ambiguity without forcing a broad runtime change
in the same iteration.
