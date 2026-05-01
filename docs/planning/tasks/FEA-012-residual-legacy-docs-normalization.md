# Task

## Header
- ID: FEA-012
- Title: Residual legacy docs normalization
- Task Type: research
- Current Stage: post-release
- Status: DONE
- Owner: Product Docs
- Depends on: FEA-011
- Priority: P3
- Coverage Ledger Rows: not applicable
- Iteration: 12
- Operation Mode: BUILDER

## Process Self-Audit
- [x] All seven autonomous loop steps are planned.
- [x] No loop step is being skipped.
- [x] Exactly one priority task is selected.
- [x] Operation mode matches the iteration number.
- [x] The task is aligned with repository source-of-truth documents.

## Context

After module ownership and project category compatibility were settled, root
documentation still contained stale Laravel/template-era content and outdated
project status notes.

## Goal

Make residual root docs either current Featherly orientation material or
explicitly deprecated template artifacts.

## Scope

- Root orientation and release-note docs.
- Docs index and high-level product overview.
- Task board, project state, and planning evidence.
- No runtime code changes.

## Implementation Plan

1. Inspect root docs and identify stale/template-era content.
2. Keep canonical guardrail docs in place.
3. Replace upstream/framework release notes with project release notes.
4. Refresh README/product overview status to match verified architecture.
5. Record task evidence and update active queue.

## Autonomous Loop Evidence

### 1. Analyze Current State
- Issues: README still listed public dynamic routing and category validation as
  known gaps; CHANGELOG was Laravel skeleton release notes.
- Gaps: docs index did not list the newest current implementation/category
  policy documents.
- Inconsistencies: NEW_PROJECT_BOOTSTRAP was deprecated but could be mistaken
  for an active checklist.
- Architecture constraints: canonical root guardrails must remain available.

### 2. Select One Priority Task
- Selected task: FEA-012 residual legacy docs normalization.
- Priority rationale: it was the first READY local task after FEA-018.
- Why other candidates were deferred: FEA-015 requires external Coolify
  evidence; FEA-019 is a later DB/data planning task.

### 3. Plan Implementation
- Files or surfaces to modify: README, CHANGELOG, NEW_PROJECT_BOOTSTRAP,
  docs index, overview/product docs, task/context/planning files.
- Logic: update stale docs, deprecate template artifact, avoid moving canonical
  root guardrails.
- Edge cases: preserve critical root files referenced by AGENTS and delivery
  policy.

### 4. Execute Implementation
- Implementation notes: root CHANGELOG now records Featherly changes only;
  README reflects the current localized routing/category/update-manager state.

### 5. Verify and Test
- Validation performed:
  - `git diff --check`
  - documentation/code-read review
- Result: passed.

### 6. Self-Review
- Simpler option considered: delete legacy root docs outright.
- Technical debt introduced: no
- Scalability assessment: root docs now point into canonical docs instead of
  competing with them.
- Refinements made: kept the bootstrap artifact but labeled it deprecated to
  avoid breaking references.

### 7. Update Documentation and Knowledge
- Docs updated:
  - `README.md`
  - `CHANGELOG.md`
  - `NEW_PROJECT_BOOTSTRAP.md`
  - `docs/README.md`
  - `docs/overview.md`
  - `docs/product/product.md`
  - `.codex/context/PROJECT_STATE.md`
  - `.codex/context/TASK_BOARD.md`
  - `docs/planning/mvp-next-commits.md`
  - `docs/planning/mvp-execution-plan.md`
- Context updated: yes
- Learning journal updated: not applicable

## Acceptance Criteria
- [x] Legacy root docs are migrated or explicitly deprecated.
- [x] README status no longer contradicts verified architecture.
- [x] Project-level changelog no longer contains upstream Laravel skeleton
  release notes.

## Success Signal
- User or operator problem: returning to the project no longer starts from
  stale root guidance.
- Expected product or reliability outcome: docs orientation points to current
  source-of-truth files.
- How success will be observed: root docs and docs index describe current
  routing/category/update-manager state.
- Post-launch learning needed: no

## Deliverable For This Stage

Normalized documentation and updated task/state evidence.

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

## Validation Evidence
- Tests: not applicable, docs-only
- Manual checks: root docs and docs index reviewed
- Screenshots/logs: not applicable
- High-risk checks: verified canonical root guardrail docs were not removed
- Coverage ledger updated: not applicable
- Coverage rows closed or changed: not applicable

## Integration Evidence
- `INTEGRATION_CHECKLIST.md` reviewed: yes
- Real API/service path used: not applicable
- Endpoint and client contract match: not applicable
- DB schema and migrations verified: not applicable
- Loading state verified: not applicable
- Error state verified: not applicable
- Refresh/restart behavior verified: not applicable
- Regression check performed: docs diff review

## Architecture Evidence (required for architecture-impacting tasks)
- Architecture source reviewed:
  - `docs/architecture/current-implementation-map.md`
  - `docs/architecture/project-category-compatibility-policy.md`
- Fits approved architecture: yes
- Mismatch discovered: no
- Decision required from user: no
- Approval reference if architecture changed: not applicable
- Follow-up architecture doc updates: not applicable

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
- Task summary: normalized stale root documentation after routing/category/module
  decisions.
- Files changed:
  - `README.md`
  - `CHANGELOG.md`
  - `NEW_PROJECT_BOOTSTRAP.md`
  - `docs/README.md`
  - `docs/overview.md`
  - `docs/product/product.md`
  - `.codex/context/TASK_BOARD.md`
  - `.codex/context/PROJECT_STATE.md`
  - `docs/planning/mvp-next-commits.md`
  - `docs/planning/mvp-execution-plan.md`
  - `docs/planning/tasks/FEA-012-residual-legacy-docs-normalization.md`
- How tested: `git diff --check` and docs review.
- What is incomplete: Coolify rollout evidence remains external; FEA-019 is
  still a later data-planning item.
- Next steps: refill local work from scaling backlog or plan FEA-019.
- Decisions made: keep canonical root guardrails; replace template-era
  changelog content.
