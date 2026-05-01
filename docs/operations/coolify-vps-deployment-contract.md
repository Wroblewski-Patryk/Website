# Coolify VPS Deployment Contract

Featherly uses this file to make the deployment target explicit. Coolify on a
VPS is the default contract shape until the project records a different
production target in `.codex/context/PROJECT_STATE.md`.

## Deployment Target

- VPS provider:
- Coolify project or environment:
- Public domains:
- Private services:

## Runtime Inventory

- Main app services: Laravel application runtime, Vite-built public/admin
  assets
- Worker or cron services:
- Databases:
- Cache or queue:
- Persistent volumes:

## Required Artifacts

- Dockerfile paths:
- Compose or service-definition paths:
- Env example files: `.env.example`
- Health or readiness endpoints:
- Migration entrypoint:

## Env And Secrets Contract

- Which env files exist:
- Which values must come from Coolify secrets:
- Which values are safe to keep in examples:
- Who owns secret rotation:

## Release Requirements

- Required checks before deploy:
- Required smoke checks after deploy:
- Rollback trigger:
- Rollback method:

## Automatic Updates

- Coolify is the first supported automatic update driver for VPS deployments.
- Featherly owns update discovery, admin settings, status, and audit evidence.
- Coolify owns deployment execution through a configured webhook or API path.
- The webhook secret must live in the hosting environment and must never be
  exposed to the browser.
- Operator rollout, evidence, confirmation, and failure handling must follow
  `docs/operations/coolify-update-rollout-runbook.md` before the driver is
  treated as production-ready.
- Installations outside Coolify must use the driver contract in
  `docs/architecture/system-update-manager-contract.md`.

## Data Safety

- Backup strategy:
- Restore verification expectation:
- Risky migration policy:
