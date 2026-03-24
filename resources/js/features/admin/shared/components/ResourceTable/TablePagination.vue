<script setup>
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';
import { sanitizeHtml } from '@/Utils/sanitizeHtml';

const { t } = useTranslations();

const props = defineProps({
    resources: {
        type: Object,
        required: true
    },
    // Optional filters to preserve state
    filters: {
        type: Object,
        default: () => ({})
    }
});

const cleanPaginationLabel = (label) => {
    if (!label) return '';
    
    let cleaned = label;
    if (cleaned.includes('Previous')) {
        cleaned = cleaned.replace('Previous', t('admin.common.previous', 'Previous'));
    }
    if (cleaned.includes('Next')) {
        cleaned = cleaned.replace('Next', t('admin.common.next', 'Next'));
    }
    
    return cleaned;
};

const safeHtml = (value) => sanitizeHtml(cleanPaginationLabel(value));
</script>

<template>
    <div class="p-4 bg-base-200/30 border-t border-base-300 flex items-center justify-between">
        <div class="text-xs opacity-40 font-medium">
            {{ t('admin.common.showing', 'Showing') }} {{ resources.from || 0 }} {{ t('admin.common.to', 'to') }} {{ resources.to || 0 }} {{ t('admin.common.of', 'of') }} {{ resources.total }} {{ t('admin.common.entries', 'entries') }}
        </div>
        
        <div class="join shadow-sm rounded-xl overflow-hidden border border-white/5" v-if="resources.links && resources.links.length > 3">
            <Link 
                v-for="(link, k) in resources.links" 
                :key="k" 
                :href="link.url || '#'" 
                class="join-item btn btn-xs h-10 px-4 min-w-[40px] bg-base-100 border-base-300 hover:bg-base-200 transition-all font-bold"
                :class="{'btn-active bg-primary/10 text-primary border-primary/20': link.active, 'btn-disabled opacity-30': !link.url}"
                :data-replace="link.url ? true : null"
                :preserve-state="true"
                :preserve-scroll="true"
                :data="filters"
                v-html="safeHtml(link.label)" 
            />
        </div>
    </div>
</template>

<style scoped>
.join-item:first-child { border-left: none; }
.join-item:last-child { border-right: none; }
</style>
