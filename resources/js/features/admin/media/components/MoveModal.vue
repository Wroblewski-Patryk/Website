<script setup>
import { ref } from 'vue';
import { PhCaretRight, PhHouse, PhCheck, PhX } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';
import FolderTreeItem from './FolderTreeItem.vue';

const { t } = useTranslations();

const props = defineProps({
    isOpen: Boolean,
    folderTree: Array,
    targetFolderId: [Number, String],
    isAnySelected: Boolean
});

const emit = defineEmits(['close', 'update:targetFolderId', 'confirm']);
</script>

<template>
    <dialog class="modal" :class="isOpen ? 'modal-open' : ''">
        <div class="modal-box border border-base-300 shadow-xl rounded-box p-8 max-h-[85vh] flex flex-col overflow-hidden">
            <button @click="$emit('close')" class="btn btn-ghost btn-sm btn-square absolute right-6 top-6"><PhX class="w-5 h-5" /></button>

            <div class="flex items-center gap-4 mb-8">
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shadow-inner">
                    <PhCaretRight weight="bold" class="w-7 h-7" />
                </div>
                <div>
                    <h3 class="font-black text-2xl tracking-tight">{{ t('admin.media.move_to_folder', 'Move to folder') }}</h3>
                    <p class="text-[10px] opacity-40 uppercase font-black tracking-widest mt-1">{{ t('admin.media.select_destination', 'Select destination directory') }}</p>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto custom-scrollbar pr-4 -mr-4">
                <div class="space-y-3">
                    <!-- Root Folder Option -->
                    <div 
                        class="flex items-center gap-4 p-5 rounded-xl border-2 transition-all cursor-pointer group"
                        :class="targetFolderId === null ? 'border-primary bg-primary/5 text-primary' : 'border-base-200 hover:border-primary/30 hover:bg-base-100'"
                        @click="$emit('update:targetFolderId', null)"
                    >
                        <div class="w-10 h-10 rounded-lg bg-base-200 group-hover:bg-primary/10 flex items-center justify-center transition-colors shadow-sm">
                            <PhHouse :weight="targetFolderId === null ? 'fill' : 'bold'" class="w-5 h-5" />
                        </div>
                        <div class="flex-1 font-black uppercase tracking-widest text-xs">{{ t('admin.media.root_folder', 'Root folder') }}</div>
                        <PhCheck v-if="targetFolderId === null" weight="bold" class="w-5 h-5 animate-in zoom-in-50 duration-300" />
                    </div>

                    <div class="divider text-[10px] uppercase font-black tracking-[0.3em] opacity-20 py-6">{{ t('admin.media.or_select_subfolder', 'Or select a subfolder') }}</div>

                    <!-- Recursive Tree -->
                    <div class="bg-base-200/30 rounded-xl p-6 border border-base-300/50 space-y-2">
                        <FolderTreeItem 
                            v-for="folder in folderTree" :key="folder.id"
                            :folder="folder" 
                            :current-folder-id="targetFolderId"
                            :is-move-mode="true"
                            @click="$emit('update:targetFolderId', $event)"
                        />
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 justify-end mt-10 pt-6 border-t border-base-200">
                <button type="button" @click="$emit('close')" class="btn btn-ghost h-14 px-8 font-bold">{{ t('admin.common.cancel', 'Cancel') }}</button>
                <button 
                    @click="$emit('confirm')" 
                    class="btn btn-primary h-14 px-10 font-bold shadow-xl shadow-primary/20" 
                    :disabled="!isAnySelected"
                >
                    {{ t('admin.media.move_here', 'Move here') }}
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop" @click="$emit('close')">
            <button>close</button>
        </form>
    </dialog>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: hsl(var(--bc) / 0.1); border-radius: 10px; }
</style>
