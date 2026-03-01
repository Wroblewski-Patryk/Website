<template>
    <div class="join w-full">
        <input 
            type="number" 
            :value="numericValue" 
            @input="updateValue($event.target.value, unit)"
            class="input input-sm input-bordered join-item w-full bg-base-100" 
            :placeholder="placeholder" 
        />
        <select 
            :value="unit" 
            @change="updateValue(numericValue, $event.target.value)"
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
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'auto' }
});

const emit = defineEmits(['update:modelValue']);

const numericValue = computed(() => {
    if (!props.modelValue) return '';
    const match = String(props.modelValue).match(/^-?\d*\.?\d+/);
    return match ? match[0] : '';
});

const unit = computed(() => {
    if (!props.modelValue) return 'px';
    const match = String(props.modelValue).match(/[a-zA-Z%]+$/);
    return match ? match[0] : 'px';
});

const updateValue = (num, u) => {
    if (!num && num !== 0 && num !== '0') {
        emit('update:modelValue', undefined);
    } else {
        emit('update:modelValue', `${num}${u}`);
    }
};
</script>
