---
name: add_theme_token_pipeline
description: Add a new theme token pipeline from admin configuration to runtime CSS variables and component usage. Use when introducing configurable colors, spacing, typography, or shape settings.
---

# Procedure

## Step 1
Define token name, value type, default, and intended UI scope.

## Step 2
Add token field in admin configuration UI and persist it through backend settings flow.

## Step 3
Map persisted value to runtime CSS variable generation and consume in target components.

## Step 4
Handle invalid values with safe fallback and preview behavior.

## Step 5
Add regression checks so token changes do not break existing themes.

## Validation
- verify token persists and reloads correctly
- verify CSS variable appears in rendered document styles
- verify affected components update without full refresh

## Output
- settings UI/backend/token mapping changes
- fallback and validation coverage
- brief note on token naming and usage conventions
