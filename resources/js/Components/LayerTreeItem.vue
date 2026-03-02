<template>
    <draggable 
        :list="blocks"
        item-key="id" 
        handle=".drag-handle"
        group="blocks"
        ghost-class="opacity-50"
        @change="$emit('change', $event)"
        class="space-y-1"
    >
        <template #item="{ element }">
            <div>
                <!-- Item Row -->
                <div 
                    class="text-xs py-2 px-3 border border-white/5 rounded-lg flex items-center gap-3 bg-base-200/30 hover:bg-base-200 transition-colors"
                    :class="{ 'ring-1 ring-primary bg-primary/10': store.activeBlockId === element.id }"
                >
                    <!-- Indentation Visuals -->
                    <div v-if="depth > 0" class="flex items-center" :style="{ paddingLeft: (depth - 1) * 1 + 'rem' }">
                        <div class="h-px border-t border-dashed border-white/20 w-3 -ml-1 mr-2"></div>
                    </div>

                    <i class="fas fa-grip-vertical opacity-20 cursor-move drag-handle"></i>
                    <i :class="element.icon || 'fas fa-cube'" class="opacity-50"></i>
                    <span class="font-semibold cursor-pointer flex-1" @click="store.activeBlockId = element.id">
                        {{ element.type.charAt(0).toUpperCase() + element.type.slice(1).replace('_', ' ') }}
                    </span>
                    <span class="opacity-30 text-[10px] font-mono">{{ element.id.split('-')[0] }}</span>
                    <button @click.stop="store.removeBlock(element.id)" class="btn btn-ghost btn-xs btn-circle text-error ml-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <!-- Recursive Children Rendering -->
                <div v-if="element.children && element.children.length > 0" class="mt-1">
                    <LayerTreeItem 
                        :blocks="element.children" 
                        :depth="depth + 1" 
                        @change="$emit('change', $event)"
                    />
                </div>
            </div>
        </template>
    </draggable>
</template>

<script setup>
import draggable from 'vuedraggable';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';

const props = defineProps({
    blocks: {
        type: Array,
        required: true
    },
    depth: {
        type: Number,
        default: 0
    }
});

defineEmits(['change']);

const store = useBlockBuilderStore();
</script>
