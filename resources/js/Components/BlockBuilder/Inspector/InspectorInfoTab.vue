<template>
    <div class="flex-1 overflow-y-auto px-4 pb-4 custom-scrollbar">
        <div class="space-y-6 pt-2">
            <!-- Common Info (Module Specific) -->
            <slot name="module-info"></slot>

            <!-- Module Specific SEO/Advanced toggle if provided via slot -->
            <slot name="extra-info"></slot>
        </div>
    </div>
</template>

<script setup>
defineProps({
    totalBlocks: {
        type: Number,
        default: 0
    },
    lastSaved: {
        type: String,
        default: ''
    }
});
</script>
