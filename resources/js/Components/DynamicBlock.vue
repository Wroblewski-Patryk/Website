<script setup>
import { ref, onMounted, computed, watch, inject } from 'vue';
import { useGsapRuntime } from '@/Composables/useGsapRuntime';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import draggable from 'vuedraggable';
import { PhArrowsOut, PhCopy, PhTrash, PhInfo, PhCheckCircle, PhSlidersHorizontal, PhPuzzlePiece, PhSquare, PhCube, PhLayout, PhCalendarBlank, PhUser } from '@phosphor-icons/vue';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { useTranslations } from '@/Composables/useTranslations';
import placeholderImg from '../../images/placeholder.png';
import moment from 'moment';

const props = defineProps(['block']);
const page = usePage();
const store = useBlockBuilderStore();
const isEditor = inject('isEditor', false);
const { t } = useTranslations();

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
            fillStyles.backgroundImage = `url(${st.backgroundFill.image})`;
            fillStyles.backgroundSize = 'cover';
            fillStyles.backgroundPosition = 'center';
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
        gridTemplateColumns: st.gridTemplateColumns,
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
            : `translate3d(0, 0, 0px)`,
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
            textStyles.backgroundImage = `url(${st.textFill.image})`;
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
    console.log("Contact form submitted");
};

