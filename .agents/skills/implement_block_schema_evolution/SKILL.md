---
name: implement_block_schema_evolution
description: Evolve a block schema safely across existing Featherly content by updating renderer, editor config, migration logic, and backward compatibility paths. Use when changing block JSON structures.
---

# Procedure

## Step 1
Document old and new block schema versions with transformation rules.

## Step 2
Update builder/editor definitions and runtime renderer mappings.

## Step 3
Add migration or fallback transform for legacy stored block payloads.

## Step 4
Ensure serialization/deserialization stays stable across save/edit cycles.

## Step 5
Add regression tests for legacy payloads and newly created blocks.

## Validation
- verify old content still renders after deployment
- verify editor does not drop unknown legacy fields silently
- verify save cycle remains idempotent for unchanged blocks

## Output
- updated block schema handling paths
- compatibility tests and migration notes
- rollout checklist for production content safety
