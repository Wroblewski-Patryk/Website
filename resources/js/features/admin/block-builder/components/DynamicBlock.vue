<script setup>
import { ref, onMounted, computed, watch, inject, provide, reactive } from 'vue';
import { useGsapRuntime } from '@/Composables/useGsapRuntime';
import { useForm, usePage, Link } from '@inertiajs/vue3';
// Self-import removed to avoid Vite recursion issues. 
// Vue 3 naturally supports recursive components via their own name.
import draggable from 'vuedraggable';
import { PhArrowsOut, PhCopy, PhTrash, PhInfo, PhCheckCircle, PhSlidersHorizontal, PhPuzzlePiece, PhSquare, PhCube, PhLayout, PhCalendarBlank, PhUser } from '@phosphor-icons/vue';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { useTranslations } from '@/Composables/useTranslations';
import placeholderImg from '@/../images/placeholder.png';
import { useToastStore } from '@/Stores/useToastStore';
import moment from 'moment';

const props = defineProps(['block']);
const page = usePage();
const toast = useToastStore();
const store = useBlockBuilderStore();
const isEditor = inject('isEditor', false);

// Safe reactive injection handles both refs and plain values
const headerContent = computed(() => {
    const injectedVal = inject('headerContent', []);
    const value = (injectedVal && typeof injectedVal === 'object' && 'value' in injectedVal) ? injectedVal.value : injectedVal;
    return Array.isArray(value) ? value : [];
});
const footerContent = computed(() => {
    const injectedVal = inject('footerContent', []);
    const value = (injectedVal && typeof injectedVal === 'object' && 'value' in injectedVal) ? injectedVal.value : injectedVal;
    return Array.isArray(value) ? value : [];
});
const sidebarContent = computed(() => {
    const injectedVal = inject('sidebarContent', []);
    const value = (injectedVal && typeof injectedVal === 'object' && 'value' in injectedVal) ? injectedVal.value : injectedVal;
    return Array.isArray(value) ? value : [];
});
const mainContent = computed(() => {
    const injectedVal = inject('mainContent', []);
    const value = (injectedVal && typeof injectedVal === 'object' && 'value' in injectedVal) ? injectedVal.value : injectedVal;
    return Array.isArray(value) ? value : [];
});
const { t: originalT } = useTranslations();

/**
 * Enhanced translation helper for blocks.
 * Wraps useTranslations to respect editor's active locale.
 */
const t = (val, fallback = null) => {
    // If val is null/undefined or already a string (not a translation object)
    if (!val) return fallback || '';
    
    const locale = isEditor ? store.editingLocale : page.props.locale;
    return originalT(val, {}, fallback, locale);
};

/**
 * Recursively resolves translation objects in a content structure.
 */
const resolveContent = (content) => {
    if (!content) return content;

    // If it's a translation object { pl: '...', en: '...' }
    if (typeof content === 'object' && !Array.isArray(content) && (content.pl !== undefined || content.en !== undefined)) {
        return t(content);
    }

    // If it's an array
    if (Array.isArray(content)) {
        return content.map(item => resolveContent(item));
    }

    // If it's a plain object (not translation)
    if (typeof content === 'object' && content !== null) {
        const resolved = {};
        Object.keys(content).forEach(key => {
            // Avoid resolving internal settings or IDs if they overlap (rare)
            resolved[key] = resolveContent(content[key]);
        });
        return resolved;
    }

    return content;
};

const resolvedContent = computed(() => resolveContent(props.block.content));

const menuItems = computed(() => {
    if (props.block.type !== 'menu' || !props.block.content.menu_id) return [];
    const menu = page.props.menus?.find(m => m.id === props.block.content.menu_id);
    return menu?.items || [];
});

const blockRef = ref(null);
const { animateBlock } = useGsapRuntime();

const initAnimations = () => {
    if (props.block.settings?.animations?.enabled) {
        animateBlock(blockRef.value, props.block.settings.animations);
    }
};

onMounted(() => {
    initAnimations();
});

// Re-run animations if settings change in editor
watch(() => props.block.settings?.animations, () => {
    initAnimations();
}, { deep: true });

const displayedProjects = computed(() => {
    if (props.block.type !== 'portfolio') return [];
    if (props.block.content.use_projects_module) {
        return page.props.all_projects || [];
    }
    return props.block.content.projects || [];
});

