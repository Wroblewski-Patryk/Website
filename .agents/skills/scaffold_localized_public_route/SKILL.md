---
name: scaffold_localized_public_route
description: Scaffold a new locale-aware public route in Featherly with controller, Inertia page, SEO metadata, and route registration. Use when exposing new public CMS pages under localized routing.
---

# Procedure

## Step 1
Choose canonical slug pattern and locale behavior aligned with current route groups.

## Step 2
Add route to `routes/public.php` using localized prefix conventions and named route patterns.

## Step 3
Create controller action that resolves localized content and returns Inertia view props.

## Step 4
Create Vue page component with fallback handling for missing translation/content.

## Step 5
Add SEO defaults (title/description/canonical) and not-found behavior.

## Validation
- verify route works for all enabled locales
- verify missing translation fallback is deterministic
- verify generated links preserve locale context

## Output
- route/controller/view additions
- localization + SEO handling updates
- manual test matrix for locale variants
