const normalizeIds = (ids = []) => new Set(ids.map((id) => Number(id)).filter((id) => Number.isFinite(id)));

export const cloneRows = (rows = []) => rows.map((row) => ({ ...row }));

export const applyBulkOptimistic = (rows = [], action, ids = []) => {
    const selected = normalizeIds(ids);
    const nextRows = cloneRows(rows);

    if (!selected.size) {
        return nextRows;
    }

    if (action === 'delete') {
        return nextRows.filter((row) => !selected.has(Number(row.id)));
    }

    const statusMap = {
        publish: 'published',
        unpublish: 'draft',
        archive: 'archived',
    };

    const nextStatus = statusMap[action];
    if (!nextStatus) {
        return nextRows;
    }

    return nextRows.map((row) => {
        if (!selected.has(Number(row.id))) {
            return row;
        }

        return {
            ...row,
            status: nextStatus,
        };
    });
};
