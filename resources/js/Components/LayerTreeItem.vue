<template>
    <draggable 
        :list="blocks"
        item-key="id" 
        handle=".drag-handle"
        group="blocks"
        ghost-class="layer-ghost"
        @start="onDragStart"
        @end="onDragEnd"
        :class="[
            'space-y-0.5 transition-all duration-300', 
            { 'empty-drop-zone border-2 border-dashed border-primary/40 bg-primary/5 rounded-lg m-1 scale-100 opacity-100 shadow-inner': blocks.length === 0 && store.isDragging },
            { 'h-0 opacity-0 pointer-events-none scale-95 overflow-hidden m-0 p-0': blocks.length === 0 && !store.isDragging }
        ]"
    >
        <template #item="{ element }">
            <div class="layer-item-wrapper">
                <!-- Item Row -->
                <div 
                    class="group relative text-[11px] py-1 pl-1 pr-2 flex items-center gap-1.5 cursor-pointer transition-all border-l-2 border-transparent hover:bg-base-content/5 rounded-r-md"
                    :class="{ 
                        'active-layer bg-primary/10 text-primary border-primary shadow-sm': store.activeBlockId === element.id,
                        'opacity-50': element.settings?.hidden 
                    }"
                    @click="store.activeBlockId = element.id"
                >
                    <!-- Indentation Vertical Lines -->
                    <div v-if="depth > 0" class="flex h-full items-stretch" :style="{ width: (depth * 0.75) + 'rem' }">
                        <div v-for="i in depth" :key="i" class="h-full w-px bg-base-content/10 mx-auto"></div>
                    </div>

                    <!-- Expand/Collapse Chevron (for containers or blocks with children) -->
                    <div class="w-4 h-4 flex items-center justify-center">
                        <button 
                            v-if="element.children && element.children.length > 0"
                            @click.stop="toggleExpand(element.id)"
                            class="p-0.5 hover:bg-base-content/10 rounded transition-transform duration-200"
                            :class="{ 'rotate-90': isExpanded(element.id) }"
                        >
                            <PhCaretRight weight="bold" class="w-2.5 h-2.5 opacity-40 group-hover:opacity-100" />
                        </button>
                    </div>

                    <!-- Icon -->
                    <div class="shrink-0 w-4 h-4 flex items-center justify-center">
                        <component 
                            :is="iconMap[element.icon] || PhCube" 
                            weight="duotone" 
                            class="w-3.5 h-3.5 opacity-70" 
                            :class="{ 'text-primary opacity-100': store.activeBlockId === element.id }"
                        />
                    </div>

                    <!-- Name -->
                    <span class="font-medium flex-1 truncate tracking-tight text-[11px] opacity-90" :class="{ 'font-bold': store.activeBlockId === element.id }">
                        {{ element.name || element.type.replace('_', ' ') }}
                    </span>

                    <!-- Quick Actions (Visible when hovered or if active) -->
                    <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 pr-1"
                         :class="{ 'opacity-100': store.activeBlockId === element.id }">
                        
                        <!-- Group 1: Visibility & Lock -->
                        <div class="flex items-center">
                            <button @click.stop="store.toggleBlockVisibility(element.id)" 
                                    class="btn btn-ghost btn-xs btn-square h-5 w-5 min-h-0 text-base-content/30 hover:text-primary transition-colors" 
                                    :title="element.settings?.hidden ? t('admin.builder.action_show', 'Show Block') : t('admin.builder.action_hide', 'Hide Block')">
                                <component :is="element.settings?.hidden ? PhEyeSlash : PhEye" weight="bold" class="w-3 h-3" />
                            </button>
                            <button @click.stop="store.toggleBlockLock(element.id)" 
                                    class="btn btn-ghost btn-xs btn-square h-5 w-5 min-h-0 text-base-content/30 hover:text-warning transition-colors" 
                                    :title="element.settings?.locked ? t('admin.builder.action_unlock', 'Unlock Block') : t('admin.builder.action_lock', 'Lock Block')">
                                <component :is="element.settings?.locked ? PhLock : PhLockSimpleOpen" weight="bold" class="w-3 h-3" />
                            </button>
                        </div>

                        <!-- Separator -->
                        <div class="w-px h-3 bg-base-content/10 mx-1"></div>

                        <!-- Group 2: Drag & Drop -->
                        <div class="drag-handle btn btn-ghost btn-xs btn-square h-5 w-5 min-h-0 text-base-content/30 hover:text-primary transition-colors cursor-move" :title="t('admin.builder.action_drag', 'Drag to reorder')">
                            <PhDotsSixVertical weight="bold" class="w-3 h-3" />
                        </div>

                        <!-- Separator -->
                        <div class="w-px h-3 bg-base-content/10 mx-1"></div>

                        <!-- Group 3: Settings, Duplicate, Remove -->
                        <div class="flex items-center">
                            <button @click.stop="store.isEditingBlock = true; store.showRightSidebar = true; store.activeBlockId = element.id" 
                                    class="btn btn-ghost btn-xs btn-square h-5 w-5 min-h-0 text-base-content/30 hover:text-primary transition-colors" 
                                    :title="t('admin.builder.action_settings', 'Settings')">
                                <PhSlidersHorizontal weight="bold" class="w-3 h-3" />
                            </button>
                            <button @click.stop="store.duplicateBlock(element.id)" 
                                    class="btn btn-ghost btn-xs btn-square h-5 w-5 min-h-0 text-base-content/30 hover:text-primary transition-colors" 
                                    :title="t('admin.builder.action_duplicate', 'Duplicate')">
                                <PhCopy weight="bold" class="w-3.5 h-3.5" />
                            </button>
                            <button @click.stop="store.removeBlock(element.id)" 
                                    class="btn btn-ghost btn-xs btn-square h-5 w-5 min-h-0 text-base-content/30 hover:text-error transition-colors" 
                                    :title="t('admin.builder.action_delete', 'Delete')">
                                <PhTrash weight="bold" class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Recursive Children Rendering -->
                <transition name="fade-slide">
                    <div v-if="isExpanded(element.id) && element.children && (element.children.length > 0 || element.type === 'container')" class="mt-0.5">
                        <LayerTreeItem 
                            :blocks="element.children" 
                            :depth="depth + 1" 
                        />
                    </div>
                </transition>
            </div>
        </template>
    </draggable>
