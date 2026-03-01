<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import moment from 'moment';

defineProps({
    post: Object,
});
</script>

<template>
    <SeoHead 
        :title="post.meta_title || post.title" 
        :description="post.meta_description || post.excerpt"
        :image="post.og_image || post.featured_image"
    />

    <AppLayout>
        <article class="relative z-20 max-w-4xl mx-auto px-4 py-32 text-white">
            <!-- Back button -->
            <Link href="/blog" class="inline-flex items-center text-sm font-bold tracking-widest uppercase text-white/50 hover:text-white transition-colors mb-12">
                <i class="fa-solid fa-arrow-left mr-3"></i> Back to Blog
            </Link>

            <!-- Header -->
            <header class="mb-16">
                <h1 class="text-5xl md:text-7xl font-black mb-6 uppercase tracking-tight leading-none">{{ post.title }}</h1>
                <div class="flex items-center text-sm font-bold tracking-widest uppercase text-white/60 mb-10">
                    <span>{{ moment(post.published_at).format('MMM D, YYYY') }}</span>
                </div>
                
                <div v-if="post.featured_image" class="w-full h-80 md:h-[500px] overflow-hidden rounded-xl mb-12 border border-white/10">
                    <img :src="`/storage/${post.featured_image}`" class="w-full h-full object-cover filter grayscale hover:grayscale-0 transition duration-1000" />
                </div>
            </header>

            <!-- Content -->
            <div class="prose prose-xl prose-invert max-w-none">
                <div v-html="post.content"></div>
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
