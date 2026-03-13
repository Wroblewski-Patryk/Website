<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import DynamicBlock from '@/Components/DynamicBlock.vue';

const props = defineProps({
    page: {
        type: Object,
        default: null,
    },
});

const blocks = computed(() => {
    if (!props.page?.content) return [];
    // Handle old format: { pl: [], en: [] }
    if (!Array.isArray(props.page.content) && typeof props.page.content === 'object') {
        const locale = usePage().props.locale || 'pl';
        return props.page.content[locale] || props.page.content['pl'] || props.page.content['en'] || Object.values(props.page.content)[0] || [];
    }
    return Array.isArray(props.page.content) ? props.page.content : [];
});
</script>

<template>
    <SeoHead 
        :title="page?.meta_title || page?.title || 'Welcome'" 
        :description="page?.meta_description" 
        :image="page?.og_image" 
    />

    <AppLayout>
        <template v-if="blocks.length > 0">
            <DynamicBlock 
                v-for="(block, index) in blocks" 
                :key="index"
                :block="block" 
            />
        </template>
        
        <div v-else-if="!page" class="flex flex-col items-center justify-center min-h-screen p-8 text-white relative z-20">
            <h1 class="text-4xl font-bold mb-4">Strona nie została znaleziona</h1>
            <p class="text-white/60 mb-8 max-w-lg text-center">Strona nie istnieje lub nie została jeszcze wybrana w Ustawieniach.</p>
            <a :href="route('admin.dashboard.index')" class="px-6 py-3 border border-white hover:bg-white hover:text-black font-bold tracking-widest uppercase transition-colors">
                Przejdź do Panelu Admina
            </a>
        </div>
        
        <div v-else class="flex flex-col items-center justify-center min-h-[60vh] py-32 p-8 text-white relative z-20">
            <h1 class="text-4xl font-bold mb-4 uppercase tracking-wider">{{ page.title }}</h1>
            <p class="text-white/60 mb-8 max-w-lg text-center">Ta strona nie ma jeszcze żadnych bloków zawartości. Dodaj je w panelu administracyjnym używając Builder'a.</p>
            <a :href="route('admin.pages.edit', page.id)" class="px-6 py-3 border border-white hover:bg-white hover:text-black font-bold tracking-widest uppercase transition-colors">
                Edytuj tę stronę
            </a>
        </div>
    </AppLayout>
</template>
