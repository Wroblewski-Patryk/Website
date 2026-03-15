<template>
    <div class="h-16 border-b border-base-300 bg-base-100/80 backdrop-blur-md flex items-center justify-between px-6 sticky top-0 z-[100]">
        <!-- 1: Title & Icon (Container 1) -->
        <div class="flex items-center gap-3 shrink-0 mr-2 min-w-0 max-w-[300px]">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-primary shrink-0 bg-primary/5">
                <slot name="icon">
                    <PhLayout weight="duotone" class="w-4 h-4" />
                </slot>
            </div>
            <div class="flex flex-col min-w-0">
                <span v-if="moduleLabel" class="text-[9px] font-black uppercase tracking-widest opacity-30 leading-none mb-1">{{ moduleLabel }}</span>
                <div class="flex items-center gap-1.5">
                    <input
                        type="text"
                        :value="title"
                        @input="$emit('update:title', $event.target.value)"
                        :placeholder="t('admin.builder.untitled', 'Untitled...')"
                        class="bg-transparent border-none focus:outline-none text-xs font-bold p-0 placeholder:opacity-30 truncate"
                    />
                </div>
            </div>
        </div>

        <div class="h-4 w-[1px] bg-base-content/10 mx-4 shrink-0"></div>

        <!-- 2: Languages (Container 2) -->
        <div class="flex items-center shrink-0 px-2">
            <slot name="languages"></slot>
        </div>

        <div class="h-4 w-[1px] bg-base-content/10 mx-4 shrink-0"></div>

        <!-- 3: Sidebars Toggles (Container 3) -->
        <div class="flex items-center shrink-0 px-2">
            <slot name="sidebars"></slot>
        </div>

        <div class="h-4 w-[1px] bg-base-content/10 mx-4 shrink-0"></div>

        <!-- 4: Viewport Controls (Container 4) -->
        <div class="flex items-center gap-1 shrink-0 px-2">
            <div class="join bg-base-200/50 p-0.5 rounded-lg">
                <button
                    v-for="v in viewports" :key="v.id"
                    @click="$emit('update:viewport', v.id)"
                    class="btn btn-xs join-item h-7 px-2.5 font-normal border-none"
                    :class="currentViewport === v.id ? 'btn-primary shadow-sm' : 'btn-ghost opacity-60 hover:opacity-100'"
                    :title="v.name"
                >
                    <component :is="v.icon" weight="bold" class="w-3.5 h-3.5" />
                </button>
            </div>

            <div v-if="currentViewport === 'custom'" class="flex items-center gap-1 ml-2 bg-base-200/50 p-0.5 rounded-lg px-2">
                <input
                    type="number"
                    min="1"
                    :value="customWidth"
                    @input="emit('update:customWidth', parseDimension($event.target.value, customWidth))"
                    class="bg-transparent w-10 text-[10px] text-center font-mono focus:outline-none"
                />
                <span class="text-[9px] opacity-30 font-black">×</span>
                <input
                    type="number"
                    min="1"
                    :value="customHeight"
                    @input="emit('update:customHeight', parseDimension($event.target.value, customHeight))"
                    class="bg-transparent w-10 text-[10px] text-center font-mono focus:outline-none"
                />
            </div>
        </div>

        <div class="h-4 w-[1px] bg-base-content/10 mx-4 shrink-0"></div>

        <!-- 5: Tools (3D, Layers) (Container 5) -->
        <div class="flex items-center shrink-0 px-2">
            <slot name="tools"></slot>
        </div>

        <div class="h-4 w-[1px] bg-base-content/10 mx-4 shrink-0"></div>

        <!-- 6: Zoom (Container 6) -->
        <div class="flex items-center shrink-0 px-2">
            <slot name="zoom"></slot>
        </div>

        <div class="h-4 w-[1px] bg-base-content/10 mx-4 shrink-0"></div>

        <!-- 7: Actions (Container 7) -->
        <div class="flex items-center gap-2 shrink-0 pl-2">
            <slot name="actions-pre"></slot>
            
            <a
                v-if="previewUrl"
                :href="previewUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-ghost btn-sm h-8 min-h-0 px-3 gap-2 font-medium text-xs rounded-lg shadow-sm bg-base-200/50"
                :title="t('admin.builder.preview_action', 'Open Preview')"
            >
                <PhEye weight="bold" class="w-3.5 h-3.5 text-primary" />
                <span class="hidden xl:inline tracking-wide">{{ t('admin.builder.preview', 'Preview') }}</span>
            </a>

            <button
                @click="$emit('save')"
                class="btn btn-primary btn-sm h-8 min-h-0 px-4 gap-2 font-bold text-xs rounded-lg shadow-lg shadow-primary/20"
                :disabled="saving"
            >
                <PhFloppyDisk v-if="!saving" weight="bold" class="w-3.5 h-3.5" />
                <span v-else class="loading loading-spinner loading-xs text-primary-content"></span>
                <span>{{ t('admin.builder.save', 'Save') }}</span>
            </button>

            <slot name="actions-post"></slot>
        </div>
    </div>
</template>

<script setup>
import {
    PhLayout,
    PhDesktop,
    PhDeviceTablet,
    PhDeviceMobile,
    PhArrowsOut,
    PhPencilSimple,
    PhFloppyDisk,
    PhEye
} from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';

defineProps({
    title: String,
    moduleLabel: String,
    currentViewport: {
        type: String,
        default: 'desktop'
    },
    customWidth: {
        type: Number,
        default: 1920
    },
    customHeight: {
        type: Number,
        default: 1080
    },
    previewUrl: {
        type: String,
        default: ''
    },
    saving: Boolean
});

const emit = defineEmits(['update:title', 'update:viewport', 'update:customWidth', 'update:customHeight', 'save']);
const { t } = useTranslations();

const parseDimension = (value, fallback) => {
    const next = Number(value);
    if (!Number.isFinite(next) || next < 1) return fallback;
    return Math.round(next);
};

const viewports = computed(() => [
    { id: 'desktop', name: t('admin.builder.viewport_desktop', 'Desktop'), icon: PhDesktop },
    { id: 'tablet', name: t('admin.builder.viewport_tablet', 'Tablet'), icon: PhDeviceTablet },
    { id: 'mobile', name: t('admin.builder.viewport_mobile', 'Mobile'), icon: PhDeviceMobile },
    { id: 'custom', name: t('admin.builder.viewport_custom', 'Custom'), icon: PhArrowsOut }
]);
</script>
