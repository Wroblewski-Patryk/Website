<template>
    <div class="px-4 py-4 border-b border-base-content/10 flex items-center justify-between sticky top-0 bg-base-100/70 backdrop-blur-md z-20">
        <div class="flex items-center gap-2.5 min-w-0 text-base-content/80">
            <component :is="icon" :weight="iconWeight" :class="[iconClass, 'shrink-0', 'text-primary']" />
            <h3 class="text-sm font-medium truncate">{{ title }}</h3>
        </div>

        <slot name="actions"></slot>
    </div>
</template>

<script setup>
defineProps({
    icon: {
        type: [Object, Function],
        required: true
    },
    iconWeight: {
        type: String,
        default: 'duotone'
    },
    iconClass: {
        type: String,
        default: 'w-4 h-4'
    },
    title: {
        type: String,
        required: true
    }
});
</script>

