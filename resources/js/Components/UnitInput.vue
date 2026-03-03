<template>
    <div class="join w-full">
        <input 
            type="number" 
            :value="numericValue" 
            @input="handleInput($event.target.value)"
            class="input input-sm input-bordered join-item w-full bg-base-100 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
            :placeholder="placeholder" 
        />
        <select 
            v-model="localUnit"
            @change="handleUnitChange"
            class="select select-sm select-bordered join-item bg-base-200"
        >
            <option value="px">px</option>
            <option value="%">%</option>
            <option value="rem">rem</option>
            <option value="em">em</option>
            <option value="vh">vh</option>
            <option value="vw">vw</option>
        </select>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'auto' }
});

const emit = defineEmits(['update:modelValue']);

// Local state for unit to prevent reverting when typing or empty
const localUnit = ref('px');

watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        const match = String(newVal).match(/[a-zA-Z%]+$/);
        if (match) {
            localUnit.value = match[0];
        }
    }
}, { immediate: true });

const numericValue = computed(() => {
    if (!props.modelValue) return '';
    const match = String(props.modelValue).match(/^-?\d*\.?\d+/);
    return match ? match[0] : '';
});

const handleInput = (val) => {
    if (val === '' || val === null || val === undefined) {
        emit('update:modelValue', undefined);
    } else {
        emit('update:modelValue', `${val}${localUnit.value}`);
    }
};

const handleUnitChange = () => {
    // Only emit if there's a number to attach it to, otherwise just keep unit in local state
    if (numericValue.value !== '') {
        emit('update:modelValue', `${numericValue.value}${localUnit.value}`);
    }
};
</script>
