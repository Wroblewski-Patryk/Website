<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            title="Template" 
            save-label="Save Template"
            back-label="Back"
            :back-route="route('admin.templates.index')"
            :categories="store.categories"
            :saving="form.processing"
            :templates="templates"
            @save="save"
        >
            <template #info>
                <div class="form-control">
                    <label class="label"><span class="label-text">Template Name</span></label>
                    <input type="text" v-model="form.name" class="input input-bordered input-sm" placeholder="e.g. Main Header" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Type</span></label>
                    <select v-model="form.type" class="select select-bordered select-sm">
                        <option value="header">Header</option>
                        <option value="footer">Footer</option>
                        <option value="sidebar">Sidebar</option>
                        <option value="page">Page Template</option>
                    </select>
                </div>
                <div class="form-control flex-row items-center gap-3 p-4 bg-base-200 rounded-2xl border border-white/5">
                    <input type="checkbox" v-model="form.is_default" class="checkbox checkbox-primary" id="is_default" />
                    <label for="is_default" class="cursor-pointer">
                        <span class="text-sm font-bold">Set as Default</span>
                        <p class="text-[10px] opacity-50">Applies to all pages without override</p>
                    </label>
                </div>
            </template>

            <template #canvas-footer v-if="form.type === 'header'">
                <div class="h-[400px] bg-base-200/5 flex items-center justify-center opacity-10 select-none border-y border-black/5">
                    <div class="text-center">
                        <PhFileText weight="regular" class="w-16 h-16 mb-4 mx-auto block" />
                        <p class="text-xl font-bold uppercase tracking-widest">Page Content Area</p>
                    </div>
                </div>
            </template>

            <template #canvas-header v-if="form.type === 'footer'">
                 <div class="h-[400px] bg-base-200/5 flex items-center justify-center opacity-10 select-none border-y border-black/5">
                    <div class="text-center">
                        <PhFileText weight="regular" class="w-16 h-16 mb-4 mx-auto block" />
                        <p class="text-xl font-bold uppercase tracking-widest">Page Content Area</p>
                    </div>
                </div>
            </template>
        </BlockBuilder>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/Components/BlockBuilder.vue';
import { useForm } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { PhFileText, PhLayout } from '@phosphor-icons/vue';
import { onMounted } from 'vue';

const props = defineProps({
    template: Object,
    templates: [Array, Object],
});

const store = useBlockBuilderStore();

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
