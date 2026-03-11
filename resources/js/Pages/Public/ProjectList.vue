<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { PhImage } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    projects: Array,
    settings: Object,
    seo: Object,
});

const { t } = useTranslations();
</script>

<template>
    <SeoHead v-if="seo" v-bind="seo" />
    <AppLayout :settings="settings">
        
        <div class="max-w-7xl mx-auto px-6 py-24">
            <h1 class="text-6xl font-black italic uppercase tracking-tighter mb-12">Latest Projects</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div v-for="project in projects" :key="project.id" class="group relative overflow-hidden rounded-3xl bg-base-200 aspect-[4/3]">
                    <img v-if="t(project.image)" :src="t(project.image)" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                    <div v-else class="w-full h-full flex items-center justify-center bg-primary/5 text-primary">
                        <PhImage weight="thin" class="w-16 h-16 opacity-40" />
                    </div>
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-8">
                        <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                            <span class="text-xs font-bold uppercase tracking-widest text-primary mb-2 block">{{ t(project.category) }}</span>
                            <h2 class="text-3xl font-black text-white italic uppercase mb-4">{{ t(project.title) }}</h2>
                            <Link :href="`/${$page.props.archive_slugs.projects}/${t(project.slug)}`" class="btn btn-primary rounded-xl">View Details</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}
</style>
