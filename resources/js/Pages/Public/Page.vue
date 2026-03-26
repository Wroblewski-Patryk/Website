<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
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
    coming_soon_countdown_to: {
        type: String,
        default: null,
    },
});

const now = ref(Date.now());
let timerId = null;

const countdown = computed(() => {
    if (!props.coming_soon_countdown_to) {
        return null;
    }

    const targetTime = new Date(props.coming_soon_countdown_to).getTime();
    if (!Number.isFinite(targetTime)) {
        return null;
    }

    const diff = Math.max(0, targetTime - now.value);
    return {
        days: Math.floor(diff / 86400000),
        hours: Math.floor((diff % 86400000) / 3600000),
        minutes: Math.floor((diff % 3600000) / 60000),
        seconds: Math.floor((diff % 60000) / 1000),
    };
});

onMounted(() => {
    if (!props.coming_soon_countdown_to) return;
    timerId = window.setInterval(() => {
        now.value = Date.now();
    }, 1000);
});

onUnmounted(() => {
    if (timerId) {
        window.clearInterval(timerId);
    }
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
        <div v-if="countdown" class="sticky top-0 z-40 border-b border-base-content/10 bg-base-100/90 backdrop-blur">
            <div class="mx-auto max-w-4xl px-4 py-3">
                <div class="flex items-center justify-center gap-3 text-xs uppercase tracking-widest font-bold">
                    <span>Coming Soon</span>
                    <span class="opacity-50">{{ countdown.days }}d {{ countdown.hours }}h {{ countdown.minutes }}m {{ countdown.seconds }}s</span>
                </div>
            </div>
        </div>

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
