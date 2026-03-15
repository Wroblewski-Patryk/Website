<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useMediaPickerStore } from '@/Stores/useMediaPickerStore';
import { PhX, PhMagnifyingGlass, PhArrowLeft, PhImageSquare } from '@phosphor-icons/vue';
import axios from 'axios';

// Reuse existing components
import Browser from './Browser.vue';
import Toolbar from './Toolbar.vue';
import Uploader from './Uploader.vue';

const store = useMediaPickerStore();

// Local Media State
const media = ref({ data: [] });
const folders = ref([]);
const subfolders = ref([]);
const currentFolder = ref(null);
const filters = ref({
    sort: 'created_at',
    direction: 'desc',
    view_type: 'nested',
    search: '',
    folder_id: null
});

const loading = ref(false);
const viewMode = ref('grid');
const isUploaderOpen = ref(false);

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('admin.media.index'), {
            params: {
                ...filters.value,
                folder_id: filters.value.view_type === 'flat' ? null : filters.value.folder_id
            },
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        media.value = response.data.media;
        folders.value = response.data.folders;
        subfolders.value = response.data.subfolders;
        currentFolder.value = response.data.currentFolder;
    } catch (error) {
        console.error('Failed to fetch media:', error);
    } finally {
        loading.value = false;
    }
};

watch(() => store.isOpen, (val) => {
    if (val) {
        fetchData();
    }
});

watch(filters, () => {
    fetchData();
}, { deep: true });

const handleFolderClick = (id) => {
    filters.value.folder_id = id;
};

const handleSelect = (type, id) => {
    if (type === 'media') {
        const item = media.value.data.find(m => m.id === id);
        if (item) {
            store.select(normalizeSelectedItem(item));
        }
    }
};

const toAbsoluteMediaUrl = (item) => {
    if (!item) return '';
    if (typeof item.url === 'string' && item.url.trim() !== '') {
        if (/^https?:\/\//i.test(item.url)) return item.url;
        if (item.url.startsWith('/')) return `${window.location.origin}${item.url}`;
    }

    const path = typeof item.path === 'string' ? item.path.trim() : '';
    if (!path) return '';
    if (/^https?:\/\//i.test(path)) return path;
    if (path.startsWith('/')) return `${window.location.origin}${path}`;
    return `${window.location.origin}/storage/${path.replace(/^\/+/, '')}`;
};

const normalizeSelectedItem = (item) => {
    const absoluteUrl = toAbsoluteMediaUrl(item);
    return {
        ...item,
        relative_path: item.path,
        path: absoluteUrl,
        url: absoluteUrl
    };
};

const close = () => {
    store.close();
};

const breadcrumbs = computed(() => {
    const crumbs = [{ label: 'Media', id: null }];
    if (currentFolder.value) {
        const parents = [];
        let curr = currentFolder.value;
        while (curr) {
            parents.unshift({ label: curr.name, id: curr.id });
            curr = folders.value.find(f => f.id === curr.parent_id);
        }
        crumbs.push(...parents);
    }
    return crumbs;
});
</script>

<template>
    <div v-if="store.isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-8 animate-in fade-in duration-300">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="close"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-6xl h-full max-h-[85vh] bg-base-100 rounded-[2rem] shadow-2xl border border-white/5 flex flex-col overflow-hidden animate-in zoom-in-95 duration-300">
            
            <!-- Header -->
            <div class="px-8 py-6 border-b border-base-content/5 flex items-center justify-between bg-base-200/30">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <PhImageSquare weight="bold" class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-xl font-black tracking-tight">Wybierz media</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <template v-for="(crumb, idx) in breadcrumbs" :key="idx">
                                <button 
                                    @click="filters.folder_id = crumb.id"
                                    class="text-[10px] uppercase font-bold tracking-widest transition-colors hover:text-primary"
                                    :class="idx === breadcrumbs.length - 1 ? 'text-primary' : 'opacity-40'"
                                >
                                    {{ crumb.label }}
                                </button>
                                <span v-if="idx < breadcrumbs.length - 1" class="opacity-20 text-[10px]">/</span>
                            </template>
                        </div>
                    </div>
                </div>
                
                <button @click="close" class="btn btn-ghost btn-sm btn-circle">
                    <PhX weight="bold" class="w-5 h-5 opacity-50" />
                </button>
            </div>

            <!-- Toolbar -->
            <div class="px-8 py-4 border-b border-base-content/5 bg-base-100">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="relative w-full md:w-72">
                        <PhMagnifyingGlass class="absolute left-3 top-1/2 -translate-y-1/2 opacity-30 w-4 h-4" />
                        <input 
                            v-model="filters.search" 
                            type="text" 
                            placeholder="Szukaj plików..." 
                            class="input input-sm input-bordered w-full pl-10 bg-base-200/50 border-none focus:bg-base-200 transition-all"
                        />
                    </div>
                    
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <Toolbar 
                            v-model:sort="filters.sort"
                            v-model:direction="filters.direction"
                            v-model:view-mode="viewMode"
                            v-model:view-type="filters.view_type"
                            :selected-count="0"
                            :all-selected="false"
                            class="!p-0 border-none shadow-none bg-transparent"
                        />
                        <button @click="isUploaderOpen = !isUploaderOpen" class="btn btn-sm btn-primary gap-2 font-bold px-4">
                            Dodaj
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar relative">
                <Uploader 
                    :is-open="isUploaderOpen"
                    :processing="loading"
                    :current-folder-id="filters.folder_id"
                    @close="isUploaderOpen = false"
                    @upload="fetchData"
                    class="mb-8"
                />

                <div v-if="loading" class="absolute inset-0 bg-base-100/50 backdrop-blur-[2px] z-10 flex items-center justify-center">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>

                <Browser 
                    :view-mode="viewMode"
                    :subfolders="subfolders"
                    :media="media.data"
                    :selected-folder-ids="[]"
                    :selected-media-ids="[]"
                    @folder-click="handleFolderClick"
                    @selection-toggle="handleSelect"
                    @preview="(item) => store.select(normalizeSelectedItem(item))"
                />
            </div>

            <!-- Footer -->
            <div class="px-8 py-4 border-t border-base-content/5 bg-base-200/30 flex justify-end items-center gap-3">
                <p class="text-[10px] uppercase font-bold opacity-30 tracking-widest mr-auto">Kliknij dwukrotnie lub wybierz podgląd, aby zatwierdzić</p>
                <button @click="close" class="btn btn-sm btn-ghost font-bold uppercase tracking-widest">Anuluj</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.1);
}

[data-theme='dark'] .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
}
[data-theme='dark'] .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}
</style>
