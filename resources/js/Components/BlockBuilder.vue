<template>
    <div class="h-full flex flex-col overflow-hidden bg-base-300" style="--admin-p: var(--color-primary, #38bdf8); --admin-radius: var(--radius-box, var(--rounded-box, 1rem));">
        <!-- Top Bar -->
        <div class="flex items-center justify-between px-6 py-2 bg-base-100 border-b border-white/5 shadow-md z-20">
            <div class="flex items-center gap-4">
                <slot name="back-button">
                    <button @click="$inertia.visit(backRoute)" class="btn btn-ghost btn-sm">
                        <i class="fas fa-chevron-left mr-2"></i> {{ backLabel }}
                    </button>
                </slot>
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
            <!-- Left Sidebar: Block Palette -->
            <div 
                class="bg-base-100 border-r border-white/5 flex flex-col z-10 shadow-xl transition-all duration-300"
                :class="showLeftSidebar ? 'w-80' : 'w-0 overflow-hidden border-r-0'"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-white/5 bg-base-100/80 backdrop-blur-xl flex items-center justify-between sticky top-0 z-20">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-base-content/5 flex items-center justify-center text-base-content/70 flex-shrink-0">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <div class="whitespace-nowrap">
                            <h3 class="text-sm font-bold capitalize">Block Palette</h3>
                            <p class="text-[10px] opacity-40 uppercase tracking-widest leading-none">Modules</p>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    <!-- Categories -->
                    <div class="space-y-4">
                        <div v-for="cat in categories" :key="cat.id" class="collapse collapse-arrow bg-base-200 border border-base-content/10 rounded-box overflow-hidden">
                            <input type="checkbox" :checked="cat.id === 'content' || cat.id === 'forms'" /> 
                            <div class="collapse-title text-xs font-bold uppercase tracking-widest flex items-center gap-3 bg-base-300/50">
                                <div class="w-8 h-8 rounded-lg bg-base-100 flex items-center justify-center text-primary shadow-sm">
                                    <i :class="cat.icon"></i>
                                </div>
                                {{ cat.label || cat.name }}
                            </div>
                            <div class="collapse-content p-0">
                                <div class="bg-base-200 p-3 mt-1 rounded-b-box">
                                    <draggable 
                                        :list="cat.blocks" 
                                        :group="{ name: 'blocks', pull: 'clone', put: false }" 
                                        :clone="cloneBlock"
                                        :sort="false"
                                        item-key="type"
                                        class="grid grid-cols-2 gap-3">
                                        <template #item="{ element }">
                                            <div @click="store.addBlock(element.type)" 
                                                 class="flex flex-col items-center justify-center gap-2 p-3 bg-base-100 border border-base-content/5 rounded-box hover:border-primary hover:shadow-md hover:text-primary transition-all cursor-grab active:cursor-grabbing group">
                                                <i :class="[element.icon, 'text-2xl text-base-content/50 group-hover:text-primary transition-colors']"></i>
                                                <span class="text-[10px] font-bold leading-tight text-center">{{ element.label }}</span>
                                            </div>
                                        </template>
                                    </draggable>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center: Wrapper -->
            <div class="flex-1 flex flex-col relative overflow-hidden bg-base-300">
                <!-- Floating Island for Viewport/Zoom Controls -->
                <div class="absolute top-4 left-1/2 -translate-x-1/2 z-50 flex items-center gap-1 bg-base-100/90 backdrop-blur border border-base-content/10 shadow-xl px-2 py-1 rounded-full transition-all">
                    <!-- Toggles -->
                    <div class="flex items-center gap-1">
                        <button @click="showLeftSidebar = !showLeftSidebar" class="btn btn-circle btn-xs" :class="showLeftSidebar ? 'btn-primary' : 'btn-ghost text-base-content/60'" title="Toggle Block Palette">
                            <i class="fas fa-cubes"></i>
                        </button>
                        <button @click="showRightSidebar = !showRightSidebar" class="btn btn-circle btn-xs" :class="showRightSidebar ? 'btn-primary' : 'btn-ghost text-base-content/60'" title="Toggle Document Inspector">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                        <button @click="toggleTimeline" class="btn btn-circle btn-xs" :class="showTimeline ? 'btn-primary' : 'btn-ghost text-base-content/60'" title="Toggle Timeline">
                            <i class="fas fa-stopwatch"></i>
                        </button>
                    </div>

                    <div class="h-4 w-[1px] bg-base-content/20 mx-1"></div>

                    <!-- Viewport Controls -->
                    <div class="flex items-center gap-1">
                        <button @click="viewport = 'desktop'" class="btn btn-circle btn-xs" :class="viewport === 'desktop' ? 'btn-primary' : 'btn-ghost text-base-content/60'" title="Desktop (1280px)">
                            <i class="fas fa-desktop"></i>
                        </button>
                        <button @click="viewport = 'tablet'" class="btn btn-circle btn-xs" :class="viewport === 'tablet' ? 'btn-primary' : 'btn-ghost text-base-content/60'" title="Tablet (768px)">
                            <i class="fas fa-tablet-alt"></i>
                        </button>
                        <button @click="viewport = 'mobile'" class="btn btn-circle btn-xs" :class="viewport === 'mobile' ? 'btn-primary' : 'btn-ghost text-base-content/60'" title="Mobile (375px)">
                            <i class="fas fa-mobile-alt"></i>
                        </button>
                        <button @click="viewport = 'custom'" class="btn btn-circle btn-xs" :class="viewport === 'custom' ? 'btn-primary' : 'btn-ghost text-base-content/60'" title="Custom Width">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>

                    <div v-if="viewport === 'custom'" class="flex items-center gap-1 px-2 border-l border-base-content/10 ml-1">
                        <input type="number" v-model="customWidth" class="input input-xs input-bordered w-16 px-1 text-center font-mono" title="Width" />
                        <span class="text-[10px] opacity-40 text-base-content font-bold">x</span>
                        <input type="number" v-model="customHeight" class="input input-xs input-bordered w-16 px-1 text-center font-mono" title="Height" />
                        <span class="text-[10px] opacity-40 text-base-content font-bold">px</span>
                    </div>

                    <div class="h-4 w-[1px] bg-base-content/20 mx-1"></div>

                    <!-- Zoom Controls -->
                    <div class="flex items-center gap-0">
                        <button @click="zoomOut" class="btn btn-circle btn-xs btn-ghost text-base-content/60" title="Zoom Out"><i class="fas fa-minus"></i></button>
                        <div class="px-1 text-[10px] font-mono font-bold opacity-70 w-12 text-center text-base-content">{{ Math.round(zoomLevel * 100) }}%</div>
                        <button @click="zoomIn" class="btn btn-circle btn-xs btn-ghost text-base-content/60" title="Zoom In"><i class="fas fa-plus"></i></button>
                        <button @click="resetZoom" class="btn btn-circle btn-xs btn-ghost text-base-content/60" title="Reset Zoom"><i class="fas fa-undo"></i></button>
                    </div>
                </div>

                <!-- Center: Canvas Area -->
                <div class="flex-1 overflow-auto custom-scrollbar text-center whitespace-nowrap pt-18 pb-32 px-4 md:px-8">
                    <div class="inline-block text-left align-top transition-all duration-300" 
                         :style="{ width: `${(viewport === 'custom' ? customWidth : (viewport === 'desktop' ? 1280 : (viewport === 'tablet' ? 768 : 375))) * zoomLevel}px` }">
                        <div :class="[
                            'bg-base-100 shadow-2xl transition-all duration-300 overflow-x-hidden relative flex flex-col whitespace-normal',
                            viewport === 'desktop' ? 'min-h-screen' : '',
                            viewport === 'tablet' ? 'min-h-screen' : '',
                            viewport === 'mobile' ? 'min-h-screen' : ''
                        ]" 
                        :style="[
                            { 
                                transform: `scale(${zoomLevel})`, 
                                transformOrigin: 'top left',
                                width: viewport === 'custom' ? `${customWidth}px` : (viewport === 'desktop' ? '1280px' : (viewport === 'tablet' ? '768px' : '375px'))
                            },
                            viewport === 'custom' ? { minHeight: `${customHeight}px` } : {}
                        ]"
                        data-theme="light">
                        <slot name="canvas-header"></slot>

                        <draggable 
                            v-model="blocks" 
                            :group="'blocks'"
                            item-key="id"
                            handle=".drag-handle"
                            ghost-class="ghost-block"
                            class="flex-1 w-full min-h-[600px] flex flex-col">
                            <template #item="{ element }">
                                <div class="group/block relative w-full"
                                     @click="store.activeBlockId = element.id"
                                     @mouseover.stop="store.hoveredBlockId = element.id"
                                     @mouseout.stop="store.hoveredBlockId = null"
                                     :class="{ 'ring-2 ring-primary ring-inset': store.activeBlockId === element.id }">
                                    
                                    <div class="absolute left-2 top-1/2 -translate-y-1/2 z-50 transition-opacity"
                                         :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                        <div class="drag-handle btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur cursor-move text-base-content/60 hover:text-primary hover:bg-base-200 shadow-sm rounded-box relative z-50 pointer-events-auto"><i class="fas fa-arrows-alt"></i></div>
                                    </div>
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 z-50 transition-opacity"
                                         :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                        <button type="button" @click.stop.prevent="store.removeBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-error/80 hover:bg-error hover:text-error-content shadow-sm rounded-box relative z-50 pointer-events-auto"><i class="fas fa-trash"></i></button>
                                    </div>

                                    <DynamicBlock :block="element" />
                                </div>
                            </template>
                        </draggable>

                        <div v-if="store.blocks.length === 0" class="absolute inset-x-0 top-32 flex flex-col items-center justify-center pointer-events-none">
                            <i class="fas fa-layer-group text-4xl mb-4 opacity-10 text-base-content"></i>
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-30">Drag blocks here</p>
                        </div>

                        <slot name="canvas-footer"></slot>
                    </div>
                    </div>
                </div>

                <!-- Bottom Timeline Panel -->
                <div ref="timelinePanel" class="absolute bottom-0 left-0 right-0 h-48 bg-base-100 border-t border-white/10 shadow-[0_-10px_40px_rgba(0,0,0,0.2)] flex flex-col z-[60] translate-y-full">
                    <div class="flex items-center justify-between px-4 py-2 bg-base-200/50 border-b border-white/5 backdrop-blur shadow-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-stopwatch text-primary"></i>
                            <span class="text-xs font-bold uppercase tracking-widest">GSAP Timeline sequence</span>
                        </div>
                        <button @click="toggleTimeline" class="btn btn-ghost btn-xs btn-circle"><i class="fas fa-times opacity-50"></i></button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar flex flex-col">
                        <GsapTimelineEditor :blocks="timelineBlocks" class="flex-1" />
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Document Inspector / Block Settings -->
            <div 
                class="bg-base-100 border-l border-white/5 overflow-hidden z-10 shadow-2xl flex flex-col transition-all duration-300"
                :class="showRightSidebar ? 'w-80' : 'w-0 border-l-0'"
            >
                <div class="w-80 h-full flex flex-col">
                    <BlockEditorSidebar :menus="menus">
                        <template #info>
                            <slot name="info"></slot>
                        </template>
                        <template #history>
                            <slot name="history"></slot>
                        </template>
                    </BlockEditorSidebar>
                </div>
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
import GsapTimelineEditor from '@/Components/GsapTimelineEditor.vue';
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

