<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DynamicBlock from '@/features/admin/block-builder/components/DynamicBlock.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    project: Object,
    settings: Object,
    seo: Object,
    header: Object,
    footer: Object,
    sidebar: Object,
    page_template: Object,
});

const { t } = useTranslations();
</script>

<template>
    <SeoHead v-if="seo" v-bind="seo" />
    <AppLayout 
        :settings="props.settings" 
        :page="props.project" 
        :header="props.header" 
        :footer="props.footer" 
        :sidebar="props.sidebar" 
        :page_template="props.page_template"
    >
        
        <div class="max-w-7xl mx-auto px-6 py-24">
            <div class="mb-12">
                <span class="text-sm font-bold uppercase tracking-widest text-primary mb-4 block">{{ t(project.category) }}</span>
                <h1 class="text-6xl md:text-8xl font-black italic uppercase tracking-tighter mb-8 leading-none">
                    {{ t(project.title) }}
                </h1>
                <p v-if="t(project.description)" class="text-xl opacity-60 max-w-2xl leading-relaxed">
                    {{ t(project.description) }}
                </p>
            </div>

            <div v-if="project.content" class="project-content space-y-8">
                <DynamicBlock 
                    v-for="block in project.content" 
                    :key="block.id" 
                    :block="block" 
                />
            </div>
            
            <div v-if="!project.content && t(project.image)" class="rounded-3xl overflow-hidden shadow-2xl">
                <img :src="t(project.image)" class="w-full" />
            </div>
        </div>
    </AppLayout>
</template>
