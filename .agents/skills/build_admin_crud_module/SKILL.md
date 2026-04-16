---
name: build_admin_crud_module
description: Build a full admin CRUD module in Featherly (routes, controller, requests, policies, Inertia/Vue views, and navigation entry). Use when adding a new admin-managed resource.
---

# Procedure

## Step 1
Define resource fields, permissions, and list/filter/sort requirements.

## Step 2
Add admin routes and controller methods for index/create/store/edit/update/destroy with FormRequest validation.

## Step 3
Implement policy checks and middleware gating for all actions.

## Step 4
Create Inertia Vue pages using existing admin layout and shared UI patterns.

## Step 5
Add navigation/sidebar entry and basic feature tests for CRUD flow.

## Validation
- verify unauthorized users cannot access module routes
- verify validation errors are shown and persisted correctly
- verify index performance with pagination and filters

## Output
- complete admin module slice (backend + frontend)
- tests and menu wiring
- short usage notes for editors/admins
