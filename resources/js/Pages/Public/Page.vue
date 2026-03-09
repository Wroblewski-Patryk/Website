<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    page: Object,
    settings: Object,
    seo: Object,
});
</script>

<template>
    <SeoHead v-if="seo" v-bind="seo" />
    <AppLayout :settings="settings">

        <div v-if="page.content" class="page-content">
            <DynamicBlock 
                v-for="block in page.content" 
                :key="block.id" 
                :block="block" 
            />
        </div>
        <div v-else class="py-20 text-center text-base-content/50">
            <p>This page has no content yet.</p>
        </div>
    </AppLayout>
</template>
