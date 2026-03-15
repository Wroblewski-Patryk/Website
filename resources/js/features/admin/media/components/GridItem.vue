```
<script setup>
import { 
    PhFolder, PhImageSquare, PhVideo, PhFilePdf, PhFileZip, 
    PhFile, PhTrash, PhCaretRight, PhEye, PhPencilSimple,
    PhSquare, PhCheckSquare
} from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    item: Object,
    type: { type: String, default: 'media' }, // 'media' or 'folder'
    isSelected: Boolean
});

const emit = defineEmits(['click', 'selection-toggle', 'preview', 'move', 'delete', 'folder-click', 'rename']);

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
    <div 
        class="group relative aspect-square rounded-2xl overflow-hidden transition-all duration-300 border border-base-300 hover:border-primary/50 bg-base-100 hover:shadow-xl hover:shadow-primary/5 select-none"
        :class="isSelected ? 'ring-2 ring-primary border-transparent' : ''"
        @click="type === 'folder' ? $emit('folder-click', item.id) : $emit('selection-toggle')"
    >
        <!-- Content Area (Background/Icon) -->
        <div class="absolute inset-0 z-0 overflow-hidden">
             <!-- Folder View -->
             <div v-if="type === 'folder'" class="w-full h-full flex items-center justify-center bg-base-200/30">
                <div class="w-20 h-20 rounded-3xl bg-primary/5 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-500">
                    <PhFolder weight="fill" class="w-12 h-12" />
                </div>
             </div>

             <!-- Media View -->
             <template v-else>
                 <img v-if="isImage" :src="'/storage/' + item.path" :alt="item.alt_text" class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110" />
                 <div v-else class="w-full h-full flex flex-col items-center justify-center bg-base-200/30">
                    <div class="w-16 h-16 rounded-2xl bg-base-200 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform duration-500">
                        <component :is="getFileIcon(item.mime)" weight="duotone" class="w-8 h-8 opacity-40 text-primary" />
                    </div>
                 </div>
             </template>
             
             <!-- Hover Overlay Dim -->
             <div class="absolute inset-0 bg-base-content/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
        </div>

        <!-- Selection Bubble (Top Left) -->
        <div 
            class="absolute top-3 left-3 z-20 transition-all duration-300"
            :class="[isSelected ? 'opacity-100 scale-100' : 'opacity-0 scale-75 group-hover:opacity-100 group-hover:scale-100']"
        >
             <button 
                @click.stop="$emit('selection-toggle')"
                class="btn btn-circle btn-xs h-8 w-8 bg-base-100/90 backdrop-blur-md border-white/20 shadow-lg hover:bg-primary hover:text-white hover:border-primary transition-all duration-300"
                :class="isSelected ? 'bg-primary text-white border-primary' : 'text-primary/40'"
            >
                <PhCheckSquare v-if="isSelected" weight="fill" class="w-4 h-4" />
                <PhSquare v-else weight="bold" class="w-4 h-4" />
            </button>
        </div>

        <!-- Unified Actions (Top Right) -->
        <div 
            class="absolute top-3 right-3 z-20 flex gap-1.5 opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500"
            @click.stop=""
        >
            <template v-if="type === 'folder'">
                <button 
                    @click="$emit('rename')" 
                    class="btn btn-circle btn-xs h-8 w-8 bg-base-100/90 backdrop-blur-md border-white/20 shadow-lg hover:bg-base-100 hover:text-primary transition-all"
                    :title="t('admin.common.rename', 'Rename')"
                >
                    <PhPencilSimple weight="bold" class="w-4 h-4" />
                </button>
            </template>
            <template v-else>
                <button 
                    @click="$emit('preview')" 
                    class="btn btn-circle btn-xs h-8 w-8 bg-base-100/90 backdrop-blur-md border-white/20 shadow-lg hover:bg-base-100 hover:text-primary transition-all"
                    :title="t('admin.common.preview', 'Preview')"
                >
                    <PhEye weight="bold" class="w-4 h-4" />
                </button>
            </template>
            
            <button 
                @click="$emit('move')" 
                class="btn btn-circle btn-xs h-8 w-8 bg-base-100/90 backdrop-blur-md border-white/20 shadow-lg hover:bg-base-100 hover:text-primary transition-all"
                :title="t('admin.common.move', 'Move')"
            >
                <PhCaretRight weight="bold" class="w-4 h-4" />
            </button>
            <button 
                @click="$emit('delete')" 
                class="btn btn-circle btn-xs h-8 w-8 bg-base-100/90 backdrop-blur-md border-white/20 shadow-lg hover:bg-error hover:text-white hover:border-error text-error transition-all"
                :title="t('admin.common.delete', 'Delete')"
            >
                <PhTrash weight="bold" class="w-4 h-4" />
            </button>
        </div>

        <!-- Glassmorphism Name Bar (Bottom) -->
        <div class="absolute bottom-2 inset-x-2 z-10">
            <div class="bg-base-100/70 backdrop-blur-md border border-white/10 rounded-2xl p-2 px-3 shadow-sm group-hover:shadow-md transition-all duration-300">
                <p class="text-[11px] font-black truncate text-base-content/70 group-hover:text-primary transition-colors uppercase tracking-wider">
                    {{ type === 'folder' ? item.name : item.path.split('/').pop() }}
                </p>
                <p v-if="type === 'media'" class="text-[9px] opacity-40 font-bold uppercase tracking-tighter">
                    {{ formatSize(item.size) }}
                </p>
                <p v-else class="text-[9px] opacity-40 font-bold uppercase tracking-tighter">
                    {{ t('admin.media.folder', 'Folder') }}
                </p>
            </div>
        </div>
    </div>
</template>

