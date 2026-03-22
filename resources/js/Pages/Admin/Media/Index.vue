<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, markRaw, computed, watch } from 'vue';
import { 
    PhUpload, PhHouse, PhImageSquare, PhFolderPlus 
} from '@phosphor-icons/vue';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/features/admin/shared/components/ModuleHeader.vue';
import { useToastStore } from '@/Stores/useToastStore';
import { useTranslations } from '@/Composables/useTranslations';

// Media Components
import Uploader from '@/features/admin/media/components/Uploader.vue';
import Toolbar from '@/features/admin/media/components/Toolbar.vue';
import Browser from '@/features/admin/media/components/Browser.vue';
import PreviewModal from '@/features/admin/media/components/PreviewModal.vue';
import MoveModal from '@/features/admin/media/components/MoveModal.vue';
import FolderModal from '@/features/admin/media/components/FolderModal.vue';

const props = defineProps(['media', 'folders', 'subfolders', 'currentFolder', 'filters']);
const { t } = useTranslations();
const toastStore = useToastStore();
const uploaderRef = ref(null);

// Dynamic Breadcrumbs
const breadcrumbs = computed(() => {
    const crumbs = [
        { label: t('admin.dashboard.title', 'Pulpit'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
        { label: t('admin.media.title', 'Biblioteka mediów'), url: route('admin.media.index') }
    ];

    if (props.currentFolder) {
        // Simple mock for hierarchy if folders flat, or recursive find
        const parents = [];
        let curr = props.currentFolder;
        while (curr) {
            parents.unshift(curr);
            curr = props.folders.find(f => f.id === curr.parent_id);
        }
        parents.forEach(p => {
            crumbs.push({ label: p.name, url: route('admin.media.index', { folder_id: p.id }) });
        });
    }

    return crumbs;
});

// Forms
const uploadForm = useForm({
    files: [],
    alt_text: '',
    folder_id: props.currentFolder?.id || null
});

const folderForm = useForm({
    name: '',
    parent_id: null
});

// UI State
const isUploaderOpen = ref(false);
const isFolderModalOpen = ref(false);
const isMoveModalOpen = ref(false);
const editingFolder = ref(null); // When renaming

const sortBy = ref(props.filters?.sort || 'created_at');
const sortDirection = ref(props.filters?.direction || 'desc');
const viewMode = ref('grid'); 
const viewType = ref(props.filters?.view_type || 'nested');
const selectedMediaForPreview = ref(null);

// Bulk Selection
const selectedMediaIds = ref([]);
const selectedFolderIds = ref([]);
const targetFolderId = ref(null);

const isAnySelected = computed(() => selectedMediaIds.value.length > 0 || selectedFolderIds.value.length > 0);
const allCurrentSelected = computed(() => {
    const currentMediaIds = props.media.data.map(m => m.id);
    const currentFolderIds = viewType.value === 'nested' ? props.subfolders.map(f => f.id) : [];
    
    if (currentMediaIds.length === 0 && currentFolderIds.length === 0) return false;
    
    return currentMediaIds.every(id => selectedMediaIds.value.includes(id)) && 
           currentFolderIds.every(id => selectedFolderIds.value.includes(id));
});

const currentFolderId = computed(() => props.currentFolder?.id || null);

// Watch filters - Auto update
watch([sortBy, sortDirection, currentFolderId, viewType], () => {
    fetchMedia();
});

const fetchMedia = () => {
    router.reload({
        data: {
            folder_id: viewType.value === 'flat' ? null : currentFolderId.value,
            sort: sortBy.value,
            direction: sortDirection.value,
            view_type: viewType.value
        },
        only: ['media', 'subfolders', 'breadcrumbs', 'currentFolder'],
        preserveState: true,
        onSuccess: () => {
            selectedMediaIds.value = [];
            selectedFolderIds.value = [];
        }
    });
};

// Navigation & Selection Logic
function handleFolderClick(id) {
    selectedMediaIds.value = [];
    selectedFolderIds.value = [];
    router.get(route('admin.media.index'), { folder_id: id });
}

function toggleSelection(type, id) {
    const arr = type === 'media' ? selectedMediaIds : selectedFolderIds;
    const index = arr.value.indexOf(id);
    if (index === -1) arr.value.push(id);
    else arr.value.splice(index, 1);
}

function toggleSelectAll() {
    if (allCurrentSelected.value) {
        selectedMediaIds.value = [];
        selectedFolderIds.value = [];
    } else {
        selectedMediaIds.value = props.media.data.map(m => m.id);
        selectedFolderIds.value = viewType.value === 'nested' ? props.subfolders.map(f => f.id) : [];
    }
}

// Actions
function handleUpload(payload) {
    uploadForm.files = payload.files;
    uploadForm.alt_text = payload.alt_text;
    uploadForm.folder_id = payload.folder_id;

    uploadForm.post(route('admin.media.store'), {
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset();
            uploaderRef.value?.clearFiles();
            isUploaderOpen.value = false;
            toastStore.success(t('admin.media.upload_success', 'Pliki zostały przesłane! 🚀'));
        },
        onError: (errors) => Object.values(errors).forEach(err => toastStore.error(err))
    });
}

function handleFolderSubmit(name) {
    if (editingFolder.value) {
        router.patch(route('admin.media.folders.update', editingFolder.value.id), { name }, {
            onSuccess: () => {
                isFolderModalOpen.value = false;
                editingFolder.value = null;
                toastStore.success(t('admin.media.folder_renamed', 'Nazwa folderu została zmieniona.'));
            }
        });
    } else {
        folderForm.name = name;
        folderForm.parent_id = props.currentFolder?.id || null;
        folderForm.post(route('admin.media.folders.store'), {
            onSuccess: () => {
                isFolderModalOpen.value = false;
                toastStore.success(t('admin.media.folder_created', 'Folder został utworzony.'));
            }
        });
    }
}

function openMoveModal(type = null, id = null) {
    if (type && id) {
        if (type === 'media') {
            selectedMediaIds.value = [id];
            selectedFolderIds.value = [];
        } else {
            selectedFolderIds.value = [id];
            selectedMediaIds.value = [];
        }
    }
    isMoveModalOpen.value = true;
}

function bulkAction(action) {
    if (action === 'delete' && !confirm(t('admin.common.confirm_delete_permanent', 'Czy na pewno? Tej operacji nie można cofnąć.'))) return;
    
    router.post(route('admin.media.bulk-action'), {
        action,
        media_ids: selectedMediaIds.value,
        folder_ids: selectedFolderIds.value,
        target_folder_id: targetFolderId.value
    }, {
        onSuccess: () => {
            selectedMediaIds.value = [];
            selectedFolderIds.value = [];
            isMoveModalOpen.value = false;
            toastStore.success(t('admin.media.action_processed', 'Akcja: {action} została wykonana.', { action }));
        }
    });
}

function updateMedia(id, data) {
    router.patch(route('admin.media.update', id), data, { preserveScroll: true });
}

function deleteSingle(type, id) {
    if (type === 'media') {
        router.delete(route('admin.media.destroy', id), { onSuccess: () => toastStore.success(t('admin.common.deleted', 'Usunięto.')) });
    } else {
        router.delete(route('admin.media.folders.destroy', id), { onSuccess: () => toastStore.success(t('admin.common.deleted', 'Usunięto.')) });
    }
}

function copyUrl(url) {
    navigator.clipboard.writeText(url);
    toastStore.info(t('admin.media.url_copied', 'URL skopiowany do schowka!'));
}

// Helpers
function formatSize(bytes) {
    if (!bytes) return '-';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

const getFileIcon = (mime) => {
    // Icons imported in separate components, but helper still useful for preview
    return PhImageSquare; // Placeholder logic, specific icons are in components
};

const isImage = (mime) => mime?.startsWith('image/');

// Folder Tree for Modals
const folderTree = computed(() => {
    const buildTree = (parentId = null) => {
        return props.folders
            .filter(f => f.parent_id === parentId)
            .map(f => ({ ...f, children: buildTree(f.id) }));
    };
    return buildTree(null);
});
</script>

<template>

    <AdminLayout>
        <div class="space-y-8 animate-in fade-in duration-500">
            <!-- Header Section -->
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <ModuleHeader 
                    :title="t('admin.media.title', 'Biblioteka mediów')" 
                    :description="t('admin.media.desc', 'Zarządzaj swoimi plikami i folderami.')" 
                    :breadcrumbs="breadcrumbs"
                    :icon="markRaw(PhImageSquare)">
                    <template #actions>
                        <div class="flex items-center gap-3">
                            <button @click="editingFolder = null; isFolderModalOpen = true" class="btn btn-ghost gap-2 font-bold group">
                                <PhFolderPlus weight="regular" class="w-5 h-5 group-hover:scale-110 transition-transform" />
                                {{ t('admin.media.new_folder', 'Nowy folder') }}
                            </button>
                            <button @click="isUploaderOpen = !isUploaderOpen" class="btn btn-primary px-6 gap-2 font-black shadow-lg shadow-primary/20">
                                <PhUpload weight="bold" class="w-5 h-5" />
                                {{ t('admin.media.upload_files', 'Prześlij pliki') }}
                            </button>
                        </div>
                    </template>
                </ModuleHeader>
            </div>

            <!-- Main Content Area -->
            <div class="flex flex-col gap-8">
                <!-- Dropzone / Uploader -->
                <Uploader 
                    ref="uploaderRef"
                    :is-open="isUploaderOpen"
                    :processing="uploadForm.processing"
                    :current-folder-id="currentFolder?.id"
                    @close="isUploaderOpen = false"
                    @upload="handleUpload"
                />

                <!-- Toolbar & Bulk Actions -->
                <Toolbar 
                    v-model:sort="sortBy"
                    v-model:direction="sortDirection"
                    v-model:view-mode="viewMode"
                    v-model:view-type="viewType"
                    :selected-count="selectedMediaIds.length + selectedFolderIds.length"
                    :all-selected="allCurrentSelected"
                    @toggle-all="toggleSelectAll"
                    @bulk-move="openMoveModal()"
                    @bulk-delete="bulkAction('delete')"
                />

                <!-- Media Browser Area -->
                <Browser 
                    :view-mode="viewMode"
                    :subfolders="subfolders"
                    :media="media.data"
                    :selected-folder-ids="selectedFolderIds"
                    :selected-media-ids="selectedMediaIds"
                    @folder-click="handleFolderClick"
                    @selection-toggle="toggleSelection"
                    @preview="selectedMediaForPreview = $event"
                    @move="openMoveModal"
                    @delete="deleteSingle"
                    @copy-link="copyUrl($event.url)"
                    @rename="(type, item) => type === 'folder' ? (editingFolder = item, isFolderModalOpen = true) : (selectedMediaForPreview = item)"
                />
            </div>
        </div>

        <!-- Modals -->
        <PreviewModal 
            v-if="selectedMediaForPreview"
            :item="selectedMediaForPreview"
            :is-image="isImage(selectedMediaForPreview.mime)"
            :get-file-icon="getFileIcon"
            :format-size="formatSize"
            @close="selectedMediaForPreview = null"
            @copy-link="copyUrl(selectedMediaForPreview.url)"
            @move="openMoveModal('media', selectedMediaForPreview.id); selectedMediaForPreview = null"
            @delete="deleteSingle('media', selectedMediaForPreview.id); selectedMediaForPreview = null"
            @update-alt="updateMedia(selectedMediaForPreview.id, { alt_text: $event })"
            @update-filename="updateMedia(selectedMediaForPreview.id, { filename: $event })"
        />

        <FolderModal 
            :is-open="isFolderModalOpen"
            :folder="editingFolder"
            :processing="folderForm.processing"
            @close="isFolderModalOpen = false; editingFolder = null"
            @submit="handleFolderSubmit"
        />

        <MoveModal 
            :is-open="isMoveModalOpen"
            :folder-tree="folderTree"
            v-model:target-folder-id="targetFolderId"
            :is-any-selected="isAnySelected"
            @close="isMoveModalOpen = false"
            @confirm="bulkAction('move')"
        />

    </AdminLayout>
</template>
