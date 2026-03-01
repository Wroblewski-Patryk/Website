<script setup>
import { Head } from '@inertiajs/vue3';
import DynamicBlock from '@/Components/DynamicBlock.vue';

defineProps({
    page: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <Head :title="page ? page.title : 'Welcome'" />
    <div class="min-h-screen bg-white text-gray-900 font-sans antialiased overflow-x-hidden">
        <template v-if="page && page.content">
            <DynamicBlock 
                v-for="(block, index) in page.content" 
                :key="index"
                :block="block" 
            />
        </template>
        
        <div v-else-if="!page" class="flex flex-col items-center justify-center min-h-screen bg-gray-50 p-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Page Not Found</h1>
            <p class="text-gray-600 mb-8">The requested page could not be found or has no content.</p>
            <a href="/admin" class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition">Go to Admin Panel</a>
        </div>
        
        <div v-else class="flex flex-col items-center justify-center py-32 p-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ page.title }}</h1>
            <p class="text-gray-600 mb-8">This page has no content blocks yet. Add them in the Admin Panel.</p>
            <a href="/admin" class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition">Edit This Page</a>
        </div>
    </div>
</template>
