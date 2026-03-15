<template>
    <div class="space-y-3 w-full">
        <!-- Header -->
        <div class="flex items-center justify-between mb-2">
            <h4 v-if="label" class="text-[10px] uppercase font-bold tracking-widest opacity-30">{{ label }}</h4>
            <div v-else></div>

            <div class="join bg-base-200 rounded-md">
                <button @click="toggleLinkAll" class="btn btn-sm join-item min-h-0 h-8 w-10 border-none" :class="linkAll ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" :title="t('admin.builder.unit_link_all', 'Link All')">
                    <PhLinkSimple class="w-4 h-4" weight="bold" />
                </button>
                <button @click="toggleLinkY" class="btn btn-sm join-item min-h-0 h-8 w-10 border-none" :class="linkY ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" :title="t('admin.builder.unit_link_y', 'Link Top & Bottom')">
                    <PhArrowsVertical class="w-4 h-4" weight="bold" />
                </button>
                <button @click="toggleLinkX" class="btn btn-sm join-item min-h-0 h-8 w-10 border-none" :class="linkX ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" :title="t('admin.builder.unit_link_x', 'Link Left & Right')">
                    <PhArrowsHorizontal class="w-4 h-4" weight="bold" />
                </button>
            </div>
        </div>

        <!-- Spatial Layout (Box Model Style) -->
        <div class="relative flex flex-col items-center justify-center gap-1 group overflow-hidden">
            <!-- Top Input -->
            <div class="w-full max-w-[8rem] z-10">
                <div class="text-[8px] uppercase font-bold tracking-widest text-center opacity-30 mb-1 cursor-default select-none">{{ t('admin.builder.unit_top', 'Top') }}</div>
                <NumberWithUnitInput
                    :model-value="internal.top"
                    @update:modelValue="updateTop"
                    placeholder="-"
                />
            </div>

            <!-- Middle Row: Left - Center - Right -->
            <div class="flex items-center w-full justify-between gap-2 z-10">
                <!-- Left Input -->
                <!-- If LinkAll OR LinkX is ON, Left takes value from Right (or Top if LinkAll) and is disabled -->
                <div class="flex-1 min-w-0">
                    <div class="text-[8px] uppercase font-bold tracking-widest text-center opacity-30 mb-1 cursor-default select-none" :class="{'opacity-10': linkAll || linkX}">{{ t('admin.builder.unit_left', 'Left') }}</div>
                    <NumberWithUnitInput
                        :model-value="internal.left"
                        @update:modelValue="updateLeft"
                        :disabled="linkAll || linkX"
                        placeholder="-"
                    />
                </div>

                <!-- Center visual indicator -->
                <div class="opacity-10 text-xl transition-all duration-300 group-hover:text-primary group-hover:opacity-40 select-none pointer-events-none shrink-0">
                    <PhSquare class="w-5 h-5" />
                </div>

                <!-- Right Input -->
                <!-- If LinkAll is ON, Right takes value from Top and is disabled -->
                <div class="flex-1 min-w-0">
                    <div class="text-[8px] uppercase font-bold tracking-widest text-center opacity-30 mb-1 cursor-default select-none" :class="{'opacity-10': linkAll}">{{ t('admin.builder.unit_right', 'Right') }}</div>
                    <NumberWithUnitInput
                        :model-value="internal.right"
                        @update:modelValue="updateRight"
                        :disabled="linkAll"
                        placeholder="-"
                    />
                </div>
            </div>

            <!-- Bottom Input -->
            <!-- If LinkAll OR LinkY is ON, Bottom takes value from Top and is disabled -->
            <div class="w-full max-w-[8rem] z-10">
                <div class="text-[8px] uppercase font-bold tracking-widest text-center opacity-30 mb-1 cursor-default select-none" :class="{'opacity-10': linkAll || linkY}">{{ t('admin.builder.unit_bottom', 'Bottom') }}</div>
                <NumberWithUnitInput
                    :model-value="internal.bottom"
                    @update:modelValue="updateBottom"
                    :disabled="linkAll || linkY"
                    placeholder="-"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, watch, ref } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';
import NumberWithUnitInput from '@/Components/ui/inputs/NumberWithUnitInput.vue';
import { PhLinkSimple, PhArrowsVertical, PhArrowsHorizontal, PhSquare } from '@phosphor-icons/vue';

const { t } = useTranslations();

const props = defineProps({
    top: { type: [String, Number], default: '' },
    right: { type: [String, Number], default: '' },
    bottom: { type: [String, Number], default: '' },
    left: { type: [String, Number], default: '' },
    label: { type: String, default: '' },
});

const emit = defineEmits(['update:top', 'update:right', 'update:bottom', 'update:left']);

