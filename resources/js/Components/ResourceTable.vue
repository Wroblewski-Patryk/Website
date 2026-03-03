<template>
    <div class="space-y-6">
        <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
            <ModuleHeader 
                :title="title" 
                :description="description" 
                :icon="icon"
                :breadcrumbs="breadcrumbs"
            >
                <template #title-append>
                    <div class="badge badge-sm badge-ghost opacity-50">{{ resources.total }}</div>
                </template>
                <template #actions>
                    <div class="flex items-center gap-3 w-full md:w-auto mt-4 md:mt-0 justify-end flex-wrap">
                        <!-- Search Input -->
                        <div class="relative group flex-1 md:flex-none">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base-content/30 group-focus-within:text-primary transition-colors z-10">
                                <i class="fas fa-search text-sm"></i>
                            </div>
                            <input 
                                v-model="search" 
                                type="text" 
                                :placeholder="searchPlaceholder || 'Search...'" 
                                class="input input-bordered w-full sm:w-[260px] pl-10 pr-12 bg-base-100 focus:bg-base-100 hover:border-base-300 focus:border-primary shadow-sm hover:shadow-md focus:shadow-md focus:ring-4 focus:ring-primary/10 transition-all font-medium"
                            />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none opacity-40 group-focus-within:opacity-0 transition-opacity duration-300">
                                <kbd class="kbd kbd-sm font-sans font-bold bg-base-200 border-none shadow-none text-[10px]">⌘K</kbd>
                            </div>
                        </div>

                        <!-- Column Toggle -->
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-square bg-base-100 border-base-200 shadow-sm hover:shadow-md hover:border-primary/40 text-base-content/60 hover:text-primary transition-all">
                                <i class="fas fa-sliders-h text-lg"></i>
                            </label>
                            <div tabindex="0" class="dropdown-content z-[20] p-4 shadow-2xl bg-base-100 rounded-box w-64 mt-3 border border-base-200">
                                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-base-200">
                                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                        <i class="fas fa-columns text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm leading-tight text-base-content">Layout</h3>
                                        <p class="text-[10px] opacity-50 uppercase tracking-widest font-bold">Toggle Columns</p>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label v-for="col in toggleableColumns" :key="col.key" class="label cursor-pointer hover:bg-base-200/50 px-3 py-2.5 rounded-xl transition-all border border-transparent hover:border-base-300 group/item">
                                        <span class="label-text font-semibold group-hover/item:text-primary transition-colors">{{ col.label }}</span>
                                        <input 
                                            type="checkbox" 
                                            :checked="visibleColumns.includes(col.key)" 
                                            @change="toggleColumn(col.key)"
                                            class="toggle toggle-primary toggle-sm" 
                                        />
                                    </label>
                                </div>
                            </div>
                        </div>

                        <slot name="header-actions"></slot>

                        <Link v-if="createRoute" :href="createRoute" class="btn btn-primary shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            <i class="fas fa-plus mr-1"></i> Create
                        </Link>
                    </div>
                </template>
            </ModuleHeader>
        </div>

        <!-- Table Section -->
        <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200/30">
                            <th v-for="col in activeColumns" :key="col.key" 
                                :class="[
                                    col.align === 'center' ? 'text-center' : (col.align === 'right' ? 'text-right' : ''),
                                    col.sortable !== false ? 'cursor-pointer hover:bg-base-200/50 transition-colors select-none' : ''
                                ]"
                                @click="col.sortable !== false ? toggleSort(col.key) : null"
                            >
                                <div class="flex items-center gap-2" :class="[
                                    col.align === 'center' ? 'justify-center' : (col.align === 'right' ? 'justify-end' : '')
                                ]">
                                    <span class="text-[10px] font-black uppercase tracking-widest opacity-40">{{ col.label }}</span>
                                    <span v-if="col.sortable !== false" class="opacity-20 flex flex-col text-[8px] leading-[4px]">
                                        <i class="fas fa-caret-up" :class="{ 'text-primary opacity-100': sortField === col.key && sortDirection === 'asc' }"></i>
                                        <i class="fas fa-caret-down" :class="{ 'text-primary opacity-100': sortField === col.key && sortDirection === 'desc' }"></i>
                                    </span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-300">
                        <tr v-for="item in resources.data" :key="item.id" class="hover:bg-base-200/30 transition-colors group">
                            <td v-for="col in activeColumns" :key="col.key"
                                :class="[
                                    col.align === 'center' ? 'text-center' : (col.align === 'right' ? 'text-right' : ''),
                                    'py-4'
                                ]"
                            >
                                <slot :name="`cell-${col.key}`" :item="item">
                                    <span v-if="col.key === 'id'" class="text-xs font-mono opacity-40">#{{ item.id }}</span>
                                    <span v-else-if="col.key === 'created_at'" class="text-xs opacity-60">
                                        {{ new Date(item[col.key]).toLocaleDateString() }}
                                    </span>
                                    <span v-else>{{ getFieldValue(item, col.key) }}</span>
                                </slot>
                            </td>
                        </tr>
                        
                        <!-- Empty State -->
                        <tr v-if="resources.data.length === 0">
                            <td :colspan="activeColumns.length" class="py-20 text-center">
                                <div class="flex flex-col items-center opacity-20">
                                    <i class="fas fa-folder-open text-6xl mb-4"></i>
                                    <p class="text-xs font-black uppercase tracking-widest">No matching records found</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer / Pagination -->
            <div class="p-4 bg-base-200/30 border-t border-base-300 flex items-center justify-between">
                <div class="text-xs opacity-40 font-medium">
                    Showing {{ resources.from || 0 }} to {{ resources.to || 0 }} of {{ resources.total }} entries
                </div>
                
                <div class="join shadow-sm rounded-xl overflow-hidden border border-white/5" v-if="resources.links.length > 3">
                    <Link 
                        v-for="(link, k) in resources.links" 
                        :key="k" 
                        :href="link.url || '#'" 
                        class="join-item btn btn-xs h-10 px-4 min-w-[40px] bg-base-100 border-base-300 hover:bg-base-200 transition-all font-bold"
                        :class="{'btn-active bg-primary/10 text-primary border-primary/20': link.active, 'btn-disabled opacity-30': !link.url}"
                        v-html="link.label" 
                    />
                </div>
            </div>
        </div>

        <!-- Universal Delete Modal -->
        <div v-if="showDeleteModal" class="modal modal-open z-[100]">
            <div class="modal-box rounded-3xl border border-white/10 shadow-2xl p-8 bg-base-100 max-w-sm text-center">
                <div class="w-16 h-16 bg-error/10 text-error rounded-full flex items-center justify-center mx-auto mb-6 text-2xl animate-bounce">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="font-black text-2xl mb-2 text-base-content">Are you sure?</h3>
                <p class="text-sm opacity-50 mb-8">This action cannot be undone. All data associated with this record will be permanently removed.</p>
                
                <div class="flex flex-col gap-2">
                    <button @click="confirmDelete" class="btn btn-error rounded-xl w-full shadow-lg shadow-error/20" :disabled="deleting">
                        <span v-if="deleting" class="loading loading-spinner loading-xs mr-2"></span>
                        Yes, Delete Permanently
                    </button>
                    <button @click="closeDeleteModal" class="btn btn-ghost rounded-xl w-full">Cancel</button>
                </div>
            </div>
            <div class="modal-backdrop bg-black/60 backdrop-blur-sm" @click="closeDeleteModal"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';

