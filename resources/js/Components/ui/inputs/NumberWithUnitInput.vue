<template>
    <div class="join w-full">
        <input
            type="number"
            :value="numericValue"
            :disabled="disabled || isAutoSelected"
            @input="handleInput($event.target.value)"
            class="input input-sm input-bordered join-item h-8 min-h-0 w-full border-r-0 bg-base-100 font-mono shadow-none focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
            :class="(disabled || isAutoSelected) ? 'cursor-not-allowed opacity-50' : ''"
            :placeholder="placeholder"
        />
        <UnitSelect
            v-model="localUnit"
            :options="unitOptions"
            :join-item="true"
            :disabled="disabled"
            width-class="w-20"
            extra-class="rounded-l-none"
        />
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import UnitSelect from './UnitSelect.vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'auto' },
    disabled: { type: Boolean, default: false },
    units: {
        type: Array,
        default: () => ['px', '%', 'rem', 'em', 'vh', 'vw']
    },
    allowAuto: { type: Boolean, default: false }
});

const emit = defineEmits(['update:modelValue']);

const localUnit = ref('px');

const unitOptions = computed(() => {
    const base = Array.isArray(props.units) ? props.units : ['px', '%', 'rem', 'em', 'vh', 'vw'];
    if (!props.allowAuto) {
        return base;
    }

    if (base.includes('auto')) {
        return base;
    }

    return ['auto', ...base];
});

const isAutoSelected = computed(() => localUnit.value === 'auto');

watch(
    () => props.modelValue,
    (newVal) => {
        if (newVal === 'auto') {
            localUnit.value = 'auto';
            return;
        }

        if (!newVal) return;
        const match = String(newVal).match(/[a-zA-Z%]+$/);
        if (match) {
            localUnit.value = match[0];
        }
    },
    { immediate: true }
);

const numericValue = computed(() => {
    if (props.modelValue === 'auto') return '';
    if (!props.modelValue) return '';
    const match = String(props.modelValue).match(/^-?\d*\.?\d+/);
    return match ? match[0] : '';
});

const handleInput = (val) => {
    if (isAutoSelected.value) {
        emit('update:modelValue', 'auto');
        return;
    }

    if (val === '' || val === null || val === undefined) {
        emit('update:modelValue', undefined);
        return;
    }
    emit('update:modelValue', `${val}${localUnit.value}`);
};

watch(localUnit, () => {
    if (localUnit.value === 'auto') {
        emit('update:modelValue', 'auto');
        return;
    }

    if (numericValue.value !== '') {
        emit('update:modelValue', `${numericValue.value}${localUnit.value}`);
    }
});
</script>
