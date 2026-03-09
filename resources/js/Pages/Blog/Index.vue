<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import moment from 'moment';
import { useTranslations } from '@/Composables/useTranslations';


const props = defineProps({
    posts: Object,
    page: Object,
    seo: Object,
});

const { t } = useTranslations();
</script>

<template>
    <SeoHead v-if="seo" v-bind="seo" />

    <AppLayout :page="page">
        <!-- Render Dynamic Blocks from the Page (if linked) -->
        <div v-if="page && page.content && page.content.length" class="page-blocks">
            <DynamicBlock 
                v-for="block in page.content" 
                :key="block.id" 
                :block="block" 
            />
        </div>

        <div v-else class="relative z-20 max-w-7xl mx-auto px-4 py-32 text-white">
            <h1 class="text-5xl md:text-7xl font-black mb-16 text-center uppercase tracking-tight">Blog</h1>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 pb-32">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <Link v-for="post in posts.data" :key="post.id" :href="`/blog/${t(post.slug)}`" class="block group">
                    <div class="overflow-hidden rounded-xl mb-6 relative">
                        <img v-if="t(post.featured_image)" :src="`/storage/${t(post.featured_image)}`" class="w-full h-64 object-cover filter grayscale group-hover:grayscale-0 transition-transform duration-700 group-hover:scale-105" />
                        <div v-else class="w-full h-64 bg-gray-900 border border-white/10 flex items-center justify-center">
                            <span class="text-white/30 uppercase tracking-widest text-sm">No Image</span>
                        </div>
                    </div>
                    <div class="flex items-center text-sm font-bold tracking-widest uppercase text-white/60 mb-3">
                        <span>{{ post.published_at ? moment(post.published_at).format('MMM D, YYYY') : 'Not Published' }}</span>
                    </div>
                    <h2 class="text-2xl font-bold mb-3 group-hover:text-gray-300 transition-colors">{{ t(post.title) }}</h2>
                    <p class="text-gray-400 line-clamp-3 leading-relaxed">{{ t(post.excerpt) || (post.content ? 'Content available' : '') }}</p>
                </Link>
            </div>

            <!-- Pagination (simplified logic for styling) -->
            <div class="mt-20 flex justify-center gap-4" v-if="posts.links && posts.links.length > 3">
                <template v-for="(link, i) in posts.links" :key="i">
                    <Link 
                        v-if="link.url"
                        :href="link.url" 
                        class="px-5 py-3 border font-bold text-sm tracking-widest transition"
                        :class="link.active ? 'bg-white text-black border-white' : 'border-white/20 text-white/50 hover:border-white hover:text-white'"
                        v-html="link.label"
                    />
                    <span v-else class="px-5 py-3 border border-white/5 text-white/20 font-bold text-sm" v-html="link.label"></span>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
