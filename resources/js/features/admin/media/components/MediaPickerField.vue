<template>
    <div class="space-y-2">
        <label v-if="label" class="label py-0">
            <span class="label-text text-[10px] uppercase font-bold opacity-50">{{ label }}</span>
        </label>

        <div
            v-if="!multiple && previewUrl"
            class="relative rounded-xl overflow-hidden border border-base-content/10 aspect-video bg-base-200 group"
        >
            <img :src="previewUrl" class="w-full h-full object-cover" :alt="t('admin.media.preview_alt', 'Selected media preview')" />
            <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                <button class="btn btn-sm btn-primary btn-circle" type="button" @click="openPicker">
                    <PhImageSquare weight="bold" class="w-4 h-4" />
                </button>
                <button class="btn btn-sm btn-error btn-circle" type="button" @click="clearSelection">
                    <PhTrash weight="bold" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <div
            v-else-if="multiple && resolvedPreviewItems.length > 0"
            class="grid grid-cols-3 gap-2 rounded-xl border border-base-content/10 p-2 bg-base-200/30"
        >
            <div
                v-for="item in resolvedPreviewItems"
                :key="item.id"
                class="relative rounded-lg overflow-hidden aspect-square bg-base-200"
            >
                <img :src="item.url" class="w-full h-full object-cover" :alt="t('admin.media.preview_alt', 'Selected media preview')" />
            </div>
        </div>

        <button
            v-else
            type="button"
            class="btn btn-sm btn-outline border-dashed border-base-content/20 hover:border-primary/50 text-[10px] uppercase tracking-widest gap-2 w-full h-24 flex flex-col items-center justify-center bg-base-200/40"
            :disabled="disabled"
            @click="openPicker"
        >
            <PhImageSquare weight="bold" class="w-6 h-6 opacity-30" />
            <span>{{ placeholder || t('admin.media.pick_file', 'Choose from media library') }}</span>
        </button>

        <div class="flex items-center gap-2">
            <input
                type="text"
                class="input input-bordered input-sm w-full font-mono text-[10px] opacity-70"
                :value="displayValue"
                readonly
            />
            <button type="button" class="btn btn-sm btn-ghost btn-square" :disabled="disabled" @click="openPicker">
                <PhArrowsClockwise weight="bold" class="w-4 h-4" />
            </button>
            <button type="button" class="btn btn-sm btn-ghost btn-square text-error" :disabled="!modelValue || disabled" @click="clearSelection">
                <PhTrash weight="bold" class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { PhArrowsClockwise, PhImageSquare, PhTrash } from '@phosphor-icons/vue';
import { useMediaPickerStore } from '@/Stores/useMediaPickerStore';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    modelValue: {
        type: [Number, String, Array, null],
        default: () => null,
    },
    previewUrl: {
        type: String,
        default: '',
    },
    mediaType: {
        type: String,
        default: 'all',
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    previewItems: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'selected', 'cleared']);
const mediaPicker = useMediaPickerStore();
const { t } = useTranslations();

const resolvedPreviewItems = computed(() => {
    if (!props.multiple) return [];
    return Array.isArray(props.previewItems) ? props.previewItems.filter((item) => item?.url) : [];
});

const displayValue = computed(() => {
    if (props.multiple) {
        const ids = Array.isArray(props.modelValue) ? props.modelValue : [];
        return ids.length > 0
            ? `${ids.length} selected (${ids.join(', ')})`
            : t('admin.media.no_file_selected', 'No file selected');
    }

    if (!props.modelValue) {
        return t('admin.media.no_file_selected', 'No file selected');
    }
    return `ID: ${props.modelValue}`;
});

const openPicker = async () => {
    if (props.disabled) return;

    try {
        const file = await mediaPicker.open({ type: props.mediaType, multiple: props.multiple });
        if (!file) return;

        if (props.multiple) {
            const items = Array.isArray(file) ? file : [];
            emit('update:modelValue', items.map((item) => item.id));
            emit('selected', items);
            return;
        }

        emit('update:modelValue', file.id ?? null);
        emit('selected', file);
    } catch (error) {
        // Cancelled picker selection.
    }
};

const clearSelection = () => {
    emit('update:modelValue', props.multiple ? [] : null);
    emit('cleared');
};
</script>
