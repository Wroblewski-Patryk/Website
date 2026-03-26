<script setup>
defineProps({
    message: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <label v-if="message" class="label">
        <span class="label-text-alt text-error font-medium">{{ message }}</span>
    </label>
</template>
