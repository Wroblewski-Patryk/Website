# Media Picker Control Contract

Date: 2026-03-26  
Status: ACTIVE

## Purpose
- Standardize how block settings open media picker modal and persist media references.
- Keep one reusable control contract for single and multi-select use cases.

## Reusable Control
- Component: `resources/js/features/admin/media/components/MediaPickerField.vue`
- Required capabilities:
  - open media picker modal
  - support single-select and multi-select
  - store selected media IDs
  - render previews for selected media

## Picker Config Schema
- Source: `resources/js/features/admin/block-builder/components/BlockSettingsManager.vue`
- Contract per block type:
  - `type`: `all|document|image|audio|video`
  - `multiple`: `boolean`

Example:
```js
{
  image: { type: 'image', multiple: false },
  carousel: { type: 'image', multiple: true }
}
```

## Modal Selection Contract
- Store: `resources/js/Stores/useMediaPickerStore.js`
- `open(options)` options:
  - `type` -> mapped to media list `file_type` filter
  - `multiple` -> toggles single vs multi confirm flow

Returned value:
- when `multiple=false`: one media object
- when `multiple=true`: ordered array of media objects

Order rule:
- for multi-select, payload order follows user click sequence.

## Persisted Block Content Contract
- Single media field:
  - `media_id`: number|null
  - optional runtime helper field: `url`

- Multi media field:
  - `image_ids`: number[] (ordered)
  - optional runtime helper field: `images` (ordered URLs aligned with `image_ids`)

## First Target Integrations
- `image` block advanced settings:
  - `media_id` + `url`
- `carousel` block advanced settings:
  - `image_ids` + `images`

## Test Coverage
- backend media type filtering: `tests/Feature/Admin/MediaManagementTest.php`
- persisted ordered media IDs in page content: `tests/Feature/Admin/PageManagementTest.php`
