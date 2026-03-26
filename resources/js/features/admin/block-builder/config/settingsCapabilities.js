const contentDisabledByType = new Set([
    'posts_list',
    'projects_list',
    'spacer',
    'divider',
    'mockup_phone',
]);

const advancedDisabledByType = new Set([
    'paragraph',
    'text_rotate',
    'content_slot',
    'composed_block',
    'mockup_window',
]);

export const hasBlockModeSettings = (type, mode) => {
    if (!type) return false;
    if (mode === 'content') return !contentDisabledByType.has(type);
    if (mode === 'advanced') return !advancedDisabledByType.has(type);
    return true;
};

