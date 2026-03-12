<template>
    <div v-if="activeBlock" class="p-6 space-y-6">
        <component 
            :is="settingsComponent" 
            v-if="settingsComponent"
            v-model="activeBlock.content"
            :type="activeBlock.type"
            :templates="templates"
        />
        <div v-else class="text-center py-10 opacity-30 italic text-xs">
            {{ t('admin.builder.no_specific_settings', 'No specific settings for {type} yet.').replace('{type}', activeBlock.type) }}
        </div>
    </div>
</template>

<script setup>
import { computed, defineAsyncComponent } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    activeBlock: Object,
    templates: {
        type: Array,
        default: () => []
    }
});

const settingsMap = {
    heading: 'HeadingSettings',
    text: 'ParagraphSettings',
    paragraph: 'ParagraphSettings',
    list: 'ListSettings',
    quote: 'QuoteSettings',
    image: 'ImageSettings',
    video: 'VideoSettings',
    gallery: 'GallerySettings',
    carousel: 'CarouselSettings',
    template_reference: 'TemplateRefSettings',
    content_slot: 'ContentSlotSettings',
    container: 'ContainerSettings',
    spacer: 'SpacerDividerSettings',
    divider: 'SpacerDividerSettings',
    table: 'TableSettings',
    mockup_browser: 'MockupSettings',
    mockup_code: 'MockupSettings',
    mockup_phone: 'MockupSettings',
    mockup_window: 'MockupSettings',
    text_input: 'FormInputSettings',
    textarea: 'FormInputSettings',
    select: 'FormInputSettings',
    checkbox: 'FormInputSettings',
    radio: 'FormInputSettings',
    toggle: 'FormInputSettings',
    range: 'FormInputSettings',
    rating: 'FormInputSettings',
    file_input: 'FormInputSettings',
    card: 'DisplaySettings',
    stat: 'DisplaySettings',
    accordion: 'DisplaySettings',
    timeline: 'DisplaySettings',
    chat: 'DisplaySettings',
    countdown: 'DisplaySettings',
    diff: 'DisplaySettings',
    alert: 'DisplaySettings',
    progress: 'DisplaySettings',
    radial_progress: 'DisplaySettings',
    breadcrumbs: 'NavigationSettings',
    menu: 'NavigationSettings',
    steps: 'NavigationSettings',
    tabs: 'NavigationSettings',
    navbar: 'NavigationSettings',
    posts_list: 'AppSpecificSettings',
    projects_list: 'AppSpecificSettings',
    text_rotate: 'AppSpecificSettings',
    custom_code: 'CustomCodeSettings'
};

const settingsComponent = computed(() => {
    if (!props.activeBlock) return null;
    const componentName = settingsMap[props.activeBlock.type];
    if (!componentName) return null;
    
    // Using simple async import since they are in the same parent directory's /Settings
    return defineAsyncComponent(() => 
        import(`./Settings/${componentName}.vue`)
    );
});
</script>
