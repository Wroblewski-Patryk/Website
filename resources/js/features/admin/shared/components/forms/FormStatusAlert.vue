<script setup>
import { computed } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'success',
    },
});

const alertClass = computed(() => {
    if (props.variant === 'error') {
        return 'alert-error';
    }

    if (props.variant === 'warning') {
        return 'alert-warning';
    }

    return 'alert-success';
});
</script>

<template>
    <div v-if="message" class="alert mt-2" :class="alertClass">
        <span>{{ message }}</span>
    </div>
</template>
