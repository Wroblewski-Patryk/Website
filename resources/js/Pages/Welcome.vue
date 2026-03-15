<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import DynamicBlock from '@/features/admin/block-builder/components/DynamicBlock.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    page: {
        type: Object,
        default: null,
    },
});

const { t } = useTranslations();

const blocks = computed(() => {
    if (!props.page?.content) return [];
    // Handle translatable content object or simple array
    return t(props.page.content) || [];
});
</script>

<template>
    <SeoHead 
        :title="t(page?.meta_title) || t(page?.title) || 'Welcome'" 
        :description="t(page?.meta_description)" 
        :image="t(page?.og_image)" 
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
            <h1 class="text-4xl font-bold mb-4">{{ t('errors.page_not_found') }}</h1>
            <p class="text-white/60 mb-8 max-w-lg text-center">{{ t('errors.page_not_selected_desc') }}</p>
            <a :href="route('admin.dashboard.index')" class="px-6 py-3 border border-white hover:bg-white hover:text-black font-bold tracking-widest uppercase transition-colors">
                {{ t('nav.go_to_admin_panel') }}
            </a>
        </div>
        
        <div v-else class="flex flex-col items-center justify-center min-h-[60vh] py-32 p-8 text-white relative z-20">
            <h1 class="text-4xl font-bold mb-4 uppercase tracking-wider">{{ t(page.title) }}</h1>
            <p class="text-white/60 mb-8 max-w-lg text-center">{{ t('errors.no_blocks_desc') }}</p>
            <a :href="route('admin.pages.edit', page.id)" class="px-6 py-3 border border-white hover:bg-white hover:text-black font-bold tracking-widest uppercase transition-colors">
                {{ t('common.edit_this_page') }}
            </a>
        </div>
    </AppLayout>
</template>
