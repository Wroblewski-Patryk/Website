# Builder Design Controls Contract

## Scope
Defines editor behavior for:
- lightweight WYSIWYG text editing for text-oriented blocks,
- responsive spacing controls with breakpoint targeting,
- hidden empty tabs in block settings UI.

## WYSIWYG Pipeline
- Component: `LightweightWysiwyg`
- Initial rollout:
  - paragraph content settings
  - heading content settings
- Output format: sanitized HTML string persisted in block `content.text`.

## Spacing Controls Contract
- UI control: top/right/bottom/left linked inputs with unit selector.
- `auto` unit support:
  - allowed in spacing controls,
  - numeric input disabled when selected.
- Storage model:
  - base style keys remain supported (`marginTop`, `paddingLeft`, ...),
  - breakpoint-scoped overrides are stored under:
    - `settings.style.responsiveSpacing.desktop`
    - `settings.style.responsiveSpacing.tablet`
    - `settings.style.responsiveSpacing.phone`

## Breakpoint Resolution
- Editor mode: uses active builder viewport (`desktop/tablet/mobile -> phone`).
- Runtime mode: uses window width:
  - `<768`: `phone`
  - `768-1023`: `tablet`
  - `>=1024`: `desktop`
- Fallback: base spacing values when breakpoint override is not defined.

## Empty Tab Visibility
- Block settings tabs are filtered by capability map:
  - hide tab when no controls are available for active block + mode.
- Capability source:
  - `settingsCapabilities.js`

## Validation Evidence
- JS tests:
  - `tests/js/blockBuilderWave6.test.mjs`
- Focus:
  - responsive spacing persistence/fallback behavior,
  - breakpoint resolution,
  - empty-tab capability filtering.
