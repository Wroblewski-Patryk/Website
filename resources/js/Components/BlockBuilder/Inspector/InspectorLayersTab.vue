<template>
    <div class="flex-1 overflow-y-auto px-4 pb-4 custom-scrollbar pt-4">
        <LayerTreeItem 
            :blocks="modelValue" 
        />
    </div>
</template>

<script setup>
import LayerTreeItem from '@/Components/LayerTreeItem.vue';

const props = defineProps({
    modelValue: {
        type: Array,
        required: true
    },
    activeBlockId: String
});

const emit = defineEmits(['select', 'delete', 'toggle-visibility']);
</script>

<style scoped>
.ghost-block {
    position: relative !important;
    opacity: 1 !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    height: 24px !important;
    margin: 0 !important;
    overflow: visible !important;
}

.ghost-block::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 2px;
    background: var(--admin-primary);
    transform: translateY(-50%);
    border-radius: 2px;
    box-shadow: 0 0 8px var(--admin-primary);
    z-index: 10;
}
</style>
