<template>
    <div class="space-y-6">
        <div v-if="showHeader" class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
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

        <slot name="after-header"></slot>

        <!-- Table Section -->
        <div class="bg-base-100 rounded-box shadow-sm border border-base-300 overflow-hidden">
            <div
                ref="tableScrollRef"
                class="overflow-x-auto"
                :class="{ 'overflow-y-auto': shouldVirtualize }"
                :style="tableContainerStyle"
                @scroll="handleVirtualScroll"
            >
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200/30">
                            <th v-if="selectionEnabled" class="w-12">
                                <label class="label cursor-pointer justify-center">
                                    <input
                                        type="checkbox"
                                        class="checkbox checkbox-sm"
                                        :checked="allVisibleSelected"
                                        @change="toggleSelectAllVisible($event.target.checked)"
                                    />
                                </label>
                            </th>
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
                        <tr v-if="shouldVirtualize && topSpacerHeight > 0" aria-hidden="true">
                            <td :colspan="columnCount" class="!p-0 border-0" :style="{ height: `${topSpacerHeight}px` }"></td>
                        </tr>

                        <tr v-for="(item, rowIndex) in renderedRows" :key="`${item.id}-${virtualStartIndex + rowIndex}`" class="hover:bg-base-200/30 transition-colors group">
                            <td v-if="selectionEnabled" class="py-4">
                                <label class="label cursor-pointer justify-center">
                                    <input
                                        type="checkbox"
                                        class="checkbox checkbox-sm"
                                        :checked="isSelected(item.id)"
                                        @change="toggleSelectRow(item.id, $event.target.checked)"
                                    />
                                </label>
                            </td>
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

                        <tr v-if="shouldVirtualize && bottomSpacerHeight > 0" aria-hidden="true">
                            <td :colspan="columnCount" class="!p-0 border-0" :style="{ height: `${bottomSpacerHeight}px` }"></td>
                        </tr>
                        
                        <!-- Empty State -->
                        <tr v-if="allRows.length === 0">
                            <td :colspan="columnCount" class="py-20 text-center">
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
            >
                <template #controls>
                    <div v-if="selectionEnabled && selectedCount > 0" class="flex items-center gap-2 flex-wrap">
                        <span class="text-[10px] font-bold uppercase tracking-widest opacity-50">
                            {{ selectedCount }} selected
                        </span>
                        <select v-model="selectedBulkAction" class="select select-bordered select-sm min-w-[140px]">
                            <option value="">Choose action</option>
                            <option v-for="action in resolvedBulkActions" :key="action.value" :value="action.value">
                                {{ action.label }}
                            </option>
                        </select>
                        <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            :disabled="isApplyingBulk || !selectedBulkAction"
                            @click="applyBulkAction"
                        >
                            {{ isApplyingBulk ? 'Applying...' : 'Apply' }}
                        </button>
                        <button type="button" class="btn btn-sm btn-ghost" @click="clearSelection">
                            Clear
                        </button>
                    </div>
                </template>
            </TablePagination>
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
import { applyBulkOptimistic, cloneRows } from './ResourceTable/bulkOptimistic';
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
    deleteRoute: String,
    // Optional: virtualization controls for very large datasets
    virtualizeThreshold: Number,
    virtualizedRowHeight: Number,
    virtualizedOverscan: Number,
    virtualizedViewportHeight: Number,
    bulkRoute: String,
    showHeader: {
        type: Boolean,
        default: true,
    },
    bulkActions: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['delete-confirmed', 'selection-change']);

const search = ref(new URLSearchParams(window.location.search).get('search') || '');
const sortField = ref(new URLSearchParams(window.location.search).get('sort') || '');
const sortDirection = ref(new URLSearchParams(window.location.search).get('direction') || '');
const tableScrollRef = ref(null);
const virtualStartIndex = ref(0);
const selectedIds = ref([]);
const selectedBulkAction = ref('');
const isApplyingBulk = ref(false);
const tableRows = ref([]);

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

const allRows = computed(() => tableRows.value);
const visibleRowIds = computed(() => allRows.value.map((row) => Number(row.id)).filter((id) => Number.isFinite(id)));
const selectedCount = computed(() => selectedIds.value.length);
const selectionEnabled = computed(() => Boolean(props.bulkRoute));
const allVisibleSelected = computed(() => {
    if (!visibleRowIds.value.length) return false;
    return visibleRowIds.value.every((id) => selectedIds.value.includes(id));
});
const resolvedBulkActions = computed(() => {
    if (props.bulkActions.length) return props.bulkActions;
    return [
        { value: 'publish', label: 'Publish' },
        { value: 'unpublish', label: 'Unpublish' },
        { value: 'archive', label: 'Archive' },
        { value: 'delete', label: 'Delete' },
    ];
});
const columnCount = computed(() => activeColumns.value.length + (selectionEnabled.value ? 1 : 0));
const virtualizeThreshold = computed(() => props.virtualizeThreshold ?? 120);
const virtualizedRowHeight = computed(() => props.virtualizedRowHeight ?? 72);
const virtualizedOverscan = computed(() => props.virtualizedOverscan ?? 8);
const virtualizedViewportHeight = computed(() => props.virtualizedViewportHeight ?? 680);

