<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { 
    PhDeviceMobile, PhGlobe, PhEnvelope, PhPhone, 
    PhImage, PhHouse, PhUsers, PhCheck, PhArrowLeft, PhTrash
} from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/features/admin/shared/components/ModuleHeader.vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useToastStore } from '@/Stores/useToastStore';

const { t } = useTranslations();
const toast = useToastStore();
const props = defineProps(['client', 'languages']);

const activeLocale = ref(usePage().props.locale || 'pl');
const form = useForm({
    title: props.client.title || {},
    slug: props.client.slug || {},
    description: props.client.description || {},
    logo: props.client.logo || '',
    website_url: props.client.website_url || '',
    email: props.client.email || '',
    phone: props.client.phone || '',
    is_active: props.client.is_active ?? true,
});

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: PhHouse },
    { label: t('admin.menu.projects', 'Projects'), url: route('admin.projects.index') },
    { label: t('admin.menu.clients', 'Clients'), url: route('admin.projects.clients.index'), icon: PhUsers },
    { label: props.client?.id ? t('admin.common.edit', 'Edit') : t('admin.common.add', 'Add New') }
];

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

function submit() {
    if (props.client?.id) {
        form.put(route('admin.projects.clients.update', props.client.id), {
            onSuccess: () => toast.success(t('admin.clients.update_success', 'Client updated successfully!')),
        });
    } else {
        form.post(route('admin.projects.clients.store'), {
            onSuccess: () => toast.success(t('admin.clients.create_success', 'Client created successfully!')),
        });
    }
}
</script>

<template>
    <Head :title="client?.id ? t('admin.clients.edit_title', 'Edit Client') : t('admin.clients.add_title', 'Add Client')" />
    <AdminLayout>
        <ModuleHeader
            :title="client?.id ? (client.title?.[activeLocale] || t('admin.clients.edit_title', 'Edit Client')) : t('admin.clients.add_title', 'Add New Client')"
            :breadcrumbs="breadcrumbs"
        >
            <template #actions>
                <div class="flex gap-2">
                    <button @click="submit" class="btn btn-primary rounded-xl px-6" :disabled="form.processing">
                        <PhCheck v-if="!form.processing" weight="bold" class="w-4 h-4" />
                        <span v-else class="loading loading-spinner loading-xs"></span>
                        {{ t('admin.common.save', 'Save') }}
                    </button>
                </div>
            </template>
        </ModuleHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <div class="card bg-base-100 shadow-sm border border-base-200 overflow-hidden rounded-2xl">
                    <div class="card-body gap-6 p-8">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-black tracking-tight flex items-center gap-3">
                                {{ t('admin.clients.basic_info', 'Basic Information') }}
                            </h2>
                            <div class="flex gap-1 p-1 bg-base-200 rounded-xl">
                                <button 
                                    v-for="lang in languages" 
                                    :key="lang.code"
                                    @click="activeLocale = lang.code"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all"
                                    :class="activeLocale === lang.code ? 'bg-base-100 shadow-sm text-primary' : 'opacity-40 hover:opacity-100'"
                                >
                                    {{ lang.code.toUpperCase() }}
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.clients.name_label', 'Name') }}</span></label>
                                <input 
                                    type="text" 
                                    v-model="form.title[activeLocale]" 
                                    class="input input-bordered w-full rounded-xl bg-base-200/50" 
                                    placeholder="Google / Apple / Individual Name"
                                    @input="form.slug[activeLocale] = generateSlug(form.title[activeLocale])"
                                />
                            </div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.common.slug', 'Slug') }}</span></label>
                                <input type="text" v-model="form.slug[activeLocale]" class="input input-bordered w-full rounded-xl bg-base-200/50 font-mono text-xs" />
                            </div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.common.description', 'Description') }}</span></label>
                                <textarea v-model="form.description[activeLocale]" class="textarea textarea-bordered h-32 rounded-xl bg-base-200/50" :placeholder="t('admin.clients.desc_placeholder', 'Brief company overview...')"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info Card -->
                <div class="card bg-base-100 shadow-sm border border-base-200 overflow-hidden rounded-2xl">
                    <div class="card-body gap-6 p-8">
                        <h2 class="text-lg font-black tracking-tight flex items-center gap-3">
                            {{ t('admin.clients.contact_info', 'Contact details') }}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.clients.website_label', 'Website URL') }}</span></label>
                                <div class="join w-full">
                                    <div class="join-item bg-base-200 flex items-center px-4"><PhGlobe class="w-4 h-4 opacity-40" /></div>
                                    <input type="text" v-model="form.website_url" class="input input-bordered join-item w-full bg-base-200/50 rounded-xl" placeholder="https://..." />
                                </div>
                            </div>
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.clients.email_label', 'Email Address') }}</span></label>
                                <div class="join w-full">
                                    <div class="join-item bg-base-200 flex items-center px-4"><PhEnvelope class="w-4 h-4 opacity-40" /></div>
                                    <input type="email" v-model="form.email" class="input input-bordered join-item w-full bg-base-200/50 rounded-xl" placeholder="contact@example.com" />
                                </div>
                            </div>
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-bold opacity-50">{{ t('admin.clients.phone_label', 'Phone Number') }}</span></label>
                                <div class="join w-full">
                                    <div class="join-item bg-base-200 flex items-center px-4"><PhPhone class="w-4 h-4 opacity-40" /></div>
                                    <input type="text" v-model="form.phone" class="input input-bordered join-item w-full bg-base-200/50 rounded-xl" placeholder="+48 ..." />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Status & Logo Card -->
                <div class="card bg-base-100 shadow-sm border border-base-200 overflow-hidden rounded-2xl">
                    <div class="card-body gap-6 p-8">
                        <div class="form-control">
                            <label class="label cursor-pointer flex justify-between p-0">
                                <span class="label-text font-bold opacity-50">{{ t('admin.common.status_active', 'Active') }}</span>
                                <input type="checkbox" v-model="form.is_active" class="toggle toggle-primary toggle-sm" />
                            </label>
                        </div>

                        <div class="divider opacity-10 my-0"></div>

                        <div class="form-control w-full">
                            <label class="label pt-0"><span class="label-text font-bold opacity-50">{{ t('admin.clients.logo_label', 'Logo') }}</span></label>
                            <div class="aspect-video rounded-2xl bg-base-200/50 border-2 border-dashed border-base-300 flex flex-col items-center justify-center gap-3 cursor-pointer hover:border-primary/50 transition-all overflow-hidden relative group">
                                <img v-if="form.logo" :src="form.logo" class="w-full h-full object-contain p-4 transition-transform group-hover:scale-105" />
                                <div v-else class="flex flex-col items-center gap-2 opacity-30">
                                    <PhImage weight="bold" class="w-8 h-8" />
                                    <span class="text-[10px] font-black uppercase tracking-widest">{{ t('admin.common.upload', 'Upload') }}</span>
                                </div>
                                <div v-if="form.logo" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                     <button @click.stop="form.logo = ''" class="btn btn-circle btn-sm btn-error"><PhTrash class="w-4 h-4" /></button>
                                </div>
                                <input type="text" v-model="form.logo" class="absolute inset-0 opacity-0 cursor-pointer" />
                            </div>
                            <label class="label"><span class="label-text-alt opacity-40 text-[10px]">URL for now, media library integration later.</span></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
