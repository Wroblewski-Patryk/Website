# Post-Deploy Smoke

Use this file to record the minimum checks after each deploy.

## Global Checks

- [ ] Health endpoint returns success
- [ ] Main app is reachable through the public URL
- [ ] Logs show no startup crash loop
- [ ] Background workers are connected and healthy

## User Journey Checks

- [ ] Primary entry flow works
- [ ] One critical happy-path action works
- [ ] One negative or validation path behaves correctly

## Ops Checks

- [ ] Migrations completed successfully
- [ ] Required env values are present
- [ ] If a Featherly update deployment was triggered, `php artisan
      updates:confirm` reports the expected `APP_VERSION` and passing
      DB/cache/queue health checks
- [ ] Metrics or error tracking show no new critical issue

## Evidence

- Timestamp:
- Environment:
- Commands run:
- Screenshots or logs:
- Coolify update rollout evidence, when applicable:
  - deployment history entry:
  - target version:
  - `updates:confirm` result:
