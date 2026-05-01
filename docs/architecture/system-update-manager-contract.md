# System Update Manager Contract

## Purpose

Featherly supports WordPress-like update behavior across different hosting
models without assuming that every installation runs on Coolify or on an
operator-controlled VPS.

The application owns update discovery, admin preferences, status visibility,
preflight checks, audit evidence, and user-facing controls. Code replacement
and service restart are delegated to an environment-specific update driver.

## Implementation Status

Current implemented baseline:

- admin settings expose update status and update preferences
- manual "check now" action fetches the trusted release manifest server-side
- `updates:check` command records current/latest version status
- manual "apply update" action and `updates:apply` run the configured driver
  apply contract
- `updates:confirm` confirms a triggered deployment only after the running
  application version matches the expected target version and operational
  health checks pass
- scheduler runs `updates:check` daily without overlap
- invalid, missing, or unavailable manifests fail closed to status only
- effective production fallback driver is currently `manual`
- manual driver records operator instructions and does not mutate files
- fake driver exists only when explicitly enabled for automated tests
- Coolify driver preflight validates configured webhook presence without
  exposing the secret URL
- Coolify apply can trigger the configured webhook only when
  `FEATHERLY_UPDATE_COOLIFY_APPLY_ENABLED=true`
- archive driver preflight validates configured staging/release paths and
  writable parent directories, and requires release archive URL plus SHA-256
  metadata before apply can be considered
- archive driver apply can download the release archive to staging and verify
  SHA-256 without extracting or switching live files
- archive driver records whether ZIP extraction support is unavailable or
  pending after archive verification
- when PHP `ZipArchive` is available, archive driver can extract the verified
  archive to staging and validate the required Laravel release files without
  switching live files
- archive staging validation records a switch/rollback plan that must be
  reviewed before any live file replacement is executed
- archive switch execution is implemented only when
  `FEATHERLY_UPDATE_ARCHIVE_SWITCH_ENABLED=true`; it backs up the current
  release path, preserves `.env`, `storage`, and `public/storage`, switches the
  staged release files, and records rollback evidence
- archive rollback execution is implemented through
  `php artisan updates:rollback-archive --force`; it restores the configured
  release path from the recorded archive backup while preserving local runtime
  paths

Not implemented yet:

- automatic update application
- unattended archive apply execution
- Coolify live rollout validation and production rollback evidence
- Docker and Git runtime drivers, intentionally deferred from v1
- driver-specific rollback execution

Until those items are complete, Featherly must treat update availability as a
notification/status/manual-instructions feature by default. Archive file
replacement is available only as an explicitly enabled operator path.

Current v1 gate status: local implementation and automated tests cover update
discovery, safe driver gating, Coolify trigger/confirmation contracts, archive
verify/stage/switch/rollback, and Docker/Git v1 deferral. Coolify production
enablement remains blocked until the rollout runbook evidence is captured in a
configured staging/live Coolify environment.

## Supported Hosting Models

- Coolify or similar platform-as-a-service on a VPS.
- Generic VPS with Docker, Docker Compose, Git, or shell access.
- Shared hosting where shell access, Git, Composer, Node, or process restarts
  may be unavailable.
- Third-party self-hosted installations where the Featherly maintainers do not
  have server access.

## Core Decision

Featherly must not assume direct control over every runtime environment. The
update system must be environment-adaptive:

- update checks are application-owned and enabled by default
- automatic update application is available only when a safe driver passes
  preflight checks
- users can disable automatic updates in the admin settings
- an environment-level kill switch must be able to disable automatic update
  application even if the database setting is enabled
- unsupported environments must fall back to update notification and manual
  instructions instead of attempting unsafe self-mutation

## Responsibilities

### Application Core

The application core is responsible for:

- storing update preferences and status
- checking the current installed version against the release manifest
- showing current version, latest version, last check, last attempt, and
  failure details in the admin panel
- running scheduler-based update checks
- selecting a configured update driver
- running preflight validation before update application
- recording audit logs for checks and update attempts
- refusing automatic application when risk controls are missing

### Update Drivers

Drivers are responsible for applying a known release through the hosting model
they support.

Initial driver set:

- `coolify`: trigger a Coolify deploy webhook or API-backed deployment
- `archive`: download a built release archive, verify it, unpack it into a
  staging path, preserve local state, and switch the application files safely
- `manual`: report availability and produce operator instructions only

Deferred v1 driver directions:

