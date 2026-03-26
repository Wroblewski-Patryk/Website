# Admin Search Provider Contract (v1)

Date: 2026-03-25  
Status: active baseline for FEX-011..FEX-020

## Goal
Define a stable backend contract for global admin search so entity providers (pages/posts/projects/others) can be added incrementally without rewriting core search logic.

## Core Interfaces
- `App\Services\AdminSearch\AdminSearchProvider`
  - `key(): string` unique provider identifier.
  - `search(string $query, int $limit = 5): array<int, AdminSearchResult>`.
- `App\Services\AdminSearch\AdminSearchResult`
  - Normalized result payload:
    - `type`
    - `id`
    - `title`
    - `url`
    - `subtitle`
    - `score`
- `App\Services\AdminSearch\AdminSearchManager`
  - Aggregates provider results.
  - Sorts combined output by descending `score`.

## v1 Scope
- Initial providers target base-module entities:
  - pages
  - posts
  - projects
- Additional modules should implement provider contract and register in manager composition.

## Ranking Policy (baseline)
- Higher `score` means higher priority.
- Provider implementation owns scoring details (exact match, prefix match, status weighting, recency).
- Manager only aggregates and global-sorts.

## Extension Rules
- Provider output must stay within normalized `AdminSearchResult`.
- Providers should be side-effect free (read-only search).
- Provider additions should include tests for result shape + score order assumptions.
