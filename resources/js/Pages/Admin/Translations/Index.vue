<template>
    <AdminLayout>
        <Head title="Translations" />

        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                        <i class="fas fa-language text-primary"></i>
                        Translations
                    </h1>
                    <p class="text-sm opacity-50 mt-1">Manage multi-language strings for your website UI.</p>
                </div>
                <button @click="showAddModal = true" class="btn btn-primary rounded-full px-6 shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-2"></i> Add Key
                </button>
            </div>
        </template>

        <div class="p-0">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="stat bg-base-100 rounded-box border border-base-300 shadow-sm overflow-hidden relative">
                    <div class="stat-title opacity-40 uppercase text-[10px] font-bold tracking-widest text-base-content">Total Keys</div>
                    <div class="stat-value text-2xl font-black text-primary">{{ translations.length }}</div>
                    <i class="fas fa-key absolute -right-2 -bottom-2 text-4xl opacity-5"></i>
                </div>
                <div class="stat bg-base-100 rounded-box border border-base-300 shadow-sm overflow-hidden relative">
                    <div class="stat-title opacity-40 uppercase text-[10px] font-bold tracking-widest text-base-content">Active Locales</div>
                    <div class="stat-value text-2xl font-black text-secondary">2</div>
                    <i class="fas fa-globe absolute -right-2 -bottom-2 text-4xl opacity-5"></i>
                </div>
            </div>

            <div class="bg-base-100 rounded-box border border-base-300 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-lg w-full">
                        <thead>
                            <tr class="bg-base-200/50">
                                <th class="text-[10px] uppercase opacity-40 tracking-widest font-bold">Group / Key</th>
                                <th class="text-[10px] uppercase opacity-40 tracking-widest font-bold">Polish (PL)</th>
                                <th class="text-[10px] uppercase opacity-40 tracking-widest font-bold">English (EN)</th>
                                <th class="w-20"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in translations" :key="item.id" class="hover:bg-base-200/30 transition-colors group">
                                <td>
                                    <div class="flex flex-col">
                                        <span class="badge badge-xs badge-neutral opacity-50 mb-1">{{ item.group }}</span>
                                        <span class="font-bold font-mono text-sm">{{ item.key }}</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" 
                                           v-model="item.text.pl" 
                                           @change="updateTranslation(item)"
                                           class="input input-ghost w-full focus:bg-base-200 focus:border-primary/30" />
                                </td>
                                <td>
                                    <input type="text" 
                                           v-model="item.text.en" 
                                           @change="updateTranslation(item)"
                                           class="input input-ghost w-full focus:bg-base-200 focus:border-primary/30" />
                                </td>
                                <td class="text-right">
                                    <button @click="deleteTranslation(item)" class="btn btn-ghost btn-square btn-sm text-error opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="translations.length === 0">
                                <td colspan="4" class="text-center py-20 opacity-30 italic">No translations found. Start by adding a new key.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div v-if="showAddModal" class="modal modal-open">
            <div class="modal-box rounded-3xl border border-white/10 shadow-2xl p-8">
                <h3 class="font-black text-2xl mb-6">Add New Translation Key</h3>
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text opacity-60">Group</span></label>
                        <select v-model="addForm.group" class="select select-bordered w-full">
                            <option value="frontend">Frontend</option>
                            <option value="admin">Admin</option>
                            <option value="emails">Emails</option>
                            <option value="default">Default</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text opacity-60">Key</span></label>
                        <input type="text" v-model="addForm.key" class="input input-bordered w-full font-mono" placeholder="my_awesome_key" />
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div class="form-control">
                            <label class="label"><span class="label-text opacity-60">Value (PL)</span></label>
                            <input type="text" v-model="addForm.text.pl" class="input input-bordered w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text opacity-60">Value (EN)</span></label>
                            <input type="text" v-model="addForm.text.en" class="input input-bordered w-full" />
                        </div>
                    </div>
                </div>
                <div class="modal-action mt-10">
                    <button @click="showAddModal = false" class="btn btn-ghost rounded-full">Cancel</button>
                    <button @click="submitAdd" class="btn btn-primary rounded-full px-8 shadow-lg shadow-primary/20">Create Translation</button>
                </div>
            </div>
            <div class="modal-backdrop bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    translations: Array
});

const showAddModal = ref(false);

const addForm = useForm({
    group: 'frontend',
    key: '',
    text: { pl: '', en: '' }
});

const submitAdd = () => {
    addForm.post(route('admin.translations.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        }
    });
};

const updateTranslation = (item) => {
    router.put(route('admin.translations.update', item.id), {
        text: item.text
    }, {
        preserveScroll: true
    });
};

const deleteTranslation = (item) => {
    if (confirm('Are you sure you want to delete this key?')) {
        router.delete(route('admin.translations.destroy', item.id));
    }
};
</script>
