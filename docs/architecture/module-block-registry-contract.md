# Module Block Registry Contract

## Purpose
Define module-scoped block palette additions with inheritance so each content module can expose only relevant block groups in the editor.

## Source of Truth
- Config file: `config/block_builder.php`
- Resolver service: `App\Services\BlockBuilder\ModuleBlockRegistry`

## Registry Shape
- `module_registry.<module>.extends` (optional): parent module key.
- `module_registry.<module>.categories` (array):
  - `id` (required)
  - `label` (optional)
  - `icon` (optional)
  - `blocks` (array)
    - `type` (required)
    - `label` (optional)
    - `icon` (optional)
    - `template_types` (optional): list of template types where block is visible (evaluated in template editor)

## Merge Rules
- Parent categories are loaded before child categories.
- Categories merge by `id`.
- Blocks merge by `type` inside each category.
- First-seen block order is preserved.
- Invalid category/block entries are ignored.

## Delivery Path
- Base admin content controllers expose resolved module categories via shared Inertia prop:
  - `moduleCategories`
- Block editor pages pass this prop to `BlockBuilder`.
- `BlockBuilderMain` merges module categories with base palette categories.

## Current Module Coverage
- `pages` -> inherits base content categories
- `posts` -> adds `posts_list`
- `projects` -> adds `projects_list`
- `templates` -> adds building/slot blocks with per-template-type filtering support
- `forms` -> adds data input blocks

## Validation
- Unit: `tests/Unit/ModuleBlockRegistryTest.php`
- Feature: `tests/Feature/Admin/ModuleBlockCategoriesShareTest.php`
