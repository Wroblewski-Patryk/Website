<script setup>
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { computed, ref, watch, useSlots } from 'vue';
import { 
    PhX, PhStack, PhClockCounterClockwise, PhSelection, PhSlidersHorizontal, PhInfo, PhCloudArrowUp, PhFloppyDisk, PhGlobe, PhGear
} from '@phosphor-icons/vue';

// New Modular Components
import BlockSettingsManager from './BlockBuilder/BlockSettingsManager.vue';
import StyleSettings from './BlockBuilder/Settings/StyleSettings.vue';
import AnimationSettings from './BlockBuilder/Settings/AnimationSettings.vue';

// Inspector Tabs
import InspectorLayersTab from './BlockBuilder/Inspector/InspectorLayersTab.vue';
import InspectorHistoryTab from './BlockBuilder/Inspector/InspectorHistoryTab.vue';
import InspectorInfoTab from './BlockBuilder/Inspector/InspectorInfoTab.vue';
import SidebarPanelHeader from './BlockBuilder/Layout/SidebarPanelHeader.vue';

const props = defineProps({
    templates: [Array, Object],
    saving: Boolean
});

const store = useBlockBuilderStore();
defineEmits(['save']);
const activeSidebarTab = ref('content');
const activeInspectorTab = ref('layers'); // Layers, History, Info, SEO
const $slots = useSlots();

// Helper to get a flat list of templates for dropdowns
const flattenedTemplates = computed(() => {
    if (Array.isArray(props.templates)) return props.templates;
    if (typeof props.templates === 'object' && props.templates !== null) {
        return Object.values(props.templates).flat();
    }
    return [];
});

watch(() => store.activeBlock, (newBlock) => {
    if (newBlock) {
        if (!newBlock.settings) newBlock.settings = {};
        if (!newBlock.settings.style) newBlock.settings.style = {};
        if (!newBlock.settings.animation) newBlock.settings.animation = {};
        // Ensure default animation fields if enabled
        if (newBlock.settings.animation.type && !newBlock.settings.animation.duration) {
            newBlock.settings.animation.duration = 800;
        }
    }
}, { immediate: true, deep: true });

const closeSidebar = () => {
    store.isEditingBlock = false;
    store.activeBlockId = null;
};

const showSeo = computed(() => !!$slots.seo);
const showAdvanced = computed(() => !!$slots.advanced);
</script>