</template>

<script setup>
import draggable from 'vuedraggable';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { useTranslations } from '@/Composables/useTranslations';
import { 
    PhDotsSixVertical, PhSlidersHorizontal, PhCopy, PhTrash, PhCube, PhTextT, 
    PhTextAa, PhTextHOne, PhListBullets, PhQuotes, PhCode, PhCursorClick, 
    PhHandPointing, PhCaretDown, PhBrowsers, PhArrowsLeftRight, PhDesktop, 
    PhListDashes, PhUserCircle, PhCertificate, PhIdentificationCard, PhChats, 
    PhTimer, PhCircleHalf, PhChartLineUp, PhTable, PhWarningCircle, PhWarning, 
    PhListChecks, PhCircleNotch, PhPencilSimple, PhCheckSquare, PhRadioButton, 
    PhToggleRight, PhStarHalf, PhUploadSimple, PhStack, PhBoundingBox, PhColumns, 
    PhList, PhMinus, PhStar, PhImage, PhVideoCamera, PhNavigationArrow, PhDotsThree, 
    PhBrowser, PhFootprints, PhFolder, PhTerminal, PhDeviceMobile, PhAppWindow, 
    PhPlusCircle, PhArticle, PhBriefcase, PhArrowsClockwise, PhListNumbers, 
    PhDeviceTablet, PhArrowsOut, PhArrowUUpLeft, PhPlus, PhX, PhCaretRight,
    PhEye, PhEyeSlash, PhLock, PhLockSimpleOpen
} from '@phosphor-icons/vue';
import { ref } from 'vue';

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
    PhBriefcase, PhArrowsClockwise, PhListNumbers, PhEye, PhEyeSlash, PhLock, PhLockSimpleOpen
};

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


const store = useBlockBuilderStore();
const { t } = useTranslations();

// Local state for expanded nodes (persisted in store for pro feel)
// If store doesn't have it, we use a local ref for now but let's check
if (!store.expandedNodes) {
    store.expandedNodes = {}; 
}

const isExpanded = (id) => {
    return store.expandedNodes[id] !== false; // Default to expanded
};

const toggleExpand = (id) => {
    store.expandedNodes[id] = !isExpanded(id);
};

const onDragStart = () => {
    store.isDragging = true;
};

const onDragEnd = () => {
    store.isDragging = false;
    store.isDirty = true;
};
</script>

<style scoped>
.layer-item-wrapper {
    display: flex;
    flex-direction: column;
}

.active-layer {
    background: linear-gradient(90deg, rgba(var(--p), 0.15) 0%, rgba(var(--p), 0.05) 100%);
    backdrop-filter: blur(4px);
}

.layer-ghost {
    position: relative !important;
    opacity: 1 !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    height: 24px !important; /* Match layer item height */
    margin: 0 !important;
    overflow: visible !important;
}

.layer-ghost > * {
    display: none !important;
}

.layer-ghost::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    height: 2px;
    background-color: var(--p);
    border-radius: 9999px;
    z-index: 50;
    box-shadow: 0 0 12px rgba(var(--p), 0.6);
}

.layer-ghost::after {
    content: '';
    position: absolute;
    left: 0.25rem;
    top: 50%;
    transform: translateY(-50%);
    width: 0.6rem;
    height: 0.6rem;
    background-color: var(--p);
    border: 2px solid white;
    border-radius: 9999px;
    z-index: 50;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Transitions */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.2s ease-out;
    max-height: 500px;
    overflow: hidden;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-5px);
}

/* Ensure empty drop zone looks good */
.empty-drop-zone {
    height: 24px !important;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-drop-zone::before {
    content: 'DROP INSIDE';
    font-size: 8px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    opacity: 0.15;
}
</style>
