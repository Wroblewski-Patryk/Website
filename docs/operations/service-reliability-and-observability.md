# Service Reliability And Observability

Use this for deployable services, background workers, public APIs, scheduled
jobs, and product flows where downtime or data loss would matter.

## Reliability Contract

Define the smallest useful reliability contract before launch:

- Critical user journey:
- SLI:
- SLO:
- Error budget window:
- Alert threshold:
- Owner or escalation path:
- Dashboard or log query:
- Smoke test:
- Rollback or disable path:

## Choosing SLIs

Prefer user-centered indicators:

- availability: did the system respond successfully?
- latency: did it respond fast enough for the user journey?
- correctness: did it return the right result or persist the right state?
- freshness: is synchronized or cached data current enough?
- durability: did data survive restart, retry, and deploy?

Do not track every available metric as an SLI. Pick a small set that describes
whether users are actually receiving the expected service.

## Error Budget Posture

Use error budget thinking for release decisions:

- Healthy: normal feature delivery can continue.
- Burning: reduce risky change size, increase validation, and prioritize
  reliability fixes.
- Exhausted: pause non-critical risky launches until the failure mode is
  understood and mitigated.

## Observability Minimum

Every meaningful runtime path should provide:

- structured logs for success and failure
- request or job correlation where practical
- health/readiness signal
- visible dependency failures
- operator-readable error messages
- smoke command or manual verification path

## Featherly System Update Check Baseline

- Critical user journey: admin or scheduler checks for a trusted Featherly
  release manifest without risking runtime self-mutation.
- SLI: successful manifest check records `system_update_status` with current
  version, latest version, update availability, and failure details.
- SLO: scheduled check should complete without blocking public page serving.
- Alert threshold: repeated failed checks or missing manifest configuration
  should be visible in admin update status before automatic apply is enabled.
- Owner or escalation path: Ops/Release + Backend Builder.
- Dashboard or log query: audit log events `updates.checked` and
  `updates.check_failed`; admin settings update status card.
- Smoke test: `php artisan updates:check`.
- Rollback or disable path: disable `update_checks_enabled` in settings or
  leave manifest URL unset; automatic application is not implemented in the
  current baseline.

## Featherly Manual Update Apply Baseline

- Critical user journey: admin records a safe update-apply attempt and receives
  operator instructions without runtime file mutation.
- SLI: `system_update_status` records `apply_status`, `operator_message`,
  `operator_instructions`, and rollback note.
- SLO: manual apply attempt should complete as a normal admin request and
  should not restart services or modify files.
- Alert threshold: any real driver apply failure should be visible in admin
  status before a production driver is enabled.
- Owner or escalation path: Ops/Release + Backend Builder.
- Dashboard or log query: audit events `updates.manual_required`,
  `updates.apply_blocked`, `updates.apply_preflight_failed`,
  `updates.applied`, and `updates.apply_failed`.
- Smoke test: `php artisan updates:apply --force` in manual mode.
- Rollback or disable path: manual mode performs no file mutation; fake driver
  is disabled unless config-enabled.

## Featherly Coolify Apply Trigger Baseline

- Critical user journey: operator triggers a configured Coolify deployment
  webhook from the server without exposing the webhook URL to the browser.
- SLI: `system_update_status.apply_status` records `deployment_triggered` when
  Coolify accepts the webhook request, then records `confirmed` only after the
  running app reports the expected version and DB/cache/queue health checks
  pass.
- SLO: trigger request should complete quickly and never block public serving.
- Alert threshold: failed webhook request records `updates.apply_failed` and
  visible failure details; deployments that remain in `awaiting_confirmation`
  or `confirmation_health_failed` need operator review.
- Owner or escalation path: Ops/Release.
- Dashboard or log query: audit event `updates.deployment_triggered` or
  `updates.apply_failed`; `updates.confirmed` or
  `updates.awaiting_confirmation`; `updates.confirmation_health_failed`;
  Coolify deployment history for execution details.
- Smoke test: `php artisan updates:apply --force` with HTTP fake in automated
  tests, then `php artisan updates:confirm` after the deployment restarts with
  the new `APP_VERSION` and passing DB/cache/queue checks; live smoke requires
  a configured staging Coolify webhook.
- Rollback or disable path: unset `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED` or
  set it false; rollback uses Coolify deployment history.

## Featherly Archive Verification Baseline

- Critical user journey: operator verifies that a trusted archive release can
  be downloaded and matched against manifest SHA-256 without changing live
  application files.
- SLI: `system_update_status.apply_status` records `archive_verified` only
  after SHA-256 matches; checksum mismatch records
  `archive_verification_failed`.
- SLO: verification should complete as a bounded operator command and must not
  block public page serving.
- Alert threshold: repeated archive download failures or checksum mismatches
  require release artifact investigation before retry.
- Owner or escalation path: Ops/Release + Backend Builder.
- Dashboard or log query: admin update status and audit events for update
  attempts.
- Smoke test: `php artisan updates:apply --force` with archive driver
  configured and HTTP fake in automated tests.
- Rollback or disable path: no live files are changed; remove the staged
  archive or disable archive driver configuration.
- Runtime prerequisite: PHP `ZipArchive` support is required before archive
  extraction validation can run. Without it, verification records
  `archive_extraction_status=unavailable`.
- Staging validation: when PHP `ZipArchive` support exists, verification can
  extract the archive to staging and requires `artisan`, `composer.json`,
  `bootstrap/app.php`, and `public/index.php` before recording
  `archive_extraction_status=validated`.

## Incident Learning

After a production incident, failed deploy, or serious smoke failure:

- record what happened
- record user impact
- record root cause or current best hypothesis
- record detection gap
- record fix and rollback outcome
- add a regression, alert, runbook update, or task-board follow-up
