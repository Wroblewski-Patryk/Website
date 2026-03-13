<template>
    <div class="h-full flex flex-col overflow-hidden bg-base-300" style="--admin-p: var(--color-primary, #38bdf8); --admin-radius: var(--radius-box, var(--rounded-box, 1rem));">
        
        <!-- Unified Header -->
        <BuilderHeader 
            v-model:title="docTitle"
            :current-viewport="viewport"
            :custom-width="customWidth"
            :custom-height="customHeight"
            :preview-url="previewUrl"
            :saving="saving"
            @update:viewport="viewport = $event"
            @update:customWidth="customWidth = $event"
            @update:customHeight="customHeight = $event"
            @save="$emit('save')"
        >
            <template #actions-left>
                <div class="flex items-center mr-2">
                    <div class="join">
                        <button @click="showLeftSidebar = !showLeftSidebar" class="btn btn-xs join-item h-8 px-3 gap-2" :class="showLeftSidebar ? 'btn-primary' : 'btn-ghost'" :title="t('admin.builder.toggle_palette', 'Toggle Block Palette')">
                            <PhCube weight="bold" class="w-3 h-3" />
                        </button>
                        <button @click="toggleTimeline" class="btn btn-xs join-item h-8 px-3 gap-2" :class="showTimeline ? 'btn-primary' : 'btn-ghost'" :title="t('admin.builder.toggle_timeline', 'Toggle Timeline')">
                            <PhTimer weight="bold" class="w-3 h-3" />
                        </button>
                        <button @click="store.showRightSidebar = !store.showRightSidebar" class="btn btn-xs join-item h-8 px-3 gap-2" :class="store.showRightSidebar ? 'btn-primary' : 'btn-ghost'" :title="t('admin.builder.toggle_inspector', 'Toggle Document Inspector')">
                            <PhSlidersHorizontal weight="bold" class="w-3 h-3" />
                        </button>
                    </div>

                    <div class="h-4 w-[1px] bg-base-content/15 mx-2"></div>

                    <button @click="store.isDepthView = !store.isDepthView" class="btn btn-xs h-8 px-3 gap-2" :class="store.isDepthView ? 'btn-primary' : 'btn-ghost'" :title="t('admin.builder.toggle_3d', 'Toggle 3D View')">
                        <PhStack weight="bold" class="w-3 h-3" />
                    </button>

                    <div class="h-4 w-[1px] bg-base-content/15 mx-2"></div>

                    <div class="join items-center">
                        <button @click="zoomOut" class="btn btn-xs btn-ghost join-item h-8 px-3 gap-2" :title="t('admin.builder.zoom_out', 'Zoom Out')">
                            <PhMinus weight="bold" class="w-3 h-3" />
                        </button>
                        <span class="join-item h-8 px-2 text-[10px] font-black font-mono text-center flex items-center">{{ Math.round(zoomLevel * 100) }}%</span>
                        <button @click="zoomIn" class="btn btn-xs btn-ghost join-item h-8 px-3 gap-2" :title="t('admin.builder.zoom_in', 'Zoom In')">
                            <PhPlus weight="bold" class="w-3 h-3" />
                        </button>
                        <button @click="resetZoom" class="btn btn-xs btn-ghost join-item h-8 px-3 gap-2" :title="t('admin.builder.zoom_reset', 'Reset Zoom')">
                            <PhArrowUUpLeft weight="bold" class="w-3 h-3" />
                        </button>
                    </div>
                </div>
            </template>
        </BuilderHeader>

        <div class="flex-1 flex overflow-hidden">
            <!-- Left Sidebar: Block Palette -->
            <div 
                class="bg-base-100 border-r border-white/5 flex flex-col z-10 shadow-xl transition-all duration-300"
                :class="showLeftSidebar ? 'w-80' : 'w-0 overflow-hidden border-r-0'"
            >
                <SidebarPanelHeader
                    :icon="PhCube"
                    icon-weight="regular"
                    :title="t('admin.builder.palette_title', 'Palette')" />

                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                    <!-- Categories -->
                    <div class="space-y-4">
                        <AdminCollapse 
                            v-for="cat in paletteCategories" 
                            :key="cat.id" 
                            :title="cat.label || cat.name" 
                            :icon="cat.icon" 
                            :persistKey="'palette_cat_' + cat.id"
                            contentClass="!p-2 !bg-base-200/50"
                        >
                            <draggable 
                                :list="cat.blocks" 
                                :group="{ name: 'admin.blocks', pull: 'clone', put: false }" 
                                :clone="cloneBlock"
                                :sort="false"
                                item-key="type"
                                class="grid grid-cols-2 gap-2">
                                <template #item="{ element }">
                                    <BlockPaletteTile
                                        :icon-component="iconMap[element.icon] || iconMap.PhCube"
                                        :label="element.label"
                                        @add="store.addBlock(element.type)"
                                    />
                                </template>
                            </draggable>
                        </AdminCollapse>
                    </div>
                </div>
            </div>

            <!-- Center: Wrapper -->
            <div class="flex-1 flex flex-col relative overflow-hidden bg-base-300">

                <!-- Center: Canvas Area -->
                <div ref="scrollerRef" 
                     :class="[
                        'flex-1 overflow-auto custom-scrollbar bg-base-200/50 grid place-items-center',
                        { 'is-initial-load': isInitialLoad }
                     ]"
                     :style="{ 
                        padding: store.isDepthView ? '10rem' : '6rem',
                        scrollbarGutter: 'stable'
                     }">
                    <div class="inline-block text-left align-top relative flex-shrink-0" 
                         :style="[
                             { width: `${(viewport === 'custom' ? customWidth : (viewport === 'desktop' ? 1280 : (viewport === 'tablet' ? 768 : 375))) * zoomLevel}px` },
                             { height: `${canvasHeight * zoomLevel}px` },
                             { 
                                transform: `${hasDepthPerspective ? 'perspective(1000px) ' : ''}${store.isDepthView ? 'rotateY(75deg)' : 'rotateY(0deg)'}`,
                                transformOrigin: 'left center', 
                                transformStyle: 'preserve-3d',
                                transition: 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1), width 0.5s cubic-bezier(0.4, 0, 0.2, 1), height 0.5s cubic-bezier(0.4, 0, 0.2, 1)'
                             }
                         ]">
                         <div ref="canvasRef"
                        class="bg-base-100 shadow-2xl transition-all duration-500 relative flex flex-col whitespace-normal" 
                        :style="[
                            { 
                                transform: `scale(${zoomLevel})`,
                                transformOrigin: 'top left',
                                width: viewport === 'custom' ? `${customWidth}px` : (viewport === 'desktop' ? '1280px' : (viewport === 'tablet' ? '768px' : '375px')),
                                transformStyle: 'preserve-3d',
                                minHeight: effectiveCanvasMinHeight
                            },
                            viewport === 'custom' ? { minHeight: `${customHeight}px` } : {}
                        ]"
                        data-theme="light">
                        <slot name="canvas-header"></slot>
                        <draggable 
                            v-model="blocks" 
                            :group="'admin.blocks'"
                            item-key="id"
                            handle=".drag-handle"
                            ghost-class="ghost-block"
                            class="flex-1 w-full flex flex-col gap-4"
                            :style="{ transformStyle: 'preserve-3d', minHeight: effectiveCanvasMinHeight }">
                            <template #item="{ element }">
                                <DynamicBlock :block="element" />
                            </template>
                        </draggable>

                        <div v-if="store.blocks.length === 0" class="absolute inset-x-0 top-32 flex flex-col items-center justify-center pointer-events-none">
                            <PhStack weight="thin" class="w-12 h-12 mb-4 opacity-10 text-base-content" />
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-30">{{ t('admin.builder.drag_blocks_here', 'Drag blocks here') }}</p>
                        </div>

                        <slot name="canvas-footer"></slot>
                    </div>
                </div>
            </div>


                <!-- Bottom Timeline Panel -->
                <div ref="timelinePanel" class="absolute bottom-0 left-0 right-0 h-48 bg-base-100 border-t border-white/10 shadow-[0_-10px_40px_rgba(0,0,0,0.2)] flex flex-col z-[60] translate-y-full">
                    <div class="flex items-center justify-between px-4 py-2 bg-base-200/50 border-b border-white/5 backdrop-blur shadow-sm">
                        <div class="flex items-center gap-2">
                            <PhTimer weight="bold" class="w-4 h-4 text-primary" />
                            <span class="text-xs font-bold uppercase tracking-widest">{{ t('admin.builder.timeline_sequence', 'GSAP Timeline sequence') }}</span>
                        </div>
                        <button @click="toggleTimeline" class="btn btn-ghost btn-xs btn-circle">
                            <PhX weight="bold" class="w-4 h-4 opacity-50" />
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar flex flex-col">
                        <GsapTimelineEditor :blocks="timelineBlocks" class="flex-1" />
                    </div>
                </div>
            </div>

            <div 
                class="bg-base-100 border-l border-white/5 overflow-hidden z-10 shadow-2xl transition-all duration-300"
                :class="store.showRightSidebar ? 'w-[360px]' : 'w-0 border-l-0'"
            >
                <div class="w-[360px] h-full flex flex-col">
                    <BlockEditorSidebar 
                        :menus="menus" 
                        :templates="templates" 
                        :saving="saving"
                        @save="$emit('save')"
                    >
                        <template #info>
                            <slot name="info"></slot>
                        </template>
                        <template v-if="$slots.seo" #seo>
                            <slot name="seo"></slot>
                        </template>
                        <template v-if="$slots.advanced" #advanced>
                            <slot name="advanced"></slot>
                        </template>
                        <template #history>
                            <slot name="history"></slot>
                        </template>
                    </BlockEditorSidebar>
                </div>
            </div>
        </div>
        <MediaPickerModal />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick, provide } from 'vue';
