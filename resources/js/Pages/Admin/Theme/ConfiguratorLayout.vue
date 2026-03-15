<script setup>
import { ref, markRaw } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/features/admin/shared/components/ModuleHeader.vue';
import { PhCheckCircle, PhFloppyDisk, PhPaintRoller } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    title: String,
    description: String,
    breadcrumbs: Array,
});

const { t } = useTranslations();
const page = usePage();

// We initialize the form with global data so it can be saved from any tab
const initialConfig = page.props.themeConfig || page.props.theme_config || {
    globals: {
        colors: {},
        fonts: {},
        borderRadius: {},
        layout: {}
    },
    block_defaults: {}
};

const form = useForm({
    globals: initialConfig.globals || {},
    block_defaults: initialConfig.block_defaults || {}
});

const isSaving = ref(false);
const savedMessage = ref(false);

const saveConfig = () => {
    isSaving.value = true;
    form.post(route('admin.theme.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isSaving.value = false;
            savedMessage.value = true;
            setTimeout(() => { savedMessage.value = false; }, 3000);
            
            // Re-mount the custom styles dynamically to show changes without reload
            window.dispatchEvent(new CustomEvent('theme-config-updated', { detail: form.globals }));
        },
        onError: () => {
            isSaving.value = false;
        }
    });
};

</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <ModuleHeader 
                    :title="title" 
                    :description="description" 
                    :icon="markRaw(PhPaintRoller)"
                    :breadcrumbs="breadcrumbs">
                    <template #actions>
                        <span v-if="savedMessage" class="text-success text-sm font-semibold flex items-center gap-1 mr-4">
                            <PhCheckCircle weight="regular" class="w-4 h-4" /> 
                            {{ t('admin.theme.saved', 'Saved') }}
                        </span>
                        <button @click="saveConfig" class="btn btn-primary shadow-lg shadow-primary/20" :disabled="isSaving">
                            <span v-if="isSaving" class="loading loading-spinner loading-xs"></span>
                            <PhFloppyDisk weight="regular" class="w-4 h-4" v-else /> 
                            {{ t('admin.theme.save_btn', 'Save') }}
                        </button>
                    </template>
                </ModuleHeader>
            </div>

            <div class="space-y-6 mt-6">
            <!-- Content Slot where specific forms will be injected -->
            <slot :form="form"></slot>
            </div>
        </div>
    </AdminLayout>
</template>
