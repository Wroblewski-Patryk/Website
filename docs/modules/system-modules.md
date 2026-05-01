# System Modules

Use this file to maintain a repository-wide map of the major Featherly modules,
feature areas, or bounded contexts in the codebase.

## Suggested Table

| Module | Layer | Source Path | Responsibility | Key Routes or Jobs | Canonical Deep-Dive |
| --- | --- | --- | --- | --- | --- |
| Block Builder | Admin/UI + content model | `resources/js/features/admin`, `app/` | Build localized page content from reusable blocks and controls. | Admin builder routes under `/{locale}/admin` | `docs/modules/BlockBuilder.md` |
| Content Architecture | Backend + database | `app/`, `database/`, `resources/js/features/admin` | Manage content types, localized fields, publishing, and module contracts. | Admin content routes under `/{locale}/admin` | `docs/modules/ContentArchitecture.md` |
| Renderer | Public frontend | `resources/js/`, `routes/`, `app/` | Render localized public pages from saved content and block schemas. | Public routes under `/{locale}` | `docs/modules/Renderer.md` |
| RBAC | Backend policies + admin guards | `app/`, `routes/`, `resources/js/` | Protect admin-only actions, ownership, and permission-sensitive workflows. | Auth/admin routes | `docs/modules/RBAC.md` |
| Media | Admin/backend | `app/`, `resources/js/features/admin`, `storage/` | Manage media selection, storage, and signed/private access expectations. | Admin media surfaces | `docs/modules/media.md` |

## Rules

- update this map when a meaningful module is added, removed, renamed, or
  split
- keep names aligned with code and planning docs
- link deep-dives only when they actually exist
