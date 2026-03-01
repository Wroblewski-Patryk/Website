<template>
  <Head>
    <title>{{ finalTitle }}</title>
    <meta v-if="description" name="description" :content="description" />
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:title" :content="finalTitle" />
    <meta v-if="description" property="og:description" :content="description" />
    <meta v-if="finalImageUrl" property="og:image" :content="finalImageUrl" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:title" :content="finalTitle" />
    <meta v-if="description" property="twitter:description" :content="description" />
    <meta v-if="finalImageUrl" property="twitter:image" :content="finalImageUrl" />
    
    <slot />
  </Head>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  title: String,
  description: String,
  image: String,
})

const finalTitle = computed(() => {
  return props.title ? `${props.title} - ${import.meta.env.VITE_APP_NAME || 'Portfolio'}` : (import.meta.env.VITE_APP_NAME || 'Portfolio')
})

const finalImageUrl = computed(() => {
  if (!props.image) return null;
  // If it's already an absolute URL, return it
  if (props.image.startsWith('http')) return props.image;
  // Otherwise, assume it's a relative storage path (Filament default)
  return `/storage/${props.image}`;
})
</script>