import { usePage } from '@inertiajs/vue3';
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
    PhArrowUUpLeft, PhPlus, PhX, PhPerspective
} from '@phosphor-icons/vue';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { useTranslations } from '@/Composables/useTranslations';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import BlockEditorSidebar from '@/Components/BlockEditorSidebar.vue';
import BuilderHeader from '@/Components/BlockBuilder/Layout/BuilderHeader.vue';
import SidebarPanelHeader from '@/Components/BlockBuilder/Layout/SidebarPanelHeader.vue';
import LayerTreeItem from '@/Components/LayerTreeItem.vue';
import GsapTimelineEditor from '@/Components/GsapTimelineEditor.vue';
import AdminCollapse from '@/Components/AdminCollapse.vue';
import BlockPaletteTile from '@/Components/BlockBuilder/Palette/BlockPaletteTile.vue';
import MediaPickerModal from '@/Components/Admin/Media/MediaPickerModal.vue';
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
    PhBriefcase, PhArrowsClockwise, PhListNumbers, PhPerspective
};

const props = defineProps({
    title: { type: String, default: 'Untitled' },
    categories: { type: Array, default: () => [] },
    moduleCategories: { type: Array, default: () => [] },
    menus: { type: Array, default: () => [] },
    templates: { type: [Array, Object], default: () => [] },
    previewUrl: { type: String, default: null },
    saving: { type: Boolean, default: false },
    canvasMinHeight: { type: String, default: '100px' },
    useViewportMinHeight: { type: Boolean, default: false }
});


