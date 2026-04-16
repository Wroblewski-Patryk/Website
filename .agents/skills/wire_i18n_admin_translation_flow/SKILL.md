---
name: wire_i18n_admin_translation_flow
description: Wire end-to-end admin i18n translation flow in Featherly, including key usage, scan command integration, and integrity checks. Use when adding or refactoring translatable admin UI.
---

# Procedure

## Step 1
Add/normalize translation keys in frontend and backend usage points.

## Step 2
Run i18n scan workflow to discover keys and sync storage.

## Step 3
Update language resource entries and fallback behavior for missing keys.

## Step 4
Ensure translated strings are shared via Inertia props where required.

## Step 5
Add or update tests for translation integrity and key coverage.

## Validation
- run `php artisan i18n:scan --scope=admin`
- run translation integrity tests
- verify no hardcoded admin labels remain in touched views

## Output
- updated translation keys/resources
- scan and integrity test evidence
- list of newly introduced keys
