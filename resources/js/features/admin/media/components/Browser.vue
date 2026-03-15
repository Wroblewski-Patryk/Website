<script setup>
import { PhImageSquare } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';
import GridItem from './GridItem.vue';
import ListItem from './ListItem.vue';

const { t } = useTranslations();

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
            <div v-else class="bg-base-100 rounded-box border border-base-300 overflow-hidden shadow-sm mt-4">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-base-200/50 border-b border-base-300">
                            <th class="w-12 bg-transparent"></th>
                            <th class="w-16 bg-transparent text-[10px] uppercase font-black opacity-30 tracking-widest pl-6">{{ t('admin.common.preview', 'Preview') }}</th>
                            <th class="bg-transparent text-[10px] uppercase font-black opacity-30 tracking-widest">{{ t('admin.common.name', 'Name') }}</th>
                            <th class="bg-transparent text-[10px] uppercase font-black opacity-30 tracking-widest">{{ t('admin.common.type', 'Type') }}</th>
                            <th class="bg-transparent text-[10px] uppercase font-black opacity-30 tracking-widest">{{ t('admin.common.size', 'Size') }}</th>
                            <th class="bg-transparent text-[10px] uppercase font-black opacity-30 tracking-widest">{{ t('admin.common.date', 'Date') }}</th>
                            <th class="bg-transparent text-[10px] uppercase font-black opacity-30 tracking-widest text-right pr-6">{{ t('admin.common.actions', 'Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-200">
                        <ListItem 
                            v-for="folder in subfolders" :key="'lf-' + folder.id"
                            type="folder"
                            :item="folder"
                            :is-selected="selectedFolderIds.includes(folder.id)"
                            @folder-click="$emit('folder-click', $event)"
                            @selection-toggle="$emit('selection-toggle', 'folder', folder.id)"
                            @move="$emit('move', 'folder', folder.id)"
                            @delete="$emit('delete', 'folder', folder.id)"
                            @rename="$emit('rename', 'folder', folder)"
                        />

                        <ListItem 
                            v-for="item in media" :key="'lm-' + item.id"
                            type="media"
                            :item="item"
                            :is-selected="selectedMediaIds.includes(item.id)"
                            @selection-toggle="$emit('selection-toggle', 'media', item.id)"
                            @preview="$emit('preview', item)"
                            @move="$emit('move', 'media', item.id)"
                            @delete="$emit('delete', 'media', item.id)"
                            @copy-link="$emit('copy-link', item.path)"
                        />
                    </tbody>
                </table>
            </div>
        </template>
    </div>
</template>