- `docker`: use Coolify or another platform/operator rollout path in v1.
  Runtime image pull, service restart, health, and rollback need a dedicated
  contract before a Docker driver can exist.
- `git`: keep as manual/operator deployment in v1. Runtime checkout,
  dependency install, build, migration, secret handling, and rollback need a
  dedicated contract before a Git driver can exist.

Drivers must expose:

- whether the driver is configured
- whether the current host supports the driver
- preflight result
- apply result
- rollback or recovery notes
- operator-facing failure message

## Release Source

Automatic updates must use a trusted release source, not an arbitrary moving
branch.

The preferred release source is a stable release manifest containing:

- latest version
- release channel
- minimum PHP version
- release archive or image reference
- checksum
- migration risk
- manual-review requirement
- release notes URL

For archive releases, the manifest fields are:

- `release_archive_url`
- `release_archive_sha256`

The update manager must verify release integrity before applying an archive or
image. If integrity metadata is missing or invalid, automatic application must
fail closed.

The current archive implementation can stop after download, SHA-256
verification, staging extraction validation, and switch-plan generation by
default. When `FEATHERLY_UPDATE_ARCHIVE_SWITCH_ENABLED=true`, it can switch the
validated staged release into the configured release path, preserve local state,
and record a backup path. `updates:rollback-archive --force` can restore that
backup while preserving local state. Archive apply still must not run
migrations automatically. If the PHP `ZipArchive` extension is unavailable,
extraction is recorded as unavailable and the operator must enable ZIP support
before the archive driver can progress to extraction validation.

## Shared Hosting Strategy

Shared hosting must prefer the `archive` driver because the host may not have
Git, Composer, Node, queue workers, or deploy hooks.

Release archives intended for this driver should include:

- production PHP application files
- installed PHP dependencies or a documented requirement when they cannot be
  bundled
- built Vite assets under the public build path
- migrations
- version metadata

The archive driver must preserve local runtime state, including:

- `.env`
- `storage`
- uploaded media
- public storage links or equivalent hosting-specific media paths
- installation-specific cache, logs, and generated files where appropriate

If the host cannot safely unpack, verify, stage, or switch a release, the
driver must degrade to `manual`.

## Safety Rules

- No deploy secrets may be exposed to the browser.
- Update settings and actions must be protected by the existing admin
  permission boundary.
- Update application must run server-side only.
- Destructive file replacement must use staging and verification before
  switching live files.
- Migrations must run with explicit risk handling.
- High-risk or manual-review releases must not auto-apply.
- Failed updates must leave the installation in the previous working state or
  provide a deterministic recovery path.
- Logs must not include secrets, webhook tokens, or sensitive environment
  values.

## Admin Settings

The admin update settings should include:

- update checks enabled, default `true`
- automatic update application enabled, default `true` when a safe driver is
  configured
- release channel, default `stable`
- preferred update driver, default `auto`
- maintenance window
- last check and last attempt status
- manual "check now" action
- manual "apply update" action when a supported update is available

An environment override must be able to disable automatic application for
operators who manage deployment outside the application.

## Scheduler Contract

The Laravel scheduler should run update checks daily. The scheduled task should:

1. read admin and environment settings
2. fetch and verify release manifest metadata
3. store update availability and status
4. auto-apply only when enabled, supported, and safe
5. record result evidence

The scheduler must not block public page serving if the update source is
unavailable.

## Reliability And Rollback

Every automatic update driver must define:

- preflight checks
- expected downtime or restart behavior
- health/readiness checks
- post-update smoke checks
- failure and rollback behavior
- operator-visible logs or status

Post-deploy confirmation must not mark an update complete only because a
deployment webhook was accepted. The running application must report the
expected `APP_VERSION` and pass operational health checks before
`system_update_status` can move from `deployment_triggered`,
`awaiting_confirmation`, or `confirmation_health_failed` to `confirmed`.

For Coolify, rollback can rely on the platform deployment history when
configured, but the rollout must capture the evidence described in
`docs/operations/coolify-update-rollout-runbook.md` before the driver is marked
production-ready. For archive updates, rollback uses the recorded
`archive_backup_path` through `updates:rollback-archive --force`. For Git
updates, rollback must be designed explicitly before the driver is marked
production-ready.

## Architecture Fit

This contract extends the existing configuration and delivery domains. It does
not change localized routing, admin/public boundaries, block-builder content
contracts, or the Laravel/Vite stack.

Implementation must update operations documentation before runtime update
application is released.
