# TASK_BOARD

Last updated: 2026-05-01

## READY

- [ ] (none)

## BACKLOG

- [ ] (none)

## IN_PROGRESS

- [ ] (none)

## BLOCKED

- [ ] FEA-015 Implement archive/Docker/Git update drivers and Coolify rollout hardening
  - Status: BLOCKED
  - Owner: Backend Builder
  - Depends on: FEA-015P
  - Priority: P1
  - Blocker: Coolify staging/live rollout evidence requires an external
    configured Coolify environment that is not available in this local
    workspace.
  - Done when:
    - Coolify staging/live rollout evidence is captured from the runbook
    - deployment gate evidence is attached for the target environment

## DONE

- [x] FEA-019 Project category fallback backfill and removal plan
- [x] FEA-012 Residual legacy docs normalization
- [x] FEA-018 Decide project category compatibility retirement path
- [x] FEA-017 Decide and harden forms/templates admin ownership contract
- [x] FEA-011 Module contract audit for pages/posts/projects/forms/templates
- [x] FEA-016 Remove legacy project category authoring from admin project surfaces
- [x] FEA-014 Use taxonomy-backed project presentation in public runtime
- [x] FEA-013 Restrict V1 public taxonomy archives to posts
- [x] FEA-010 Category/taxonomy alignment decision
- [x] FEA-001 Finalize public dynamic routes for page/post/project
- [x] FEA-015P Record Coolify rollout evidence blocker for v1 gate
- [x] FEA-015O Add archive rollback command from recorded backup
- [x] FEA-015N Add gated archive live switch with backup and preserve paths
- [x] FEA-015M Add archive switch and rollback plan evidence
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