import { provide } from 'vue';
provide('isEditor', true);

const emit = defineEmits(['save']);

const store = useBlockBuilderStore();
const viewport = ref('desktop');
const customWidth = ref(1920);
const customHeight = ref(1080);

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

const showLeftSidebar = ref(true);
const showRightSidebar = ref(true);
const showTimeline = ref(false);
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
</style>

<style>
/* Editor UI dynamic theme styles (bypasses canvas data-theme="light") */
.editor-ring {
    box-shadow: inset 0 0 0 2px var(--admin-p) !important;
}

.editor-dashed-frame {
    border-color: color-mix(in srgb, var(--admin-p) 30%, transparent) !important;
    transition: border-color 0.2s;
}
.editor-dashed-frame:hover {
    border-color: color-mix(in srgb, var(--admin-p) 80%, transparent) !important;
}

.editor-dashed-frame-sub {
    border-color: color-mix(in srgb, var(--admin-p) 15%, transparent) !important;
}

/* Global styles for drag-and-drop placeholders */
.ghost-block {
    position: relative !important;
    border-radius: var(--admin-radius) !important;
    min-height: 60px !important;
}

.ghost-block::before {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    background-color: color-mix(in srgb, var(--admin-p) 10%, transparent) !important;
    border-radius: inherit !important;
    z-index: 10 !important;
    pointer-events: none !important;
}

.ghost-block::after {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    border: 2px dashed color-mix(in srgb, var(--admin-p) 50%, transparent) !important;
    border-radius: inherit !important;
    z-index: 11 !important;
    pointer-events: none !important;
}

.ghost-block > * {
    opacity: 0 !important; /* Visually empty out the ghost block to make it a clear target */
}
</style>
