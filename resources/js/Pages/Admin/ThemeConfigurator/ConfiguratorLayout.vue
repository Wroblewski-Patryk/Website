<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';

const props = defineProps({
    title: String,
    description: String,
    breadcrumbs: Array,
});

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
    form.post('/admin/theme-configurator', {
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
        <template #header>
            <ModuleHeader 
                :title="title" 
                :description="description" 
                :breadcrumbs="breadcrumbs"
                icon="fas fa-paint-roller">
                <template #actions>
                    <span v-if="savedMessage" class="text-success text-sm font-semibold flex items-center gap-1 mr-4"><i class="fas fa-check-circle"></i> Saved</span>
                    <button @click="saveConfig" class="btn btn-primary shadow-lg shadow-primary/20" :class="{'loading': isSaving}">
                        <i class="fas fa-save" v-if="!isSaving"></i> 
                        Save
                    </button>
                </template>
            </ModuleHeader>
        </template>

        <div class="space-y-6 mt-6">
            <!-- Content Slot where specific forms will be injected -->
            <slot :form="form"></slot>
        </div>
    </AdminLayout>
</template>
