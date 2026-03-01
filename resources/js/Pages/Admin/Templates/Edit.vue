<template>
    <AdminLayout>
        <div class="h-[calc(100vh-64px)] flex flex-col overflow-hidden bg-base-300">
            <!-- Top Bar -->
            <div class="flex items-center justify-between px-6 py-2 bg-base-100 border-b border-white/5 shadow-md z-20">
                <div class="flex items-center gap-4">
                    <button @click="$inertia.visit(route('admin.templates.index'))" class="btn btn-ghost btn-sm">
                        <i class="fas fa-chevron-left mr-2"></i> Back
                    </button>
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
                </div>
                
                <div class="flex items-center gap-3">
                    <span v-if="store.isDirty" class="text-xs opacity-50 italic mr-2">Unsaved changes...</span>
                    <button @click="save" class="btn btn-primary btn-sm px-6 rounded-full shadow-lg shadow-primary/20">
                        <i class="fas fa-save mr-2"></i> Save Template
                    </button>
                </div>
            </div>

            <div class="flex-1 flex overflow-hidden">
                <!-- Left Sidebar: Palette & Info -->
                <div class="w-80 bg-base-100 border-r border-white/5 flex flex-col z-10 shadow-xl">
                    <div class="tabs tabs-boxed bg-transparent p-2 m-2">
                        <button @click="leftTab = 'blocks'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'blocks' }">Blocks</button>
                        <button @click="leftTab = 'settings'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'settings' }">Info</button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                        <!-- Block Palette -->
                        <div v-show="leftTab === 'blocks'" class="space-y-6">
                            <section>
                                <h3 class="text-xs font-bold opacity-40 uppercase tracking-widest mb-3">Layout</h3>
                                <draggable 
                                    :list="layoutBlocks" 
                                    :group="{ name: 'blocks', pull: 'clone', put: false }" 
                                    :clone="cloneBlock"
                                    :sort="false"
                                    item-key="type"
                                    class="grid grid-cols-2 gap-2">
                                    <template #item="{ element }">
                                        <div @click="store.addBlock(element.type)" class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/10 hover:bg-white/5 cursor-grab active:cursor-grabbing">
                                            <i :class="[element.icon, 'text-lg text-primary']"></i>
                                            <span class="text-[10px]">{{ element.label }}</span>
                                        </div>
                                    </template>
                                </draggable>
                            </section>

                            <section>
                                <h3 class="text-xs font-bold opacity-40 uppercase tracking-widest mb-3">Content</h3>
                                <draggable 
                                    :list="contentBlocks" 
                                    :group="{ name: 'blocks', pull: 'clone', put: false }" 
                                    :clone="cloneBlock"
                                    :sort="false"
                                    item-key="type"
                                    class="grid grid-cols-2 gap-2">
                                    <template #item="{ element }">
                                        <div @click="store.addBlock(element.type)" class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/10 hover:bg-white/5 cursor-grab active:cursor-grabbing">
                                            <i :class="[element.icon, 'text-lg text-secondary']"></i>
                                            <span class="text-[10px]">{{ element.label }}</span>
                                        </div>
                                    </template>
                                </draggable>
                            </section>
                        </div>

                        <!-- Template Settings -->
                        <div v-show="leftTab === 'settings'" class="space-y-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Template Name</span></label>
                                <input type="text" v-model="form.name" class="input input-bordered input-sm" placeholder="e.g. Main Header" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Type</span></label>
                                <select v-model="form.type" class="select select-bordered select-sm">
                                    <option value="header">Header</option>
                                    <option value="footer">Footer</option>
                                </select>
                            </div>
                            <div class="form-control flex-row items-center gap-3 p-4 bg-base-200 rounded-2xl border border-white/5">
                                <input type="checkbox" v-model="form.is_default" class="checkbox checkbox-primary" id="is_default" />
                                <label for="is_default" class="cursor-pointer">
                                    <span class="text-sm font-bold">Set as Default</span>
                                    <p class="text-[10px] opacity-50">Applies to all pages without override</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center: Canvas -->
                <div class="flex-1 bg-base-300 overflow-y-auto p-12 flex justify-center custom-scrollbar">
                    <div :class="[
                        'bg-white shadow-2xl transition-all duration-500 rounded-sm overflow-x-hidden min-h-[300px]',
                        viewport === 'desktop' ? 'w-full max-w-6xl' : (viewport === 'tablet' ? 'w-[768px]' : 'w-[375px]')
                    ]">
                        <!-- Reorderable Block Canvas -->
                        <div class="p-0">
                            <draggable 
                                v-model="blocks" 
                                :group="'blocks'"
                                item-key="id"
                                handle=".drag-handle"
                                ghost-class="ghost-block"
                                class="min-h-[200px] bg-base-100/30">
                                <template #item="{ element, index }">
                                    <div class="group relative"
                                         @click="store.activeBlockId = element.id"
                                         :class="{ 'ring-2 ring-primary ring-inset': store.activeBlockId === element.id }">
                                        
                                        <!-- Block Controls Overlays -->
                                        <div class="absolute right-2 top-2 z-30 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                            <div class="drag-handle btn btn-square btn-xs btn-ghost bg-white/80 border border-black/5 backdrop-blur cursor-move text-black/60 shadow-sm" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></div>
                                            <button @click.stop="store.removeBlock(element.id)" class="btn btn-square btn-xs btn-error btn-ghost bg-red-500/80 border border-red-500/20 backdrop-blur text-white shadow-sm" title="Delete"><i class="fas fa-trash"></i></button>
                                        </div>

                                        <DynamicBlock :block="element" />
                                    </div>
                                </template>
                            </draggable>

                            <div v-if="store.blocks.length === 0" class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-base-content/10 m-10 rounded-2xl bg-base-200/20 pointer-events-none">
                                <i class="fas fa-columns text-4xl mb-4 opacity-20 text-primary"></i>
                                <p class="opacity-40">Drag header/footer blocks here</p>
                            </div>
                        </div>

                        <!-- Page Body Placeholder -->
                        <div class="h-[400px] bg-base-200/5 flex items-center justify-center opacity-10 select-none border-y border-black/5">
                            <div class="text-center">
                                <i class="fas fa-file-alt text-6xl mb-4"></i>
                                <p class="text-xl font-bold uppercase tracking-widest">Page Content Area</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Settings -->
                <div class="w-80 bg-base-100 border-l border-white/5 overflow-y-auto z-10 shadow-2xl custom-scrollbar">
                    <BlockEditorSidebar :menus="menus" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import BlockEditorSidebar from '@/Components/BlockEditorSidebar.vue';