const shouldVirtualize = computed(() => allRows.value.length >= virtualizeThreshold.value);
const virtualVisibleRows = computed(() => {
    return Math.ceil(virtualizedViewportHeight.value / virtualizedRowHeight.value) + (virtualizedOverscan.value * 2);
});

const renderedRows = computed(() => {
    if (!shouldVirtualize.value) return allRows.value;
    return allRows.value.slice(virtualStartIndex.value, virtualStartIndex.value + virtualVisibleRows.value);
});

const virtualEndIndex = computed(() => {
    return Math.min(allRows.value.length, virtualStartIndex.value + renderedRows.value.length);
});

const topSpacerHeight = computed(() => {
    if (!shouldVirtualize.value) return 0;
    return virtualStartIndex.value * virtualizedRowHeight.value;
});

const bottomSpacerHeight = computed(() => {
    if (!shouldVirtualize.value) return 0;
    return Math.max(0, (allRows.value.length - virtualEndIndex.value) * virtualizedRowHeight.value);
});

const tableContainerStyle = computed(() => {
    if (!shouldVirtualize.value) return {};

    return {
        maxHeight: `${virtualizedViewportHeight.value}px`,
    };
});

const handleVirtualScroll = (event) => {
    if (!shouldVirtualize.value) return;

    const target = event?.target;
    const scrollTop = target?.scrollTop ?? 0;
    const nextStart = Math.max(0, Math.floor(scrollTop / virtualizedRowHeight.value) - virtualizedOverscan.value);

    if (nextStart !== virtualStartIndex.value) {
        virtualStartIndex.value = nextStart;
    }
};

watch(allRows, () => {
    virtualStartIndex.value = 0;
    selectedIds.value = [];
    emit('selection-change', selectedIds.value);
    selectedBulkAction.value = '';

    if (tableScrollRef.value) {
        tableScrollRef.value.scrollTop = 0;
    }
});

watch(() => props.resources?.data, (rows) => {
    tableRows.value = cloneRows(Array.isArray(rows) ? rows : []);
}, { immediate: true });



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

function isSelected(id) {
    return selectedIds.value.includes(Number(id));
}

function toggleSelectRow(id, checked) {
    const normalizedId = Number(id);
    if (!Number.isFinite(normalizedId)) return;

    if (checked) {
        if (!selectedIds.value.includes(normalizedId)) {
            selectedIds.value.push(normalizedId);
        }
    } else {
        selectedIds.value = selectedIds.value.filter((itemId) => itemId !== normalizedId);
    }

    emit('selection-change', selectedIds.value);
}

function toggleSelectAllVisible(checked) {
    if (!checked) {
        clearSelection();
        return;
    }

    selectedIds.value = [...visibleRowIds.value];
    emit('selection-change', selectedIds.value);
}

function clearSelection() {
    selectedIds.value = [];
    selectedBulkAction.value = '';
    emit('selection-change', selectedIds.value);
}

function applyBulkAction() {
    if (!selectionEnabled.value || !selectedBulkAction.value || selectedIds.value.length === 0 || isApplyingBulk.value) {
        return;
    }

    if (['archive', 'delete'].includes(selectedBulkAction.value)) {
        const confirmed = window.confirm(`Confirm bulk ${selectedBulkAction.value} for ${selectedIds.value.length} item(s)?`);
        if (!confirmed) {
            return;
        }
    }

    isApplyingBulk.value = true;
    const snapshotRows = cloneRows(allRows.value);
    tableRows.value = applyBulkOptimistic(allRows.value, selectedBulkAction.value, selectedIds.value);

    router.post(props.bulkRoute, {
        action: selectedBulkAction.value,
        ids: selectedIds.value,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            clearSelection();
        },
        onError: () => {
            tableRows.value = snapshotRows;
        },
        onFinish: () => {
            isApplyingBulk.value = false;
        },
    });
}
</script>

<style scoped>
.join-item:first-child { border-left: none; }
.join-item:last-child { border-right: none; }
</style>
