import test from 'node:test';
import assert from 'node:assert/strict';

import { applyBulkOptimistic, cloneRows } from '../../resources/js/features/admin/shared/components/ResourceTable/bulkOptimistic.js';

test('bulk optimistic updates status for selected rows', () => {
    const rows = [
        { id: 1, status: 'draft' },
        { id: 2, status: 'draft' },
    ];

    const result = applyBulkOptimistic(rows, 'publish', [1]);
    assert.equal(result[0].status, 'published');
    assert.equal(result[1].status, 'draft');
});

test('bulk optimistic delete removes selected rows', () => {
    const rows = [
        { id: 1, status: 'draft' },
        { id: 2, status: 'published' },
    ];

    const result = applyBulkOptimistic(rows, 'delete', [2]);
    assert.equal(result.length, 1);
    assert.equal(result[0].id, 1);
});

test('cloneRows creates immutable snapshot for rollback', () => {
    const rows = [{ id: 1, status: 'draft' }];
    const snapshot = cloneRows(rows);
    snapshot[0].status = 'published';

    assert.equal(rows[0].status, 'draft');
});
