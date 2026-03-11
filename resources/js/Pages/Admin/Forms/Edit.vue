<template>
    <AdminLayout :full-width="true">
        <BlockBuilder 
            v-model:title="form.title"
            :categories="store.categories"
            :module-categories="formModuleCategories"
            :saving="form.processing"
            :templates="templates"
            :preview-url="previewUrl"
            @save="save"
        >
            <template #info>
                <div class="flex flex-col gap-6">
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">URL Slug</span></label>
                            <div class="join w-full">
                                <input type="text" v-model="form.settings.slug" class="input input-bordered input-sm join-item focus:border-primary/50 transition-all font-mono text-xs w-full" placeholder="form-slug" />
                                <button @click="form.settings.slug = generateSlug(form.title)" type="button" class="btn btn-sm btn-ghost join-item" title="Regenerate Slug">
                                    <PhArrowsClockwise weight="bold" class="w-3 h-3" />
                                </button>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Generated URL</span></label>
                            <div class="join w-full">
                                <input
                                    type="text"
                                    :value="previewUrl || ''"
                                    readonly
                                    class="input input-bordered input-sm join-item w-full font-mono text-[10px]"
                                    :placeholder="previewUrl ? '' : 'Save form first to generate preview URL'"
                                />
                                <a
                                    v-if="previewUrl"
                                    :href="previewUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn btn-sm btn-ghost join-item"
                                    title="Open Preview URL"
                                >
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3" />
                                </a>
                                <button v-else type="button" class="btn btn-sm btn-ghost join-item" disabled title="URL unavailable">
                                    <PhArrowSquareOut weight="bold" class="w-3 h-3 opacity-40" />
                                </button>
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Status</span></label>
                            <select v-model="form.status" class="select select-bordered select-sm focus:select-primary transition-all w-full">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="planned">Planned</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div v-if="form.status === 'planned' || form.status === 'published'" class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Publish Date & Time</span></label>
                            <DatePicker v-model="form.published_at" />
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Success Message</span></label>
                            <textarea v-model="form.settings.success_message" class="textarea textarea-bordered textarea-sm h-20 focus:border-primary/50 transition-all font-sans text-xs" placeholder="Thank you for your message!"></textarea>
                        </div>
                        <div class="form-control">
                            <label class="label pt-0"><span class="label-text text-xs font-bold opacity-60">Notification Email</span></label>
                            <input type="email" v-model="form.settings.notification_email" class="input input-bordered input-sm focus:border-primary/50 transition-all" placeholder="admin@example.com" />
                        </div>
                    </div>

                    <div class="divider opacity-10 my-0"></div>

                    <div class="space-y-3 bg-base-200/30 p-4 rounded-xl border border-base-content/10">
                        <div class="flex items-center justify-between text-[10px] uppercase tracking-wider opacity-60 font-bold px-1">
                            <span>Metadata</span>
                            <PhFingerprint weight="bold" class="w-3 h-3 text-primary" />
                        </div>
                        <div class="flex flex-col gap-2 text-[11px]">
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">Created</span>
                                <span class="font-mono">{{ formModel?.created_at ? new Date(formModel.created_at).toLocaleString() : 'New Content' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-base-100/50 p-2 rounded-lg border border-base-content/5">
                                <span class="opacity-60">Last Edit</span>
                                <span class="font-mono">{{ formModel?.updated_at ? new Date(formModel.updated_at).toLocaleString() : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </BlockBuilder>
    </AdminLayout>
</template>

<script setup>
import { onMounted, computed, watch } from 'vue';
import { PhArrowsClockwise, PhArrowSquareOut, PhFingerprint } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BlockBuilder from '@/Components/BlockBuilder.vue';
import DatePicker from '@/Components/DatePicker.vue';
import { useForm } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { useToastStore } from '@/Stores/useToastStore';

const props = defineProps({
    formModel: Object,
    templates: [Array, Object]
});

const store = useBlockBuilderStore();
const toast = useToastStore();

const formModuleCategories = [];

const previewUrl = computed(() => props.formModel?.id ? `/forms/${props.formModel.id}/preview` : null);

const generateSlug = (text) => {
    if (!text) return '';
    return text.toString().toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim()
        .replace(/^-+/, '')
        .replace(/-+$/, '');
};

const form = useForm({
    title: props.formModel?.title || '',
    content: props.formModel?.content || [],
    settings: {
        success_message: props.formModel?.settings?.success_message || 'Message sent!',
        notification_email: props.formModel?.settings?.notification_email || '',
        submit_url: props.formModel?.settings?.submit_url || '',
        slug: props.formModel?.settings?.slug || '',
    },
    status: props.formModel?.status || 'draft',
    published_at: props.formModel?.published_at ? props.formModel.published_at.substring(0, 19).replace('T', ' ') : '',
});

watch(() => form.title, (newTitle) => {
    form.settings.slug = generateSlug(newTitle);
});

onMounted(() => {
    store.init(props.formModel?.content || []);
});

const save = () => {
    form.content = store.blocks;
    if (props.formModel?.id) {
        form.put(route('admin.forms.update', props.formModel.id), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Formularz został pomyślnie zaktualizowany! 🎉');
            },
            onError: () => {
                toast.error('Wystąpił błąd podczas zapisywania formularza. ❌');
            },
            preserveScroll: true,
            preserveState: true
        });
    } else {
        form.post(route('admin.forms.store'), {
            onSuccess: () => {
                store.isDirty = false;
                toast.success('Formularz został pomyślnie utworzony! ✨');
            },
            onError: () => {
                toast.error('Wystąpił błąd podczas tworzenia formularza. ❌');
            }
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

</style>
