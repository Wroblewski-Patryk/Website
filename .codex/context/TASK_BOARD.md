# TASK_BOARD

Last updated: 2026-04-16

## READY

- [ ] FEA-001 Finalize public dynamic routes for page/post/project
  - Status: READY
  - Owner: Backend Builder
  - Depends on: none
  - Priority: P1
  - Done when:
    - routes are explicit in `routes/public.php`
    - localized behavior is covered by smoke or feature tests
    - docs and project state reflect the chosen route contract

## BACKLOG

- [ ] FEA-010 Category/taxonomy alignment decision
  - Status: BACKLOG
  - Owner: Product Docs Agent
  - Depends on: FEA-001
  - Priority: P1
  - Done when:
    - taxonomy direction is explicit in docs
    - implementation implications are queued as follow-up tasks

- [ ] FEA-011 Module contract audit for pages/posts/projects/forms/templates
  - Status: BACKLOG
  - Owner: Planning Agent
  - Depends on: FEA-001
  - Priority: P2
  - Done when:
    - contracts are checked against routes/controllers/models
    - mismatches are documented and queued

- [ ] FEA-012 Residual legacy docs normalization
  - Status: BACKLOG
  - Owner: Product Docs Agent
  - Depends on: FEA-011
  - Priority: P3
  - Done when:
    - legacy root docs are either migrated or explicitly deprecated

## IN_PROGRESS

- [ ] (none)

## BLOCKED

- [ ] (none)

## DONE

- [x] DOC-001 Migrate Featherly docs and agent files to template-aligned structure with project-specific content
- [x] FEX-001..FEX-080 Prior feature execution waves completed and recorded in docs/project state
