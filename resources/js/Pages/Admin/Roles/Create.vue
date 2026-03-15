<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { PhHouse, PhShield, PhArrowLeft, PhFloppyDisk } from '@phosphor-icons/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    permissions: Array
});

const { t } = useTranslations();

const form = useForm({
    name: '',
    permissions: []
});

const submit = () => {
    form.post(route('admin.roles.store'));
};

const togglePermission = (name) => {
    const index = form.permissions.indexOf(name);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(name);
    }
};
</script>

<template>
    <Head :title="t('admin.roles.create', 'Create Role')" />
    <AdminLayout>
        <div class="max-w-4xl mx-auto py-8 px-4">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.roles.index')" class="btn btn-ghost btn-square">
                        <PhArrowLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-black">{{ t('admin.roles.create', 'Create Role') }}</h1>
                        <p class="text-base-content/60 text-sm">{{ t('admin.roles.create_desc', 'Create a new role and assign its allowed actions.') }}</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
                    <div class="card-body gap-6">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text font-bold">{{ t('admin.common.name', 'Name') }}</span>
                            </label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                class="input input-bordered w-full focus:input-primary transition-all" 
                                :placeholder="t('admin.roles.name_placeholder', 'e.g. Moderator')"
                            />
                            <div v-if="form.errors.name" class="text-error text-xs mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="form-control w-full">
                            <label class="label mb-2">
                                <span class="label-text font-bold">{{ t('admin.roles.permissions', 'Permissions') }}</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div 
                                    v-for="permission in permissions" 
                                    :key="permission.id"
                                    class="p-4 rounded-xl border border-base-200 flex items-center justify-between cursor-pointer hover:border-primary/50 transition-all"
                                    :class="{'bg-primary/5 border-primary/30': form.permissions.includes(permission.name)}"
                                    @click="togglePermission(permission.name)"
                                >
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm">{{ permission.name }}</span>
                                        <span class="text-[10px] opacity-60 uppercase">{{ t('admin.permissions.' + permission.name.replace('-', '_'), permission.name) }}</span>
                                    </div>
                                    <input 
                                        type="checkbox" 
                                        :checked="form.permissions.includes(permission.name)"
                                        class="checkbox checkbox-primary"
                                        @click.stop="togglePermission(permission.name)"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <Link :href="route('admin.roles.index')" class="btn btn-ghost">{{ t('admin.common.cancel', 'Cancel') }}</Link>
                    <button 
                        type="submit" 
                        class="btn btn-primary px-8" 
                        :disabled="form.processing"
                    >
                        <PhFloppyDisk weight="fill" class="w-4 h-4 mr-2" />
                        {{ t('admin.common.create', 'Create Role') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
