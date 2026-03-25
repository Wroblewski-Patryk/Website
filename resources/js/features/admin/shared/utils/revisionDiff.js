function stableSerialize(value) {
    if (Array.isArray(value)) {
        return `[${value.map(stableSerialize).join(',')}]`;
    }

    if (value && typeof value === 'object') {
        const keys = Object.keys(value).sort();
        return `{${keys.map((key) => `${JSON.stringify(key)}:${stableSerialize(value[key])}`).join(',')}}`;
    }

    return JSON.stringify(value);
}

function flattenBlocks(blocks, bucket = new Map()) {
    (blocks || []).forEach((block) => {
        if (!block || !block.id) return;
        bucket.set(block.id, block);
        flattenBlocks(block.children || [], bucket);
    });
    return bucket;
}

export function compareRevisionContent(previousBlocks, currentBlocks) {
    const previousMap = flattenBlocks(previousBlocks || []);
    const currentMap = flattenBlocks(currentBlocks || []);

    const added = [];
    const removed = [];
    const changed = [];
    const unchanged = [];

    for (const [id, block] of previousMap.entries()) {
        if (!currentMap.has(id)) {
            removed.push({ id, type: block.type || 'unknown' });
            continue;
        }

        const currentBlock = currentMap.get(id);
        const before = stableSerialize(block);
        const after = stableSerialize(currentBlock);

        if (before === after) {
            unchanged.push({ id, type: block.type || 'unknown' });
        } else {
            changed.push({ id, type: block.type || 'unknown' });
        }
    }

    for (const [id, block] of currentMap.entries()) {
        if (!previousMap.has(id)) {
            added.push({ id, type: block.type || 'unknown' });
        }
    }

    return {
        added,
        removed,
        changed,
        unchanged,
        counts: {
            previous: previousMap.size,
            current: currentMap.size,
            added: added.length,
            removed: removed.length,
            changed: changed.length,
            unchanged: unchanged.length,
        },
        hasChanges: added.length > 0 || removed.length > 0 || changed.length > 0,
    };
}
