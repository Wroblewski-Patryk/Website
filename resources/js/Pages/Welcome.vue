<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    page: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <SeoHead 
        :title="page?.meta_title || page?.title || 'Welcome'" 
        :description="page?.meta_description" 
        :image="page?.og_image" 
    />

    <AppLayout>
        <template v-if="page && page.content">
            <DynamicBlock 
                v-for="(block, index) in page.content" 
                :key="index"
                :block="block" 
            />
        </template>
        
        <div v-else-if="!page" class="flex flex-col items-center justify-center min-h-screen p-8 text-white relative z-20">
            <h1 class="text-4xl font-bold mb-4">Strona nie została znaleziona</h1>
            <p class="text-white/60 mb-8 max-w-lg text-center">Strona nie istnieje lub nie została jeszcze wybrana w Ustawieniach.</p>
            <a :href="route('dashboard.index')" class="px-6 py-3 border border-white hover:bg-white hover:text-black font-bold tracking-widest uppercase transition-colors">
                Przejdź do Panelu Admina
            </a>
        </div>
        
        <div v-else class="flex flex-col items-center justify-center min-h-[60vh] py-32 p-8 text-white relative z-20">
            <h1 class="text-4xl font-bold mb-4 uppercase tracking-wider">{{ page.title }}</h1>
            <p class="text-white/60 mb-8 max-w-lg text-center">Ta strona nie ma jeszcze żadnych bloków zawartości. Dodaj je w panelu administracyjnym używając Builder'a.</p>
            <a :href="route('dashboard.pages.edit', page.id)" class="px-6 py-3 border border-white hover:bg-white hover:text-black font-bold tracking-widest uppercase transition-colors">
                Edytuj tę stronę
            </a>
        </div>
    </AppLayout>
</template>