provide('isEditor', true);

const emit = defineEmits(['save', 'update:title', 'update:viewport']);

const store = useBlockBuilderStore();
const page = usePage();
const { t } = useTranslations();
const viewport = ref('desktop');

const paletteCategories = computed(() => {
    const baseCategories = Array.isArray(props.categories) && props.categories.length
        ? props.categories
        : store.categories;

    const merged = baseCategories.map((cat) => ({
        ...cat,
        blocks: Array.isArray(cat.blocks) ? [...cat.blocks] : []
    }));

    if (!Array.isArray(props.moduleCategories) || props.moduleCategories.length === 0) {
        return merged;
    }

    props.moduleCategories.forEach((moduleCat) => {
        if (!moduleCat || !moduleCat.id) return;

        const moduleBlocks = Array.isArray(moduleCat.blocks) ? moduleCat.blocks : [];
        const existing = merged.find((cat) => cat.id === moduleCat.id);

        if (!existing) {
            merged.push({
                ...moduleCat,
                blocks: [...moduleBlocks]
            });
            return;
        }

        const knownTypes = new Set((existing.blocks || []).map((block) => block.type));
        moduleBlocks.forEach((block) => {
            if (!block?.type || knownTypes.has(block.type)) return;
            existing.blocks.push(block);
            knownTypes.add(block.type);
        });
    });

    return merged;
});

