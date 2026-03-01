<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import BlockEditorSidebar from '@/Components/BlockEditorSidebar.vue';

const props = defineProps(['template']);
const store = useBlockBuilderStore();

const form = useForm({
    name: props.template?.name || '',
    type: props.template?.type || 'header',
    is_active: props.template?.is_active || true,
    content: null,
});

onMounted(() => {
    store.init(props.template?.content || []);
});

function saveTemplate() {
    form.content = store.blocks;
    if (props.template?.id) {
        form.put(`/admin/templates/${props.template.id}`);
    } else {
        form.post(`/admin/templates`);
    }
}
</script>

<template>
    <Head :title="template?.id ? 'Edit Template' : 'Create Template'" />
    <AdminLayout>
        <div class="h-[calc(100vh-4rem)] flex overflow-hidden -m-6">
            <!-- Center Canvas -->
            <div class="flex-1 bg-base-300 overflow-y-auto relative p-8">
                <div class="max-w-screen-xl mx-auto shadow-2xl bg-base-100 min-h-[400px] border border-base-200 relative group transition-all duration-300 ease-in-out p-4">
                    
                    <div v-for="block in store.blocks" :key="block.id" 
                         @click="store.setActiveBlock(block.id)"
                         :class="['relative border border-transparent transition-all cursor-pointer', store.activeBlockId === block.id ? '!border-primary z-10' : 'hover:border-primary/50']">
                        
                        <DynamicBlock :block="block" />
                        
                        <!-- Block Overlays -->
                        <div v-if="store.activeBlockId === block.id" class="absolute top-0 right-0 translate-x-12 bg-base-100 shadow-xl border border-base-200 p-1 rounded-lg flex flex-col gap-1 z-20">
                            <button @click.stop="store.removeBlock(block.id)" class="btn btn-sm btn-error btn-ghost p-2 text-error" title="Delete">×</button>
                        </div>
                        
                        <div v-if="!block.content || Object.keys(block.content).length === 0" class="p-8 text-center text-base-content/50 border border-dashed border-base-300 bg-base-200/50 m-2 rounded">
                            Empty {{ block.type }} block
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="w-96 bg-base-100 shadow-xl z-30 flex flex-col overflow-hidden shrink-0 border-l border-base-200">
                <div class="p-4 border-b border-base-200 bg-base-200/50 flex justify-between items-center backdrop-blur-md">
                    <h2 class="font-bold">Template Builder</h2>
                    <button @click="saveTemplate" class="btn btn-primary btn-sm" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner"></span> <span v-else>Save</span>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-6">
                    <div v-if="!store.activeBlockId">
                        <h3 class="font-bold mb-4">Template Settings</h3>
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Name</span></label>
                            <input type="text" v-model="form.name" class="input input-bordered input-sm w-full" placeholder="e.g. Main Header" />
                        </div>
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text">Type</span></label>
                            <select v-model="form.type" class="select select-bordered select-sm w-full">
                                <option value="header">Header</option>
                                <option value="footer">Footer</option>
                            </select>
                        </div>

                        <div class="divider">Add Blocks</div>
                        <div class="grid grid-cols-2 gap-2">
                            <button @click="store.addBlock('nav')" class="btn btn-outline btn-sm">Navigation</button>
                            <button @click="store.addBlock('text')" class="btn btn-outline btn-sm">Text</button>
                            <button @click="store.addBlock('image')" class="btn btn-outline btn-sm">Image</button>
                        </div>
                    </div>
                    
                    <!-- Active Block Settings -->
                    <BlockEditorSidebar v-else />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
