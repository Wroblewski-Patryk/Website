<script setup>
import { 
    PhFolder, PhFile, PhCaretRight, PhTrash, PhEye, 
    PhLink, PhPencilSimple, PhImageSquare, PhVideo, 
    PhFilePdf, PhFileZip 
} from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const { t } = useTranslations();
const { formatDate } = useFormatter();

const props = defineProps({
    item: Object,
    type: { type: String, default: 'media' },
    isSelected: Boolean
});

const emit = defineEmits(['selection-toggle', 'preview', 'move', 'delete', 'copy-link', 'folder-click', 'rename']);

const isImage = computed(() => props.type === 'media' && props.item.mime?.startsWith('image/'));

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
    <tr 
        class="hover:bg-base-200/50 transition-colors group cursor-pointer" 
        :class="isSelected ? 'bg-primary/5' : ''"
        @click="type === 'folder' ? $emit('folder-click', item.id) : $emit('selection-toggle')"
    >
        <td @click.stop="">
            <input 
                type="checkbox" 
                class="checkbox checkbox-primary checkbox-sm" 
                :checked="isSelected" 
                @change="$emit('selection-toggle')" 
            />
        </td>
        <td class="w-16">
            <div class="w-10 h-10 rounded-lg overflow-hidden flex items-center justify-center shadow-inner" :class="type === 'folder' ? 'bg-primary/10 text-primary' : 'bg-base-300'">
                <template v-if="type === 'folder'">
                    <PhFolder weight="fill" class="w-5 h-5" />
                </template>
                <template v-else>
                    <img v-if="isImage" :src="'/storage/' + item.path" class="object-cover w-full h-full" />
                    <component v-else :is="getFileIcon(item.mime)" weight="regular" class="w-5 h-5 opacity-40" />
                </template>
            </div>
        </td>
        <td class="font-medium truncate max-w-[200px]">
            {{ type === 'folder' ? item.name : item.path.split('/').pop() }}
        </td>
        <td>
            <span class="badge badge-sm opacity-60 uppercase text-[10px]" :class="type === 'folder' ? 'badge-ghost' : 'badge-outline'">
                {{ type === 'folder' ? t('admin.media.folder', 'Folder') : (item.mime?.split('/')[1] || t('admin.media.file', 'File')) }}
            </span>
        </td>
        <td class="text-xs opacity-60">{{ type === 'folder' ? '-' : formatSize(item.size) }}</td>
        <td class="text-xs opacity-60">{{ formatDate(item.created_at) }}</td>
        <td class="text-right">
            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop="">
                <button v-if="type === 'media'" @click="$emit('preview')" class="btn btn-ghost btn-xs btn-square" :title="t('admin.common.preview', 'Preview')"><PhEye class="w-4 h-4" /></button>
                <button v-if="type === 'media'" @click="$emit('copy-link')" class="btn btn-ghost btn-xs btn-square" :title="t('admin.media.copy_link', 'Copy link')"><PhLink class="w-4 h-4" /></button>
                <button @click="$emit('rename')" class="btn btn-ghost btn-xs btn-square" :title="t('admin.common.rename', 'Rename')">
                    <PhPencilSimple weight="bold" class="w-4 h-4" />
                </button>
                <button @click="$emit('move')" class="btn btn-ghost btn-xs btn-square" :title="t('admin.common.move', 'Move')"><PhCaretRight class="w-4 h-4" /></button>
                <button @click="$emit('delete')" class="btn btn-ghost btn-xs btn-square text-error" :title="t('admin.common.delete', 'Delete')"><PhTrash class="w-4 h-4" /></button>
            </div>
        </td>
    </tr>
</template>
