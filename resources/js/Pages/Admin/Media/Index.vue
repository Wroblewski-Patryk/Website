<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps(['media']);

const uploadForm = useForm({
    file: null,
    alt_text: ''
});

const deleteForm = useForm({});
const fileInput = ref(null);

function submitUpload() {
    uploadForm.post('/admin/media', {
        onSuccess: () => {
            uploadForm.reset();
            if (fileInput.value) fileInput.value.value = '';
        }
    });
}

function handleFileChange(event) {
    uploadForm.file = event.target.files[0];
}

function deleteMedia(id) {
    if (confirm('Are you sure you want to delete this media file?')) {
        deleteForm.delete(`/admin/media/${id}`);
    }
}
function copyUrl(path) {
    const url = window.location.origin + '/storage/' + path;
    navigator.clipboard.writeText(url);
    alert('URL copied to clipboard!');
}
</script>

<template>
    <Head title="Media Library" />
    <AdminLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-primary flex items-center gap-3">
                        <i class="fas fa-photo-video"></i>
                        Media Library
                    </h1>
                    <p class="text-sm opacity-60 mt-1">Manage your high-resolution assets</p>
                </div>
                
                <form @submit.prevent="submitUpload" class="flex flex-wrap items-center gap-3">
                    <input type="file" @change="handleFileChange" class="file-input file-input-primary file-input-bordered file-input-sm w-full max-w-xs" accept="image/*" required ref="fileInput" />
                    <input type="text" v-model="uploadForm.alt_text" placeholder="Alt Text" class="input input-bordered input-sm focus:input-primary" />
                    <button type="submit" class="btn btn-primary btn-sm px-6 shadow-md shadow-primary/20" :disabled="uploadForm.processing">
                        <span v-if="uploadForm.processing" class="loading loading-spinner loading-xs"></span>
                        Upload
                    </button>
                </form>
            </div>
        </template>

        <div class="p-0">
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                <div v-for="item in media.data" :key="item.id" 
                     class="group relative aspect-square bg-base-300 rounded-box overflow-hidden border border-white/5 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
                    
                    <img :src="'/storage/' + item.path" :alt="item.alt_text" class="object-cover w-full h-full grayscale group-hover:grayscale-0 transition-all duration-700 scale-100 group-hover:scale-110" />
                    
                    <!-- Overlay with Glassmorphism -->
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center gap-3">
                        <div class="flex gap-2">
                             <button @click="deleteMedia(item.id)" class="btn btn-error btn-circle btn-sm shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                            <button @click="copyUrl(item.path)" class="btn btn-primary btn-circle btn-sm shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            </button>
                        </div>
                        <span class="text-[10px] text-white/70 font-mono px-2 truncate w-full text-center">{{ item.path }}</span>
                    </div>
                </div>

                <div v-if="media.data.length === 0" class="col-span-full py-24 text-center bg-base-100/30 backdrop-blur-lg border-2 border-dashed border-base-content/10 rounded-box animate-pulse">
                    <p class="text-xl font-medium opacity-40 italic">Your gallery is waiting for its first masterpiece...</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
