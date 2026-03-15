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
                    <TableToolbar 
                        v-model:search="search"
                        v-model:visibleColumns="visibleColumns"
                        :searchPlaceholder="searchPlaceholder"
                        :toggleableColumns="toggleableColumns"
                        :createRoute="createRoute"
                        :createLabel="createLabel"
                        :persistenceKey="persistenceKey"
                    >
                        <template #actions>
                            <slot name="header-actions"></slot>
                        </template>
                    </TableToolbar>
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
                                        <PhCaretUp weight="bold" :class="{ 'text-primary opacity-100': sortField === col.key && sortDirection === 'asc' }" />
                                        <PhCaretDown weight="bold" :class="{ 'text-primary opacity-100': sortField === col.key && sortDirection === 'desc' }" />
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
                                    <span v-else-if="['created_at', 'updated_at', 'published_at'].includes(col.key)" class="text-xs opacity-60">
                                        {{ item[col.key] ? formatDate(item[col.key]) : '-' }}
                                    </span>
                                    <span v-else>{{ getFieldValue(item, col.key) }}</span>
                                </slot>
                            </td>
                        </tr>
                        
                        <!-- Empty State -->
                        <tr v-if="resources.data.length === 0">
                            <td :colspan="activeColumns.length" class="py-20 text-center">
                                <div class="flex flex-col items-center opacity-20">
                                    <PhFolderOpen class="text-6xl mb-4" />
                                    <p class="text-xs font-black uppercase tracking-widest">{{ t('admin.common.no_records', 'No matching records found') }}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer / Pagination -->
            <TablePagination 
                :resources="resources" 
                :filters="{ search, sort: sortField, direction: sortDirection }" 
            />
        </div>

        <!-- Universal Delete Modal -->
        <DeleteModal 
            :show="showDeleteModal"
            :deleting="deleting"
            @confirm="confirmDelete"
            @close="closeDeleteModal"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { PhCaretUp, PhCaretDown, PhFolderOpen } from '@phosphor-icons/vue';
import ModuleHeader from '@/features/admin/shared/components/ModuleHeader.vue';
import TableToolbar from './ResourceTable/TableToolbar.vue';
import TablePagination from './ResourceTable/TablePagination.vue';
import DeleteModal from './ResourceTable/DeleteModal.vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const { t } = useTranslations();
const { formatDate } = useFormatter();



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
    icon: [String, Object, Function],
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
    // Handle nested keys
    if (key.includes('.')) {
        return key.split('.').reduce((o, i) => o?.[i], item);
    }
    
    const val = item[key];
    
    // Use the translate helper for objects (handles pl/en and fallbacks)
    if (val && typeof val === 'object') {
        return t(val);
    }
    
    return val;
}
</script>

<style scoped>
.join-item:first-child { border-left: none; }
.join-item:last-child { border-right: none; }
</style>
