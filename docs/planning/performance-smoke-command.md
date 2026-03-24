# Performance Smoke Command

## Command
`php artisan perf:smoke`

## Goal
Quickly sample response time and status for selected routes to detect obvious regressions before deeper profiling.

## Options
- `--path=/en --path=/en/blog` custom path list (repeatable)
- `--threshold=1200` threshold in milliseconds
- `--fail-on-slow` fail command when routes exceed threshold

## Defaults
- Threshold: `PERF_SMOKE_THRESHOLD_MS` (default `1200`)
- Paths from `config/performance.php` (`/en`, `/en/blog`, `/en/projects`)

## Output
Command prints a table per route:
- path
- status code
- measured response time (ms)
- slow flag
- server error flag
