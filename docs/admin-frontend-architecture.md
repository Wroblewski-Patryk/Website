# Admin Frontend Architecture

This document describes the refactored, feature-first architecture of the administrative panel frontend.

## Directory Structure

The frontend is organized into self-contained features located in `resources/js/features/admin/`.

```
resources/js/features/admin/
├── block-builder/          # Core editor logic and components
│   ├── components/         # specialized builder components
│   ├── store/              # pinia store for block builder
│   └── useBlockBuilder.js  # core logic composable
├── media/                  # Media library and picker
├── settings/               # Admin settings modules
├── shared/                 # Reusable admin-only components
│   └── components/
│       ├── ResourceTable/  # Modular table component
│       ├── ModuleHeader.vue
│       └── ...
└── theme/                  # Theme configuration and font select
```

## Naming Conventions

### Block Builder Components
All core components related to the visual editor use the `BlockBuilder` prefix for consistency:
- `BlockBuilderMain.vue`: Main editor entry point.
- `BlockBuilderHeader.vue`: Top toolbar with device/zoom/save actions.
- `BlockBuilderSidebar.vue`: Right inspector/settings/history panel.
- `BlockBuilderTimeline.vue`: Bottom GSAP timeline interface.

## Shared Components

### ResourceTable
A universal component for listing resources. It has been modularized into:
- `TableToolbar`: Search and column visibility controls.
- `TablePagination`: Pagination controls.
- `DeleteModal`: Reusable confirmation dialog.

## Best Practices

1. **Feature Isolation**: Logic specific to a feature should live within its feature directory.
2. **Aliased Imports**: Always use the `@` alias (e.g., `@/features/admin/shared/...`) instead of relative paths.
3. **Admin Consistency**: Use components from `features/admin/shared` to maintain a unified look and feel across modules.
4. **Translations**: Use the `useTranslations` composable and ensure all UI strings are localized.