import { onMounted, watch, ref, computed } from 'vue';
import draggable from 'vuedraggable';

const props = defineProps({
    template: Object,
    menus: Array
});

const store = useBlockBuilderStore();

const blocks = computed({
    get: () => store.blocks,
    set: (value) => {
        store.blocks = value;
        store.isDirty = true;
    }
});
const viewport = ref('desktop');
const leftTab = ref('blocks');

const layoutBlocks = ref([
    { type: 'section', label: 'Section', icon: 'fas fa-layer-group' },
    { type: 'columns', label: 'Columns', icon: 'fas fa-columns' },
]);

const contentBlocks = ref([
    { type: 'heading', label: 'Heading', icon: 'fas fa-heading' },
    { type: 'image', label: 'Image', icon: 'fas fa-image' },
    { type: 'button', label: 'Button', icon: 'fas fa-mouse-pointer' },
    { type: 'portfolio', label: 'Portfolio', icon: 'fas fa-briefcase' },
    { type: 'custom_code', label: 'Code', icon: 'fas fa-code' },
    { type: 'contact_form', label: 'Form', icon: 'fas fa-envelope' },
    { type: 'language_switcher', label: 'Lang', icon: 'fas fa-globe' },
    { type: 'menu', label: 'Menu', icon: 'fas fa-bars' },
]);

const cloneBlock = (block) => {
    return store.createBlockObject(block.type);
};

const form = useForm({
    name: props.template?.name || '',
    type: props.template?.type || 'header',
    content: props.template?.content || [],
    is_default: props.template?.is_default ?? false,
});

onMounted(() => {
    store.init(props.template?.content || []);
});

const save = () => {
    form.content = store.blocks;
    if (props.template?.id) {
        form.put(route('admin.templates.update', props.template.id), {
            onSuccess: () => store.isDirty = false
        });
    } else {
        form.post(route('admin.templates.store'), {
            onSuccess: () => store.isDirty = false
        });
    }
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