const contactForm = useForm({
    name: '',
    email: '',
    message: '',
    website: '' // honeypot
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
            { 'pointer-events-none cursor-not-allowed select-none': isEditor && block.locked }
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
                    :group="'blocks'"
                    item-key="id"
                    handle=".drag-handle"
                    ghost-class="ghost-block"
                    class="min-h-[100px] w-full"
                    :class="[
                        block.content.layoutType === 'flex' ? 'flex' : '',
                        block.content.layoutType === 'flex' && block.content.flexConfig.direction === 'row' ? 'flex-row' : 'flex-col',
                        block.content.layoutType === 'grid' ? 'grid' : '',
                        block.content.layoutType === 'grid' ? `grid-cols-${block.content.gridConfig.cols || '1'}` : '',
                        (block.content.layoutType === 'flex' || block.content.layoutType === 'grid') && block.content.flexConfig?.gap ? `gap-${block.content.flexConfig.gap}` : ''
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
             v-html="t(block.content.text)"></div>
        
        <div v-else-if="block.type === 'heading'">
            <component :is="'h' + (block.content.level || 2)" 
                       :style="textStyleObj"
                       class="font-black tracking-tighter pr-[0.2em] whitespace-pre-wrap"
                       :class="[
                           !block.settings?.style?.fontSize ? (block.content.level === 1 ? 'text-6xl md:text-8xl' : (block.content.level === 2 ? 'text-4xl md:text-6xl' : 'text-2xl md:text-4xl')) : '',
                           block.content.align === 'center' ? 'text-center' : ''
                       ]"
                       v-html="t(block.content.text) || 'Heading'">
            </component>
        </div>

        <div v-else-if="block.type === 'list'">
            <ul v-if="block.content.listType !== 'tasks'" 
                :class="[block.content.listType === 'ol' ? 'list-decimal' : 'list-disc', 'ml-6 opacity-80']"
                :style="{ gap: (block.content.gap || 0) + 'px', display: 'flex', flexDirection: 'column' }">
                <li v-for="(item, i) in block.content.items" :key="i">{{ t(item) }}</li>
            </ul>
            <div v-else class="space-y-2 opacity-80" :style="{ gap: (block.content.gap || 0) + 'px', display: 'flex', flexDirection: 'column' }">
                <div v-for="(item, i) in block.content.items" :key="i" class="flex items-center gap-3">
                    <input type="checkbox" class="checkbox checkbox-xs checkbox-primary" checked readonly />
                    <span>{{ t(item) }}</span>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'quote'" class="pl-6 border-l-4 border-primary/40 italic bg-primary/5 py-4 rounded-r-2xl">
            <p class="text-xl mb-2 font-serif">"{{ t(block.content.text) }}"</p>
            <div v-if="block.content.author" class="mt-4 flex flex-col">
                <span class="text-sm font-bold opacity-80">— {{ t(block.content.author) }}</span>
                <span v-if="block.content.authorTitle" class="text-[10px] uppercase tracking-widest opacity-40 font-bold mt-1">{{ t(block.content.authorTitle) }}</span>
            </div>
        </div>

        <div v-else-if="block.type === 'custom_code'" class="w-full">
            <div v-html="block.content.html"></div>
        </div>

        <!-- 2. Actions -->
        <div v-else-if="block.type === 'button'" :class="[block.content.align === 'center' ? 'text-center' : block.content.align === 'right' ? 'text-right' : 'text-left']">
            <a :href="t(block.content.url)" class="btn" :class="`btn-${block.content.style || 'primary'}`">
                {{ t(block.content.label) }}
            </a>
        </div>

        <div v-else-if="block.type === 'dropdown'" class="dropdown">
            <div tabindex="0" role="button" class="btn m-1">{{ t(block.content.label) }}</div>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow border border-white/10">
                <li v-for="(item, i) in block.content.items" :key="i"><a>{{ t(item) }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'modal'">
            <button class="btn" @click="isEditor ? null : document.getElementById(block.content.id).showModal()">{{ t(block.content.buttonLabel) }}</button>
            <dialog :id="block.content.id" class="modal">
                <div class="modal-box">
                    <h3 class="font-bold text-lg">{{ t(block.content.title) }}</h3>
                    <p class="py-4">{{ t(block.content.text) }}</p>
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
                <input type="checkbox" :checked="block.content.active" disabled v-if="isEditor" />
                <input type="checkbox" :checked="block.content.active" v-else />
                <i :class="block.content.onIcon + ' swap-on w-10 h-10 flex items-center justify-center text-2xl'"></i>
                <i :class="block.content.offIcon + ' swap-off w-10 h-10 flex items-center justify-center text-2xl'"></i>
            </label>
        </div>

        <!-- 3. Data Display -->
        <div v-else-if="block.type === 'accordion'" class="space-y-4">
            <div v-for="(item, i) in block.content.items" :key="i" class="collapse collapse-arrow bg-base-200">
                <input type="radio" :name="'accordion-' + block.id" :checked="i === 0" /> 
                <div class="collapse-title text-xl font-medium">{{ t(item.title) }}</div>
                <div class="collapse-content"><p>{{ t(item.content) }}</p></div>
            </div>
        </div>

        <div v-else-if="block.type === 'avatar'" class="avatar" :class="block.content.online ? 'online' : 'offline'">
            <div class="w-24 rounded-full">
                <img :src="block.content.url || placeholderImg" />
            </div>
        </div>

        <div v-else-if="block.type === 'badge'" class="badge" :class="`badge-${block.content.style || 'primary'}`">
            {{ t(block.content.text) }}
        </div>

        <div v-else-if="block.type === 'card'" class="card bg-base-100 shadow-xl border border-white/5">
            <figure v-if="block.content.image"><img :src="t(block.content.image)" alt="Image" /></figure>
            <div class="card-body">
                <h2 class="card-title">{{ t(block.content.title) }}</h2>
                <p>{{ t(block.content.description) }}</p>
                <div class="card-actions justify-end" v-if="block.content.buttonText">
                    <button class="btn btn-primary">{{ t(block.content.buttonText) }}</button>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'carousel'" class="carousel w-full rounded-box">
            <div v-for="(img, i) in (block.content.images || [])" :key="i" :id="`slide-${block.id}-${i}`" class="carousel-item relative w-full">
                <img :src="img" class="w-full object-cover aspect-video" />
                <div class="absolute inset-0 flex items-center justify-between p-4" v-if="block.content.images?.length > 1">
                    <a :href="`#slide-${block.id}-${i === 0 ? block.content.images.length - 1 : i - 1}`" class="btn btn-circle btn-sm">❮</a>
                    <a :href="`#slide-${block.id}-${i === block.content.images.length - 1 ? 0 : i + 1}`" class="btn btn-circle btn-sm">❯</a>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'chat'" class="w-full flex flex-col gap-2">
            <div v-for="(msg, i) in block.content.messages" :key="i" class="chat" :class="msg.side === 'start' ? 'chat-start' : 'chat-end'">
                <div class="chat-bubble" :class="msg.side === 'start' ? 'chat-bubble-primary' : ''">{{ t(msg.text) }}</div>
            </div>
        </div>

        <div v-else-if="block.type === 'countdown'" class="grid auto-cols-max grid-flow-col gap-5 text-center mx-auto w-fit">
            <div class="flex flex-col">
                <span class="countdown font-mono text-5xl">
                    <span :style="`--value:${block.content.value};`"></span>
                </span>
                {{ t(block.content.unit) }}
            </div>
        </div>

        <div v-else-if="block.type === 'diff'" class="diff aspect-[16/9] w-full max-w-2xl mx-auto rounded-box">
            <div class="diff-item-1">
                <img :src="block.content.img1 || placeholderImg" alt="daisy" />
            </div>
            <div class="diff-item-2">
                <img :src="block.content.img2 || placeholderImg" alt="daisy" />
            </div>
            <div class="diff-resizer"></div>
        </div>

        <!-- 3.5 Feedback -->
        <div v-else-if="block.type === 'alert'" role="alert" class="alert" :class="block.content.type || 'alert-info'">
            <PhInfo weight="bold" class="w-5 h-5" />
            <span>{{ t(block.content.text) }}</span>
        </div>

        <div v-else-if="block.type === 'progress'" class="w-full">
            <progress class="progress w-full" :class="block.content.color || 'progress-primary'" :value="block.content.value" :max="block.content.max"></progress>
        </div>

        <div v-else-if="block.type === 'radial_progress'" class="mx-auto w-fit">
            <div class="radial-progress text-primary" :style="`--value:${block.content.value}; --size:4rem; --thickness: 4px;`" role="progressbar">{{ block.content.value }}%</div>
        </div>


        <div v-else-if="block.type === 'stat'" class="stats shadow mx-auto flex w-fit">
            <div class="stat">
                <div class="stat-figure text-primary" v-if="block.content.icon"><i :class="block.content.icon + ' text-3xl'"></i></div>
                <div class="stat-title">{{ t(block.content.title) }}</div>
                <div class="stat-value text-primary">{{ t(block.content.value) }}</div>
                <div class="stat-desc">{{ t(block.content.desc) }}</div>
            </div>
        </div>

        <div v-else-if="block.type === 'table'" class="overflow-x-auto w-full">
            <table class="table table-zebra w-full border border-base-content/10">
                <tbody>
                    <tr v-for="(row, rIdx) in block.content.rows" :key="rIdx">
                        <td v-for="(cell, cIdx) in row" :key="cIdx" class="border border-base-content/5">{{ t(cell) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else-if="block.type === 'timeline'">
            <ul class="timeline timeline-vertical">
                <li v-for="(item, i) in block.content.items" :key="i">
                    <hr v-if="i > 0" class="bg-primary" />
                    <div class="timeline-start timeline-box">{{ t(item.year) }}</div>
                    <div class="timeline-middle text-primary"><PhCheckCircle weight="fill" class="w-5 h-5" /></div>
                    <div class="timeline-end mb-10">
                        <div class="text-lg font-black">{{ t(item.title) }}</div>
                        <p>{{ t(item.content) }}</p>
                    </div>
                    <hr v-if="i < block.content.items.length - 1" class="bg-primary" />
                </li>
            </ul>
        </div>

        <!-- 4. Data Input -->
        <div v-else-if="block.type === 'text_input'" class="form-control w-full max-w-xs mx-auto">
            <label class="label"><span class="label-text">{{ t(block.content.label) }}</span></label>
            <input type="text" :placeholder="t(block.content.placeholder)" class="input input-bordered w-full" :disabled="isEditor" />
        </div>

        <div v-else-if="block.type === 'textarea'" class="form-control w-full max-w-2xl mx-auto">
            <label class="label"><span class="label-text">{{ t(block.content.label) }}</span></label>
            <textarea class="textarea textarea-bordered h-24" :placeholder="t(block.content.placeholder)" :disabled="isEditor"></textarea>
        </div>

        <div v-else-if="block.type === 'select'" class="form-control w-full max-w-xs mx-auto">
            <label class="label"><span class="label-text">{{ t(block.content.label) }}</span></label>
            <select class="select select-bordered" :disabled="isEditor">
                <option disabled selected>Pick one</option>
                <option v-for="(opt, i) in (t(block.content.options)?.split('\n') || [])" :key="i">{{ opt }}</option>
            </select>
        </div>

        <div v-else-if="block.type === 'checkbox'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <input type="checkbox" :checked="block.content.checked" class="checkbox" :disabled="isEditor" />
                <span class="label-text">{{ t(block.content.label) }}</span>
            </label>
        </div>

        <div v-else-if="block.type === 'radio'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <input type="radio" :name="block.content.group" class="radio" checked :disabled="isEditor" />
                <span class="label-text">{{ t(block.content.label) }}</span>
            </label>
        </div>

        <div v-else-if="block.type === 'toggle'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <span class="label-text">{{ t(block.content.label) }}</span>
                <input type="checkbox" class="toggle toggle-primary" :checked="block.content.checked" :disabled="isEditor" />
            </label>
        </div>

        <div v-else-if="block.type === 'range'" class="flex flex-col gap-2 w-full max-w-xs mx-auto">
            <input type="range" :min="block.content.min" :max="block.content.max" :value="block.content.val" class="range" :disabled="isEditor" />
        </div>

        <div v-else-if="block.type === 'rating'" class="rating mx-auto flex w-fit">
            <input v-for="i in block.content.max" :key="i" type="radio" :name="'rating-' + block.id" class="mask mask-star-2 bg-orange-400" :checked="i === block.content.val" :disabled="isEditor" />
        </div>

        <div v-else-if="block.type === 'file_input'" class="form-control w-full max-w-xs mx-auto">
            <label class="label"><span class="label-text">{{ t(block.content.label) }}</span></label>
            <input type="file" class="file-input file-input-bordered w-full max-w-xs" :disabled="isEditor" />
        </div>

        <!-- 6. Navigation -->
        <div v-else-if="block.type === 'breadcrumbs'" class="breadcrumbs text-sm">
            <ul>
                <li v-for="(item, i) in block.content.items" :key="i"><a>{{ t(item) }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'menu'">
            <ul class="menu bg-base-200 rounded-box w-56">
                <li v-for="(item, i) in block.content.items" :key="i"><a>{{ t(item) }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'navbar'" class="navbar bg-base-200 shadow border border-white/5 rounded-box">
            <div class="flex-1">
                <a class="btn btn-ghost text-xl">{{ t(block.content.title) }}</a>
            </div>
            <div class="flex-none hidden lg:flex">
                <ul class="menu menu-horizontal px-1">
                    <li v-for="(link, i) in block.content.links" :key="i"><a>{{ t(link) }}</a></li>
                </ul>
            </div>
            <div class="flex-none">
                <a class="btn btn-primary btn-sm ml-4" v-if="block.content.actionButton">{{ t(block.content.actionButton) }}</a>
            </div>
        </div>

        <div v-else-if="block.type === 'steps'">
            <ul class="steps w-full">
                <li v-for="(item, i) in block.content.items" :key="i" class="step" :class="i===0 ? 'step-primary' : ''">{{ t(item) }}</li>
            </ul>
        </div>

        <div v-else-if="block.type === 'tabs'" role="tablist" class="tabs tabs-bordered">
            <template v-for="(t_item, i) in block.content.tabs" :key="i">
                <input type="radio" :name="'tab-' + block.id" role="tab" class="tab" :aria-label="t(t_item.title)" :checked="i === 0" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">{{ t(t_item.content) }}</div>
            </template>
        </div>

        <!-- 7. Mockup -->
        <div v-else-if="block.type === 'mockup_browser'" class="mockup-browser border border-white/10 bg-base-300">
            <div class="mockup-browser-toolbar">
                <div class="input">{{ t(block.content.url) }}</div>
            </div>
            <div class="flex justify-center px-4 py-16 bg-base-200 border-t border-white/5">{{ t(block.content.content) }}</div>
        </div>

        <div v-else-if="block.type === 'mockup_code'" class="mockup-code">
            <pre data-prefix="$"><code>{{ t(block.content.code) }}</code></pre>
        </div>

        <div v-else-if="block.type === 'mockup_phone'" class="mockup-phone">
            <div class="camera"></div> 
            <div class="display">
                <div class="artboard artboard-demo phone-1 bg-base-200" :style="t(block.content.url) ? `background-image: url(${t(block.content.url)}); background-size: cover;` : ''">
                    <span v-if="!t(block.content.url)">Hi.</span>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'mockup_window'" class="mockup-window border border-white/10 bg-base-300">
            <div class="flex justify-center px-4 py-16 bg-base-200 border-t border-white/5">{{ t(block.content.content) }}</div>
        </div>

        <!-- 8. Extended -->
        <div v-else-if="block.type === 'posts_list'" class="space-y-4">
            <div :class="block.content.layout === 'grid' ? 'grid grid-cols-1 md:grid-cols-3 gap-6' : 'space-y-4'">
                <!-- Real Data Fallback -->
                <template v-if="Object.keys(page.props.posts || {}).length">
                    <div v-for="post in (page.props.posts.data || page.props.posts).slice(0, block.content.count || 3)" :key="post.id" class="card bg-base-200 shadow border border-white/5" :class="block.content.layout === 'list' ? 'card-side' : ''">
                        <figure v-if="block.content.layout === 'grid' && post.featured_image">
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
                <div v-else v-for="i in (block.content.count || 3)" :key="i" class="card bg-base-200 shadow border border-white/5" :class="block.content.layout === 'list' ? 'card-side' : ''">
                    <figure v-if="block.content.layout === 'grid'"><img :src="placeholderImg" alt="Post" /></figure>
                    <div class="card-body">
                        <h2 class="card-title text-lg">Sample Blog Post #{{i}}</h2>
                        <p class="opacity-50 text-xs line-clamp-2">This is where the post excerpt would appear in the final page.</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'projects_list'" class="space-y-4">
            <div :class="block.content.layout === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-4'">
                <!-- Real Data -->
                <template v-if="page.props.all_projects?.length">
                    <div v-for="project in (page.props.all_projects || []).slice(0, block.content.count || 3)" :key="project.id" class="card bg-base-200 shadow border border-white/5 image-full group">
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
                <div v-else v-for="i in (block.content.count || 3)" :key="i" class="card bg-base-200 shadow border border-white/5 image-full">
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
            <span>{{ t(block.content.prefix) }}</span>
            <span class="text-primary italic border-b-4 border-primary pb-1 mx-2">
                {{ t(block.content.words)?.split('\n')?.[0] || 'Animated Word' }}
            </span>
            <span>{{ t(block.content.suffix) }}</span>
        </div>

        <div v-else-if="block.type === 'divider'" class="divider">{{ t(block.content.text) }}</div>

        <div v-else-if="block.type === 'hero'" class="hero min-h-screen bg-base-200" :style="t(block.content.bg_image) ? `background-image: url(${t(block.content.bg_image)});` : ''">
            <div v-if="t(block.content.bg_image)" class="hero-overlay bg-opacity-60"></div>
            <div class="hero-content text-center" :class="t(block.content.bg_image) ? 'text-neutral-content' : ''">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold">{{ t(block.content.headline) }}</h1>
                    <p class="py-6">{{ t(block.content.subheadline) }}</p>
                    <button class="btn btn-primary" v-if="block.content.primaryLabel">{{ t(block.content.primaryLabel) }}</button>
                </div>
            </div>
        </div>

        <!-- 9. Building -->
        <div v-else-if="block.type === 'template_reference'" 
             class="template-reference-block border-2 border-dashed border-primary/30 rounded-3xl p-8 bg-primary/5 group/ref transition-all hover:bg-primary/10 hover:border-primary/50">
            <div class="flex flex-col items-center justify-center text-center py-4">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-4 transition-transform group-hover/ref:scale-110">
                    <PhPuzzlePiece weight="duotone" class="w-8 h-8 text-primary" />
                </div>
                <h4 class="text-xl font-black italic uppercase tracking-tighter text-primary">Template Part</h4>
                <p class="text-xs opacity-50 mt-1 uppercase tracking-widest font-bold">
                    {{ block.content.template_id ? 'Template ID: ' + block.content.template_id : 'No Template Selected' }}
                </p>
                
                <!-- If we have template data, we'd render it here in non-editor mode -->
                <div v-if="!isEditor && block.content.template_content" class="w-full mt-8">
                     <DynamicBlock v-for="child in block.content.template_content" :key="child.id" :block="child" />
                </div>
                
                <div v-else-if="isEditor" class="mt-4">
                    <button class="btn btn-sm btn-outline btn-primary rounded-full px-8" @click.stop="store.activeBlockId = block.id; store.isEditingBlock = true">
                        Select Template
                    </button>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'content_slot'" 
             class="content-slot-block border-2 border-dashed border-secondary/30 rounded-3xl p-12 bg-secondary/5 group/slot">
            <div class="flex flex-col items-center justify-center text-center py-10 opacity-40 group-hover/slot:opacity-60 transition-opacity">
                <PhSquare weight="duotone" class="w-12 h-12 text-secondary mb-4" />
                <h4 class="text-2xl font-black italic uppercase tracking-widest text-secondary">{{ t(block.content.label) }}</h4>
                <p class="text-sm mt-2 font-medium">This is where the unique page content will be injected.</p>
            </div>
        </div>


    </div>
</template>