<template>
    <div class="h-full flex flex-col bg-base-100 min-w-[320px] w-full">
        
        <!-- CASE 1: BLOCK SETTINGS -->
        <template v-if="store.activeBlock && store.isEditingBlock">
            <!-- Sidebar Header -->
            <SidebarPanelHeader
                :icon="PhSelection"
                icon-weight="bold"
                title="Settings">
                <template #actions>
                    <button @click="closeSidebar" class="btn btn-ghost btn-xs btn-circle">
                        <PhX weight="bold" class="w-4 h-4" />
                    </button>
                </template>
            </SidebarPanelHeader>

            <!-- Sidebar Tabs -->
            <div class="flex border-b border-base-content/10 bg-base-200/50">
                <button v-for="tab in ['content', 'design', 'animations', 'advanced']" :key="tab"
                        @click="activeSidebarTab = (tab === 'design' ? 'style' : tab)"
                        class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-all relative"
                        :class="activeSidebarTab === (tab === 'design' ? 'style' : tab) ? 'text-primary' : 'opacity-40 hover:opacity-100'">
                    {{ tab }}
                    <div v-if="activeSidebarTab === (tab === 'design' ? 'style' : tab)" class="absolute bottom-0 left-4 right-4 h-0.5 bg-primary rounded-full"></div>
                </button>
            </div>

            <!-- Sidebar Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                
                <!-- CONTENT TAB -->
                <div v-if="activeSidebarTab === 'content'">
                    <BlockSettingsManager 
                        :active-block="store.activeBlock" 
                        :templates="flattenedTemplates" 
                    />
                </div>

                <!-- STYLE / DESIGN TAB -->
                <div v-if="activeSidebarTab === 'style' || activeSidebarTab === 'design'" class="p-6">
                    <StyleSettings 
                        :settings="store.activeBlock.settings" 
                        :block-type="store.activeBlock.type" 
                    />
                </div>

                <!-- ANIMATIONS TAB -->
                <div v-if="activeSidebarTab === 'animations'" class="p-6">
                    <AnimationSettings :settings="store.activeBlock.settings" />
                </div>

                <!-- ADVANCED TAB -->
                <div v-if="activeSidebarTab === 'advanced'" class="p-6 space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Custom CSS Class</span></label>
                        <input type="text" v-model="store.activeBlock.settings.customClass" class="input input-bordered w-full font-mono text-xs" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Block ID</span></label>
                        <input type="text" v-model="store.activeBlock.id" readonly class="input input-bordered w-full font-mono text-[10px] opacity-50 cursor-not-allowed" />
                    </div>
                </div>
            </div>
        </template>

        <!-- CASE 2: DOCUMENT INSPECTOR (No block selected) -->
        <template v-else>
            <!-- Inspector Header -->
            <SidebarPanelHeader
                :icon="PhSlidersHorizontal"
                icon-weight="duotone"
                title="Inspector" />

            <!-- Inspector Tabs -->
            <div class="flex border-b border-base-content/10 bg-base-200/50 backdrop-blur-sm sticky z-10 overflow-x-auto no-scrollbar scroll-smooth">
                <button v-for="tab in ['layers', 'history', 'info', ...(showSeo ? ['seo'] : []), ...(showAdvanced ? ['advanced'] : [])]" :key="tab"
                        @click="activeInspectorTab = tab"
                        class="flex-1 min-w-[60px] py-3 text-[9px] font-normal uppercase tracking-tighter transition-all relative flex flex-col items-center justify-center gap-1.5"
                        :class="activeInspectorTab === tab ? 'text-primary' : 'opacity-40 hover:opacity-100 hover:bg-base-content/5'">
                    
                    <PhStack v-if="tab === 'layers'" weight="bold" class="w-4 h-4" />
                    <PhClockCounterClockwise v-if="tab === 'history'" weight="bold" class="w-4 h-4" />
                    <PhInfo v-if="tab === 'info'" weight="bold" class="w-4 h-4" />
                    <PhGlobe v-if="tab === 'seo'" weight="bold" class="w-4 h-4" />
                    <PhGear v-if="tab === 'advanced'" weight="bold" class="w-4 h-4" />
                    
                    <span class="leading-none whitespace-nowrap overflow-hidden text-ellipsis max-w-full px-1">
                        {{ tab === 'advanced' ? 'Advanced' : tab }}
                    </span>

                    <div v-if="activeInspectorTab === tab" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary shadow-[0_-2px_10px_rgba(var(--admin-p),0.5)]"></div>
                </button>
            </div>

            <!-- Inspector Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden bg-base-200/10">
                <InspectorLayersTab 
                    v-if="activeInspectorTab === 'layers'"
                    :model-value="store.blocks"
                    :active-block-id="store.activeBlockId"
                    @select="store.selectBlock($event)"
                    @delete="store.deleteBlock($event)"
                    @toggle-visibility="store.toggleBlockVisibility($event)"
                    class="pt-4"
                />

                <div v-if="activeInspectorTab === 'history'" class="h-full overflow-y-auto custom-scrollbar p-4">
                    <slot name="history">
                        <InspectorHistoryTab 
                            :history="store.history"
                            :current-index="store.historyIndex"
                            @restore="store.restoreHistory"
                            class="pt-4"
                        />
                    </slot>
                </div>

                <InspectorInfoTab 
                    v-if="activeInspectorTab === 'info'"
                    :total-blocks="store.blocks.length"
                    :last-saved="store.lastSaved"
                    class="pt-4"
                >
                    <template #module-info>
                        <slot name="info"></slot>
                    </template>
                </InspectorInfoTab>

                <div v-if="activeInspectorTab === 'seo'" class="p-6 h-full overflow-y-auto custom-scrollbar">
                    <slot name="seo"></slot>
                </div>
                <div v-if="activeInspectorTab === 'advanced'" class="p-6 h-full overflow-y-auto custom-scrollbar">
                    <slot name="advanced">
                        <div class="text-center py-20 opacity-20 italic text-xs font-serif">
                            No advanced options for this module.
                        </div>
                    </slot>
                </div>
            </div>


        </template>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
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


