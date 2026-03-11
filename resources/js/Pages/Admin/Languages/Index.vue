<script setup>
import { ref, markRaw } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { PhPencilSimple, PhPlusCircle, PhGlobe, PhPlus, PhTrash, PhHouse } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';

const props = defineProps({
    languages: Object // Now paginated
});

const tableRef = ref(null);
const isCreating = ref(false);
const editingLanguage = ref(null);

const form = useForm({
    code: '',
    name: '',
    is_default: false,
    is_active: true
});

const breadcrumbs = [
    { label: 'Dashboard', url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Languages' }
];

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'code', label: 'Code', sortable: true },
    { key: 'is_active', label: 'Status', sortable: true },
    { key: 'is_default', label: 'Default', sortable: true },
    { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
];

function openCreate() {
    form.reset();
    editingLanguage.value = null;
    isCreating.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function openEdit(lang) {
    editingLanguage.value = lang;
    form.code = lang.code;
    form.name = lang.name;
    form.is_default = lang.is_default;
    form.is_active = lang.is_active;
    isCreating.value = true;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function submit() {
    if (editingLanguage.value) {
        form.put(route('admin.languages.update', editingLanguage.value.id), {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('admin.languages.store'), {
            onSuccess: () => {
                isCreating.value = false;
                form.reset();
            }
        });
    }
}

function deleteLanguage(item) {
    router.delete(route('admin.languages.destroy', item.id));
}
</script>

<template>
    <Head title="Language Management" />
    <AdminLayout>
        <div v-if="isCreating" class="mb-8 p-8 bg-base-100 rounded-box border border-primary/20 shadow-xl max-w-2xl mx-auto">
            <h3 class="text-xl font-black text-primary mb-6 flex items-center gap-2">
                <PhPencilSimple v-if="editingLanguage" weight="regular" class="w-6 h-6" />
                <PhPlusCircle v-else weight="regular" class="w-6 h-6" />
                {{ editingLanguage ? 'Edit Language' : 'Add New Language' }}
            </h3>
            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-bold opacity-50">Language Code (e.g. en, pl)</span></label>
                    <input type="text" v-model="form.code" class="input input-bordered w-full rounded-xl bg-base-200/50" :disabled="editingLanguage" required />
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-bold opacity-50">Display Name (e.g. English)</span></label>
                    <input type="text" v-model="form.name" class="input input-bordered w-full rounded-xl bg-base-200/50" required />
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-4 p-0">
                        <input type="checkbox" v-model="form.is_default" class="checkbox checkbox-primary rounded-lg" />
                        <span class="label-text font-bold">Set as Default Language</span>
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-4 p-0">
                        <input type="checkbox" v-model="form.is_active" class="checkbox checkbox-secondary rounded-lg" />
                        <span class="label-text font-bold">Language is Active</span>
                    </label>
                </div>

                <div class="md:col-span-2 flex justify-end gap-3 pt-4 border-t border-base-200">
                    <button type="button" @click="isCreating = false" class="btn btn-ghost rounded-xl px-6">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-xl px-8 shadow-lg shadow-primary/20" :disabled="form.processing">
                        {{ editingLanguage ? 'Update Language' : 'Create Language' }}
                    </button>
                </div>
            </form>
        </div>

        <ResourceTable
            title="Languages"
            description="Manage available locales and site internationalization."
            :icon="markRaw(PhGlobe)"
            :breadcrumbs="breadcrumbs"
            :resources="languages"
            :columns="columns"
            persistence-key="languages"
            ref="tableRef"
            @create="openCreate"
            @delete-confirmed="deleteLanguage"
        >
            <template #header-actions>
                <button @click="openCreate" class="btn btn-primary shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                    <PhPlus weight="bold" class="w-3 h-3 mr-1" /> Create
                </button>
            </template>

            <template #cell-code="{ item }">
                <span class="text-xs font-mono bg-base-200 p-1 px-2 rounded-lg opacity-70">
                    {{ item.code }}
                </span>
            </template>

            <template #cell-is_active="{ item }">
                <div class="badge font-bold py-3 px-4 border-none shadow-sm transition-all" 
                     :class="item.is_active ? 'bg-success/10 text-success' : 'bg-base-200 text-base-content/40'">
                    <span class="w-2 h-2 rounded-full mr-2" :class="item.is_active ? 'bg-success' : 'bg-base-content/20'"></span>
                    {{ item.is_active ? 'Active' : 'Inactive' }}
                </div>
            </template>

            <template #cell-is_default="{ item }">
                <div v-if="item.is_default" class="badge badge-primary font-bold py-3 px-4 shadow-sm">
                    Default
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-2">
                    <button @click="openEdit(item)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all">
                        <PhPencilSimple weight="regular" class="w-4 h-4" />
                    </button>
                    <button @click="tableRef?.openDeleteModal(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all" :disabled="item.is_default">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>
    </AdminLayout>
</template>
