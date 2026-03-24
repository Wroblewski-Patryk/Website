<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DynamicBlock from '@/features/admin/block-builder/components/DynamicBlock.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    page: Object,
    settings: Object,
    seo: Object,
    header: Object,
    footer: Object,
    sidebar: Object,
    page_template: Object,
});

const blocks = computed(() => {
    if (!props.page?.content) return [];
    // Handle old format: { pl: [], en: [] }
    if (!Array.isArray(props.page.content) && typeof props.page.content === 'object') {
        const page = usePage();
        const locale = page.props.locale
            || page.props.default_locale
            || page.props.languages?.find?.(lang => lang?.is_default)?.code
            || page.props.languages?.[0]?.code
            || 'en';
        return props.page.content[locale] || Object.values(props.page.content)[0] || [];
    }
    return Array.isArray(props.page.content) ? props.page.content : [];
});
</script>

<template>
    <SeoHead v-if="seo" v-bind="seo" />
    <AppLayout 
        :settings="props.settings" 
        :page="props.page" 
        :header="props.header" 
        :footer="props.footer" 
        :sidebar="props.sidebar" 
        :page_template="props.page_template"
    >

        <div v-if="blocks.length > 0" class="page-content">
            <DynamicBlock 
                v-for="block in blocks" 
                :key="block.id" 
                :block="block" 
            />
        </div>
        <div v-else class="py-20 text-center text-base-content/50">
            <p>This page has no content yet.</p>
        </div>
    </AppLayout>
</template>
