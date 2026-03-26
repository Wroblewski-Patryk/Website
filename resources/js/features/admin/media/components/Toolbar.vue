<script setup>
import { 
    PhSortAscending, PhSortDescending, 
    PhGridFour, PhList, PhCaretRight, PhTrash,
    PhFolders, PhStack, PhSquare, PhCheckSquare, PhMinusSquare
} from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    sort: String,
    direction: String,
    viewMode: String,
    viewType: { type: String, default: 'nested' },
    fileType: { type: String, default: 'all' },
    selectedCount: Number,
    allSelected: Boolean
});

defineEmits([
    'update:sort', 'update:direction', 'update:viewMode', 'update:viewType', 'update:fileType',
    'toggle-all', 'bulk-move', 'bulk-delete'
]);
</script>

<template>
    <div 
        class="sticky top-0 z-30 transition-all duration-300 rounded-box border bg-base-100/80 backdrop-blur-xl p-3 px-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)]"
        :class="selectedCount > 0 ? 'border-primary/30 bg-primary/[0.03] scale-[1.01]' : 'border-base-300'"
    >
        <div class="flex flex-wrap items-center justify-between gap-4 py-1">
            <!-- Left Side: Selection & Bulk Actions -->
            <div class="flex items-center gap-2">
                <!-- Premium Selection Toggle -->
                <button 
                    @click="$emit('toggle-all')"
                    class="btn btn-ghost btn-sm btn-square hover:bg-primary/10 transition-all duration-300 group"
                    :class="selectedCount > 0 ? 'text-primary' : 'text-base-content/30'"
                >
                    <PhCheckSquare v-if="allSelected" weight="fill" class="w-6 h-6 animate-in zoom-in duration-200" />
                    <PhMinusSquare v-else-if="selectedCount > 0" weight="fill" class="w-6 h-6 animate-in zoom-in duration-200" />
                    <PhSquare v-else weight="bold" class="w-6 h-6 group-hover:scale-110" />
                </button>

                <div v-if="selectedCount > 0" class="flex items-center gap-3 animate-in slide-in-from-left duration-300">
                    <div class="h-4 w-px bg-primary/20 mx-1"></div>
                    <span class="text-xs font-black text-primary uppercase tracking-wider">{{ selectedCount }} {{ t('admin.common.selected', 'selected') }}</span>
                    
                    <div class="flex items-center gap-1 ml-2">
                        <button 
                            @click="$emit('bulk-move')" 
                            class="btn btn-ghost btn-sm h-9 px-3 gap-2 text-xs font-bold hover:bg-primary/10"
                        >
                            <PhCaretRight weight="bold" class="w-3.5 h-3.5" /> {{ t('admin.common.move', 'Move') }}
                        </button>
                        <button 
                            @click="$emit('bulk-delete')" 
                            class="btn btn-ghost btn-sm h-9 px-3 gap-2 text-xs font-bold text-error hover:bg-error/10"
                        >
                            <PhTrash weight="bold" class="w-3.5 h-3.5" /> {{ t('admin.common.delete', 'Delete') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Filters & View Options -->
            <div class="flex items-center gap-4">
                <!-- View Type Toggle (Nested vs Flat) -->
                <div class="flex bg-base-200/50 p-1 rounded-xl border border-base-300/50">
                    <button 
                        @click="$emit('update:viewType', 'nested')"
                        class="btn btn-xs h-8 px-3 gap-2 border-0 transition-all duration-300 rounded-lg text-xs font-bold"
                        :class="viewType === 'nested' ? 'bg-base-100 text-primary shadow-sm' : 'text-base-content/40 hover:text-base-content'"
                        :title="t('admin.media.folders_view', 'Folders view')"
                    >
                        <PhFolders weight="bold" class="w-3.5 h-3.5" />
                        <span class="hidden sm:inline">{{ t('admin.media.folders', 'Folders') }}</span>
                    </button>
                    <button 
                        @click="$emit('update:viewType', 'flat')"
                        class="btn btn-xs h-8 px-3 gap-2 border-0 transition-all duration-300 rounded-lg text-xs font-bold"
                        :class="viewType === 'flat' ? 'bg-base-100 text-primary shadow-sm' : 'text-base-content/40 hover:text-base-content'"
                        :title="t('admin.media.all_files_view', 'All files view')"
                    >
                        <PhStack weight="bold" class="w-3.5 h-3.5" />
                        <span class="hidden sm:inline">{{ t('admin.media.all', 'All') }}</span>
                    </button>
                </div>

                <div class="w-px h-6 bg-base-300 mx-1"></div>

                <!-- Sorting Zone -->
                <div class="flex items-center gap-2">
                    <select
                        :value="fileType"
                        @change="$emit('update:fileType', $event.target.value)"
                        class="select select-sm select-bordered h-9 text-[10px] font-black uppercase tracking-widest bg-base-200/50 border-0 focus:ring-0"
                    >
                        <option value="all">{{ t('admin.media.filter_all', 'All files') }}</option>
                        <option value="document">{{ t('admin.media.filter_document', 'Documents') }}</option>
                        <option value="image">{{ t('admin.media.filter_image', 'Images') }}</option>
                        <option value="audio">{{ t('admin.media.filter_audio', 'Audio') }}</option>
                        <option value="video">{{ t('admin.media.filter_video', 'Video') }}</option>
                    </select>

                    <select 
                        :value="sort"
                        @change="$emit('update:sort', $event.target.value)"
                        class="select select-sm select-bordered h-9 text-[10px] font-black uppercase tracking-widest bg-base-200/50 border-0 focus:ring-0"
                    >
                        <option value="created_at">{{ t('admin.common.date', 'Date') }}</option>
                        <option value="path">{{ t('admin.common.name', 'Name') }}</option>
                        <option value="size">{{ t('admin.common.size', 'Size') }}</option>
                    </select>
                    
                    <button 
                        @click="$emit('update:direction', direction === 'asc' ? 'desc' : 'asc')"
                        class="btn btn-ghost btn-sm btn-square h-9 w-9 hover:bg-base-200 transition-colors"
                    >
                        <PhSortAscending v-if="direction === 'asc'" weight="bold" class="w-4 h-4" />
                        <PhSortDescending v-else weight="bold" class="w-4 h-4" />
                    </button>
                </div>

                <div class="w-px h-6 bg-base-300 mx-1"></div>

                <!-- Grid/List Toggle -->
                <div class="flex bg-base-200/50 p-1 rounded-xl border border-base-300/50">
                    <button 
                        @click="$emit('update:viewMode', 'grid')" 
                        class="btn btn-xs h-8 w-8 btn-ghost border-0 transition-all duration-300 rounded-lg" 
                        :class="viewMode === 'grid' ? 'bg-base-100 text-primary shadow-sm' : 'text-base-content/40 hover:text-base-content'"
                    >
                        <PhGridFour weight="bold" class="w-4 h-4" />
                    </button>
                    <button 
                        @click="$emit('update:viewMode', 'list')" 
                        class="btn btn-xs h-8 w-8 btn-ghost border-0 transition-all duration-300 rounded-lg" 
                        :class="viewMode === 'list' ? 'bg-base-100 text-primary shadow-sm' : 'text-base-content/40 hover:text-base-content'"
                    >
                        <PhList weight="bold" class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
