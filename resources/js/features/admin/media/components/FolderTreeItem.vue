<script setup>
import { PhFolder, PhPlus } from '@phosphor-icons/vue';

const props = defineProps({
    folder: Object,
    currentFolderId: [Number, String],
    isMoveMode: Boolean
});

defineEmits(['click', 'add-subfolder']);
</script>

<template>
    <div class="space-y-1">
        <div 
            class="group w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-sm transition-all cursor-pointer"
            :class="currentFolderId === folder.id ? 'bg-primary text-primary-content font-bold px-4' : 'hover:bg-base-200 text-base-content/70 hover:text-base-content'"
            @click="$emit('click', folder.id)"
        >
            <div class="flex items-center gap-2 truncate">
                <PhFolder :weight="currentFolderId === folder.id ? 'fill' : 'regular'" class="w-4 h-4 flex-shrink-0" />
                <span class="truncate">{{ folder.name }}</span>
            </div>
            
            <button 
                v-if="!isMoveMode"
                @click.stop="$emit('add-subfolder', folder.id)" 
                class="btn btn-ghost btn-xs btn-square opacity-0 group-hover:opacity-100 transition-opacity"
                :class="currentFolderId === folder.id ? 'text-primary-content' : ''"
            >
                <PhPlus weight="bold" class="w-3 h-3" />
            </button>
        </div>

        <div v-if="folder.children && folder.children.length > 0" class="ml-4 border-l border-base-300 pl-2 mt-1 space-y-1">
            <FolderTreeItem 
                v-for="child in folder.children" 
                :key="child.id" 
                :folder="child" 
                :current-folder-id="currentFolderId"
                :is-move-mode="isMoveMode"
                @click="$emit('click', $event)"
                @add-subfolder="$emit('add-subfolder', $event)"
            />
        </div>
    </div>
</template>
