import test from 'node:test';
import assert from 'node:assert/strict';

import {
    ICON_LIBRARY,
    ICON_SETS,
    searchIconLibrary,
} from '../../resources/js/features/admin/icons/iconLibrary.js';

test('icon sets include phosphor and font awesome families', () => {
    const ids = ICON_SETS.map((item) => item.id);
    assert.ok(ids.includes('phosphor'));
    assert.ok(ids.includes('fontawesome-solid'));
    assert.ok(ids.includes('fontawesome-regular'));
    assert.ok(ids.includes('fontawesome-brands'));
});

test('searchIconLibrary finds items by keyword and set', () => {
    const allSearch = searchIconLibrary('search');
    assert.ok(allSearch.some((item) => item.value === 'ph:PhMagnifyingGlass'));
    assert.ok(allSearch.some((item) => item.value === 'fa:fas fa-magnifying-glass'));

    const brandOnly = searchIconLibrary('youtube', 'fontawesome-brands');
    assert.equal(brandOnly.length, 1);
    assert.equal(brandOnly[0].value, 'fa:fab fa-youtube');
});

test('icon library exports prefixed values ready for renderer', () => {
    const sample = ICON_LIBRARY.find((item) => item.set === 'phosphor');
    assert.ok(sample);
    assert.ok(sample.value.startsWith('ph:'));
});
