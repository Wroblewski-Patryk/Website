export const BREAKPOINT_KEYS = ['desktop', 'tablet', 'phone'];

export const normalizeBreakpoint = (value) => {
    if (value === 'mobile') return 'phone';
    if (BREAKPOINT_KEYS.includes(value)) return value;
    return 'desktop';
};

export const resolveBreakpointFromWidth = (width) => {
    const value = Number(width);
    if (!Number.isFinite(value)) return 'desktop';
    if (value < 768) return 'phone';
    if (value < 1024) return 'tablet';
    return 'desktop';
};

export const getResponsiveSpacingValue = (style, breakpoint, key) => {
    if (!style || typeof style !== 'object') return undefined;
    const bp = normalizeBreakpoint(breakpoint);
    const scoped = style?.responsiveSpacing?.[bp]?.[key];
    if (scoped !== undefined && scoped !== null && scoped !== '') {
        return scoped;
    }
    return style[key];
};

export const setResponsiveSpacingValue = (style, breakpoint, key, value) => {
    if (!style || typeof style !== 'object') return style;

    const bp = normalizeBreakpoint(breakpoint);

    if (!style.responsiveSpacing || typeof style.responsiveSpacing !== 'object') {
        style.responsiveSpacing = {};
    }

    if (!style.responsiveSpacing[bp] || typeof style.responsiveSpacing[bp] !== 'object') {
        style.responsiveSpacing[bp] = {};
    }

    style.responsiveSpacing[bp][key] = value;
    return style;
};
