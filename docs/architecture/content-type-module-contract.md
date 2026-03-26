# ContentType Module Contract

## Goal
Define how content modules (`pages`, `posts`, `projects`, `templates`, `forms`) inherit behavior and expose module-specific editor capabilities while keeping a shared baseline.

## Core Contract
- Base abstraction: `App\Http\Controllers\Admin\BaseAdminContentController`
- Required module key: `protected string $module`
- Shared edit/create props:
  - `templates`
  - `languages`
  - `moduleCategories` (resolved from `ModuleBlockRegistry`)
- Optional module capabilities:
  - `useTaxonomies`
  - `useSlug`

## Module Category Contract
- Source: `config/block_builder.php` (`module_registry`)
- Resolver: `App\Services\BlockBuilder\ModuleBlockRegistry`
- Rules:
  - inheritance via `extends`
  - category merge by `id`
  - block merge by `type`
  - first-seen order preserved

## Composed Block Reference Slice
- Global reusable compositions: `composed_blocks` table + `App\Models\ComposedBlock`
- Editor insertion type: `composed_block`
- Payload contract for reference blocks:
  - `content.composed_block_id` (required for runtime resolution)
  - `content.snapshot_title` (optional editor hint)
- Runtime source:
  - shared Inertia prop `composed_blocks_library` (active entries only)

## Safe Update Contract
- Composed block updates support optimistic lock (`optimistic_lock` payload).
- Revision snapshot is written before update (`revisions` morph relation).

## Reference Implementation
- `BaseAdminContentController::getSharedProps()`
- `ModuleBlockRegistry::resolveCategories()`
- `ComposedBlockController` (CRUD + optimistic lock + revisions)
- Block builder integration:
  - palette registration in `useBlockBuilderStore`
  - selector settings in `ComposedBlockSettings`
  - runtime/editor render branch in `DynamicBlock`
