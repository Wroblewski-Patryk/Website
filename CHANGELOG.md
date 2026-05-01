# Featherly Changelog

This file records project-level release notes only. Framework upstream release
notes belong in dependency upgrade evidence, not in the repository root
changelog.

## Unreleased

### Stabilization

- Reconciled public routing, taxonomy, module ownership, and project category
  compatibility documentation with the current implementation.
- Confirmed localized public page, post, and project resolution through named
  `/{locale}` routes.
- Confirmed V1 public taxonomy archive URLs are posts-only.
- Confirmed project category presentation is taxonomy-first with a read-only
  legacy fallback for existing records.
- Confirmed forms and templates are settings-owned admin modules with dedicated
  FormRequests and `manage-settings` authorization.

### Operations

- System Update Manager v1 is locally verified for update discovery, safe driver
  gating, Coolify trigger/confirmation contracts, archive
  verify/stage/switch/rollback, and Docker/Git v1 deferral.
- Production readiness for Coolify-triggered updates remains blocked until
  staging/live rollout evidence is captured in a configured external
  environment.
