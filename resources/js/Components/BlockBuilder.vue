<template>
    <div class="h-full flex flex-col overflow-hidden bg-base-300">
        <!-- Top Bar -->
        <div class="flex items-center justify-between px-6 py-2 bg-base-100 border-b border-white/5 shadow-md z-20">
            <div class="flex items-center gap-4">
                <slot name="back-button">
                    <button @click="$inertia.visit(backRoute)" class="btn btn-ghost btn-sm">
                        <i class="fas fa-chevron-left mr-2"></i> {{ backLabel }}
                    </button>
                </slot>
                
                <div class="h-6 w-[1px] bg-white/10 mx-2"></div>
                
                <div class="join bg-base-200 p-1 rounded-xl shadow-inner">
                    <button @click="viewport = 'desktop'" class="btn btn-xs join-item" :class="{ 'btn-primary': viewport === 'desktop' }">
                        <i class="fas fa-desktop"></i>
                    </button>
                    <button @click="viewport = 'tablet'" class="btn btn-xs join-item" :class="{ 'btn-primary': viewport === 'tablet' }">
                        <i class="fas fa-tablet-alt"></i>
                    </button>
                    <button @click="viewport = 'mobile'" class="btn btn-xs join-item" :class="{ 'btn-primary': viewport === 'mobile' }">
                        <i class="fas fa-mobile-alt"></i>
                    </button>
                </div>

                <div class="h-6 w-[1px] bg-white/10 mx-2"></div>

                <!-- Zoom Controls -->
                <div class="join bg-base-200 p-1 rounded-xl shadow-inner flex items-center">
                    <button @click="zoomOut" class="btn btn-xs join-item hover:btn-primary" title="Zoom Out"><i class="fas fa-minus"></i></button>
                    <div class="px-2 text-[10px] font-mono opacity-70 w-12 text-center">{{ Math.round(zoomLevel * 100) }}%</div>
                    <button @click="zoomIn" class="btn btn-xs join-item hover:btn-primary" title="Zoom In"><i class="fas fa-plus"></i></button>
                    <button @click="resetZoom" class="btn btn-xs join-item hover:btn-primary" title="Reset Zoom"><i class="fas fa-undo"></i></button>
                    <div class="h-4 w-[1px] bg-white/10 mx-1"></div>
                    <button @click="toggleTimeline" class="btn btn-xs join-item hover:btn-primary" :class="{'btn-primary': showTimeline}" title="Layers & Animations">
                        <i class="fas fa-layer-group mr-1"></i> Pro
                    </button>
                </div>
                
                <slot name="top-bar-start"></slot>
            </div>
            
            <div class="flex items-center gap-3">
                <slot name="top-bar-end"></slot>
                <span v-if="store.isDirty" class="text-xs opacity-50 italic mr-2">Unsaved changes...</span>
                <button @click="$emit('save')" class="btn btn-primary btn-sm px-6 rounded-full shadow-lg shadow-primary/20" :disabled="saving">
                    <i class="fas fa-save mr-2"></i> {{ saveLabel }}
                </button>
            </div>
        </div>

        <div class="flex-1 flex overflow-hidden">
            <!-- Left Sidebar -->
            <div class="w-80 bg-base-100 border-r border-white/5 flex flex-col z-10 shadow-xl">
                <div class="tabs tabs-boxed bg-transparent p-2 m-2">
                    <button @click="leftTab = 'blocks'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'blocks' }">Blocks</button>
                    <button @click="leftTab = 'settings'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'settings' }">Info</button>
                    <button v-if="$slots.history" @click="leftTab = 'history'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'history' }">History</button>
                </div>

                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    <!-- Block Palette -->
                    <div v-show="leftTab === 'blocks'" class="space-y-6">
                        <div v-for="cat in categories" :key="cat.id" class="collapse collapse-arrow bg-base-200/50 border border-white/5 rounded-2xl overflow-hidden mb-2">
                            <input type="checkbox" :checked="cat.id === 'content' || cat.id === 'forms'" /> 
                            <div class="collapse-title text-[10px] font-black uppercase tracking-widest opacity-40 flex items-center gap-2">
                                <i :class="cat.icon"></i>
                                {{ cat.label || cat.name }}
                            </div>
                            <div class="collapse-content px-2 pb-2">
                                <draggable 
                                    :list="cat.blocks" 
                                    :group="{ name: 'blocks', pull: 'clone', put: false }" 
                                    :clone="cloneBlock"
                                    :sort="false"
                                    item-key="type"
                                    class="grid grid-cols-2 gap-2">
                                    <template #item="{ element }">
                                        <div @click="store.addBlock(element.type)" 
                                             class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/5 bg-base-200/50 hover:bg-primary/10 hover:border-primary/30 transition-all cursor-grab active:cursor-grabbing group">
                                            <i :class="[element.icon, 'text-lg opacity-40 group-hover:opacity-100 transition-opacity text-primary']"></i>
                                            <span class="text-[9px] font-bold leading-tight opacity-60 group-hover:opacity-100">{{ element.label }}</span>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                    </div>

                    <!-- Module Specific Settings -->
                    <div v-show="leftTab === 'settings'" class="space-y-4">
                        <slot name="info"></slot>
                    </div>

                    <!-- Revision History -->
                    <div v-show="leftTab === 'history'" class="space-y-4">
                        <slot name="history"></slot>
                    </div>
                </div>
            </div>

            <!-- Center: Wrapper -->
            <div class="flex-1 flex flex-col relative overflow-hidden bg-base-300">
                <!-- Center: Canvas Area -->
                <div class="flex-1 overflow-y-auto p-4 md:p-8 flex justify-center custom-scrollbar">
                    <div :class="[
                        'bg-white shadow-2xl transition-all duration-300 rounded-sm overflow-x-hidden min-h-screen relative mx-auto',
                        viewport === 'desktop' ? 'w-full' : (viewport === 'tablet' ? 'w-[768px]' : 'w-[375px]')
                    ]" :style="{ transform: `scale(${zoomLevel})`, transformOrigin: 'top center', marginBottom: `${(zoomLevel - 1) * 100}%` }">
                        <slot name="canvas-header"></slot>

                        <draggable 
                            v-model="blocks" 
                            :group="'blocks'"
                            item-key="id"
                            handle=".drag-handle"
                            ghost-class="ghost-block"
                            class="min-h-[600px] bg-base-100/30">
                            <template #item="{ element }">
                                <div class="group/block relative"
                                     @click="store.activeBlockId = element.id"
                                     :class="{ 'ring-2 ring-primary ring-inset': store.activeBlockId === element.id }">
                                    
                                    <div class="absolute right-2 top-2 z-40 opacity-0 group-hover/block:opacity-100 transition-opacity flex gap-1">
                                        <div class="drag-handle btn btn-square btn-xs btn-ghost bg-white/90 border border-black/10 backdrop-blur cursor-move text-black/60 shadow-lg rounded-full"><i class="fas fa-grip-vertical"></i></div>
                                        <button @click.stop="store.removeBlock(element.id)" class="btn btn-square btn-xs btn-error btn-ghost bg-red-500/90 border border-red-500/20 backdrop-blur text-white shadow-lg rounded-full"><i class="fas fa-trash"></i></button>
                                    </div>

                                    <DynamicBlock :block="element" />
                                </div>
                            </template>
                        </draggable>

                        <div v-if="store.blocks.length === 0" class="h-96 flex flex-col items-center justify-center border-2 border-dashed border-base-content/10 m-10 rounded-2xl bg-base-200/20 pointer-events-none">
                            <i class="fas fa-plus-circle text-4xl mb-4 opacity-20 text-primary"></i>
                            <p class="text-xs font-black uppercase tracking-widest opacity-40">Assemble Content Architecture</p>
                        </div>

                        <slot name="canvas-footer"></slot>
                    </div>
                </div>

                <!-- Bottom Timeline & Layers Panel -->
                <div ref="timelinePanel" class="absolute bottom-0 left-0 right-0 h-64 bg-base-100 border-t border-white/10 shadow-[0_-10px_40px_rgba(0,0,0,0.2)] flex flex-col z-[60] translate-y-full">
                    <div class="flex items-center justify-between px-4 py-2 bg-base-200/50 border-b border-white/5 backdrop-blur shadow-sm">
                        <div class="tabs tabs-boxed bg-transparent p-0 gap-2">
                            <button @click="timelineTab = 'layers'" class="tab tab-sm" :class="{ 'tab-active': timelineTab === 'layers' }"><i class="fas fa-layer-group mr-2"></i> Layers</button>
                            <button @click="timelineTab = 'timeline'" class="tab tab-sm" :class="{ 'tab-active': timelineTab === 'timeline' }"><i class="fas fa-stopwatch mr-2"></i> GSAP Timeline</button>
                        </div>
                        <button @click="toggleTimeline" class="btn btn-ghost btn-xs btn-circle"><i class="fas fa-times opacity-50"></i></button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar flex gap-4">
                        <div v-show="timelineTab === 'layers'" class="flex-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-30 mb-4">Canvas Graph</p>
                            
                            <LayerTreeItem 
                                :blocks="blocks" 
                                @change="store.isDirty = true" 
                            />
                            
                            <div v-if="blocks.length === 0" class="text-xs opacity-40 italic mt-2">No blocks on canvas.</div>
                        </div>
                        <div v-show="timelineTab === 'timeline'" class="flex-1 flex flex-col">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-30 mb-4 px-2">GSAP Animation Sequence</p>
                            
                            <div class="space-y-1">
                                <div v-for="(block, index) in timelineBlocks" :key="block.id" class="text-xs py-2 px-3 border border-white/5 rounded-lg flex items-center gap-4 bg-primary/5 hover:bg-primary/10 transition-colors cursor-pointer" @click="store.activeBlockId = block.id" :class="{ 'ring-1 ring-primary bg-primary/20': store.activeBlockId === block.id }">
                                    
                                    <div class="w-6 text-center font-mono opacity-40 font-bold">{{ index + 1 }}</div>
                                    <i :class="block.icon || 'fas fa-cube'" class="opacity-50 text-primary"></i>
                                    
                                    <div class="flex-1">
                                        <div class="font-bold text-primary">{{ block.type.charAt(0).toUpperCase() + block.type.slice(1) }}</div>
                                        <div class="text-[10px] opacity-50 space-x-2">
                                            <span><i class="fas fa-bolt mr-1"></i>{{ block.settings.animations.trigger }}</span>
                                            <span><i class="fas fa-magic mr-1"></i>{{ block.settings.animations.preset }}</span>
                                            <span><i class="fas fa-stopwatch mr-1"></i>{{ block.settings.animations.duration }}s</span>
                                        </div>
                                    </div>
                                    
                                    <div class="w-32 h-2 bg-base-300 rounded-full overflow-hidden relative" title="Timeline visual representation placeholder">
                                        <div class="absolute top-0 bottom-0 bg-primary opacity-50 rounded-full" :style="{ left: `${(block.settings.animations.delay || 0) * 10}%`, width: `${(block.settings.animations.duration || 1) * 20}%`, minWidth: '4px' }"></div>
                                    </div>
                                    
                                    <span class="opacity-30 text-[10px] font-mono">{{ block.id.split('-')[0] }}</span>
                                </div>
                                
                                <div v-if="timelineBlocks.length === 0" class="flex-1 flex flex-col items-center justify-center opacity-30 my-8 py-8 pointer-events-none">
                                    <i class="fas fa-project-diagram text-4xl mb-4"></i>
                                    <p class="text-xs text-center max-w-sm">No blocks are attached to the Main Timeline. Select a block, open its Animation settings, and check "Attach to Main Timeline".</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Block Settings -->
            <div class="w-80 bg-base-100 border-l border-white/5 overflow-y-auto z-10 shadow-2xl custom-scrollbar">
                <BlockEditorSidebar :menus="menus" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import BlockEditorSidebar from '@/Components/BlockEditorSidebar.vue';
