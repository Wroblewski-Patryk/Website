<script setup>
import { watch, markRaw } from 'vue';
import ConfiguratorLayout from './ConfiguratorLayout.vue';
import { PhPalette, PhHouse, PhPaintRoller } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.menu.theme', 'Theme'), url: route('admin.theme.index') },
    { label: t('admin.menu.colors', 'Colors') }
];

// Rough approximation for live preview 
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

    return `${h} ${s}% ${l}%`;
};

// We will inject a watcher inside the template using a setup hook or directly on the component
const setupLivePreview = (form) => {
    watch(() => form.globals.colors, (newColors) => {
        const root = document.documentElement;
        Object.entries(newColors).forEach(([key, value]) => {
            const hsl = hexToHsl(value);
            switch(key) {
                case 'primary': root.style.setProperty('--p', hsl); break;
                case 'secondary': root.style.setProperty('--s', hsl); break;
                case 'accent': root.style.setProperty('--a', hsl); break;
                case 'neutral': root.style.setProperty('--n', hsl); break;
                case 'base-100': root.style.setProperty('--b1', hsl); break;
                case 'info': root.style.setProperty('--in', hsl); break;
                case 'success': root.style.setProperty('--su', hsl); break;
                case 'warning': root.style.setProperty('--wa', hsl); break;
                case 'error': root.style.setProperty('--er', hsl); break;
            }
        });
    }, { deep: true });
};
</script>

<template>
    <ConfiguratorLayout 
        :title="t('admin.theme.colors_title', 'Colors')" 
        :description="t('admin.theme.colors_desc', 'Manage the main DaisyUI color palette used across all blocks.')"
        :breadcrumbs="breadcrumbs">
        <template #default="{ form }">
            
            {{ setupLivePreview(form) }}

            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4 border-b border-base-200 pb-2">
                        <PhPalette weight="regular" class="w-6 h-6 text-primary inline-block align-text-bottom" /> 
                        {{ t('admin.theme.colors_title', 'DaisyUI Colors') }}
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <div v-for="(val, key) in form.globals.colors" :key="key" class="form-control">
                            <label class="label pb-1 cursor-pointer">
                                <span class="label-text font-medium capitalize">{{ key.replace('-', ' ') }}</span>
                                <span class="label-text-alt uppercase font-mono text-xs opacity-60">{{ val }}</span>
                            </label>
                            <div class="flex items-center gap-3 bg-base-200 p-2 rounded-lg border border-base-300">
                                <input type="color" v-model="form.globals.colors[key]" class="w-10 h-10 p-0 border-0 rounded cursor-pointer bg-transparent" />
                                <input type="text" v-model="form.globals.colors[key]" class="input input-sm border-0 bg-transparent flex-1 font-mono uppercase focus:outline-none" />
                            </div>
                        </div>
                    </div>

                    <!-- Preview Buttons -->
                    <div class="mt-8 p-6 bg-base-200 rounded-box border border-base-300 flex flex-wrap gap-4 items-center justify-center">
                        <h3 class="w-full text-center text-xs font-black uppercase tracking-widest opacity-30 mb-2">{{ t('admin.theme.live_preview', 'Live Preview') }}</h3>
                        <button class="btn" :style="{ backgroundColor: form.globals.colors.primary, color: form.globals.colors['base-100'], border: 'none' }">Primary Button</button>
                        <button class="btn" :style="{ backgroundColor: form.globals.colors.secondary, color: form.globals.colors['base-100'], border: 'none' }">Secondary Button</button>
                        <button class="btn" :style="{ backgroundColor: form.globals.colors.accent, color: form.globals.colors['base-100'], border: 'none' }">Accent Button</button>
                    </div>
                </div>
            </div>

        </template>
    </ConfiguratorLayout>
</template>
