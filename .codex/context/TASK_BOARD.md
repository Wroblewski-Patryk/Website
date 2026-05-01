# TASK_BOARD

Last updated: 2026-05-01

## READY

- [ ] FEA-015 Implement archive/Docker/Git update drivers and Coolify rollout hardening
  - Status: READY
  - Owner: Backend Builder
  - Depends on: FEA-015L
  - Priority: P1
  - Done when:
    - archive apply execution has switch/rollback tests
    - Coolify staging/live rollout evidence is captured from the runbook
    - Docker/Git follow-up contracts are queued only if v2 needs runtime drivers
    - automatic application is blocked unless a safe driver passes preflight
    - deployment, rollback, security, and smoke docs are synchronized

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

- [x] FEA-015L Add no-switch archive extraction staging validation
- [x] FEA-015K Defer Docker/Git runtime drivers from System Update Manager v1
- [x] FEA-015J Add archive extraction runtime capability gate
- [x] FEA-015I Add no-switch archive download and SHA-256 verifier
- [x] FEA-015H Add archive release integrity metadata preflight gate
- [x] FEA-015G Add Coolify update rollout evidence runbook
- [x] FEA-015F Gate post-deploy confirmation on operational health checks
- [x] FEA-015E Add post-deploy version confirmation for Coolify-triggered updates
- [x] FEA-015D Add gated Coolify apply trigger test path
- [x] FEA-015C Implement production driver preflight status
- [x] FEA-015B Implement manual/fake System Update Manager apply contract
- [x] FEA-015A Implement verified System Update Manager update-check baseline
- [x] DOC-ARCH-001 Synchronize architecture folder with current implementation map
- [x] DOC-001 Migrate Featherly docs and agent files to template-aligned structure with project-specific content
- [x] FEX-001..FEX-080 Prior feature execution waves completed and recorded in docs/project state
