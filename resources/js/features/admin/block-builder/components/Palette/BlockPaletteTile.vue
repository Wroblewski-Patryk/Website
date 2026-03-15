<template>
    <button
        type="button"
        class="w-full flex flex-col items-center justify-center gap-1.5 px-3 py-3 rounded-box border border-base-content/10 bg-base-100/40 text-base-content/75 hover:bg-base-content/5 hover:border-base-content/20 transition-colors cursor-grab active:cursor-grabbing group focus:outline-none focus-visible:ring-1 focus-visible:ring-primary/40"
        @click="$emit('add')"
    >
        <component :is="iconComponent" weight="duotone" class="w-5 h-5 text-primary transition-colors" />
        <span class="text-[10px] font-medium leading-tight text-center">{{ label }}</span>
    </button>
</template>

<script setup>
defineProps({
    iconComponent: {
        type: [Object, Function],
        required: true
    },
    label: {
        type: String,
        required: true
    }
});

defineEmits(['add']);
</script>
