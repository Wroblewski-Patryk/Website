<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { PhPlus, PhTrash, PhHouse, PhTranslate } from '@phosphor-icons/vue';
import { ref, markRaw } from 'vue';

const props = defineProps({
    translations: Object // Now paginated
});

const tableRef = ref(null);
const showAddModal = ref(false);

const addForm = useForm({
    group: 'frontend',
    key: '',
    text: { pl: '', en: '' }
});

const breadcrumbs = [
    { label: 'Dashboard', url: route('dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Translations' }
];

const columns = [
    { key: 'group_key', label: 'Group / Key', sortable: true },
    { key: 'text.pl', label: 'Polish (PL)', sortable: true },
    { key: 'text.en', label: 'English (EN)', sortable: true },
    { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
];

const submitAdd = () => {
    addForm.post(route('dashboard.settings.translations.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        }
    });
};

const updateTranslation = (item) => {
    router.put(route('dashboard.settings.translations.update', item.id), {
        text: item.text
    }, {
        preserveScroll: true
    });
};

const deleteTranslation = (item) => {
    router.delete(route('dashboard.settings.translations.destroy', item.id));
};
</script>

<template>
    <AdminLayout>
        <Head title="Translations" />



        <ResourceTable
            title="Translations"
            description="Manage multi-language strings for your website UI."
            :icon="markRaw(PhTranslate)"
            :breadcrumbs="breadcrumbs"
            :resources="translations"
            :columns="columns"
            persistence-key="translations"
            ref="tableRef"
            @delete-confirmed="deleteTranslation"
        >
            <template #header-actions>
                <button @click="showAddModal = true" class="btn btn-primary shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                    <PhPlus weight="bold" class="w-3 h-3 mr-1" /> Create
                </button>
            </template>

            <template #cell-group_key="{ item }">
                <div class="flex flex-col">
                    <span class="badge badge-xs badge-neutral opacity-50 mb-1 w-fit uppercase font-black px-2">{{ item.group }}</span>
                    <span class="font-bold font-mono text-sm group-hover:text-primary transition-colors">{{ item.key }}</span>
                </div>
            </template>

            <template #cell-text\.pl="{ item }">
                <input type="text" 
                       v-model="item.text.pl" 
                       @change="updateTranslation(item)"
                       class="input input-ghost input-sm w-full focus:bg-base-200 focus:border-primary/30 rounded-lg text-sm" />
            </template>

            <template #cell-text\.en="{ item }">
                <input type="text" 
                       v-model="item.text.en" 
                       @change="updateTranslation(item)"
                       class="input input-ghost input-sm w-full focus:bg-base-200 focus:border-primary/30 rounded-lg text-sm" />
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end order-last">
                    <button @click="tableRef?.openDeleteModal(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all opacity-0 group-hover:opacity-100">
                        <PhTrash weight="regular" class="w-4 h-4" />
                    </button>
                </div>
            </template>
        </ResourceTable>

        <!-- Add Modal -->
        <div v-if="showAddModal" class="modal modal-open">
            <div class="modal-box rounded-3xl border border-white/10 shadow-2xl p-8 bg-base-100">
                <h3 class="font-black text-2xl mb-6">Add New Translation Key</h3>
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold opacity-50">Group</span></label>
                        <select v-model="addForm.group" class="select select-bordered w-full rounded-xl bg-base-200/50">
                            <option value="frontend">Frontend</option>
                            <option value="admin">Admin</option>
                            <option value="emails">Emails</option>
                            <option value="default">Default</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold opacity-50">Key</span></label>
                        <input type="text" v-model="addForm.key" class="input input-bordered w-full font-mono rounded-xl bg-base-200/50" placeholder="my_awesome_key" />
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold opacity-50">Value (PL)</span></label>
                            <input type="text" v-model="addForm.text.pl" class="input input-bordered w-full rounded-xl bg-base-200/50" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold opacity-50">Value (EN)</span></label>
                            <input type="text" v-model="addForm.text.en" class="input input-bordered w-full rounded-xl bg-base-200/50" />
                        </div>
                    </div>
                </div>
                <div class="modal-action mt-10">
                    <button @click="showAddModal = false" class="btn btn-ghost rounded-xl px-6">Cancel</button>
                    <button @click="submitAdd" class="btn btn-primary rounded-xl px-8 shadow-lg shadow-primary/20" :disabled="addForm.processing">
                        Create Translation
                    </button>
                </div>
            </div>
            <div class="modal-backdrop bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
        </div>
    </AdminLayout>
</template>
