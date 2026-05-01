# Coolify Update Rollout Runbook

## Purpose

This runbook describes the first approved operator path for Featherly updates
that are triggered through the Coolify update driver.

The driver may trigger a Coolify deployment webhook, but Featherly must not
treat the update as complete until the running application reports the expected
version and passes operational readiness checks.

## Preconditions

- Coolify application and environment are identified in
  `docs/operations/coolify-vps-deployment-contract.md`.
- A trusted release manifest is configured through
  `FEATHERLY_UPDATE_MANIFEST_URL`.
- `APP_VERSION` is set by the deployed release artifact or runtime
  environment.
- `FEATHERLY_UPDATE_COOLIFY_WEBHOOK_URL` is configured as a secret in the
  hosting environment.
- `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED=true` is set only for environments
  where webhook-triggered deployments are allowed.
- Admin settings have automatic update application enabled only when the
  operator intends to use the Coolify driver.
- The previous working deployment is available in Coolify deployment history.

## Rollout Steps

1. Check the trusted release manifest:

   ```bash
   php artisan updates:check
   ```

2. Review `system_update_status` in the admin settings screen or database
   record. Continue only when:

   - `update_available=true`
   - `manual_review_required=false`
   - `php_requirement_ok=true`
   - the selected driver is `coolify`
   - driver preflight is passing

3. Trigger the deployment:

   ```bash
   php artisan updates:apply --force
   ```

4. Confirm that the command reports `Coolify deployment was triggered.` and
   `system_update_status.apply_status=deployment_triggered`.

5. Watch the Coolify deployment until the service has restarted and is no
   longer crash-looping.

6. Confirm the running Featherly version and readiness:

   ```bash
   php artisan updates:confirm
   ```

7. Continue only when the confirmation records:

   - `apply_status=confirmed`
   - `health_status=passed`
   - `update_available=false`
   - `status=current`

8. Run the standard post-deploy smoke checks in
   `docs/operations/post-deploy-smoke.md`.

## Evidence To Capture

- Timestamp and environment name.
- Current version before rollout.
- Target version from the release manifest.
- `updates:apply --force` output.
- Coolify deployment identifier or deployment history entry.
- `updates:confirm` output.
- `system_update_status.apply_status`.
- `system_update_status.health_status`.
- Post-deploy smoke result.

Use this evidence before marking a Coolify driver rollout as production-ready.

## Current Evidence Status

- Local automated evidence exists for webhook trigger gating, secret-safe status
  payloads, post-deploy version confirmation, and operational health
  confirmation.
- Staging/live Coolify evidence is not captured in this repository because the
  local workspace has no configured external Coolify target.
- Production enablement remains blocked by `DEPLOYMENT_GATE.md` until an
  operator captures the evidence above in the target environment.

## Failure Handling

### Webhook Trigger Fails

- Expected status: `apply_status=failed`.
- Keep `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED=false` until the secret and
  Coolify webhook configuration are fixed.
- Do not retry blindly if the previous request may have reached Coolify.

### Version Does Not Match

- Expected status: `apply_status=awaiting_confirmation`.
- Check Coolify deployment logs and confirm whether the new image or artifact
  actually deployed.
- Re-run `php artisan updates:confirm` only after Coolify reports the service
  has restarted successfully.

### Health Check Fails

- Expected status: `apply_status=confirmation_health_failed`.
- Inspect the stored `health_checks` payload for database, cache, or queue
  failure details.
- If the issue is environment configuration, fix the configuration and re-run
  `php artisan updates:confirm`.
- If the release is defective, use the rollback path below.

## Rollback Path

1. Capture the failed `system_update_status` payload and Coolify deployment
   logs.
2. Roll back to the previous successful deployment in Coolify deployment
   history.
3. Confirm the application is serving the rollback version:

   ```bash
   php artisan updates:confirm
   ```

4. Run:

   ```bash
   php artisan ops:health-check --json
   ```

5. Run the user journey checks in `docs/operations/post-deploy-smoke.md`.
6. Keep automatic Coolify apply disabled until the failure is understood and a
   follow-up task or release fix is recorded.

## Production-Ready Gate

The Coolify driver is not production-ready until at least one staging rollout
captures all evidence above and verifies:

- webhook trigger succeeds without exposing secrets
- Coolify deploys the expected release
- `updates:confirm` records `confirmed`
- operational health checks pass
- rollback to the previous deployment is tested or explicitly accepted by the
  release owner

## Docker-Based Deployments

Docker-based Featherly deployments should use this Coolify/platform rollout
path in v1. The application should not pull images or restart Docker services
from inside the runtime until a separate Docker driver contract is approved.
