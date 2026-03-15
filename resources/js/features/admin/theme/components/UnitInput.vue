<template>
    <NumberWithUnitInput v-model="model" :placeholder="placeholder" />
</template>

<script setup>
import { computed } from 'vue';
import NumberWithUnitInput from '@/Components/ui/inputs/NumberWithUnitInput.vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'auto' }
});

const emit = defineEmits(['update:modelValue']);

const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>
