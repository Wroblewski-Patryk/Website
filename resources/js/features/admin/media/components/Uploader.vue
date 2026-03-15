<script setup>
import { ref } from 'vue';
import { PhUpload, PhX } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    isOpen: Boolean,
    processing: Boolean,
    currentFolderId: [Number, String]
});

const emit = defineEmits(['close', 'upload']);

const fileInput = ref(null);
const files = ref([]);
const altText = ref('');

const handleFileSelect = (e) => {
    files.value = Array.from(e.target.files);
};

const handleDrop = (e) => {
    files.value = Array.from(e.dataTransfer.files);
    submit();
};

const submit = () => {
    if (files.value.length === 0) return;
    emit('upload', {
        files: files.value,
        alt_text: altText.value,
        folder_id: props.currentFolderId
    });
    // Reset after emit (or let parent handle it)
};

const clearFiles = () => {
    files.value = [];
    if (fileInput.value) fileInput.value.value = '';
};

defineExpose({ clearFiles, altText });
</script>

<template>
    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
    >
        <div v-if="isOpen" class="bg-base-100 rounded-box border-2 border-dashed border-base-300 p-8 shadow-sm flex flex-col items-center gap-4 relative group hover:border-primary transition-all duration-300">
            <button @click="$emit('close')" class="btn btn-ghost btn-xs btn-square absolute right-4 top-4"><PhX class="w-4 h-4" /></button>
            
            <div 
                class="flex flex-col items-center gap-3 w-full cursor-pointer"
                @dragover.prevent=""
                @drop.prevent="handleDrop"
                @click="fileInput.click()"
            >
                <div class="w-20 h-20 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 group-hover:scale-110 transition-transform duration-500">
                    <PhUpload weight="bold" class="w-10 h-10" />
                </div>
                <div class="text-center">
                    <h3 class="text-xl font-bold">{{ t('admin.media.drop_files_title', 'Drag files or click to browse') }}</h3>
                    <p class="text-sm opacity-50">{{ t('admin.media.drop_files_desc', 'You can select multiple files at once') }}</p>
                </div>
            </div>
            
            <form @submit.prevent="submit" class="flex flex-col items-center gap-4 w-full max-w-lg mt-2">
                <input 
                    type="file" 
                    multiple
                    ref="fileInput"
                    @change="handleFileSelect" 
                    class="file-input file-input-primary file-input-bordered w-full rounded-2xl" 
                />
                
                <div v-if="files.length > 0" class="flex flex-col items-center gap-4 w-full">
                    <div class="text-xs font-bold uppercase tracking-widest opacity-50">
                        {{ t('admin.media.files_selected', 'Selected {count} file(s)', { count: files.length }) }}
                    </div>
                    
                    <div class="flex gap-2 w-full">
                        <input v-model="altText" type="text" :placeholder="t('admin.media.global_alt_placeholder', 'Global alt text (for all)')" class="input input-bordered flex-1 focus:input-primary" />
                        <button type="submit" class="btn btn-primary px-8 shadow-lg shadow-primary/20" :disabled="processing">
                            <span v-if="processing" class="loading loading-spinner"></span>
                            {{ t('admin.media.upload_now', 'Upload now') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </transition>
</template>