const docTitle = computed({
    get: () => {
        const locale = store.editingLocale || page.props.locale || 'pl';
        if (props.title && typeof props.title === 'object') {
            return props.title[locale] || props.title[Object.keys(props.title)[0]] || '';
        }
        return props.title;
    },
    set: (val) => {
        const locale = store.editingLocale || page.props.locale || 'pl';
        const newTitle = typeof props.title === 'object' ? { ...props.title } : {};
        newTitle[locale] = val;
        emit('update:title', newTitle);
    }
});
const customWidth = ref(1920);
const customHeight = ref(1080);
const isInitialLoad = ref(true);
const MIN_CANVAS_DROPZONE_HEIGHT = 100;
const VIEWPORT_HEIGHT_RATIO = 9 / 16;

const viewportBaseWidth = computed(() => {
    if (viewport.value === 'custom') return customWidth.value;
    if (viewport.value === 'tablet') return 768;
    if (viewport.value === 'mobile') return 375;
    return 1280;
});

const viewportComputedMinHeight = computed(() => {
    const ratioHeight = Math.round(viewportBaseWidth.value * VIEWPORT_HEIGHT_RATIO);
    return `${Math.max(MIN_CANVAS_DROPZONE_HEIGHT, ratioHeight)}px`;
});

const effectiveCanvasMinHeight = computed(() => {
    return props.useViewportMinHeight ? viewportComputedMinHeight.value : props.canvasMinHeight;
});

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
const showTimeline = ref(false);
const timelinePanel = ref(null);
const DEPTH_TRANSITION_MS = 500;
const hasDepthPerspective = ref(store.isDepthView);
let depthPerspectiveTimeout = null;

watch(() => store.isDepthView, (isDepth) => {
    if (depthPerspectiveTimeout) {
        clearTimeout(depthPerspectiveTimeout);
        depthPerspectiveTimeout = null;
    }

    if (isDepth) {
        // 2D -> 3D: perspective is enabled before rotation starts.
        hasDepthPerspective.value = true;
        return;
    }

    // 3D -> 2D: remove perspective only after rotate-back animation ends.
    depthPerspectiveTimeout = setTimeout(() => {
        hasDepthPerspective.value = false;
        depthPerspectiveTimeout = null;
    }, DEPTH_TRANSITION_MS);
});

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

// --- AUTO-FIT ZOOM LOGIC ---
const scrollerRef = ref(null);

const fitZoom = () => {
    if (!scrollerRef.value) return;
    
    // Get raw width based on viewport
    const rawWidth = viewport.value === 'custom' ? customWidth.value 
                   : viewport.value === 'desktop' ? 1280 
                   : viewport.value === 'tablet' ? 768 
                   : 375;
                   
    const scrollerWidth = scrollerRef.value.clientWidth;
    const padding = 192; // 6rem (96px) * 2
    const availableWidth = scrollerWidth - padding;
    
    if (availableWidth <= 0) return;
    
    // Calculate zoom needed to fit the width
    let targetZoom = availableWidth / rawWidth;
    
    // Clamp zoom: don't zoom in more than 100% (1.0), but allow zooming out to 20% (0.2)
    zoomLevel.value = Math.min(1.0, Math.max(0.2, parseFloat(targetZoom.toFixed(2))));
};

// Initial fit on entry is handled in onMounted.
// No extra watchers here to avoid "refitting" after viewport switch or sidebar toggle as per user request.

// --- FIX: Dynamic Footprint Height ---
const canvasRef = ref(null);
const canvasHeight = ref(0);
let resizeObserver = null;

const handleKeyDown = (e) => {
    // Save: Ctrl+S
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        emit('save');
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);

    store.initLocales(page.props.languages, page.props.locale);

    if (canvasRef.value) {
        resizeObserver = new ResizeObserver((entries) => {
            for (let entry of entries) {
                canvasHeight.value = entry.contentRect.height;
            }
        });
        resizeObserver.observe(canvasRef.value);
    }
    
    // Initial fit (instant)
    nextTick(() => {
        fitZoom();
        // After layout settles, allow transitions again
        setTimeout(() => {
            isInitialLoad.value = false;
        }, 150);
    });
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);

    if (depthPerspectiveTimeout) {
        clearTimeout(depthPerspectiveTimeout);
        depthPerspectiveTimeout = null;
    }

    if (resizeObserver) {
        resizeObserver.disconnect();
    }
});
</script>

<style scoped>
.is-initial-load * {
    transition: none !important;
}

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

