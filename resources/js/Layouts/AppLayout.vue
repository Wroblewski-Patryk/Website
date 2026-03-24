<script setup>
import { computed, onMounted, provide } from 'vue'
import { usePage } from '@inertiajs/vue3'
import DynamicBlock from '@/features/admin/block-builder/components/DynamicBlock.vue'
import ThemeStyleProvider from '@/Components/ThemeStyleProvider.vue'
import { useTranslations } from '@/Composables/useTranslations'

const { t } = useTranslations();

const props = defineProps({
  page: {
    type: Object,
    default: () => ({ content: [] })
  },
  settings: Object,
  header: Object,
  footer: Object,
  sidebar: Object,
  page_template: Object,
});

const pageProps = usePage().props;
const fallbackLocale = computed(() => {
  return pageProps.default_locale
    || pageProps.languages?.find?.(lang => lang?.is_default)?.code
    || pageProps.languages?.[0]?.code
    || 'en';
});

// Compute winning content for each section (Page Override > Global Default)
const activeLocale = computed(() => pageProps.locale || fallbackLocale.value);

const activeHeaderContent = computed(() => {
  const content = props.header?.content || pageProps.header?.content || pageProps.settings?.default_header_content;
  if (content && typeof content === 'object' && !Array.isArray(content)) {
    return content[activeLocale.value] || content[fallbackLocale.value] || Object.values(content)[0] || [];
  }
  return content || [];
});

const activeFooterContent = computed(() => {
  const content = props.footer?.content || pageProps.footer?.content || pageProps.settings?.default_footer_content;
  if (content && typeof content === 'object' && !Array.isArray(content)) {
    return content[activeLocale.value] || content[fallbackLocale.value] || Object.values(content)[0] || [];
  }
  return content || [];
});

const activeSidebarContent = computed(() => {
  const content = props.sidebar?.content || pageProps.sidebar?.content || pageProps.settings?.default_sidebar_content;
  if (content && typeof content === 'object' && !Array.isArray(content)) {
    return content[activeLocale.value] || content[fallbackLocale.value] || Object.values(content)[0] || [];
  }
  return content || [];
});

// Provide the content to child blocks (slots)
provide('isEditor', false);
provide('mainContent', computed(() => {
  const content = props.page?.content;
  if (content && typeof content === 'object' && !Array.isArray(content)) {
    return content[activeLocale.value] || content[fallbackLocale.value] || Object.values(content)[0] || [];
  }
  return content || [];
}));
provide('headerContent', activeHeaderContent);
provide('footerContent', activeFooterContent);
provide('sidebarContent', activeSidebarContent);

onMounted(() => {
  // Reset the theme to light for the public frontend 
  document.documentElement.setAttribute('data-theme', 'light');
});
</script>

<template>
  <ThemeStyleProvider />

  <!-- Page Template Rendering -->
  <template v-if="page_template && page_template.content && page_template.content.length > 0">
    <div class="page-template-wrapper min-h-screen flex flex-col">
      <DynamicBlock 
        v-for="block in page_template.content" 
        :key="block.id" 
        :block="block" 
      />
    </div>
  </template>

  <!-- Default Layout Rendering (No template used) -->
  <div v-else class="flex flex-col min-h-screen">
    <header v-if="activeHeaderContent && activeHeaderContent.length > 0" class="sticky top-0 left-0 right-0 z-50">
      <DynamicBlock 
        v-for="block in activeHeaderContent" 
        :key="block.id" 
        :block="block" 
      />
    </header>

    <main class="flex-grow">
      <slot />
    </main>

    <footer v-if="activeFooterContent && activeFooterContent.length > 0" class="mt-auto">
      <DynamicBlock 
        v-for="block in activeFooterContent" 
        :key="block.id" 
        :block="block" 
      />
    </footer>
  </div>
</template>

<!-- Vite Rebuild Trigger -->
