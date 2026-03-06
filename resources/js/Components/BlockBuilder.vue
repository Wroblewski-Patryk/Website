<template>
    <div class="h-full flex flex-col overflow-hidden bg-base-300" style="--admin-p: var(--color-primary, #38bdf8); --admin-radius: var(--radius-box, var(--rounded-box, 1rem));">


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
                            <PhCube weight="regular" class="w-5 h-5" />
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
                        <AdminCollapse 
                            v-for="cat in categories" 
                            :key="cat.id" 
                            :title="cat.label || cat.name" 
                            :icon="cat.icon" 
                            :open="cat.id === 'content' || cat.id === 'forms' || cat.id === 'typography'"
                            contentClass="!p-2 !bg-base-200/50"
                        >
                            <draggable 
                                :list="cat.blocks" 
                                :group="{ name: 'blocks', pull: 'clone', put: false }" 
                                :clone="cloneBlock"
                                :sort="false"
                                item-key="type"
                                class="grid grid-cols-2 gap-2">
                                <template #item="{ element }">
                                    <div @click="store.addBlock(element.type)" 
                                         class="flex flex-col items-center justify-center gap-2 p-3 py-4 bg-base-100 border border-base-content/5 rounded-box hover:border-primary hover:bg-primary/5 hover:-translate-y-0.5 hover:shadow-lg transition-all cursor-grab active:cursor-grabbing group">
                                        <component :is="iconMap[element.icon] || iconMap.PhCube" weight="duotone" class="w-6 h-6 text-base-content/50 group-hover:text-primary transition-colors" />
                                        <span class="text-[10px] font-bold leading-tight text-center">{{ element.label }}</span>
                                    </div>
                                </template>
                            </draggable>
                        </AdminCollapse>
                    </div>
                </div>
            </div>

            <!-- Center: Wrapper -->
            <div class="flex-1 flex flex-col relative overflow-hidden bg-base-300">
                <!-- Floating Island for Viewport/Zoom Controls -->
                <div class="absolute top-4 left-1/2 -translate-x-1/2 z-50 flex items-center gap-1.5 bg-base-100/80 backdrop-blur-xl border border-base-content/10 shadow-2xl px-2.5 py-1.5 rounded-box transition-all">
                    <!-- Toggles -->
                    <div class="flex items-center gap-1">
                        <button @click="showLeftSidebar = !showLeftSidebar" class="btn btn-square btn-xs border-none transition-colors" :class="showLeftSidebar ? 'bg-primary text-primary-content hover:bg-primary-focus' : 'bg-transparent text-base-content/50 hover:bg-base-content/10 text-base-content/80'" title="Toggle Block Palette">
                            <PhCube weight="fill" class="w-4 h-4" v-if="showLeftSidebar" />
                            <PhCube weight="regular" class="w-4 h-4" v-else />
                        </button>
                        <button @click="showRightSidebar = !showRightSidebar" class="btn btn-square btn-xs border-none transition-colors" :class="showRightSidebar ? 'bg-primary text-primary-content hover:bg-primary-focus' : 'bg-transparent text-base-content/50 hover:bg-base-content/10 text-base-content/80'" title="Toggle Document Inspector">
                            <PhSlidersHorizontal weight="fill" class="w-4 h-4" v-if="showRightSidebar" />
                            <PhSlidersHorizontal weight="regular" class="w-4 h-4" v-else />
                        </button>
                        <button @click="toggleTimeline" class="btn btn-square btn-xs border-none transition-colors" :class="showTimeline ? 'bg-primary text-primary-content hover:bg-primary-focus' : 'bg-transparent text-base-content/50 hover:bg-base-content/10 text-base-content/80'" title="Toggle Timeline">
                            <PhTimer weight="fill" class="w-4 h-4" v-if="showTimeline" />
                            <PhTimer weight="regular" class="w-4 h-4" v-else />
                        </button>
                    </div>

                    <div class="h-5 w-[1px] bg-base-content/10 mx-1.5"></div>

                    <!-- Viewport Controls -->
                    <div class="flex items-center gap-1">
                        <button @click="viewport = 'desktop'" class="btn btn-square btn-xs border-none transition-colors" :class="viewport === 'desktop' ? 'bg-primary text-primary-content hover:bg-primary-focus' : 'bg-transparent text-base-content/50 hover:bg-base-content/10 text-base-content/80'" title="Desktop (1280px)">
                            <PhDesktop weight="fill" class="w-4 h-4" v-if="viewport === 'desktop'" />
                            <PhDesktop weight="regular" class="w-4 h-4" v-else />
                        </button>
                        <button @click="viewport = 'tablet'" class="btn btn-square btn-xs border-none transition-colors" :class="viewport === 'tablet' ? 'bg-primary text-primary-content hover:bg-primary-focus' : 'bg-transparent text-base-content/50 hover:bg-base-content/10 text-base-content/80'" title="Tablet (768px)">
                            <PhDeviceTablet weight="fill" class="w-4 h-4" v-if="viewport === 'tablet'" />
                            <PhDeviceTablet weight="regular" class="w-4 h-4" v-else />
                        </button>
                        <button @click="viewport = 'mobile'" class="btn btn-square btn-xs border-none transition-colors" :class="viewport === 'mobile' ? 'bg-primary text-primary-content hover:bg-primary-focus' : 'bg-transparent text-base-content/50 hover:bg-base-content/10 text-base-content/80'" title="Mobile (375px)">
                            <PhDeviceMobile weight="fill" class="w-4 h-4" v-if="viewport === 'mobile'" />
                            <PhDeviceMobile weight="regular" class="w-4 h-4" v-else />
                        </button>
                        <button @click="viewport = 'custom'" class="btn btn-square btn-xs border-none transition-colors" :class="viewport === 'custom' ? 'bg-primary text-primary-content hover:bg-primary-focus' : 'bg-transparent text-base-content/50 hover:bg-base-content/10 text-base-content/80'" title="Custom Width">
                            <PhArrowsOut weight="fill" class="w-4 h-4" v-if="viewport === 'custom'" />
                            <PhArrowsOut weight="regular" class="w-4 h-4" v-else />
                        </button>
                    </div>

                    <div v-if="viewport === 'custom'" class="flex items-center gap-1.5 px-3 border-l border-base-content/10 ml-1 bg-base-200/50 rounded-box py-0.5">
                        <input type="number" v-model="customWidth" class="input input-xs input-ghost hover:bg-base-100 focus:bg-base-100 w-14 px-1 text-center font-mono focus:outline-none" title="Width" />
                        <span class="text-[9px] opacity-40 text-base-content font-bold">x</span>
                        <input type="number" v-model="customHeight" class="input input-xs input-ghost hover:bg-base-100 focus:bg-base-100 w-14 px-1 text-center font-mono focus:outline-none" title="Height" />
                        <span class="text-[9px] opacity-40 text-base-content font-bold">px</span>
                    </div>

                    <div class="h-5 w-[1px] bg-base-content/10 mx-1.5"></div>

                    <!-- Zoom Controls -->
                    <div class="flex items-center gap-0.5">
                        <button @click="zoomOut" class="btn btn-square btn-xs bg-transparent border-none text-base-content/50 hover:bg-base-content/10 hover:text-base-content/80 transition-colors" title="Zoom Out">
                            <PhMinus weight="bold" class="w-3 h-3" />
                        </button>
                        <div class="px-1 text-[10px] font-mono font-bold opacity-70 w-10 text-center text-base-content select-none">{{ Math.round(zoomLevel * 100) }}%</div>
                        <button @click="zoomIn" class="btn btn-square btn-xs bg-transparent border-none text-base-content/50 hover:bg-base-content/10 hover:text-base-content/80 transition-colors" title="Zoom In">
                            <PhPlus weight="bold" class="w-3 h-3" />
                        </button>
                        <button @click="resetZoom" class="btn btn-square btn-xs bg-transparent border-none text-base-content/50 hover:bg-base-content/10 hover:text-base-content/80 transition-colors ml-1" title="Reset Zoom">
                            <PhArrowUUpLeft weight="bold" class="w-3.5 h-3.5" />
                        </button>
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
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 z-50 transition-opacity flex items-center gap-1"
                                         :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                        <button type="button" @click.stop.prevent="store.duplicateBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-primary/80 hover:bg-primary hover:text-primary-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Duplicate Block"><i class="fas fa-copy"></i></button>
                                        <button type="button" @click.stop.prevent="store.removeBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-error/80 hover:bg-error hover:text-error-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Delete Block"><i class="fas fa-trash"></i></button>
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
                        <template #seo>
                            <slot name="seo"></slot>
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
import { 
    PhCube, PhTextT, PhTextAa, PhTextHOne, PhListBullets, PhQuotes, PhCode, 
    PhCursorClick, PhHandPointing, PhCaretDown, PhBrowsers, PhArrowsLeftRight, 
    PhDesktop, PhListDashes, PhUserCircle, PhCertificate, PhIdentificationCard, 
    PhSlidersHorizontal, PhChats, PhTimer, PhCircleHalf, PhChartLineUp, PhTable, 
    PhWarningCircle, PhWarning, PhListChecks, PhCircleNotch, PhPencilSimple, 
    PhCheckSquare, PhRadioButton, PhToggleRight, PhStarHalf, PhUploadSimple, 
    PhStack, PhBoundingBox, PhColumns, PhList, PhMinus, PhStar, PhImage, 
    PhVideoCamera, PhNavigationArrow, PhDotsThree, PhBrowser, PhFootprints, 
    PhFolder, PhTerminal, PhDeviceMobile, PhAppWindow, PhPlusCircle, PhArticle, 
    PhBriefcase, PhArrowsClockwise, PhListNumbers, PhDeviceTablet, PhArrowsOut,
    PhArrowUUpLeft, PhPlus
} from '@phosphor-icons/vue';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import BlockEditorSidebar from '@/Components/BlockEditorSidebar.vue';
import LayerTreeItem from '@/Components/LayerTreeItem.vue';
import GsapTimelineEditor from '@/Components/GsapTimelineEditor.vue';
import AdminCollapse from '@/Components/AdminCollapse.vue';
import draggable from 'vuedraggable';
import gsap from 'gsap';

const iconMap = {
    PhCube, PhTextT, PhTextAa, PhTextHOne, PhListBullets, PhQuotes, PhCode, 
    PhCursorClick, PhHandPointing, PhCaretDown, PhBrowsers, PhArrowsLeftRight, 
    PhDesktop, PhListDashes, PhUserCircle, PhCertificate, PhIdentificationCard, 
    PhSlidersHorizontal, PhChats, PhTimer, PhCircleHalf, PhChartLineUp, PhTable, 
    PhWarningCircle, PhWarning, PhListChecks, PhCircleNotch, PhPencilSimple, 
    PhCheckSquare, PhRadioButton, PhToggleRight, PhStarHalf, PhUploadSimple, 
    PhStack, PhBoundingBox, PhColumns, PhList, PhMinus, PhStar, PhImage, 
    PhVideoCamera, PhNavigationArrow, PhDotsThree, PhBrowser, PhFootprints, 
    PhFolder, PhTerminal, PhDeviceMobile, PhAppWindow, PhPlusCircle, PhArticle, 
    PhBriefcase, PhArrowsClockwise, PhListNumbers 
};

const props = defineProps({
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
