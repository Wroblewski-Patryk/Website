<template>
  <ThemeStyleProvider />
  <div class="relative bg-base-100 min-h-screen flex flex-col mx-auto w-full overflow-x-hidden shadow-2xl">
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
import { computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import DynamicBlock from '@/Components/DynamicBlock.vue'
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'
import ThemeStyleProvider from '@/Components/ThemeStyleProvider.vue'
import { useTranslations } from '@/Composables/useTranslations'

const { t } = useTranslations();

const props = defineProps({
  settings: {
    type: Object,
    default: () => ({})
  },
  page: {
    type: Object,
    default: () => null
  }
});

const pageProps = usePage().props;

const activeHeader = computed(() => {
  if (pageProps.page?.header_override?.content) return pageProps.page.header_override.content;
  return pageProps.settings?.default_header_content; // Fallback or global header
});

const activeFooter = computed(() => {
  if (pageProps.page?.footer_override?.content) return pageProps.page.footer_override.content;
  return pageProps.settings?.default_footer_content;
});



onMounted(() => {
  // Reset the theme to light for the public frontend 
  // so that the admin panel theme (if any) doesn't bleed through
  document.documentElement.setAttribute('data-theme', 'light');
});
</script>
