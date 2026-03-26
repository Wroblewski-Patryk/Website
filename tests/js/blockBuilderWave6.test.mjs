import test from 'node:test';
import assert from 'node:assert/strict';

import {
    getResponsiveSpacingValue,
    normalizeBreakpoint,
    resolveBreakpointFromWidth,
    setResponsiveSpacingValue,
} from '../../resources/js/features/admin/block-builder/utils/responsiveSpacing.js';
import { hasBlockModeSettings } from '../../resources/js/features/admin/block-builder/config/settingsCapabilities.js';

test('responsive spacing persists and resolves per breakpoint with fallback', () => {
    const style = {
        marginTop: '8px',
        responsiveSpacing: {
            tablet: {
                marginTop: '16px',
            },
        },
    };

    assert.equal(getResponsiveSpacingValue(style, 'desktop', 'marginTop'), '8px');
    assert.equal(getResponsiveSpacingValue(style, 'tablet', 'marginTop'), '16px');
    assert.equal(getResponsiveSpacingValue(style, 'phone', 'marginTop'), '8px');
});

test('setting responsive spacing writes to breakpoint-scoped map', () => {
    const style = {};
    setResponsiveSpacingValue(style, 'phone', 'paddingLeft', 'auto');
    assert.equal(style.responsiveSpacing.phone.paddingLeft, 'auto');
});

test('breakpoint normalization and width resolver work as expected', () => {
    assert.equal(normalizeBreakpoint('mobile'), 'phone');
    assert.equal(resolveBreakpointFromWidth(375), 'phone');
    assert.equal(resolveBreakpointFromWidth(900), 'tablet');
    assert.equal(resolveBreakpointFromWidth(1440), 'desktop');
});

test('empty-tab capability map hides unsupported modes for selected blocks', () => {
    assert.equal(hasBlockModeSettings('paragraph', 'advanced'), false);
    assert.equal(hasBlockModeSettings('text_rotate', 'advanced'), false);
    assert.equal(hasBlockModeSettings('posts_list', 'content'), false);
    assert.equal(hasBlockModeSettings('heading', 'content'), true);
});

