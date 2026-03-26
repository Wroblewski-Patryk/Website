export function useBuilderShortcuts(options = {}) {
    const isEditableTarget = (target) => {
        if (!target) return false;
        const tag = target.tagName?.toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select') return true;
        return Boolean(target.isContentEditable);
    };

    const shortcuts = [
        {
            id: 'save',
            key: 'Ctrl/Cmd + S',
            descriptionKey: 'admin.builder.shortcuts_save',
            defaultDescription: 'Save document',
            handler: options.onSave,
            whenEditable: true,
        },
        {
            id: 'help',
            key: '?',
            descriptionKey: 'admin.builder.shortcuts_help',
            defaultDescription: 'Open keyboard shortcuts',
            handler: options.onOpenHelp,
            match: (event) => event.key === '?' || (event.shiftKey && event.key === '/'),
            whenEditable: false,
        },
        {
            id: 'zoom_in',
            key: 'Ctrl/Cmd + =',
            descriptionKey: 'admin.builder.shortcuts_zoom_in',
            defaultDescription: 'Zoom in canvas',
            handler: options.onZoomIn,
            match: (event) => (event.ctrlKey || event.metaKey) && (event.key === '=' || event.key === '+'),
            whenEditable: false,
        },
        {
            id: 'zoom_out',
            key: 'Ctrl/Cmd + -',
            descriptionKey: 'admin.builder.shortcuts_zoom_out',
            defaultDescription: 'Zoom out canvas',
            handler: options.onZoomOut,
            match: (event) => (event.ctrlKey || event.metaKey) && event.key === '-',
            whenEditable: false,
        },
        {
            id: 'zoom_reset',
            key: 'Ctrl/Cmd + 0',
            descriptionKey: 'admin.builder.shortcuts_zoom_reset',
            defaultDescription: 'Reset zoom',
            handler: options.onZoomReset,
            match: (event) => (event.ctrlKey || event.metaKey) && event.key === '0',
            whenEditable: false,
        },
        {
            id: 'toggle_palette',
            key: 'Ctrl/Cmd + B',
            descriptionKey: 'admin.builder.shortcuts_toggle_palette',
            defaultDescription: 'Toggle block palette',
            handler: options.onTogglePalette,
            match: (event) => (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'b',
            whenEditable: false,
        },
        {
            id: 'toggle_inspector',
            key: 'Ctrl/Cmd + I',
            descriptionKey: 'admin.builder.shortcuts_toggle_inspector',
            defaultDescription: 'Toggle inspector',
            handler: options.onToggleInspector,
            match: (event) => (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'i',
            whenEditable: false,
        },
        {
            id: 'toggle_timeline',
            key: 'Ctrl/Cmd + T',
            descriptionKey: 'admin.builder.shortcuts_toggle_timeline',
            defaultDescription: 'Toggle GSAP timeline',
            handler: options.onToggleTimeline,
            match: (event) => (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 't',
            whenEditable: false,
        },
    ];

    const matches = (shortcut, event) => {
        if (shortcut.match) {
            return shortcut.match(event);
        }

        if (shortcut.id === 'save') {
            return (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's';
        }

        return false;
    };

    const handleKeyDown = (event) => {
        for (const shortcut of shortcuts) {
            if (!shortcut.handler || !matches(shortcut, event)) {
                continue;
            }

            if (!shortcut.whenEditable && isEditableTarget(event.target)) {
                return;
            }

            event.preventDefault();
            shortcut.handler(event);
            return;
        }
    };

    return {
        shortcuts,
        handleKeyDown,
    };
}