const resolveMediaUrl = (value) => {
    if (!value || typeof value !== 'string') return value;
    if (/^https?:\/\//i.test(value)) return value;
    if (value.startsWith('/')) return value;
    return `/storage/${value.replace(/^\/+/, '')}`;
};

const styleObj = computed(() => {
    const s = props.block.settings || {};
    const l = s.layout || {};
    const st = s.style || {};
    
    // Get theme defaults for this specific block type
    const themeDefaults = page.props.theme_config?.block_defaults?.[props.block.type] || {};

    let fillStyles = {};

    // 1. Background Fill
    if (st.backgroundFill) {
        if (st.backgroundFill.type === 'color') {
            fillStyles.backgroundColor = st.backgroundFill.color;
            fillStyles.backgroundImage = 'none';
        } else if (st.backgroundFill.type === 'gradient') {
            fillStyles.backgroundImage = st.backgroundFill.gradient;
            fillStyles.backgroundColor = 'transparent';
        } else if (st.backgroundFill.type === 'image') {
            const bgImageUrl = resolveMediaUrl(st.backgroundFill.image);
            fillStyles.backgroundImage = bgImageUrl ? `url(${bgImageUrl})` : undefined;
            fillStyles.backgroundSize = st.backgroundSize || 'cover';
            fillStyles.backgroundPosition = st.backgroundPosition || 'center';
            fillStyles.backgroundRepeat = st.backgroundRepeat || 'no-repeat';
            fillStyles.backgroundColor = 'transparent';
        } else {
            fillStyles.backgroundColor = 'transparent';
            if (!st.textFill || !['gradient', 'image'].includes(st.textFill.type)) fillStyles.backgroundImage = 'none';
        }
    } else {
        fillStyles.backgroundColor = st.backgroundColor || (s.colors && s.colors.background) || undefined;
    }

    // 3. Border Fill
    if (st.borderFill) {
        if (st.borderFill.type === 'color') {
            fillStyles.borderColor = st.borderFill.color;
            fillStyles.borderImageSource = 'none';
        } else if (st.borderFill.type === 'gradient') {
            fillStyles.borderImageSource = st.borderFill.gradient;
            fillStyles.borderImageSlice = 1;
            fillStyles.borderColor = 'transparent';
        } else if (st.borderFill.type === 'image') {
            fillStyles.borderImageSource = `url(${st.borderFill.image})`;
            fillStyles.borderImageSlice = 1;
            fillStyles.borderColor = 'transparent';
        } else {
            fillStyles.borderColor = 'transparent';
            fillStyles.borderImageSource = 'none';
        }
    } else {
        fillStyles.borderColor = st.borderColor || undefined;
    }
    
    const zValue = Number(
        st.zIndex !== undefined && st.zIndex !== '' ? st.zIndex : 
        (l.zIndex !== undefined && l.zIndex !== '' ? l.zIndex : 
        (s.zIndex !== undefined && s.zIndex !== '' ? s.zIndex : 0))
    ) || 0;

    return {
        ...fillStyles,
        minHeight: store.isDepthView ? (l.fullHeight ? '100vh' : (st.height ? `max(${st.height}, 60px)` : '60px')) : (l.fullHeight ? '100vh' : (st.height || '0px')),
        height: st.height,
        width: st.width,
        backgroundAttachment: st.backgroundAttachment || (l.fixedBg ? 'fixed' : undefined),
        
        // Spacing (with theme defaults fallback)
        marginTop: st.marginTop || themeDefaults.marginTop,
        marginBottom: st.marginBottom || themeDefaults.marginBottom,
        marginLeft: st.marginLeft || themeDefaults.marginLeft,
        marginRight: st.marginRight || themeDefaults.marginRight,
        paddingTop: st.paddingTop || themeDefaults.paddingTop,
        paddingBottom: st.paddingBottom || themeDefaults.paddingBottom,
        paddingLeft: st.paddingLeft || themeDefaults.paddingLeft,
        paddingRight: st.paddingRight || themeDefaults.paddingRight,
        
        // Position
        position: st.position,
        display: st.display,
        zIndex: zValue,
        top: st.top,
        bottom: st.bottom,
        left: st.left,
        right: st.right,
        
        // Borders (Mapped from LinkedUnitInput)
        borderTopLeftRadius: st.borderTopLeftRadius,
        borderTopRightRadius: st.borderTopRightRadius,
        borderBottomLeftRadius: st.borderBottomLeftRadius,
        borderBottomRightRadius: st.borderBottomRightRadius,
        borderTopWidth: st.borderTopWidth,
        borderRightWidth: st.borderRightWidth,
        borderBottomWidth: st.borderBottomWidth,
        borderLeftWidth: st.borderLeftWidth,
        
        borderStyle: st.borderStyle || ((st.borderTopWidth || st.borderRightWidth || st.borderBottomWidth || st.borderLeftWidth) ? 'solid' : undefined),
        
        // Dimensions & Sizing
        width: st.width,
        minWidth: st.minWidth,
        maxWidth: st.maxWidth,
        height: st.height,
        minHeight: st.minHeight,
        maxHeight: st.maxHeight,
        aspectRatio: st.aspectRatio,
        overflow: st.overflow,

        // Flexbox & Grid
        flexDirection: st.flexDirection,
        flexWrap: st.flexWrap,
        justifyContent: st.justifyContent,
        alignItems: st.alignItems,
        justifyItems: st.justifyItems,
        alignContent: st.alignContent,
        gridTemplateColumns: st.gridTemplateColumns,
        gridTemplateRows: st.gridTemplateRows,
        gridAutoFlow: st.gridAutoFlow,
        gridAutoColumns: st.gridAutoColumns,
        gridAutoRows: st.gridAutoRows,
        gap: st.gap,
        columnGap: st.columnGap,
        rowGap: st.rowGap,

        // Typography (with theme defaults fallback)
        textAlign: st.textAlign || themeDefaults.textAlign,
        fontWeight: st.fontWeight || themeDefaults.fontWeight,
        fontFamily: st.fontFamily || themeDefaults.fontFamily,
        fontSize: st.fontSize || themeDefaults.fontSize,
        lineHeight: st.lineHeight || themeDefaults.lineHeight,
        letterSpacing: st.letterSpacing || themeDefaults.letterSpacing,
        textTransform: st.textTransform || themeDefaults.textTransform,
        
        // Effects
        opacity: st.opacity !== undefined ? st.opacity : themeDefaults.opacity,
        boxShadow: st.boxShadow,
        
        // Filters
        filter: [
            st.blur !== undefined ? `blur(${st.blur}px)` : '',
            st.brightness !== undefined ? `brightness(${st.brightness}%)` : '',
            st.contrast !== undefined ? `contrast(${st.contrast}%)` : '',
            st.saturate !== undefined ? `saturate(${st.saturate}%)` : ''
        ].filter(Boolean).join(' ') || undefined,

        // 3D Depth View (CUMULATIVE Z Depth)
        transform: store.isDepthView 
            ? `translate3d(0, 0, ${zValue * 100}px)` 
            : undefined,
        transformStyle: 'preserve-3d',
        // Add prominent border in 3D mode for better layer separation
        outline: store.isDepthView ? '2px solid rgba(255,255,255,0.15)' : undefined,
        outlineOffset: store.isDepthView ? '-2px' : undefined,
    };
});

const blockId = computed(() => {
    return props.block.settings?.style?.htmlId || `block-${props.block.id}`;
});

const blockClasses = computed(() => {
    let classes = [props.block.settings?.style?.customClass];
    // Existing container logic
    if (props.block.type === 'container') {
        const c = props.block.content;
        if (c.isBoxed) classes.push('container mx-auto');
        if (c.layoutType === 'flex') {
            classes.push('flex');
            classes.push(c.flexConfig.direction === 'row' ? 'flex-row' : 'flex-col');
        }
    }
    return classes.filter(Boolean).join(' ');
});

const containerClasses = computed(() => {
    if (props.block.type !== 'container') return '';
    const c = props.block.content;
    let classes = [];

    if (c.isBoxed) classes.push('container mx-auto');

    if (c.layoutType === 'flex') {
        classes.push('flex');
        if (c.flexConfig.direction === 'row') classes.push('flex-row');
        else classes.push('flex-col');

        if (c.flexConfig.wrap === 'wrap') classes.push('flex-wrap');
        else classes.push('flex-nowrap');

        if (c.flexConfig.align === 'start') classes.push('items-start');
        else if (c.flexConfig.align === 'center') classes.push('items-center');
        else if (c.flexConfig.align === 'end') classes.push('items-end');
        else if (c.flexConfig.align === 'baseline') classes.push('items-baseline');
        else classes.push('items-stretch');

        if (c.flexConfig.justify === 'start') classes.push('justify-start');
        else if (c.flexConfig.justify === 'center') classes.push('justify-center');
        else if (c.flexConfig.justify === 'end') classes.push('justify-end');
        else if (c.flexConfig.justify === 'between') classes.push('justify-between');
        else if (c.flexConfig.justify === 'around') classes.push('justify-around');
        else if (c.flexConfig.justify === 'evenly') classes.push('justify-evenly');

        if (c.flexConfig.gap) classes.push(`gap-${c.flexConfig.gap}`);
    } else if (c.layoutType === 'grid') {
        classes.push('grid');
        classes.push(`grid-cols-${c.gridConfig.cols || '1'}`);
        if (c.gridConfig.gap) classes.push(`gap-${c.gridConfig.gap}`);
    }

    return classes.join(' ');
});

const textStyleObj = computed(() => {
    const s = props.block.settings || {};
    const st = s.style || {};
    let textStyles = {};

    if (st.textFill) {
        if (st.textFill.type === 'color') {
            textStyles.color = st.textFill.color;
        } else if (st.textFill.type === 'gradient') {
            textStyles.backgroundImage = st.textFill.gradient;
            textStyles.WebkitBackgroundClip = 'text';
            textStyles.backgroundClip = 'text';
            textStyles.color = 'transparent';
            textStyles.display = 'inline-block';
            textStyles.width = 'fit-content';
            textStyles.paddingRight = '0.2em';
        } else if (st.textFill.type === 'image') {
            const textImageUrl = resolveMediaUrl(st.textFill.image);
            textStyles.backgroundImage = textImageUrl ? `url(${textImageUrl})` : undefined;
            textStyles.backgroundSize = 'cover';
            textStyles.backgroundPosition = 'center';
            textStyles.WebkitBackgroundClip = 'text';
            textStyles.backgroundClip = 'text';
            textStyles.color = 'transparent';
            textStyles.display = 'inline-block';
            textStyles.width = 'fit-content';
            textStyles.paddingRight = '0.2em';
        }
    } else {
        textStyles.color = st.textColor || (s.colors && s.colors.text) || undefined;
    }

    return textStyles;
});

const submitContact = () => {
    // Basic implementation for the contact form block
};

const contactForm = useForm({
    name: '',
    email: '',
    message: '',
    website: '' // honeypot
});

// Runtime Form Handling (Phase J)
const parentFormValues = inject('runtimeFormValues', null);
const isFormBlock = computed(() => props.block.type === 'form');
const localFormValues = isFormBlock.value ? reactive({ website: '' }) : null;

if (isFormBlock.value) {
    provide('runtimeFormValues', localFormValues);
}

const submitRuntimeForm = () => {
    if (isEditor) return;
    
    // Check honeypot
    if (localFormValues.website) {
        toast.info(t(props.block.content.success_message));
        return;
    }

    const submitUrl = props.block.content.submit_url || (props.block.content.form_id ? route('forms.submit', props.block.content.form_id) : null);
    
    if (!submitUrl) {
        console.error('No submission URL or form ID defined for this form container.');
        return;
    }

    const f = useForm(localFormValues);
    f.post(submitUrl, {
        onSuccess: () => {
             // Reset form
             Object.keys(localFormValues).forEach(key => {
                 if (key !== 'website') localFormValues[key] = '';
             });
             // Success message is handled by backend + flash or we can show local toast
             // The backend PublicFormController returns with('success', ...)
        },
        preserveScroll: true
    });
};

// Phase J: Input binding helper
const formValue = computed({
    get: () => {
        if (!parentFormValues) return null;
        
        // For radio, we use 'group' as the data key
        if (props.block.type === 'radio' && props.block.content.group) {
            return parentFormValues[props.block.content.group];
        }
        
        if (!props.block.content.label) return null;
        const key = t(props.block.content.label);
        return parentFormValues[key];
    },
    set: (v) => {
        if (!parentFormValues) return;

        // For radio, we use 'group' as the data key
        if (props.block.type === 'radio' && props.block.content.group) {
            parentFormValues[props.block.content.group] = v;
            return;
        }

        if (props.block.content.label) {
            const key = t(props.block.content.label);
            parentFormValues[key] = v;
        }
    }
});


</script>

<template>
    <div ref="blockRef" 
         :id="blockId"
         :data-timeline="block.settings?.animations?.timelineId"
         :style="styleObj"
        class="group relative block-wrapper transition-all duration-300"
        :class="[
            blockClasses,
            { 'editor-ring': isEditor && store.activeBlockId === block.id },
            { 'hover-ring': isEditor && !store.isDragging && !block.hidden && !block.locked },
            { 'active-block shadow-lg z-10': isEditor && store.activeBlockId === block.id },
            { 'opacity-20 pointer-events-none grayscale': isEditor && block.hidden },
            { 'pointer-events-none cursor-not-allowed select-none': isEditor && block.locked },
            { 'sticky top-0 z-[60]': block.type === 'header_slot' }
        ]"
        @click.stop="isEditor && !block.locked ? (store.activeBlockId = block.id, store.isEditingBlock = false) : null"
        @mouseenter="isEditor && !block.locked ? (store.hoveredBlockId = block.id) : null"
        @mouseleave="isEditor ? (store.hoveredBlockId = null) : null"
    >
        
        <!-- Editor Action Buttons (Only for the currently hovered/active block) -->
        <template v-if="isEditor">
            <!-- Drag Handle (Left) -->
            <div class="absolute left-2 top-1/2 -translate-y-1/2 z-[100] transition-opacity duration-200"
                 :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === block.id || store.activeBlockId === block.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== block.id && store.activeBlockId !== block.id}">
                <div class="drag-handle btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur cursor-move text-base-content/60 hover:text-primary hover:bg-base-200 shadow-sm rounded-box relative z-50 pointer-events-auto">
                    <PhArrowsOut weight="bold" class="w-3 h-3" />
                </div>
            </div>
            
            <!-- Actions (Right: Settings, Duplicate, Delete) -->
            <div class="absolute right-2 top-1/2 -translate-y-1/2 z-[100] transition-opacity duration-200 flex items-center gap-1"
                 :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === block.id || store.activeBlockId === block.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== block.id && store.activeBlockId !== block.id}">
                <button type="button" @click.stop.prevent="store.isEditingBlock = true; store.showRightSidebar = true; store.activeBlockId = block.id" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-base-content/60 hover:bg-primary hover:text-primary-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Block Settings">
                    <PhSlidersHorizontal weight="bold" class="w-3 h-3" />
                </button>
                <button type="button" @click.stop.prevent="store.duplicateBlock(block.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-primary/80 hover:bg-primary hover:text-primary-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Duplicate Block">
                    <PhCopy weight="bold" class="w-3 h-3" />
                </button>
                <button type="button" @click.stop.prevent="store.removeBlock(block.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-error/80 hover:bg-error hover:text-error-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Delete Block">
                    <PhTrash weight="bold" class="w-3 h-3" />
                </button>
            </div>
        </template>
        
        <!-- Container Block (Unified Layout) -->
        <component :is="block.content.htmlTag || 'section'" 
             v-if="block.type === 'container'" 
             class="w-full relative transition-colors"
             :class="[
                containerClasses,
                {'min-h-[100px] border border-dashed editor-dashed-frame': isEditor}
             ]"
             :style="{ transformStyle: 'preserve-3d' }">
            <template v-if="isEditor">
                <draggable 
                    v-model="block.children" 
                    :group="'admin.blocks'"
                    item-key="id"
                    handle=".drag-handle"
                    ghost-class="ghost-block"
                    class="min-h-[100px] w-full"
                    :class="[
                        resolvedContent.layoutType === 'flex' ? 'flex' : '',
                        resolvedContent.layoutType === 'flex' && resolvedContent.flexConfig.direction === 'row' ? 'flex-row' : 'flex-col',
                        resolvedContent.layoutType === 'grid' ? 'grid' : '',
                        resolvedContent.layoutType === 'grid' ? `grid-cols-${resolvedContent.gridConfig.cols || '1'}` : '',
                        (resolvedContent.layoutType === 'flex' || resolvedContent.layoutType === 'grid') && resolvedContent.flexConfig?.gap ? `gap-${resolvedContent.flexConfig.gap}` : ''
                    ]"
                    :style="{ transformStyle: 'preserve-3d' }">
                    <template #item="{ element }">
                        <DynamicBlock :block="element" />
                    </template>
                </draggable>
                <div v-if="!block.children?.length" class="absolute inset-0 flex items-center justify-center opacity-30 pointer-events-none text-xs font-bold uppercase tracking-widest border border-dashed border-base-content/10 m-2 rounded-lg bg-base-100/30 backdrop-blur-sm">Drop blocks here</div>
            </template>
            <template v-else>
                <DynamicBlock v-for="child in block.children" :key="child.id" :block="child" />
            </template>
        </component>

        <!-- 1. Typography -->
        <div v-else-if="block.type === 'paragraph'" 
             class="leading-relaxed" 
             :class="{'opacity-80': !isEditor}" 
             :style="textStyleObj" 
             v-html="t(resolvedContent.text)"></div>
        
        <div v-else-if="block.type === 'heading'">
            <component :is="'h' + (resolvedContent.level || 2)" 
                       :style="textStyleObj"
                       class="font-black tracking-tighter pr-[0.2em] whitespace-pre-wrap"
                       :class="[
                           !block.settings?.style?.fontSize ? (resolvedContent.level === 1 ? 'text-6xl md:text-8xl' : (resolvedContent.level === 2 ? 'text-4xl md:text-6xl' : 'text-2xl md:text-4xl')) : '',
                           resolvedContent.align === 'center' ? 'text-center' : ''
                       ]"
                       v-html="t(resolvedContent.text) || 'Heading'">
            </component>
        </div>

        <div v-else-if="block.type === 'list'">
            <ul v-if="resolvedContent.listType !== 'tasks'" 
                :class="[resolvedContent.listType === 'ol' ? 'list-decimal' : 'list-disc', 'ml-6 opacity-80']"
                :style="{ gap: (resolvedContent.gap || 0) + 'px', display: 'flex', flexDirection: 'column' }">
                <li v-for="(item, i) in resolvedContent.items" :key="i">{{ t(item) }}</li>
            </ul>
            <div v-else class="space-y-2 opacity-80" :style="{ gap: (resolvedContent.gap || 0) + 'px', display: 'flex', flexDirection: 'column' }">
                <div v-for="(item, i) in resolvedContent.items" :key="i" class="flex items-center gap-3">
                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" checked readonly />
                    <span>{{ t(item) }}</span>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'quote'" class="pl-6 border-l-4 border-primary/40 italic bg-primary/5 py-4 rounded-r-2xl">
            <p class="text-xl mb-2 font-serif">"{{ t(resolvedContent.text) }}"</p>
            <div v-if="resolvedContent.author" class="mt-4 flex flex-col">
                <span class="text-sm font-bold opacity-80">— {{ t(resolvedContent.author) }}</span>
                <span v-if="resolvedContent.authorTitle" class="text-[10px] uppercase tracking-widest opacity-40 font-bold mt-1">{{ t(resolvedContent.authorTitle) }}</span>
            </div>
        </div>

        <div v-else-if="block.type === 'custom_code'" class="w-full">
            <div v-html="resolvedContent.html"></div>
        </div>

        <!-- 2. Actions -->
        <div v-else-if="block.type === 'button'" :class="[resolvedContent.align === 'center' ? 'text-center' : resolvedContent.align === 'right' ? 'text-right' : 'text-left']">
            <a :href="t(resolvedContent.url)" class="btn" :class="`btn-${resolvedContent.style || 'primary'}`">
                {{ t(resolvedContent.label) }}
            </a>
        </div>

        <div v-else-if="block.type === 'dropdown'" class="dropdown">
            <div tabindex="0" role="button" class="btn m-1">{{ t(resolvedContent.label) }}</div>
            <ul v-if="page.props.menus" tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow border border-white/10">
                <li v-for="(item, i) in resolvedContent.items" :key="i"><a>{{ t(item) }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'modal'">
            <button class="btn" @click="isEditor ? null : document.getElementById(resolvedContent.id).showModal()">{{ t(resolvedContent.buttonLabel) }}</button>
            <dialog :id="resolvedContent.id" class="modal">
                <div class="modal-box">
                    <h3 class="font-bold text-lg">{{ t(resolvedContent.title) }}</h3>
                    <p class="py-4">{{ t(resolvedContent.text) }}</p>
                    <div class="modal-action">
                        <form method="dialog">
                            <button class="btn">Close</button>
                        </form>
                    </div>
                </div>
            </dialog>
        </div>

        <div v-else-if="block.type === 'swap'">
            <label class="swap swap-rotate">
                <input type="checkbox" :checked="resolvedContent.active" disabled v-if="isEditor" />
                <input type="checkbox" :checked="resolvedContent.active" v-else />
                <i :class="resolvedContent.onIcon + ' swap-on w-10 h-10 flex items-center justify-center text-2xl'"></i>
                <i :class="resolvedContent.offIcon + ' swap-off w-10 h-10 flex items-center justify-center text-2xl'"></i>
            </label>
        </div>

        <!-- 3. Data Display -->
        <div v-else-if="block.type === 'accordion'" class="space-y-4">
            <div v-for="(item, i) in resolvedContent.items" :key="i" class="collapse collapse-arrow bg-base-200">
                <input type="radio" :name="'accordion-' + block.id" :checked="i === 0" /> 
                <div class="collapse-title text-xl font-medium">{{ t(item.title) }}</div>
                <div class="collapse-content"><p>{{ t(item.content) }}</p></div>
            </div>
        </div>

        <div v-else-if="block.type === 'avatar'" class="avatar" :class="resolvedContent.online ? 'online' : 'offline'">
            <div class="w-24 rounded-full">
                <img :src="resolvedContent.url || placeholderImg" />
            </div>
        </div>

        <div v-else-if="block.type === 'badge'" class="badge" :class="`badge-${resolvedContent.style || 'primary'}`">
            {{ t(resolvedContent.text) }}
        </div>

        <div v-else-if="block.type === 'card'" class="card bg-base-100 shadow-xl border border-white/5">
            <figure v-if="resolvedContent.image"><img :src="t(resolvedContent.image)" alt="Image" /></figure>
            <div class="card-body">
                <h2 class="card-title">{{ t(resolvedContent.title) }}</h2>
                <p>{{ t(resolvedContent.description) }}</p>
                <div class="card-actions justify-end" v-if="resolvedContent.buttonText">
                    <button class="btn btn-primary">{{ t(resolvedContent.buttonText) }}</button>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'carousel'" class="carousel w-full rounded-box">
            <div v-for="(img, i) in (resolvedContent.images || [])" :key="i" :id="`slide-${block.id}-${i}`" class="carousel-item relative w-full">
                <img :src="img" class="w-full object-cover aspect-video" />
                <div class="absolute inset-0 flex items-center justify-between p-4" v-if="resolvedContent.images?.length > 1">
                    <a :href="`#slide-${block.id}-${i === 0 ? resolvedContent.images.length - 1 : i - 1}`" class="btn btn-circle btn-sm">❮</a>
                    <a :href="`#slide-${block.id}-${i === resolvedContent.images.length - 1 ? 0 : i + 1}`" class="btn btn-circle btn-sm">❯</a>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'chat'" class="w-full flex flex-col gap-2">
            <div v-for="(msg, i) in resolvedContent.messages" :key="i" class="chat" :class="msg.side === 'start' ? 'chat-start' : 'chat-end'">
                <div class="chat-bubble" :class="msg.side === 'start' ? 'chat-bubble-primary' : ''">{{ t(msg.text) }}</div>
            </div>
        </div>

        <div v-else-if="block.type === 'countdown'" class="grid auto-cols-max grid-flow-col gap-5 text-center mx-auto w-fit">
            <div class="flex flex-col">
                <span class="countdown font-mono text-5xl">
                    <span :style="`--value:${resolvedContent.value};`"></span>
                </span>
                {{ t(resolvedContent.unit) }}
            </div>
        </div>

        <div v-else-if="block.type === 'diff'" class="diff aspect-[16/9] w-full max-w-2xl mx-auto rounded-box">
            <div class="diff-item-1">
                <img :src="resolvedContent.img1 || placeholderImg" alt="daisy" />
            </div>
            <div class="diff-item-2">
                <img :src="resolvedContent.img2 || placeholderImg" alt="daisy" />
            </div>
            <div class="diff-resizer"></div>
        </div>

        <!-- 3.5 Feedback -->
        <div v-else-if="block.type === 'alert'" role="alert" class="alert" :class="resolvedContent.type || 'alert-info'">
            <PhInfo weight="bold" class="w-5 h-5" />
            <span>{{ t(resolvedContent.text) }}</span>
        </div>

        <div v-else-if="block.type === 'progress'" class="w-full">
            <progress class="progress w-full" :class="resolvedContent.color || 'progress-primary'" :value="resolvedContent.value" :max="resolvedContent.max"></progress>
        </div>

        <div v-else-if="block.type === 'radial_progress'" class="mx-auto w-fit">
            <div class="radial-progress text-primary" :style="`--value:${resolvedContent.value}; --size:4rem; --thickness: 4px;`" role="progressbar">{{ resolvedContent.value }}%</div>
        </div>


        <div v-else-if="block.type === 'stat'" class="stats shadow mx-auto flex w-fit">
            <div class="stat">
                <div class="stat-figure text-primary" v-if="resolvedContent.icon"><i :class="resolvedContent.icon + ' text-3xl'"></i></div>
                <div class="stat-title">{{ t(resolvedContent.title) }}</div>
                <div class="stat-value text-primary">{{ t(resolvedContent.value) }}</div>
                <div class="stat-desc">{{ t(resolvedContent.desc) }}</div>
            </div>
        </div>

        <div v-else-if="block.type === 'table'" class="overflow-x-auto w-full">
            <table class="table table-zebra w-full border border-base-content/10">
                <tbody>
                    <tr v-for="(row, rIdx) in resolvedContent.rows" :key="rIdx">
                        <td v-for="(cell, cIdx) in row" :key="cIdx" class="border border-base-content/5">{{ t(cell) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else-if="block.type === 'timeline'">
            <ul class="timeline timeline-vertical">
                <li v-for="(item, i) in resolvedContent.items" :key="i">
                    <hr v-if="i > 0" class="bg-primary" />
                    <div class="timeline-start timeline-box">{{ t(item.year) }}</div>
                    <div class="timeline-middle text-primary"><PhCheckCircle weight="fill" class="w-5 h-5" /></div>
                    <div class="timeline-end mb-10">
                        <div class="text-lg font-black">{{ t(item.title) }}</div>
                        <p>{{ t(item.content) }}</p>
                    </div>
                    <hr v-if="i < resolvedContent.items.length - 1" class="bg-primary" />
                </li>
            </ul>
        </div>

        <!-- 4. Data Input -->
        <component :is="'form'"
             v-else-if="block.type === 'form'"
             @submit.prevent="submitRuntimeForm"
             class="w-full relative transition-colors"
             :class="[
                containerClasses,
                {'min-h-[100px] border border-dashed border-primary/20 rounded-xl p-4': isEditor}
             ]">
            <!-- In Editor, show a badge indicating it's a form -->
            <div v-if="isEditor" class="absolute -top-3 left-4 badge badge-primary badge-sm gap-2 z-10">
                <PhFiles weight="bold" class="w-3 h-3" />
                <span>Form Container</span>
            </div>

            <template v-if="isEditor">
                <draggable 
                    v-model="block.children" 
                    :group="'admin.blocks'"
                    item-key="id"
                    handle=".drag-handle"
                    ghost-class="ghost-block"
                    class="min-h-[80px] w-full"
                    :class="[
                        resolvedContent.layoutType === 'flex' ? 'flex' : 'flex flex-col',
                        resolvedContent.layoutType === 'flex' && resolvedContent.flexConfig?.direction === 'row' ? 'flex-row' : 'flex-col',
                        resolvedContent.layoutType === 'grid' ? 'grid' : '',
                        resolvedContent.layoutType === 'grid' ? `grid-cols-${resolvedContent.gridConfig?.cols || '1'}` : '',
                        (resolvedContent.layoutType === 'flex' || resolvedContent.layoutType === 'grid') && resolvedContent.flexConfig?.gap ? `gap-${resolvedContent.flexConfig.gap}` : 'gap-4'
                    ]">
                    <template #item="{ element }">
                        <DynamicBlock :block="element" />
                    </template>
                </draggable>
            </template>
            <template v-else>
                <div class="w-full flex flex-col gap-6" 
                    :class="[
                        resolvedContent.layoutType === 'flex' ? 'flex' : 'flex flex-col',
                        resolvedContent.layoutType === 'flex' && resolvedContent.flexConfig?.direction === 'row' ? 'flex-row' : 'flex-col',
                        resolvedContent.layoutType === 'grid' ? 'grid' : '',
                        resolvedContent.layoutType === 'grid' ? `grid-cols-${resolvedContent.gridConfig?.cols || '1'}` : '',
                        (resolvedContent.layoutType === 'flex' || resolvedContent.layoutType === 'grid') && resolvedContent.flexConfig?.gap ? `gap-${resolvedContent.flexConfig.gap}` : 'gap-4'
                    ]">
                    <DynamicBlock 
                        v-for="child in block.children" 
                        :key="child.id" 
                        :block="child" 
                    />
                    
                    <!-- Hidden honeypot -->
                    <input type="text" v-model="localFormValues.website" class="hidden" />

                    <div class="mt-4 flex" :class="[
                        block.appearance?.justifyContent === 'center' ? 'justify-center' : 
                        block.appearance?.justifyContent === 'end' ? 'justify-end' : 'justify-start'
                    ]">
                        <button type="submit" class="btn btn-primary px-8 rounded-full shadow-lg shadow-primary/20">
                            {{ t(resolvedContent.submit_label) || 'Send Message' }}
                        </button>
                    </div>
                </div>
            </template>
        </component>

        <!-- 4. Data Input -->
        <div v-else-if="block.type === 'text_input'" class="form-control w-full max-w-xs mx-auto">
            <label class="label"><span class="label-text">{{ t(resolvedContent.label) }}</span></label>
            <input type="text" :placeholder="t(resolvedContent.placeholder)" class="input input-bordered w-full" :disabled="isEditor" v-model="formValue" />
        </div>

        <div v-else-if="block.type === 'textarea'" class="form-control w-full max-w-2xl mx-auto">
            <label class="label"><span class="label-text">{{ t(resolvedContent.label) }}</span></label>
            <textarea class="textarea textarea-bordered h-24" :placeholder="t(resolvedContent.placeholder)" :disabled="isEditor" v-model="formValue"></textarea>
        </div>

        <div v-else-if="block.type === 'select'" class="form-control w-full max-w-xs mx-auto">
            <label class="label"><span class="label-text">{{ t(resolvedContent.label) }}</span></label>
            <select class="select select-bordered" :disabled="isEditor" v-model="formValue">
                <option disabled selected value="">Pick one</option>
                <option v-for="(opt, i) in (t(resolvedContent.options)?.split('\n') || [])" :key="i" :value="opt">{{ opt }}</option>
            </select>
        </div>

        <div v-else-if="block.type === 'checkbox'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <input type="checkbox" class="checkbox" :disabled="isEditor" v-model="formValue" />
                <span class="label-text">{{ t(resolvedContent.label) }}</span>
            </label>
        </div>

        <div v-else-if="block.type === 'radio'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <input type="radio" :name="resolvedContent.group" class="radio" :disabled="isEditor" :value="t(resolvedContent.label)" v-model="formValue" />
                <span class="label-text">{{ t(resolvedContent.label) }}</span>
            </label>
        </div>

        <div v-else-if="block.type === 'toggle'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <span class="label-text">{{ t(resolvedContent.label) }}</span>
                <input type="checkbox" class="toggle toggle-primary" :disabled="isEditor" v-model="formValue" />
            </label>
        </div>

        <div v-else-if="block.type === 'range'" class="flex flex-col gap-2 w-full max-w-xs mx-auto">
            <input type="range" :min="resolvedContent.min" :max="resolvedContent.max" class="range" :disabled="isEditor" v-model="formValue" />
        </div>

        <div v-else-if="block.type === 'rating'" class="rating mx-auto flex w-fit">
            <input v-for="i in resolvedContent.max" :key="i" type="radio" :name="'rating-' + block.id" class="mask mask-star-2 bg-orange-400" :value="i" :disabled="isEditor" v-model="formValue" />
        </div>

        <div v-else-if="block.type === 'file_input'" class="form-control w-full max-w-xs mx-auto">
            <label class="label"><span class="label-text">{{ t(resolvedContent.label) }}</span></label>
            <input type="file" class="file-input file-input-bordered w-full max-w-xs" :disabled="isEditor" @change="(e) => formValue = e.target.files[0]" />
        </div>

        <!-- 6. Navigation -->
        <div v-else-if="block.type === 'breadcrumbs'" class="breadcrumbs text-sm">
            <ul v-if="page.props.menus">
                <li v-for="(item, i) in resolvedContent.items" :key="i"><a>{{ t(item) }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'menu'">
            <ul v-if="page.props.menus" class="menu bg-base-200 rounded-box w-56">
                <li v-for="(item, i) in resolvedContent.items" :key="i"><a>{{ t(item) }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'navbar'" 
             class="navbar bg-base-100 shadow-sm border-b border-base-content/5 px-4 md:px-8 min-h-[70px]"
             :class="[
                block.appearance?.sticky ? 'sticky top-0 z-[50]' : 'relative'
             ]">
            <div class="flex-1">
                <a class="btn btn-ghost text-xl font-black italic tracking-tighter uppercase">{{ t(resolvedContent.title) || 'Featherly' }}</a>
            </div>
            <div class="flex-none hidden lg:flex">
                <ul v-if="page.props.menus" class="menu menu-horizontal px-1 font-bold uppercase tracking-widest text-xs opacity-70">
                    <li v-for="(link, i) in resolvedContent.links" :key="i"><a>{{ t(link) }}</a></li>
                </ul>
            </div>
            <div class="flex-none">
                <a class="btn btn-primary btn-sm rounded-full px-6 ml-4" v-if="resolvedContent.actionButton">{{ t(resolvedContent.actionButton) }}</a>
            </div>
        </div>

        <div v-else-if="block.type === 'steps'">
            <ul class="steps w-full">
                <li v-for="(item, i) in resolvedContent.items" :key="i" class="step" :class="i===0 ? 'step-primary' : ''">{{ t(item) }}</li>
            </ul>
        </div>

        <div v-else-if="block.type === 'tabs'" role="tablist" class="tabs tabs-bordered">
            <template v-for="(t_item, i) in resolvedContent.tabs" :key="i">
                <input type="radio" :name="'tab-' + block.id" role="tab" class="tab" :aria-label="t(t_item.title)" :checked="i === 0" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">{{ t(t_item.content) }}</div>
            </template>
        </div>

        <!-- 7. Mockup -->
        <div v-else-if="block.type === 'mockup_browser'" class="mockup-browser border border-white/10 bg-base-300">
            <div class="mockup-browser-toolbar">
                <div class="input">{{ t(resolvedContent.url) }}</div>
            </div>
            <div class="flex justify-center px-4 py-16 bg-base-200 border-t border-white/5">{{ t(resolvedContent.content) }}</div>
        </div>

        <div v-else-if="block.type === 'mockup_code'" class="mockup-code">
            <pre data-prefix="$"><code>{{ t(resolvedContent.code) }}</code></pre>
        </div>

        <div v-else-if="block.type === 'mockup_phone'" class="mockup-phone">
            <div class="camera"></div> 
            <div class="display">
                <div class="artboard artboard-demo phone-1 bg-base-200" :style="t(resolvedContent.url) ? `background-image: url(${t(resolvedContent.url)}); background-size: cover;` : ''">
                    <span v-if="!t(resolvedContent.url)">Hi.</span>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'mockup_window'" class="mockup-window border border-white/10 bg-base-300">
            <div class="flex justify-center px-4 py-16 bg-base-200 border-t border-white/5">{{ t(resolvedContent.content) }}</div>
        </div>

        <!-- 8. Extended -->
        <div v-else-if="block.type === 'posts_list'" class="space-y-4">
            <div :class="resolvedContent.layout === 'grid' ? 'grid grid-cols-1 md:grid-cols-3 gap-6' : 'space-y-4'">
                <!-- Real Data Fallback -->
                <template v-if="Object.keys(page.props.posts || {}).length">
                    <div v-for="post in (page.props.posts.data || page.props.posts).slice(0, resolvedContent.count || 3)" :key="post.id" class="card bg-base-200 shadow border border-white/5" :class="resolvedContent.layout === 'list' ? 'card-side' : ''">
                        <figure v-if="resolvedContent.layout === 'grid' && post.featured_image">
                            <img :src="`/storage/${t(post.featured_image)}`" alt="Post" class="w-full h-48 object-cover" />
                        </figure>
                        <div class="card-body">
                            <div class="flex items-center gap-2 text-[10px] uppercase tracking-widest opacity-40 mb-2 font-bold">
                                <PhCalendarBlank class="w-3 h-3" />
                                <span>{{ moment(post.published_at).format('ll') }}</span>
                            </div>
                            <h2 class="card-title text-lg leading-tight">{{ t(post.title) }}</h2>
                            <p class="opacity-60 text-xs line-clamp-2 mt-2">{{ t(post.excerpt) }}</p>
                            <div class="card-actions justify-end mt-4">
                                <Link :href="`/blog/${t(post.slug)}`" class="btn btn-xs btn-primary rounded-full px-4">Read Now</Link>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- Placeholder for direct preview in builder or empty state -->
                <div v-else v-for="i in (resolvedContent.count || 3)" :key="i" class="card bg-base-200 shadow border border-white/5" :class="resolvedContent.layout === 'list' ? 'card-side' : ''">
                    <figure v-if="resolvedContent.layout === 'grid'"><img :src="placeholderImg" alt="Post" /></figure>
                    <div class="card-body">
                        <h2 class="card-title text-lg">Sample Blog Post #{{i}}</h2>
                        <p class="opacity-50 text-xs line-clamp-2">This is where the post excerpt would appear in the final page.</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'projects_list'" class="space-y-4">
            <div :class="resolvedContent.layout === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-4'">
                <!-- Real Data -->
                <template v-if="page.props.all_projects?.length">
                    <div v-for="project in (page.props.all_projects || []).slice(0, resolvedContent.count || 3)" :key="project.id" class="card bg-base-200 shadow border border-white/5 image-full group">
                        <figure><img :src="project.thumbnail ? `/storage/${project.thumbnail}` : placeholderImg" class="transition-transform group-hover:scale-105 duration-700" alt="Project" /></figure>
                        <div class="card-body items-center text-center justify-center bg-black/40 backdrop-blur-[2px] transition-all group-hover:bg-black/20">
                            <h2 class="card-title text-3xl font-black italic uppercase tracking-tighter">{{ t(project.title) }}</h2>
                            <p class="flex-grow-0 text-xs opacity-70 uppercase tracking-widest font-bold">{{ t(project.category) }}</p>
                            <div class="card-actions mt-6">
                                <Link :href="`/projects/${t(project.slug)}`" class="btn btn-primary rounded-full px-8">View Case</Link>
                            </div>
                        </div>
                    </div>
                </template>
                <!-- Placeholder -->
                <div v-else v-for="i in (resolvedContent.count || 3)" :key="i" class="card bg-base-200 shadow border border-white/5 image-full">
                    <figure><img :src="placeholderImg" alt="Project" /></figure>
                    <div class="card-body items-center text-center justify-center">
                        <h2 class="card-title text-3xl font-black italic uppercase">Project Sample #{{i}}</h2>
                        <div class="card-actions mt-4">
                            <button class="btn btn-primary">Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'text_rotate'" class="text-3xl md:text-5xl font-bold text-center py-10">
            <span>{{ t(resolvedContent.prefix) }}</span>
            <span class="text-primary italic border-b-4 border-primary pb-1 mx-2">
                {{ t(resolvedContent.words)?.split('\n')?.[0] || 'Animated Word' }}
            </span>
            <span>{{ t(resolvedContent.suffix) }}</span>
        </div>

        <div v-else-if="block.type === 'divider'" class="divider">{{ t(resolvedContent.text) }}</div>

        <!-- 9. Building -->
        <div v-else-if="block.type === 'template_reference'" 
             class="template-reference-block"
             :class="{'border-2 border-dashed border-primary/30 rounded-3xl p-8 bg-primary/5 group/ref transition-all hover:bg-primary/10 hover:border-primary/50': isEditor || !block.content?.template_content?.length}">
            
            <!-- Real Content (Runtime) -->
            <template v-if="!isEditor && block.content?.template_content?.length">
                <DynamicBlock 
                    v-for="child in resolvedContent.template_content" 
                    :key="child.id" 
                    :block="child" 
                />
            </template>

            <!-- Placeholder (Editor or Empty) -->
            <div v-else class="flex flex-col items-center justify-center text-center py-4">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover/ref:scale-110">
                    <PhPuzzlePiece weight="duotone" class="w-8 h-8 text-primary" />
                </div>
                <h4 class="text-xl font-black italic uppercase tracking-tighter text-primary">Template Part</h4>
                <p class="text-xs opacity-50 mt-1 uppercase tracking-widest font-bold">
                    {{ resolvedContent.template_id ? 'Template ID: ' + resolvedContent.template_id : 'No Template Selected' }}
                </p>
                
                <div v-if="isEditor" class="mt-4">
                    <button class="btn btn-sm btn-outline btn-primary rounded-full px-8" @click.stop="store.activeBlockId = block.id; store.isEditingBlock = true">
                        Select Template
                    </button>
                </div>
            </div>
        </div>

        <!-- 10. Footer -->
        <footer v-else-if="block.type === 'footer_standard'" 
                class="w-full py-12 px-6"
                :style="{ backgroundColor: block.appearance?.backgroundColor || '#1f2937', color: block.appearance?.textColor || '#ffffff' }">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    <span class="text-2xl font-black italic uppercase tracking-tighter">Featherly</span>
                    <span class="opacity-40 text-sm font-medium">| CMS</span>
                </div>
                <div class="text-sm opacity-60 font-medium tracking-wide">
                    {{ t(block.content.copyright) || '© 2024 Featherly CMS' }}
                </div>
                <div class="flex gap-6 text-sm font-bold uppercase tracking-widest opacity-40 hover:opacity-100 transition-opacity">
                    <a href="#" class="hover:text-primary">Privacy</a>
                    <a href="#" class="hover:text-primary">Terms</a>
                </div>
            </div>
        </footer>


        <!-- Header Slot -->
        <header v-else-if="block.type === 'header_slot'" 
             class="header-slot-block"
             :class="{'border-2 border-dashed border-primary/30 rounded-2xl p-6 bg-primary/5 group/slot': isEditor}">
            
            <template v-if="headerContent?.length">
                <DynamicBlock 
                    v-for="subBlock in headerContent" 
                    :key="subBlock.id" 
                    :block="subBlock" 
                />
            </template>

            <div v-else-if="isEditor" class="flex flex-col items-center justify-center text-center opacity-40 py-4">
                <PhPuzzlePiece weight="duotone" class="w-10 h-10 mb-2" />
                <span class="text-xs font-bold uppercase tracking-widest">Header Slot Placeholder</span>
            </div>
        </header>

        <!-- Footer Slot -->
        <footer v-else-if="block.type === 'footer_slot'" 
             class="footer-slot-block mt-auto"
             :class="{'border-2 border-dashed border-secondary/30 rounded-2xl p-6 bg-secondary/5 group/slot': isEditor}">
            
            <template v-if="footerContent?.length">
                <DynamicBlock 
                    v-for="subBlock in footerContent" 
                    :key="subBlock.id" 
                    :block="subBlock" 
                />
            </template>

            <div v-else-if="isEditor" class="flex flex-col items-center justify-center text-center opacity-40 py-4">
                <PhPuzzlePiece weight="duotone" class="w-10 h-10 mb-2" />
                <span class="text-xs font-bold uppercase tracking-widest">Footer Slot Placeholder</span>
            </div>
        </footer>

        <!-- Sidebar Slot -->
        <div v-else-if="block.type === 'sidebar_slot'" 
             class="sidebar-slot-block"
             :class="{'border-2 border-dashed border-accent/30 rounded-2xl p-6 bg-accent/5 group/slot': isEditor}">
            
            <template v-if="sidebarContent?.length">
                <DynamicBlock 
                    v-for="subBlock in sidebarContent" 
                    :key="subBlock.id" 
                    :block="subBlock" 
                />
            </template>

            <div v-else-if="isEditor" class="flex flex-col items-center justify-center text-center opacity-40 py-4">
                <PhPuzzlePiece weight="duotone" class="w-10 h-10 mb-2" />
                <span class="text-xs font-bold uppercase tracking-widest">Sidebar Slot Placeholder</span>
            </div>
        </div>

        <div v-else-if="block.type === 'content_slot'" 
             class="content-slot-block flex-grow"
             :class="{'border-2 border-dashed border-secondary/30 rounded-3xl p-12 bg-secondary/5 group/slot': isEditor}">
            
            <!-- Real Content Injection (Runtime) -->
            <template v-if="!isEditor">
                <template v-if="mainContent?.length">
                    <DynamicBlock 
                        v-for="subBlock in mainContent" 
                        :key="subBlock.id" 
                        :block="subBlock" 
                    />
                </template>
            </template>

            <!-- Placeholder (Editor ONLY) -->
            <div v-else-if="isEditor" class="flex flex-col items-center justify-center text-center py-10 opacity-40 group-hover/slot:opacity-60 transition-opacity">
                <PhSquare weight="duotone" class="w-12 h-12 text-secondary mb-4" />
                <h4 class="text-2xl font-black italic uppercase tracking-widest text-secondary">{{ t(block.content.label) || 'Content Slot' }}</h4>
                <p class="text-sm mt-2 font-medium">This is where the unique page content will be injected.</p>
            </div>
        </div>


    </div>
</template>
