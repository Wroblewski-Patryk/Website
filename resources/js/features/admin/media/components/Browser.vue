<script setup>
import {
    PhImageSquare,
    PhFolder,
    PhFile,
    PhCaretRight,
    PhTrash,
    PhEye,
    PhLink,
    PhPencilSimple,
    PhVideo,
    PhFilePdf,
    PhFileZip
} from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';
import ResourceTable from '@/features/admin/shared/components/ResourceTable.vue';
import GridItem from './GridItem.vue';

const { t } = useTranslations();
const { formatDate } = useFormatter();

const props = defineProps({
    viewMode: { type: String, default: 'grid' },
    subfolders: Array,
    media: Array,
    selectedFolderIds: Array,
    selectedMediaIds: Array
});

defineEmits([
    'folder-click', 
    'selection-toggle', 
    'preview', 
    'move', 
    'delete', 
    'copy-link',
    'rename'
]);

const listRows = computed(() => {
    const folderRows = (props.subfolders || []).map((folder) => ({
        id: `folder-${folder.id}`,
        original_id: folder.id,
        row_kind: 'folder',
        name: folder.name,
        mime: null,
        size: null,
        created_at: folder.created_at,
        item: folder,
    }));

    const mediaRows = (props.media || []).map((item) => ({
        id: `media-${item.id}`,
        original_id: item.id,
        row_kind: 'media',
        name: item.path?.split('/').pop() || item.filename || item.name || `#${item.id}`,
        mime: item.mime,
        size: item.size,
        created_at: item.created_at,
        item,
    }));

    return [...folderRows, ...mediaRows];
});

const listResources = computed(() => {
    const rows = listRows.value;
    const total = rows.length;

    return {
        data: rows,
        from: total ? 1 : 0,
        to: total,
        total,
        links: [],
    };
});

const listColumns = computed(() => ([
    { key: 'select', label: '', sortable: false, align: 'center' },
    { key: 'preview', label: t('admin.common.preview', 'Preview'), sortable: false },
    { key: 'name', label: t('admin.common.name', 'Name'), sortable: false },
    { key: 'type', label: t('admin.common.type', 'Type'), sortable: false },
    { key: 'size', label: t('admin.common.size', 'Size'), sortable: false },
    { key: 'date', label: t('admin.common.date', 'Date'), sortable: false },
    { key: 'actions', label: t('admin.common.actions', 'Actions'), sortable: false, align: 'right' },
]));

const isImageMime = (mime) => Boolean(mime?.startsWith('image/'));

const getFileIcon = (mime) => {
    if (!mime) return PhFile;
    if (mime.startsWith('image/')) return PhImageSquare;
    if (mime.startsWith('video/')) return PhVideo;
    if (mime.includes('pdf')) return PhFilePdf;
    if (mime.includes('zip') || mime.includes('rar')) return PhFileZip;
    return PhFile;
};

const formatSize = (bytes) => {
    if (!bytes) return '-';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};
</script>

