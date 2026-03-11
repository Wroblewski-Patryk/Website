<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ResourceTable from '@/Components/ResourceTable.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { PhPlus, PhTrash, PhHouse, PhTranslate, PhPencilSimple } from '@phosphor-icons/vue';
import { ref, markRaw, computed } from 'vue';

const props = defineProps({
    translations: Object // Now paginated
});

const page = usePage();
const languages = computed(() => page.props.languages || []);

const tableRef = ref(null);
const showAddModal = ref(false);

const addForm = useForm({
    group: 'frontend',
    key: '',
    text: languages.value.reduce((acc, lang) => {
        acc[lang.code] = '';
        return acc;
    }, {})
});

const breadcrumbs = [
    { label: 'Dashboard', url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Translations' }
];

const columns = computed(() => {
    const cols = [
        { key: 'group', label: 'Group', sortable: true },
        { key: 'key', label: 'Key', sortable: true },
        ...languages.value.map(lang => ({
            key: `text.${lang.code}`,
            label: `${lang.name} (${lang.code.toUpperCase()})`,
            sortable: true
        })),
        { key: 'actions', label: 'Actions', sortable: false, align: 'right' }
    ];
    return cols;
});

const submitAdd = () => {
    addForm.post(route('admin.translations.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        }
    });
};

const showEditModal = ref(false);
const editForm = useForm({
    id: null,
    group: '',
    key: '',
    text: {}
});

const openEditModal = (item) => {
    editForm.id = item.id;
    editForm.group = item.group;
    editForm.key = item.key;
    editForm.text = languages.value.reduce((acc, lang) => {
        acc[lang.code] = item.text[lang.code] || '';
        return acc;
    }, {});
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route('admin.translations.update', editForm.id), {
        onSuccess: () => showEditModal.value = false
    });
};

const deleteTranslation = (item) => {
    router.delete(route('admin.translations.destroy', item.id));
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
            persistence-key="translations-v2"
            ref="tableRef"
            @delete-confirmed="deleteTranslation"
        >
            <template #header-actions>
                <button @click="showAddModal = true" class="btn btn-primary shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                    <PhPlus weight="bold" class="w-3 h-3 mr-1" /> Create
                </button>
            </template>

            <template #cell-group="{ item }">
                <span class="badge badge-xs badge-neutral opacity-50 uppercase font-black px-2">{{ item.group }}</span>
            </template>
            <template #cell-key="{ item }">
                <span class="font-bold font-mono text-sm text-primary/80">{{ item.key }}</span>
            </template>

            <template v-for="lang in languages" :key="lang.code" #[`cell-text.${lang.code}`]="{ item }">
                <div class="text-sm truncate max-w-xs opacity-80" :title="item.text[lang.code]">
                    {{ item.text[lang.code] || '-' }}
                </div>
            </template>

            <template #cell-actions="{ item }">
                <div class="flex justify-end gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-all">
                    <button @click="openEditModal(item)" class="btn btn-sm btn-ghost btn-square hover:bg-primary/10 hover:text-primary transition-all" title="Edytuj">
                        <PhPencilSimple class="w-4 h-4" weight="bold" />
                    </button>
                    <button @click="tableRef?.openDeleteModal(item)" class="btn btn-sm btn-ghost btn-square hover:bg-error/10 hover:text-error transition-all" title="Usuń">
                        <PhTrash weight="bold" class="w-4 h-4" />
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div v-for="lang in languages" :key="lang.code" class="form-control">
                            <label class="label"><span class="label-text font-bold opacity-50">Value ({{ lang.code.toUpperCase() }})</span></label>
                            <input type="text" v-model="addForm.text[lang.code]" class="input input-bordered w-full rounded-xl bg-base-200/50" />
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

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="modal modal-open">
            <div class="modal-box rounded-3xl border border-white/10 shadow-2xl p-8 bg-base-100">
                <h3 class="font-black text-2xl mb-6">Edit Translation</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 opacity-70">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">Group</span></label>
                            <input type="text" :value="editForm.group" readonly class="input input-sm input-bordered w-full rounded-xl bg-base-200/50" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">Key</span></label>
                            <input type="text" :value="editForm.key" readonly class="input input-sm input-bordered w-full font-mono rounded-xl bg-base-200/50" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mt-6">
                        <div v-for="lang in languages" :key="lang.code" class="form-control">
                            <label class="label"><span class="label-text font-bold opacity-50">Value ({{ lang.code.toUpperCase() }})</span></label>
                            <textarea v-model="editForm.text[lang.code]" class="textarea textarea-bordered w-full rounded-xl bg-base-200/50 focus:bg-base-100 focus:border-primary" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-action mt-10">
                    <button @click="showEditModal = false" class="btn btn-ghost rounded-xl px-6">Cancel</button>
                    <button @click="submitEdit" class="btn btn-primary rounded-xl px-8 shadow-lg shadow-primary/20" :disabled="editForm.processing">
                        Save Changes
                    </button>
                </div>
            </div>
            <div class="modal-backdrop bg-black/60 backdrop-blur-sm" @click="showEditModal = false"></div>
        </div>
    </AdminLayout>
</template>
