<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, markRaw, computed, watch } from 'vue';
import { 
    PhUpload, PhHouse, PhImageSquare, PhFolderPlus 
} from '@phosphor-icons/vue';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';
import { useToastStore } from '@/Stores/useToastStore';

// Media Components
import Uploader from '@/Components/Admin/Media/Uploader.vue';
import Toolbar from '@/Components/Admin/Media/Toolbar.vue';
import Browser from '@/Components/Admin/Media/Browser.vue';
import PreviewModal from '@/Components/Admin/Media/PreviewModal.vue';
import MoveModal from '@/Components/Admin/Media/MoveModal.vue';
import FolderModal from '@/Components/Admin/Media/FolderModal.vue';

const props = defineProps(['media', 'folders', 'subfolders', 'currentFolder', 'filters']);
const toastStore = useToastStore();
const uploaderRef = ref(null);

// Dynamic Breadcrumbs
const breadcrumbs = computed(() => {
    const crumbs = [
        { label: 'Dashboard', url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
        { label: 'Biblioteka mediów', url: route('admin.media.index') }
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

const sort = ref(props.filters?.sort || 'created_at');
const direction = ref(props.filters?.direction || 'desc');
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
watch([sort, direction, currentFolderId, viewType], () => {
    fetchMedia();
});

const fetchMedia = () => {
    router.reload({
        data: {
            folder_id: viewType.value === 'flat' ? null : currentFolderId.value,
            sort: sort.value,
            direction: direction.value,
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
            toastStore.success('Files uploaded! 🚀');
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
                toastStore.success('Folder renamed.');
            }
        });
    } else {
        folderForm.name = name;
        folderForm.parent_id = props.currentFolder?.id || null;
        folderForm.post(route('admin.media.folders.store'), {
            onSuccess: () => {
                isFolderModalOpen.value = false;
                toastStore.success('Folder created.');
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
    if (action === 'delete' && !confirm('Are you sure? This cannot be undone.')) return;
    
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
            toastStore.success(`Action: ${action} processed.`);
        }
    });
}

function updateMedia(id, data) {
    router.patch(route('admin.media.update', id), data, { preserveScroll: true });
}

function deleteSingle(type, id) {
    if (type === 'media') {
        router.delete(route('admin.media.destroy', id), { onSuccess: () => toastStore.success('Deleted.') });
    } else {
        router.delete(route('admin.media.folders.destroy', id), { onSuccess: () => toastStore.success('Deleted.') });
    }
}

function copyUrl(path) {
    const url = window.location.origin + '/storage/' + path;
    navigator.clipboard.writeText(url);
    toastStore.info('URL copied to clipboard!');
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
    <Head title="Media Library" />
    <AdminLayout>
        <div class="space-y-8 animate-in fade-in duration-500">
            <!-- Header Section -->
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <!-- Title, Description, and Breadcrumbs -->
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-xl shadow-inner">
                            <PhImageSquare weight="regular" class="w-6 h-6" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-black tracking-tight flex items-center gap-2">Biblioteka mediów</h1>
                                <span class="text-base-content/30 text-xl font-light">|</span>
                                <p class="text-sm opacity-60 m-0 pt-1">Zarządzaj swoimi plikami i folderami.</p>
                            </div>
                            <div class="flex flex-col gap-1 mt-0.5">
                                <!-- Breadcrumbs stacked below title/description -->
                                <div class="breadcrumbs text-xs text-base-content/50 m-0 pt-2 p-0">
                                    <ul>
                                        <li v-for="(crumb, index) in breadcrumbs" :key="index">
                                            <Link v-if="crumb.url" :href="crumb.url" class="hover:text-primary transition-colors flex items-center gap-1">
                                                <component :is="crumb.icon" v-if="crumb.icon" weight="regular" class="w-4 h-4" />
                                                {{ crumb.label }}
                                            </Link>
                                            <span v-else class="text-base-content/70 font-medium tracking-wide flex items-center gap-1">
                                                {{ crumb.label }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Actions Slot -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-3">
                            <button @click="editingFolder = null; isFolderModalOpen = true" class="btn btn-ghost gap-2 font-bold group">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="1em" height="1em" fill="currentColor" class="w-5 h-5 group-hover:scale-110 transition-transform"><g><path d="M216,68H133.39l-26-29.29a20,20,0,0,0-15-6.71H40A20,20,0,0,0,20,52V200.62A19.41,19.41,0,0,0,39.38,220H216.89A19.13,19.13,0,0,0,236,200.89V88A20,20,0,0,0,216,68ZM90.61,56l10.67,12H44V56ZM212,196H44V92H212Zm-72-76v12h12a12,12,0,0,1,0,24H140v12a12,12,0,0,1-24,0V156H104a12,12,0,0,1,0-24h12V120a12,12,0,0,1,24,0Z"></path></g></svg>
                                Nowy folder
                            </button>
                            <button @click="isUploaderOpen = !isUploaderOpen" class="btn btn-primary px-6 gap-2 font-black shadow-lg shadow-primary/20">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="1em" height="1em" fill="currentColor" class="w-5 h-5"><g><path d="M188,184a16,16,0,1,1,16-16A16,16,0,0,1,188,184Zm36-68H180a12,12,0,0,0,0,24h40v56H36V140H76a12,12,0,0,0,0-24H32a20,20,0,0,0-20,20v64a20,20,0,0,0,20,20H224a20,20,0,0,0,20-20V136A20,20,0,0,0,224,116ZM88.49,80.49,116,53v75a12,12,0,0,0,24,0V53l27.51,27.52a12,12,0,1,0,17-17l-48-48a12,12,0,0,0-17,0l-48,48a12,12,0,1,0,17,17Z"></path></g></svg>
                                Prześlij pliki
                            </button>
                        </div>
                    </div>
                </div>
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
                    v-model:sort="sort"
                    v-model:direction="direction"
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
                    @copy-link="copyUrl"
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
            @copy-link="copyUrl(selectedMediaForPreview.path)"
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
