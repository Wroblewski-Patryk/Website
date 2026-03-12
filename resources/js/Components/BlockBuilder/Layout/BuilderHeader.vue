<template>
    <div class="h-16 border-b border-base-300 bg-base-100/80 backdrop-blur-md flex items-center justify-between px-6 sticky top-0 z-[100]">
        <!-- Left: Title & Module Icon -->
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center text-primary shrink-0">
                <slot name="icon">
                    <PhLayout weight="duotone" class="w-5 h-5" />
                </slot>
            </div>
            <div class="flex flex-col min-w-0">
                <div class="flex items-center gap-1.5">
                    <input
                        type="text"
                        :value="title"
                        @input="$emit('update:title', $event.target.value)"
                        :placeholder="t('admin.builder.untitled', 'Untitled...')"
                        class="bg-transparent border-none focus:outline-none text-sm font-bold p-0 placeholder:opacity-30 min-w-[180px] max-w-[360px] truncate"
                    />
                    <PhPencilSimple weight="bold" class="w-3 h-3 text-base-content/40 shrink-0" />
                </div>
            </div>
        </div>

        <!-- Center: Viewport Controls -->
        <div class="flex items-center gap-2">
            <div class="join">
                <button
                    v-for="v in viewports" :key="v.id"
                    @click="$emit('update:viewport', v.id)"
                    class="btn btn-xs join-item gap-2 h-8 px-3 font-normal"
                    :class="currentViewport === v.id ? 'btn-primary' : 'btn-ghost'"
                >
                    <component :is="v.icon" weight="bold" class="w-3 h-3" />
                    <span v-if="currentViewport === v.id" class="text-[10px] font-normal uppercase">{{ v.name }}</span>
                </button>
            </div>

             <div v-if="currentViewport === 'custom'" class="join">
                <input
                    type="number"
                    min="1"
                    :value="customWidth"
                    @input="emit('update:customWidth', parseDimension($event.target.value, customWidth))"
                    class="input input-bordered input-xs join-item w-16 px-1 text-center font-mono"
                    :title="t('admin.builder.viewport_width', 'Custom width')"
                />
                <span class="join-item px-1 text-[10px] opacity-50 font-black flex items-center">x</span>
                <input
                    type="number"
                    min="1"
                    :value="customHeight"
                    @input="emit('update:customHeight', parseDimension($event.target.value, customHeight))"
                    class="input input-bordered input-xs join-item w-16 px-1 text-center font-mono"
                    :title="t('admin.builder.viewport_height', 'Custom height')"
                />
                <span class="join-item px-2 text-[10px] opacity-50 font-black flex items-center">px</span>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-3">
            <slot name="actions-left"></slot>

            <div class="h-8 w-[1px] bg-base-content/15 mx-1"></div>

            <a
                v-if="previewUrl"
                :href="previewUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-ghost btn-sm gap-2 font-normal"
                :title="t('admin.builder.preview_action', 'Open Preview')"
            >
                <PhEye weight="bold" class="w-3 h-3" />
                <span class="hidden sm:inline">{{ t('admin.builder.preview', 'Preview') }}</span>
            </a>

            <button
                @click="$emit('save')"
                class="btn btn-primary btn-sm gap-2 font-normal"
                :disabled="saving"
            >
                <PhFloppyDisk v-if="!saving" weight="bold" class="w-3 h-3" />
                <span v-else class="loading loading-spinner loading-xs"></span>
                <span class="hidden sm:inline">{{ t('admin.builder.save', 'Save') }}</span>
            </button>

            <slot name="actions-right"></slot>
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
