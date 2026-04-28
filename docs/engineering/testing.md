# Testing Strategy

## Test Pyramid
- Unit tests: business rules, validation, and utility functions.
- Integration tests: module and API contract behavior with realistic data.
- E2E tests: critical user journeys in browser automation (for example Playwright).
- Manual smoke checks: focused human verification for UX, copy, visual parity, and edge interactions.

## Required Checks Per Change Type
- Backend change:
  - Unit tests for changed logic.
  - Integration tests for affected endpoints/contracts.
  - At least one E2E regression if API behavior impacts existing journeys.
- Frontend change:
  - Component/unit tests for logic and state transitions.
  - E2E journey coverage for the affected path.
  - Manual smoke checklist for UI/UX states.
- DB migration:
  - Migration up/down validation in local or CI environment.
  - Integration tests proving compatibility with migrated schema.
  - E2E smoke for high-risk data flows.
- Infra/ops change:
  - Deployment or pipeline validation in non-production environment.
  - Synthetic check or smoke endpoint verification.
  - Manual verification of monitoring/alerts when relevant.

## Determinism Rules
- Keep automated tests deterministic: fixed seeds, stable clocks, and isolated data.
- Do not merge if tests rely on random external behavior without explicit mocks.
- Manual smoke must use a defined seed dataset and predefined account fixtures.
- Each failed check must include a minimal reproduction path.

## Evidence Format
- Each PR includes:
  - automated test summary (commands + pass/fail),
  - manual smoke result (pass/fail by journey),
  - artifacts for failures or high-risk changes.
- Recommended artifact folder:
  - `docs/testing-evidence/YYYY-MM-DD/pr-<ID>/`
- Allowed artifacts:
  - screenshot(s),
  - HTML/DOM snapshot,
  - browser console/network logs,
  - short notes with expected vs actual behavior.

## Browser-Driven Manual Simulation
- Browser agents (for example Playwright or browser MCP) are allowed to execute journey checks as a human proxy.
- Required for browser-driven checks:
  - use realistic form inputs, including invalid variants (for example malformed email),
  - verify navigation across full flow (entry page to successful action),
  - capture screenshot and DOM evidence at key milestones,
  - report blockers with exact step number and observable symptom.
- Final sign-off remains human-owned for release-critical work.

## AI And Integration Validation

AI features require repeatable multi-turn validation using `AI_TESTING_PROTOCOL.md`. Required coverage includes memory consistency, multi-step context stability, adversarial contradiction handling, role break and prompt injection resistance, memory corruption resistance, edge cases, data leakage, and unauthorized access attempts.

Runtime features require integration validation using `INTEGRATION_CHECKLIST.md`. A feature is not complete until real UI/client paths, API contracts, database schema or migrations, validation, loading states, error states, refresh or restart behavior, and regression risk are verified.

Completion evidence must satisfy `DEFINITION_OF_DONE.md` and include exact commands, manual checks, scenario results, and residual risks.
