<template>
    <div class="space-y-6">
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-4">
                <input type="checkbox" v-model="modelValue.enabled" class="checkbox checkbox-primary checkbox-sm" />
                <span class="text-[10px] uppercase font-bold opacity-50">{{ t('admin.builder.tab_animations', 'Animations') }}</span>
            </label>
        </div>

        <div v-if="modelValue.enabled" class="space-y-4 rounded-2xl border border-white/5 bg-base-300/30 p-4">
            <div class="form-control" v-if="libraryOptions.length">
                <label class="label"><span class="label-text text-[10px] uppercase opacity-50">Preset Library</span></label>
                <select v-model="selectedLibraryPresetId" class="select select-bordered select-sm w-full" @change="applyLibraryPreset">
                    <option value="">Built-in only</option>
                    <option v-for="item in libraryOptions" :key="item.id" :value="String(item.id)">
                        {{ item.name }}
                    </option>
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase opacity-50">{{ t('admin.builder.animation_entrance', 'Entrance Animation') }}</span></label>
                <select v-model="modelValue.preset" class="select select-bordered select-sm w-full">
                    <option v-for="option in builtInPresetOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase opacity-50">Trigger</span></label>
                <select v-model="modelValue.trigger" class="select select-bordered select-sm w-full">
                    <option value="onEnter">On Enter</option>
                    <option value="onLoad">On Load</option>
                    <option value="onScroll">On Scroll</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="form-control">
                    <label class="label flex justify-between">
                        <span class="label-text text-[10px] uppercase opacity-50">{{ t('admin.builder.animation_duration', 'Duration') }}</span>
                        <span class="text-[10px] font-mono opacity-50">{{ normalizedDuration.toFixed(2) }}s</span>
                    </label>
                    <input type="range" v-model.number="modelValue.duration" min="0.1" max="6" step="0.1" class="range range-xs range-primary" />
                </div>

                <div class="form-control">
                    <label class="label flex justify-between">
                        <span class="label-text text-[10px] uppercase opacity-50">{{ t('admin.builder.animation_delay', 'Delay') }}</span>
                        <span class="text-[10px] font-mono opacity-50">{{ normalizedDelay.toFixed(2) }}s</span>
                    </label>
                    <input type="range" v-model.number="modelValue.delay" min="0" max="4" step="0.1" class="range range-xs" />
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase opacity-50">{{ t('admin.builder.animation_easing', 'Easing') }}</span></label>
                <select v-model="modelValue.ease" class="select select-bordered select-xs w-full">
                    <option value="power1.out">{{ t('admin.builder.animation_ease_out', 'Ease Out') }}</option>
                    <option value="power2.out">{{ t('admin.builder.animation_strong_out', 'Strong Out') }}</option>
                    <option value="back.out(1.7)">{{ t('admin.builder.animation_elastic', 'Elastic') }}</option>
                    <option value="none">{{ t('admin.builder.animation_linear', 'Linear') }}</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase opacity-50">Timeline ID</span></label>
                <input
                    v-model="modelValue.timelineId"
                    type="text"
                    class="input input-bordered input-sm w-full font-mono text-xs"
                    placeholder="main-sequence"
                />
            </div>

            <label class="label cursor-pointer justify-start gap-4">
                <input type="checkbox" v-model="modelValue.once" class="checkbox checkbox-primary checkbox-xs" />
                <span class="text-[10px] uppercase font-bold opacity-50">{{ t('admin.builder.animation_once', 'Play once only') }}</span>
            </label>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();
const page = usePage();

const builtInPresetOptions = [
    { value: 'fade-up', label: 'Fade Up' },
    { value: 'fade-in', label: 'Fade In' },
    { value: 'slide-left', label: 'Slide Left' },
    { value: 'slide-right', label: 'Slide Right' },
    { value: 'zoom-in', label: 'Zoom In' },
    { value: 'clip-reveal', label: 'Clip Reveal' },
    { value: 'reveal-text', label: 'Reveal Text' },
];

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
        default: () => ({}),
    },
});

const selectedLibraryPresetId = ref('');

const libraryOptions = computed(() => {
    const items = page.props.animation_presets_library;
    return Array.isArray(items) ? items : [];
});

const normalizedDuration = computed(() => Number(props.modelValue.duration ?? 0.8) || 0.8);
const normalizedDelay = computed(() => Number(props.modelValue.delay ?? 0) || 0);

const ensureDefaults = () => {
    if (props.modelValue.enabled === undefined) props.modelValue.enabled = false;
    if (!props.modelValue.trigger) props.modelValue.trigger = 'onEnter';
    if (!props.modelValue.preset) props.modelValue.preset = 'fade-up';
    if (props.modelValue.duration === undefined || props.modelValue.duration === null) props.modelValue.duration = 0.8;
    if (props.modelValue.delay === undefined || props.modelValue.delay === null) props.modelValue.delay = 0;
    if (!props.modelValue.ease) props.modelValue.ease = 'power2.out';
    if (props.modelValue.once === undefined) props.modelValue.once = true;
    if (!props.modelValue.timelineId) props.modelValue.timelineId = '';
};

const applyLibraryPreset = () => {
    if (!selectedLibraryPresetId.value) {
        props.modelValue.presetSource = 'builtin';
        props.modelValue.presetId = null;
        return;
    }

    const selected = libraryOptions.value.find((item) => String(item.id) === selectedLibraryPresetId.value);
    if (!selected || !selected.definition) return;

    const def = selected.definition;
    props.modelValue.presetSource = 'library';
    props.modelValue.presetId = selected.id;
    props.modelValue.enabled = true;
    props.modelValue.trigger = def.trigger || 'onEnter';
    props.modelValue.preset = def.preset || 'fade-up';
    props.modelValue.duration = Number(def.duration ?? 0.8) || 0.8;
    props.modelValue.delay = Number(def.delay ?? 0) || 0;
    props.modelValue.ease = def.ease || 'power2.out';
    props.modelValue.once = def.once ?? true;
    props.modelValue.timelineId = def.timeline_id || '';
    props.modelValue.tween = def.tween || { from: {}, to: {} };
};

watch(() => props.modelValue, () => {
    ensureDefaults();
}, { immediate: true, deep: true });

watch(() => props.modelValue.presetId, (value) => {
    selectedLibraryPresetId.value = value ? String(value) : '';
}, { immediate: true });
</script>