<template>
    <div class="min-h-[500px]">
        <!-- Empty State -->
        <div v-if="media.length === 0 && subfolders.length === 0" class="flex flex-col items-center justify-center py-40 text-center select-none group">
            <div class="w-32 h-32 rounded-full bg-base-200 flex items-center justify-center mb-8 border border-base-300 group-hover:scale-110 transition-transform duration-700 group-hover:rotate-12 group-hover:bg-primary/5">
                <PhImageSquare weight="thin" class="w-16 h-16 opacity-10 group-hover:opacity-30 group-hover:text-primary transition-all duration-700" />
            </div>
            <p class="text-2xl font-black italic opacity-20 uppercase tracking-[0.3em]">{{ t('admin.media.empty_title', 'No files') }}</p>
            <p class="text-sm opacity-30 mt-2 font-medium">{{ t('admin.media.empty_desc', 'Start uploading your assets') }}</p>
        </div>

        <!-- Content rendering -->
        <template v-else>
            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-8 pt-4">
                <!-- Subfolders -->
                <GridItem 
                    v-for="folder in subfolders" :key="'f-' + folder.id"
                    type="folder"
                    :item="folder"
                    :is-selected="selectedFolderIds.includes(folder.id)"
                    @folder-click="$emit('folder-click', $event)"
                    @selection-toggle="$emit('selection-toggle', 'folder', folder.id)"
                    @move="$emit('move', 'folder', folder.id)"
                    @delete="$emit('delete', 'folder', folder.id)"
                    @rename="$emit('rename', 'folder', folder)"
                />

                <!-- Media -->
                <GridItem 
                    v-for="item in media" :key="'m-' + item.id"
                    type="media"
                    :item="item"
                    :is-selected="selectedMediaIds.includes(item.id)"
                    @selection-toggle="$emit('selection-toggle', 'media', item.id)"
                    @preview="$emit('preview', item)"
                    @move="$emit('move', 'media', item.id)"
                    @delete="$emit('delete', 'media', item.id)"
                />
            </div>

            <!-- List View -->
            <div v-else class="mt-4">
                <ResourceTable
                    :show-header="false"
                    :resources="listResources"
                    :columns="listColumns"
                    persistence-key="media-browser-list"
                >
                    <template #cell-select="{ item }">
                        <input
                            type="checkbox"
                            class="checkbox checkbox-primary checkbox-sm"
                            :checked="item.row_kind === 'folder' ? selectedFolderIds.includes(item.original_id) : selectedMediaIds.includes(item.original_id)"
                            @change="$emit('selection-toggle', item.row_kind, item.original_id)"
                        />
                    </template>

                    <template #cell-preview="{ item }">
                        <button
                            v-if="item.row_kind === 'folder'"
                            type="button"
                            class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shadow-inner"
                            @click="$emit('folder-click', item.original_id)"
                        >
                            <PhFolder weight="fill" class="w-5 h-5" />
                        </button>
                        <button
                            v-else
                            type="button"
                            class="w-10 h-10 rounded-lg bg-base-300 overflow-hidden flex items-center justify-center shadow-inner"
                            @click="$emit('preview', item.item)"
                        >
                            <img v-if="isImageMime(item.mime)" :src="item.item.url" class="object-cover w-full h-full" />
                            <component v-else :is="getFileIcon(item.mime)" class="w-5 h-5 opacity-40" />
                        </button>
                    </template>

                    <template #cell-name="{ item }">
                        <button
                            v-if="item.row_kind === 'folder'"
                            type="button"
                            class="font-medium hover:text-primary transition-colors"
                            @click="$emit('folder-click', item.original_id)"
                        >
                            {{ item.name }}
                        </button>
                        <span v-else class="font-medium truncate block max-w-[220px]">{{ item.name }}</span>
                    </template>

                    <template #cell-type="{ item }">
                        <span class="badge badge-sm opacity-60 uppercase text-[10px]" :class="item.row_kind === 'folder' ? 'badge-ghost' : 'badge-outline'">
                            {{ item.row_kind === 'folder' ? t('admin.media.folder', 'Folder') : (item.mime?.split('/')[1] || t('admin.media.file', 'File')) }}
                        </span>
                    </template>

                    <template #cell-size="{ item }">
                        <span class="text-xs opacity-60">{{ item.row_kind === 'folder' ? '-' : formatSize(item.size) }}</span>
                    </template>

                    <template #cell-date="{ item }">
                        <span class="text-xs opacity-60">{{ formatDate(item.created_at) }}</span>
                    </template>

                    <template #cell-actions="{ item }">
                        <div class="flex justify-end gap-1">
                            <button
                                v-if="item.row_kind === 'media'"
                                type="button"
                                class="btn btn-ghost btn-xs btn-square"
                                :title="t('admin.common.preview', 'Preview')"
                                @click="$emit('preview', item.item)"
                            >
                                <PhEye class="w-4 h-4" />
                            </button>
                            <button
                                v-if="item.row_kind === 'media'"
                                type="button"
                                class="btn btn-ghost btn-xs btn-square"
                                :title="t('admin.media.copy_link', 'Copy link')"
                                @click="$emit('copy-link', item.item.url)"
                            >
                                <PhLink class="w-4 h-4" />
                            </button>
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs btn-square"
                                :title="t('admin.common.rename', 'Rename')"
                                @click="$emit('rename', item.row_kind, item.item)"
                            >
                                <PhPencilSimple class="w-4 h-4" />
                            </button>
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs btn-square"
                                :title="t('admin.common.move', 'Move')"
                                @click="$emit('move', item.row_kind, item.original_id)"
                            >
                                <PhCaretRight class="w-4 h-4" />
                            </button>
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs btn-square text-error"
                                :title="t('admin.common.delete', 'Delete')"
                                @click="$emit('delete', item.row_kind, item.original_id)"
                            >
                                <PhTrash class="w-4 h-4" />
                            </button>
                        </div>
                    </template>
                </ResourceTable>
            </div>
        </template>
    </div>
</template>
