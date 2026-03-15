<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import DynamicBlock from '@/features/admin/block-builder/components/DynamicBlock.vue';
import { PhArrowLeft } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useFormatter } from '@/Composables/useFormatter';

const props = defineProps({
    post: Object,
    seo: Object,
    header: Object,
    footer: Object,
    page_template: Object,
});

const { t } = useTranslations();
const { formatDate } = useFormatter();
</script>

<template>
    <SeoHead v-if="seo" v-bind="seo" />

    <AppLayout 
        :header="props.header" 
        :footer="props.footer" 
        :page_template="props.page_template" 
        :page="props.post"
    >
        <article v-if="post" class="relative z-20 max-w-4xl mx-auto px-4 py-32 text-white">
            <!-- Back button -->
            <Link :href="`/${$page.props.archive_slugs.blog}`" class="inline-flex items-center text-sm font-bold tracking-widest uppercase text-white/50 hover:text-white transition-colors mb-12">
                <PhArrowLeft weight="bold" class="w-4 h-4 mr-3" /> Back to Blog
            </Link>

            <!-- Header -->
            <header class="mb-16">
                <h1 class="text-5xl md:text-7xl font-black mb-6 uppercase tracking-tight leading-none">{{ t(post.title) }}</h1>
                <div class="flex items-center text-sm font-bold tracking-widest uppercase text-white/60 mb-10">
                    <span>{{ post.published_at ? formatDate(post.published_at) : 'Not Published' }}</span>
                </div>
                
                <div v-if="t(post.featured_image)" class="w-full h-80 md:h-[500px] overflow-hidden rounded-xl mb-12 border border-white/10">
                    <img :src="`/storage/${t(post.featured_image)}`" class="w-full h-full object-cover filter grayscale hover:grayscale-0 transition duration-1000" />
                </div>
            </header>

            <!-- Content -->
            <div class="page-blocks">
                <DynamicBlock 
                    v-for="block in post.content" 
                    :key="block.id" 
                    :block="block" 
                />
            </div>
        </article>
    </AppLayout>
</template>

<style>
/* Adjust RichEditor injected styles to fit the dark theme */
.prose img {
    border-radius: 0.75rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
.prose a {
    color: white;
    text-decoration-color: rgba(255,255,255,0.3);
    text-underline-offset: 4px;
    transition: text-decoration-color 0.2s;
}
.prose a:hover {
    text-decoration-color: white;
}
</style>