import LayerTreeItem from '@/Components/LayerTreeItem.vue';
import draggable from 'vuedraggable';
import gsap from 'gsap';

const props = defineProps({
    saveLabel: { type: String, default: 'Save Changes' },
    backLabel: { type: String, default: 'Back' },
    backRoute: { type: String, default: '#' },
    saving: { type: Boolean, default: false },
    categories: { type: Array, required: true },
    menus: { type: Array, default: () => [] }
});

const emit = defineEmits(['save']);

const store = useBlockBuilderStore();
const viewport = ref('desktop');
const leftTab = ref('blocks');

const blocks = computed({
    get: () => store.blocks,
    set: (value) => {
        store.blocks = value;
        store.isDirty = true;
    }
});

const timelineBlocks = computed(() => {
    return store.blocks.filter(b => b.settings?.animations?.enabled && b.settings?.animations?.trigger === 'timeline');
});

const zoomLevel = ref(1);

const zoomIn = () => {
    if (zoomLevel.value < 2) zoomLevel.value = parseFloat((zoomLevel.value + 0.1).toFixed(1));
};

const zoomOut = () => {
    if (zoomLevel.value > 0.2) zoomLevel.value = parseFloat((zoomLevel.value - 0.1).toFixed(1));
};

const resetZoom = () => {
    zoomLevel.value = 1;
};

const showTimeline = ref(false);
const timelineTab = ref('layers');
const timelinePanel = ref(null);

const toggleTimeline = () => {
    showTimeline.value = !showTimeline.value;
    if (showTimeline.value) {
        gsap.to(timelinePanel.value, { y: 0, duration: 0.5, ease: 'power3.out' });
    } else {
        gsap.to(timelinePanel.value, { y: '100%', duration: 0.4, ease: 'power3.in' });
    }
};

const cloneBlock = (block) => {
    return store.createBlockObject(block.type);
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}

.ghost-block {
    opacity: 0.5;
    background: #c8ebfb;
    border: 2px dashed #000;
}
</style>
