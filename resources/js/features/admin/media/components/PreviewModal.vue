<script setup>
import { ref, watch } from 'vue';
import { PhLink, PhX, PhCaretRight, PhTrash, PhImageSquare, PhFile } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const { t } = useTranslations();
const { formatDate } = useFormatter();

const props = defineProps({
    item: Object,
    isImage: Boolean,
    getFileIcon: Function,
    formatSize: Function
});

const emit = defineEmits(['close', 'copy-link', 'move', 'delete', 'update-alt', 'update-filename']);

const editAlt = ref('');
const editFilename = ref('');

watch(() => props.item, (newItem) => {
    if (newItem) {
        editAlt.value = newItem.alt_text || '';
        editFilename.value = newItem.path.split('/').pop().split('.').shift();
    }
}, { immediate: true });
</script>

<template>
    <dialog class="modal modal-open backdrop-blur-md" @click.self="$emit('close')">
        <div class="modal-box max-w-6xl p-0 overflow-hidden bg-transparent shadow-none border-0">
            <button @click="$emit('close')" class="btn btn-circle btn-sm absolute right-6 top-6 z-30 bg-base-100/50 hover:bg-base-100 border-0 shadow-xl">
                <PhX weight="bold" class="w-4 h-4" />
            </button>
            
            <div class="flex flex-col lg:flex-row bg-base-100 rounded-box overflow-hidden min-h-[600px] shadow-xl border border-base-300">
                <!-- Preview Area -->
                <div class="flex-1 bg-base-200/50 flex items-center justify-center p-8 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,#fff,rgba(255,255,255,0.6))] -z-10"></div>
                    
                    <img v-if="isImage" :src="'/storage/' + item.path" class="max-h-[75vh] w-auto shadow-lg rounded-lg transform transition-transform duration-700 group-hover:scale-[1.02]" />
                    <div v-else class="flex flex-col items-center gap-6 opacity-20">
                        <component :is="getFileIcon(item.mime)" weight="thin" class="w-64 h-64" />
                        <span class="text-3xl font-black tracking-[0.2em] uppercase">{{ item.mime }}</span>
                    </div>
                </div>
                
                <!-- Sidebar Info Area -->
                <div class="w-full lg:w-96 p-10 flex flex-col bg-base-100 border-l border-base-200 z-10">
                    <div class="flex-1 space-y-8 overflow-y-auto custom-scrollbar pr-4 -mr-4">
                        <div class="space-y-4">
                            <h3 class="text-2xl font-black tracking-tight leading-none break-all">{{ item.path.split('/').pop() }}</h3>
                            <div class="flex gap-2">
                                <span class="badge badge-primary badge-sm rounded-lg font-bold uppercase tracking-widest text-[9px]">{{ item.mime?.split('/')[1] || 'FILE' }}</span>
                                <span class="badge badge-ghost badge-sm rounded-lg font-bold uppercase tracking-widest text-[9px]">{{ formatSize(item.size) }}</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-base-200/50 p-4 rounded-xl border border-base-300/50">
                                <p class="text-[10px] uppercase font-black opacity-30 tracking-widest mb-1">{{ t('admin.common.created_at', 'Created At') }}</p>
                                <p class="text-xs font-bold">{{ formatDate(item.created_at) }}</p>
                            </div>
                            <div class="bg-base-200/50 p-4 rounded-xl border border-base-300/50">
                                <p class="text-[10px] uppercase font-black opacity-30 tracking-widest mb-1">{{ t('admin.media.dimensions', 'Dimensions') }}</p>
                                <p class="text-xs font-bold">{{ isImage ? t('admin.media.original', 'Original') : '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="form-control group">
                                <label class="label"><span class="label-text text-[10px] uppercase font-black opacity-30 tracking-widest">{{ t('admin.media.filename_seo', 'Filename (SEO)') }}</span></label>
                                <input 
                                    v-model="editFilename" 
                                    type="text"
                                    class="input input-bordered focus:input-primary bg-base-100 transition-all"
                                    :placeholder="t('admin.media.enter_new_filename', 'Enter new filename...')"
                                    @blur="$emit('update-filename', editFilename)"
                                />
                            </div>

                            <div class="form-control group">
                                <label class="label"><span class="label-text text-[10px] uppercase font-black opacity-30 tracking-widest">{{ t('admin.media.alt_text', 'Alternative Text (Alt)') }}</span></label>
                                <textarea 
                                    v-model="editAlt" 
                                    class="textarea textarea-bordered h-32 focus:textarea-primary bg-base-100 transition-all leading-relaxed" 
                                    :placeholder="t('admin.media.alt_desc_placeholder', 'Describe image for better SEO and accessibility...')"
                                    @blur="$emit('update-alt', editAlt)"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-10 flex flex-col gap-3">
                        <button @click="$emit('copy-link')" class="btn btn-primary h-14 gap-3 shadow-lg shadow-primary/20 transition-transform">
                            <PhLink weight="bold" class="w-5 h-5" /> {{ t('admin.media.copy_url', 'Copy URL') }}
                        </button>
                        <div class="flex gap-3">
                            <button @click="$emit('move')" class="btn btn-ghost flex-1 h-14 gap-3 bg-base-200/50 hover:bg-primary hover:text-white border-0 transition-all">
                                <PhCaretRight weight="bold" class="w-5 h-5" /> {{ t('admin.common.move', 'Move') }}
                            </button>
                            <button @click="$emit('delete')" class="btn btn-error btn-outline h-14 w-14 border-2 hover:bg-error hover:text-white transition-all">
                                <PhTrash weight="bold" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </dialog>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: hsl(var(--bc) / 0.1); border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: hsl(var(--bc) / 0.2); }
</style>
