# Open Decisions

## Decision Item
- ID: DEC-001
- Context: Public dynamic routes exist in controller logic but are not fully exposed.
- Options:
  - A) expose all dynamic routes immediately
  - B) expose incrementally behind validation gates
- Recommendation: B
- Owner: Backend Builder + QA/Test
- Due date: 2026-03-31
- Status: OPEN

## Decision Item
- ID: DEC-002
- Context: Category/taxonomy route and model alignment has historical inconsistencies.
- Options:
  - A) complete missing implementation
  - B) remove stale route contracts
  - C) maintain partial placeholder state
- Recommendation: A or B after audit evidence
- Owner: Backend Builder
- Due date: 2026-04-05
- Status: OPEN

## Decision Item
- ID: DEC-003
- Context: Admin authentication model for the new registration flow is undefined.
- Options:
  - A) public self-registration
  - B) invite-only registration
  - C) registration disabled (admin-created accounts only)
- Recommendation: B for security and controlled onboarding
- Owner: Product + Security
- Due date: 2026-03-29
- Status: CLOSED
- Decision: Admin registration is not public; admin accounts are created by existing admins from the admin panel.

## Decision Item
- ID: DEC-004
- Context: Installation flow delivery mode is not finalized.
- Options:
  - A) web installer only
  - B) CLI installer only
  - C) hybrid (CLI bootstrap + web finalize)
- Recommendation: C for operational flexibility
- Owner: Ops/Release + Backend Builder
- Due date: 2026-03-29
- Status: CLOSED
- Decision: Web installer is the primary path for fresh setup (DB + language first-run wizard). CLI installer is deferred as optional follow-up.

## Decision Item
- ID: DEC-005
- Context: Global search architecture and v1 entity scope are not finalized.
- Options:
  - A) navbar dropdown only, entities: pages/posts/projects
  - B) command palette only, entities: pages/posts/projects/media/templates
  - C) both UIs with staged entity rollout
- Recommendation: A first, then iterate to C
- Owner: Product + Frontend Builder + Backend Builder
- Due date: 2026-03-30
- Status: CLOSED
- Decision: Start with navbar dropdown search. v1 entities are base-module content types (pages/posts/projects and compatible inherited modules).

## Decision Item
- ID: DEC-006
- Context: Block extensibility model (module-scoped blocks vs composed/user blocks) needs one shared contract.
- Options:
  - A) strict module allowlist registry
  - B) inheritance-first with module overrides
  - C) hybrid (core allowlist + composed block scopes)
- Recommendation: C
- Owner: Architecture + Backend Builder + Frontend Builder
- Due date: 2026-04-02
- Status: CLOSED
- Decision: Use inheritance + override for module-scoped blocks. User-created composed blocks are global.

## Decision Item
- ID: DEC-007
- Context: Brand/system-page fallback behavior is unclear when home page is non-public or scheduled.
- Options:
  - A) temporary 302 redirect to configurable "coming soon" page
  - B) render dedicated maintenance/coming-soon template with optional countdown
  - C) serve 404/503 depending on status mapping
- Recommendation: B with explicit status rules
- Owner: Product + Ops/Release
- Due date: 2026-04-01
- Status: CLOSED
- Decision: If configured home page is non-public and unscheduled, serve 404. If scheduled, render a Coming Soon page with countdown to publication.

## Decision Item
- ID: DEC-008
- Context: Autosave conflict behavior in Block Builder is not finalized.
- Options:
  - A) last-write-wins (newest save overwrites previous state)
  - B) optimistic lock with conflict prompt and compare/reload choice
- Recommendation: B for safer editorial workflows
- Owner: Product + Frontend Builder + Backend Builder
- Due date: 2026-03-30
- Status: CLOSED
- Decision: Use optimistic lock with conflict prompt that allows compare and reload while keeping local draft; no overwrite path in conflict modal.
