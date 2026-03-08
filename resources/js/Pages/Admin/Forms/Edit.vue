<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            title="Form" 
            save-label="Save Form"
            back-label="Back"
            :back-route="route('admin.forms.index')"
            :categories="categories"
            :saving="form.processing"
            :templates="templates"
            @save="save"
        >
            <template #info>
                <div class="form-control">
                    <label class="label"><span class="label-text">Form Title</span></label>
                    <input type="text" v-model="form.title" class="input input-bordered input-sm" placeholder="e.g. Contact Us" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Success Message</span></label>
                    <textarea v-model="form.settings.success_message" class="textarea textarea-bordered textarea-sm h-20" placeholder="Thank you for your message!"></textarea>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Notification Email</span></label>
                    <input type="email" v-model="form.settings.notification_email" class="input input-bordered input-sm" placeholder="admin@example.com" />
                </div>
                
                <div class="divider opacity-5 my-2"></div>
                
                <div class="form-control">
                    <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Status</span></label>
                    <select v-model="form.status" class="select select-bordered select-sm text-xs bg-base-100/50 hover:bg-base-200/50 transition-all border-white/10 focus:border-primary/50">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="planned">Planned</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <DatePicker 
                    v-if="form.status === 'planned' || form.status === 'published'"
                    v-model="form.published_at" 
                    label="Publish Date & Time"
                />
            </template>

            <template #canvas-header>
                <div class="p-12 bg-primary/5 border-b border-black/5 flex flex-col items-center text-center">
                    <h1 class="text-4xl font-black mb-2">{{ form.title || 'Untitled Form' }}</h1>
                    <p class="opacity-40 text-sm">Preview of the generated form interface</p>
                </div>
            </template>
        </BlockBuilder>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { PhTextbox } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/Components/BlockBuilder.vue';
import DatePicker from '@/Components/DatePicker.vue';
import { useForm } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';

const props = defineProps({
    formModel: Object,
    templates: Array
});

const store = useBlockBuilderStore();

const categories = ref([
    {
        id: 'forms',
        name: 'Form Fields',
        icon: 'Textbox',
        blocks: [
            { type: 'form_input', label: 'Text Input', icon: 'TextT' },
            { type: 'form_textarea', label: 'Long Text', icon: 'TextAlignLeft' },
            { type: 'form_select', label: 'Dropdown', icon: 'ListBullets' },
            { type: 'button', label: 'Submit Button', icon: 'CursorClick' },
        ]
    },
    {
        id: 'content',
        name: 'Design Elements',
        icon: 'PaintBrush',
        blocks: [
            { type: 'heading', label: 'Heading', icon: 'TextHOne' },
            { type: 'paragraph', label: 'Instruction', icon: 'Paragraph' },
            { type: 'image', label: 'Illustration', icon: 'Image' },
            { type: 'divider', label: 'Divider', icon: 'Minus' },
            { type: 'spacer', label: 'Spacer', icon: 'ArrowsVertical' },
        ]
    },
    {
        id: 'layout',
        name: 'Structure',
        icon: 'SquaresFour',
        blocks: [
            { type: 'columns', label: 'Side by Side', icon: 'Columns' },
            { type: 'group', label: 'Field Group', icon: 'ObjectGroup' },
        ]
    }
]);

const form = useForm({
    title: props.formModel?.title || '',
    content: props.formModel?.content || [],
    settings: props.formModel?.settings || { success_message: 'Message sent!', notification_email: '', submit_url: '' },
    status: props.formModel?.status || 'draft',
    published_at: props.formModel?.published_at ? props.formModel.published_at.substring(0, 19).replace('T', ' ') : '',
});

onMounted(() => {
    store.init(props.formModel?.content || []);
});

const save = () => {
    form.content = store.blocks;
    if (props.formModel?.id) {
        form.put(route('admin.forms.update', props.formModel.id), {
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