// Toggles logic
const linkAll = ref(true); 
const linkX = ref(false);
const linkY = ref(false);

const internal = reactive({
    top: '',
    right: '',
    bottom: '',
    left: ''
});

const normalizeIncoming = (value) => {
    if (value === undefined || value === null) return '';
    return String(value);
};

const extractNumeric = (value) => {
    if (!value && value !== 0) return '';
    const match = String(value).match(/^-?\d*\.?\d+/);
    return match ? match[0] : '';
};

const extractUnit = (value) => {
    if (!value && value !== 0) return '';
    const match = String(value).match(/[a-zA-Z%]+$/);
    return match ? match[0] : '';
};

const numericKey = (value) => {
    const numeric = extractNumeric(value);
    if (numeric === '') return null;
    const parsed = Number(numeric);
    return Number.isFinite(parsed) ? parsed : null;
};

const hasNumeric = (value) => numericKey(value) !== null;

const sameNumeric = (a, b) => {
    const aNum = numericKey(a);
    const bNum = numericKey(b);
    return aNum !== null && bNum !== null && aNum === bNum;
};

const applyNumericFromSource = (sourceValue, targetValue) => {
    const numeric = extractNumeric(sourceValue);
    if (numeric === '') return '';
    const unit = extractUnit(targetValue) || extractUnit(sourceValue) || 'px';
    return `${numeric}${unit}`;
};

// Watch incoming value to sync local state
watch([() => props.top, () => props.right, () => props.bottom, () => props.left], ([t, r, b, l]) => {
    internal.top = normalizeIncoming(t);
    internal.right = normalizeIncoming(r);
    internal.bottom = normalizeIncoming(b);
    internal.left = normalizeIncoming(l);

    if (
        hasNumeric(internal.top) &&
        hasNumeric(internal.right) &&
        hasNumeric(internal.bottom) &&
        hasNumeric(internal.left) &&
        sameNumeric(internal.top, internal.right) &&
        sameNumeric(internal.top, internal.bottom) &&
        sameNumeric(internal.top, internal.left)
    ) {
        linkAll.value = true;
        linkX.value = false;
        linkY.value = false;
    } else {
        linkAll.value = false;
        linkX.value = sameNumeric(internal.left, internal.right);
        linkY.value = sameNumeric(internal.top, internal.bottom);
    }
}, { immediate: true, deep: true });

const emitChanges = () => {
    const formatValue = (value) => {
        if (value === '' || value === null || value === undefined) return undefined;
        return value;
    };

    emit('update:top', formatValue(normalizeIncoming(internal.top)));
    emit('update:right', formatValue(normalizeIncoming(internal.right)));
    emit('update:bottom', formatValue(normalizeIncoming(internal.bottom)));
    emit('update:left', formatValue(normalizeIncoming(internal.left)));
};

// Toggles Mutators
const toggleLinkAll = () => {
    linkAll.value = !linkAll.value;
    if (linkAll.value) {
        linkX.value = false;
        linkY.value = false;
        if (hasNumeric(internal.top)) {
            internal.right = applyNumericFromSource(internal.top, internal.right);
            internal.bottom = applyNumericFromSource(internal.top, internal.bottom);
            internal.left = applyNumericFromSource(internal.top, internal.left);
            emitChanges();
        }
    }
};

const toggleLinkX = () => {
    linkX.value = !linkX.value;
    if (linkX.value) {
        linkAll.value = false;
        if (hasNumeric(internal.right)) {
            internal.left = applyNumericFromSource(internal.right, internal.left);
            emitChanges();
        }
    }
};

const toggleLinkY = () => {
    linkY.value = !linkY.value;
    if (linkY.value) {
        linkAll.value = false;
        if (hasNumeric(internal.top)) {
            internal.bottom = applyNumericFromSource(internal.top, internal.bottom);
            emitChanges();
        }
    }
};

// Inputs Mutators
const updateTop = (val) => {
    internal.top = normalizeIncoming(val);
    if (linkAll.value) {
        internal.right = applyNumericFromSource(internal.top, internal.right);
        internal.bottom = applyNumericFromSource(internal.top, internal.bottom);
        internal.left = applyNumericFromSource(internal.top, internal.left);
    } else if (linkY.value) {
        internal.bottom = applyNumericFromSource(internal.top, internal.bottom);
    }
    emitChanges();
};

const updateRight = (val) => {
    internal.right = normalizeIncoming(val);
    if (!linkAll.value && linkX.value) {
        internal.left = applyNumericFromSource(internal.right, internal.left);
    }
    emitChanges();
};

const updateBottom = (val) => {
    internal.bottom = normalizeIncoming(val);
    emitChanges();
};

const updateLeft = (val) => {
    internal.left = normalizeIncoming(val);
    emitChanges();
};
</script>

