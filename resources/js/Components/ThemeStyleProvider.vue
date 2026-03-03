<script setup>
import { computed, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Rough conversion from HEX to HSL space for DaisyUI
const hexToHsl = (hex) => {
    if (!hex) return '';
    let r = 0, g = 0, b = 0;
    if (hex.length === 4) {
        r = parseInt(hex[1] + hex[1], 16);
        g = parseInt(hex[2] + hex[2], 16);
        b = parseInt(hex[3] + hex[3], 16);
    } else if (hex.length === 7) {
        r = parseInt(hex[1] + hex[2], 16);
        g = parseInt(hex[3] + hex[4], 16);
        b = parseInt(hex[5] + hex[6], 16);
    }
    r /= 255; g /= 255; b /= 255;
    let cmin = Math.min(r,g,b),
        cmax = Math.max(r,g,b),
        delta = cmax - cmin,
        h = 0, s = 0, l = 0;

    if (delta == 0) h = 0;
    else if (cmax == r) h = ((g - b) / delta) % 6;
    else if (cmax == g) h = (b - r) / delta + 2;
    else h = (r - g) / delta + 4;

    h = Math.round(h * 60);
    if (h < 0) h += 360;
    l = (cmax + cmin) / 2;
    s = delta == 0 ? 0 : delta / (1 - Math.abs(2 * l - 1));
    s = +(s * 100).toFixed(1);
    l = +(l * 100).toFixed(1);

    // DaisyUI requires just "h s% l%"
    return `${h} ${s}% ${l}%`;
};

const page = usePage();

const generateCss = (config) => {
    if (!config || !config.globals) return '';
    
    const globals = config.globals;
    const colors = globals.colors || {};
    const fonts = globals.fonts || {};
    const radius = globals.borderRadius || {};
    
    // We override the :root elements so all themes get affected
    // We might want to target `[data-theme]` specifically if they switch themes, but overriding :root is safer for universal application
    return `
        :root, [data-theme] {
            ${colors['primary'] ? `--p: ${hexToHsl(colors['primary'])};` : ''}
            ${colors['secondary'] ? `--s: ${hexToHsl(colors['secondary'])};` : ''}
            ${colors['accent'] ? `--a: ${hexToHsl(colors['accent'])};` : ''}
            ${colors['neutral'] ? `--n: ${hexToHsl(colors['neutral'])};` : ''}
            ${colors['base-100'] ? `--b1: ${hexToHsl(colors['base-100'])};` : ''}
            ${colors['info'] ? `--in: ${hexToHsl(colors['info'])};` : ''}
            ${colors['success'] ? `--su: ${hexToHsl(colors['success'])};` : ''}
            ${colors['warning'] ? `--wa: ${hexToHsl(colors['warning'])};` : ''}
            ${colors['error'] ? `--er: ${hexToHsl(colors['error'])};` : ''}
            
            ${radius.box ? `--rounded-box: ${radius.box};` : ''}
            ${radius.btn ? `--rounded-btn: ${radius.btn};` : ''}
            ${radius.badge ? `--rounded-badge: ${radius.badge};` : ''}
            
            --font-primary: '${fonts.heading || 'Inter'}', sans-serif;
            --font-secondary: '${fonts.body || 'Inter'}', sans-serif;
        }

        /* Apply fonts */
        body { font-family: var(--font-secondary); }
        h1, h2, h3, h4, h5, h6, .card-title { font-family: var(--font-primary); }
    `;
};

const updateStyles = (config) => {
    let styleEl = document.getElementById('theme-configurator-styles');
    if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = 'theme-configurator-styles';
        document.head.appendChild(styleEl);
    }
    styleEl.innerHTML = generateCss(config);
};

onMounted(() => {
    updateStyles(page.props.theme_config);
    
    // Listen to live updates from the configurator UI
    window.addEventListener('theme-config-updated', (e) => {
        const liveConfig = { globals: e.detail };
        updateStyles(liveConfig);
    });
});

watch(() => page.props.theme_config, (newConfig) => {
    updateStyles(newConfig);
}, { deep: true });

</script>

<template>
    <!-- This component is renderless -->
</template>
