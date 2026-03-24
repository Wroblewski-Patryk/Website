<template>
    <div v-if="activeBlock" class="p-6 space-y-6">
        <component 
            :is="settingsComponent" 
            v-if="settingsComponent"
            v-model="proxiedContent"
            :type="activeBlock.type"
            :templates="templates"
            :mode="mode"
        />
        <div v-else class="text-center py-10 opacity-30 italic text-xs">
            {{ t('admin.builder.no_specific_settings', 'No specific settings for {type} yet.').replace('{type}', activeBlock.type) }}
        </div>
    </div>
</template>

<script setup>
import { computed, defineAsyncComponent } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { usePage } from '@inertiajs/vue3';

const { t } = useTranslations();
const store = useBlockBuilderStore();
const page = usePage();
const defaultLocale = computed(() => {
    return page.props.default_locale
        || page.props.languages?.find?.(lang => lang?.is_default)?.code
        || page.props.languages?.[0]?.code
        || 'en';
});

const props = defineProps({
    activeBlock: Object,
    mode: {
        type: String, // 'content' or 'advanced'
        default: 'content'
    },
    templates: {
        type: Array,
        default: () => []
    }
});

/**
 * Global (Non-localized) Content Proxy
 * This proxy handles settings that are shared across all languages.
 */
const globalContent = computed(() => {
    if (!props.activeBlock) return {};
    
    return new Proxy(props.activeBlock.content, {
        get(target, key) {
            const val = target[key];
            const locale = store.editingLocale;

            // If it's a translatable object, return the current locale value BUT setters will overwrite the whole object
            // or we might want to return the raw object if we're editing at a technical level.
            // For Advanced tab, if a value is localzed, we probably shouldn't be here, but if we are,
            // we show the current locale's value.
            if (val && typeof val === 'object' && !Array.isArray(val) && Object.keys(val).some(k => store.availableLocales.some(al => al.code === k))) {
                return val[locale] !== undefined ? val[locale] : '';
            }
            return val;
        },
        set(target, key, value) {
            // In global mode, we overwrite the key completely, making it shared (non-localized)
            target[key] = value;
            store.isDirty = true;
            return true;
        }
    });
});

/**
 * Localized Content Proxy
 * This proxy wraps the activeBlock.content and handles the translation logic
 * transparently for the specific settings components.
 */
const localizedContent = computed(() => {
    if (!props.activeBlock) return {};
    
    return new Proxy(props.activeBlock.content, {
        get(target, key) {
            const val = target[key];
            const locale = store.editingLocale;

            // If it's a translatable object, return the specific locale
            if (val && typeof val === 'object' && !Array.isArray(val) && Object.keys(val).some(k => store.availableLocales.some(al => al.code === k))) {
                return val[locale] !== undefined ? val[locale] : '';
            }
            return val;
        },
        set(target, key, value) {
            const locale = store.editingLocale;
            let val = target[key];

            // For now, we only translate strings (this covers most content like text, links, etc.)
            if (typeof value === 'string') {
                const isTranslatableObject = val && typeof val === 'object' && !Array.isArray(val) && Object.keys(val).some(k => store.availableLocales.some(al => al.code === k));

                if (isTranslatableObject) {
                    target[key][locale] = value;
                } else {
                    // Convert from string to object if we are in a non-default locale OR if another translation exists
                    const newVal = {};
                    
                    // If the old value was a string, we assume it's for the default locale
                    if (typeof val === 'string' && locale !== defaultLocale.value) {
                        newVal[defaultLocale.value] = val;
                    }
                    
                    newVal[locale] = value;
                    target[key] = newVal;
                }
            } else {
                // Non-string values (booleans, numbers, etc.) are shared across all languages
                target[key] = value;
            }
            store.isDirty = true;
            return true;
        }
    });
});

const proxiedContent = computed(() => props.mode === 'advanced' ? globalContent.value : localizedContent.value);


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

const settingsLoaders = {
    HeadingSettings: () => import('./Settings/HeadingSettings.vue'),
    ParagraphSettings: () => import('./Settings/ParagraphSettings.vue'),
    ListSettings: () => import('./Settings/ListSettings.vue'),
    QuoteSettings: () => import('./Settings/QuoteSettings.vue'),
    ImageSettings: () => import('./Settings/ImageSettings.vue'),
    VideoSettings: () => import('./Settings/VideoSettings.vue'),
    GallerySettings: () => import('./Settings/GallerySettings.vue'),
    CarouselSettings: () => import('./Settings/CarouselSettings.vue'),
    TemplateRefSettings: () => import('./Settings/TemplateRefSettings.vue'),
    ContentSlotSettings: () => import('./Settings/ContentSlotSettings.vue'),
    ContainerSettings: () => import('./Settings/ContainerSettings.vue'),
    SpacerDividerSettings: () => import('./Settings/SpacerDividerSettings.vue'),
    TableSettings: () => import('./Settings/TableSettings.vue'),
    MockupSettings: () => import('./Settings/MockupSettings.vue'),
    FormInputSettings: () => import('./Settings/FormInputSettings.vue'),
    DisplaySettings: () => import('./Settings/DisplaySettings.vue'),
    NavigationSettings: () => import('./Settings/NavigationSettings.vue'),
    AppSpecificSettings: () => import('./Settings/AppSpecificSettings.vue'),
    CustomCodeSettings: () => import('./Settings/CustomCodeSettings.vue'),
};

const settingsComponent = computed(() => {
    if (!props.activeBlock) return null;
    const componentName = settingsMap[props.activeBlock.type];
    if (!componentName) return null;

    const load = settingsLoaders[componentName];
    return load ? defineAsyncComponent(load) : null;
});
</script>