// Simple debounce implementation to avoid lodash dependency
function debounce(fn, delay) {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
}

const props = defineProps({
    title: String,
    description: String,
    icon: String,
    resources: Object,
    columns: Array,
    createRoute: String,
    createLabel: String,
    searchPlaceholder: String,
    routeName: String,
    breadcrumbs: Array,
    // Optional: persistence key for visible columns
    persistenceKey: String,
    // Optional: automatically handle deletion
    deleteRoute: String
});

const emit = defineEmits(['delete-confirmed']);

const search = ref(new URLSearchParams(window.location.search).get('search') || '');
const sortField = ref(new URLSearchParams(window.location.search).get('sort') || '');
const sortDirection = ref(new URLSearchParams(window.location.search).get('direction') || '');

// Delete Modal State
const showDeleteModal = ref(false);
const deletingItem = ref(null);
const deleting = ref(false);

const openDeleteModal = (item) => {
    deletingItem.value = item;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    if (deleting.value) return;
    showDeleteModal.value = false;
    deletingItem.value = null;
};

const confirmDelete = () => {
    if (!deletingItem.value) return;
    
    deleting.value = true;
    
    // If deleteRoute prop is provided, handle it automatically
    if (props.deleteRoute) {
        const url = props.deleteRoute.replace(':id', deletingItem.value.id);
        router.delete(url, {
            onSuccess: () => {
                deleting.value = false;
                closeDeleteModal();
            },
            onFinish: () => {
                deleting.value = false;
            },
            preserveScroll: true
        });
    } else {
        // Otherwise emit event for parent to handle
        emit('delete-confirmed', deletingItem.value);
        // We close it after a timeout if parent doesn't handle it or just as fallback
        setTimeout(() => {
            if (showDeleteModal.value) {
                deleting.value = false;
                closeDeleteModal();
            }
        }, 1000);
    }
};

// Expose openDeleteModal for slot usage
defineExpose({ openDeleteModal });

// Column Visibility State
const visibleColumns = ref([]);

// Initialize visible columns
onMounted(() => {
    const saved = props.persistenceKey ? localStorage.getItem(`resource-table-columns-${props.persistenceKey}`) : null;
    if (saved) {
        try {
            visibleColumns.value = JSON.parse(saved);
        } catch (e) {
            visibleColumns.value = props.columns.filter(col => !col.optional).map(col => col.key);
        }
    } else {
        // Default: show columns that are not marked as optional: true
        visibleColumns.value = props.columns
            .filter(col => !col.optional)
            .map(col => col.key);
    }
});

const toggleableColumns = computed(() => {
    return props.columns.filter(col => col.key !== 'actions' && col.key !== 'id');
});

const activeColumns = computed(() => {
    return props.columns.filter(col => 
        visibleColumns.value.includes(col.key) || 
        col.key === 'actions' || 
        col.key === 'id'
    );
});

function toggleColumn(key) {
    if (visibleColumns.value.includes(key)) {
        visibleColumns.value = visibleColumns.value.filter(k => k !== key);
    } else {
        visibleColumns.value.push(key);
    }
    
    if (props.persistenceKey) {
        localStorage.setItem(`resource-table-columns-${props.persistenceKey}`, JSON.stringify(visibleColumns.value));
    }
}

const updateFilters = debounce(() => {
    router.get(window.location.pathname, {
        search: search.value,
        sort: sortField.value,
        direction: sortDirection.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}, 300);

watch(search, () => {
    updateFilters();
});

function toggleSort(field) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    updateFilters();
}

function getFieldValue(item, key) {
    // Handle nested keys or translatable objects
    if (key.includes('.')) {
        return key.split('.').reduce((o, i) => o?.[i], item);
    }
    
    const val = item[key];
    if (val && typeof val === 'object' && (val.pl || val.en)) {
        return val.pl || val.en;
    }
    
    return val;
}
</script>

<style scoped>
.join-item:first-child { border-left: none; }
.join-item:last-child { border-right: none; }
</style>
