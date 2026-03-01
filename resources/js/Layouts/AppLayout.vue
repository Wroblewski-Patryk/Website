<template>
  <div class="relative min-h-screen" :style="cssVariables">
    <!-- Legacy background structure -->
    <div id="color" class="fixed inset-0 z-[-3]"></div>
    <div id="image" class="fixed inset-0 z-[-2]">
        <div id="image-1" class="absolute inset-0"></div>
        <div id="image-2" class="absolute inset-0"></div>
        <div id="image-3" class="absolute inset-0"></div>
    </div>
    <div id="border" class="fixed inset-0 pointer-events-none z-50 transition-all duration-300"></div>

    <!-- Header Wrapper -->
    <header v-if="activeHeader" class="relative">
      <div class="fixed top-6 right-6 z-[60]">
        <LanguageSwitcher />
      </div>
      <DynamicBlock 
        v-for="(block, index) in activeHeader" 
        :key="'header-'+index" 
        :block="block" 
      />
    </header>

    <!-- Main Content -->
    <main>
      <slot />
    </main>

    <!-- Footer Wrapper -->
    <footer v-if="activeFooter">
      <DynamicBlock 
        v-for="(block, index) in activeFooter" 
        :key="'footer-'+index" 
        :block="block" 
      />
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import DynamicBlock from '@/Components/DynamicBlock.vue'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'

const pageProps = usePage().props;

const activeHeader = computed(() => {
  if (pageProps.page?.header_override?.content) return pageProps.page.header_override.content;
  return pageProps.settings?.default_header_content; // Fallback or global header
});

const activeFooter = computed(() => {
  if (pageProps.page?.footer_override?.content) return pageProps.page.footer_override.content;
  return pageProps.settings?.default_footer_content;
});

const cssVariables = computed(() => {
  const brand = pageProps.settings?.brand_colors || {};
  const fonts = pageProps.settings?.brand_fonts || {};
  return {
    '--p': brand.primary || '#4f46e5',
    '--s': brand.secondary || '#10b981',
    '--a': brand.accent || '#f59e0b',
    '--font-heading': fonts.heading ? `"${fonts.heading}", sans-serif` : 'inherit',
    '--font-body': fonts.body ? `"${fonts.body}", sans-serif` : 'inherit',
  };
});
</script>

<style>
/* Base port of !oldCode animated background styles */
#color {
  background-color: var(--color-primary); /* Uses Global Brand Setting */
}
#border {
  border: 4px solid rgba(255,255,255,0.1);
  margin: 10px;
  /* Adjust based on screen size, matching legacy */
}

@media (min-width: 768px) {
  #border {
    border-width: 8px;
    margin: 20px;
  }
}
</style>
