<template>
    <AdminLayout>
        <div class="h-[calc(100vh-64px)] flex flex-col overflow-hidden bg-base-300">
            <!-- Top Bar -->
            <div class="flex items-center justify-between px-6 py-2 bg-base-100 border-b border-white/5 shadow-md z-20">
                <div class="flex items-center gap-4">
                    <button @click="$inertia.visit(route('admin.forms.index'))" class="btn btn-ghost btn-sm">
                        <i class="fas fa-chevron-left mr-2"></i> Back
                    </button>
                    <div class="h-6 w-[1px] bg-white/10 mx-2"></div>
                    <div class="join bg-base-200 p-1 rounded-xl">
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
                        <i class="fas fa-save mr-2"></i> Save Form
                    </button>
                </div>
            </div>

            <div class="flex-1 flex overflow-hidden">
                <!-- Left Sidebar -->
                <div class="w-80 bg-base-100 border-r border-white/5 flex flex-col z-10 shadow-xl">
                    <div class="tabs tabs-boxed bg-transparent p-2 m-2">
                        <button @click="leftTab = 'blocks'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'blocks' }">Fields</button>
                        <button @click="leftTab = 'settings'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'settings' }">Settings</button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                        <!-- Field Palette -->
                        <div v-show="leftTab === 'blocks'" class="space-y-6">
                            <section>
                                <h3 class="text-xs font-bold opacity-40 uppercase tracking-widest mb-3">Form Fields</h3>
                                <draggable 
                                    :list="formBlocks" 
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
                                            <i :class="[element.icon, 'text-lg text-secondary']"></i>
                                            <span class="text-[10px]">{{ element.label }}</span>
                                        </div>
                                    </template>
                                </draggable>
                            </section>
                        </div>

                        <!-- Form Settings -->
                        <div v-show="leftTab === 'settings'" class="space-y-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Form Name</span></label>
                                <input type="text" v-model="form.name" class="input input-bordered input-sm" placeholder="e.g. Contact Form" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Success Message</span></label>
                                <textarea v-model="form.settings.success_message" class="textarea textarea-bordered textarea-sm" placeholder="Thank you for your message!"></textarea>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Submit URL (Optional)</span></label>
                                <input type="text" v-model="form.settings.submit_url" class="input input-bordered input-sm" placeholder="/api/custom-submit" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center: Canvas -->
                <div class="flex-1 bg-base-300 overflow-y-auto p-12 flex justify-center custom-scrollbar">
                    <div :class="[
                        'bg-white shadow-2xl transition-all duration-500 rounded-xl overflow-hidden min-h-[500px] border border-black/5',
                        viewport === 'desktop' ? 'w-full max-w-2xl' : (viewport === 'tablet' ? 'w-[768px]' : 'w-[375px]')
                    ]">
                        <!-- Form Header Preview -->
                        <div class="p-8 bg-primary text-primary-content">
                            <h2 class="text-2xl font-bold">{{ form.name || 'New Form' }}</h2>
                            <p class="opacity-80 text-sm">Building a custom interaction for your users.</p>
                        </div>

                        <!-- Reorderable Block Canvas -->
                        <div class="p-8">
                            <draggable 
                                v-model="blocks" 
                                :group="'blocks'"
                                item-key="id"
                                handle=".drag-handle"
                                ghost-class="ghost-block"
                                class="min-h-[300px] space-y-4">
                                <template #item="{ element, index }">
                                    <div class="group relative"
                                         @click="store.activeBlockId = element.id"
                                         :class="{ 'ring-2 ring-primary ring-inset rounded-lg': store.activeBlockId === element.id }">
                                        
                                        <!-- Block Controls Overlays -->
                                        <div class="absolute -right-2 -top-2 z-30 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                            <div class="drag-handle btn btn-square btn-xs btn-ghost bg-white/90 border border-black/10 backdrop-blur cursor-move text-black/60 shadow-md rounded-full" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></div>
                                            <button @click.stop="store.removeBlock(element.id)" class="btn btn-square btn-xs btn-error btn-ghost bg-red-500/90 border border-red-500/20 backdrop-blur text-white shadow-md rounded-full" title="Delete"><i class="fas fa-trash"></i></button>
                                        </div>

                                        <DynamicBlock :block="element" />
                                    </div>
                                </template>
                            </draggable>

                            <div v-if="store.blocks.length === 0" class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-base-content/10 rounded-2xl bg-base-200/20 pointer-events-none">
                                <i class="fas fa-wpforms text-4xl mb-4 opacity-20 text-primary"></i>
                                <p class="opacity-40">Drag form fields here</p>
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
import { onMounted, ref, computed } from 'vue';
import draggable from 'vuedraggable';

const props = defineProps({
    form_data: Object,
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

const formBlocks = ref([
    { type: 'form_input', label: 'Input', icon: 'fas fa-font' },
    { type: 'form_textarea', label: 'Textarea', icon: 'fas fa-align-justify' },
    { type: 'image', label: 'Image', icon: 'fas fa-image' },
    { type: 'button', label: 'Button', icon: 'fas fa-mouse-pointer' },
    { type: 'portfolio', label: 'Portfolio', icon: 'fas fa-briefcase' },
    { type: 'custom_code', label: 'Code', icon: 'fas fa-code' },
    { type: 'contact_form', label: 'Form', icon: 'fas fa-envelope' },
    { type: 'language_switcher', label: 'Lang', icon: 'fas fa-globe' },
    { type: 'menu', label: 'Menu', icon: 'fas fa-bars' },
]);

const layoutBlocks = ref([
    { type: 'section', label: 'Section', icon: 'fas fa-layer-group' },
    { type: 'columns', label: 'Columns', icon: 'fas fa-columns' },
]);

const cloneBlock = (block) => {
    return store.createBlockObject(block.type);
};

const form = useForm({
    name: props.form_data?.name || '',
    content: props.form_data?.content || [],
    settings: props.form_data?.settings || { success_message: 'Message sent!', submit_url: '' },
});

onMounted(() => {
    store.init(props.form_data?.content || []);
});

const save = () => {
    form.content = store.blocks;
    if (props.form_data?.id) {
        form.put(route('admin.forms.update', props.form_data.id), {
            onSuccess: () => store.isDirty = false
        });
    } else {
        form.post(route('admin.forms.store'), {
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
