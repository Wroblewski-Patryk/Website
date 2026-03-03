<script setup>
import { ref, onMounted, computed, watch, inject } from 'vue';
import { useGsapRuntime } from '@/Composables/useGsapRuntime';
import { useForm, usePage } from '@inertiajs/vue3';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import draggable from 'vuedraggable';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';

const props = defineProps(['block']);
const page = usePage();
const store = useBlockBuilderStore();
const isEditor = inject('isEditor', false);

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
    
    return {
        ...fillStyles,
        minHeight: l.fullHeight ? '100vh' : st.height,
        height: st.height,
        width: st.width,
        backgroundAttachment: l.fixedBg ? 'fixed' : undefined,
        
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
        zIndex: st.zIndex,
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
        
        borderStyle: (st.borderTopWidth || st.borderRightWidth || st.borderBottomWidth || st.borderLeftWidth) ? 'solid' : undefined,
        
        // Typography (with theme defaults fallback)
        textAlign: st.textAlign || themeDefaults.textAlign,
        fontWeight: st.fontWeight || themeDefaults.fontWeight,
        fontFamily: st.fontFamily || themeDefaults.fontFamily,
        fontSize: st.fontSize || themeDefaults.fontSize,
        letterSpacing: st.letterSpacing || themeDefaults.letterSpacing,
        opacity: themeDefaults.opacity || undefined, // Opacity is not in default style panel yet, but can be set in theme
    };
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
         :id="block.settings?.id"
         :data-timeline="block.settings?.animations?.timelineId"
         :class="[block.settings?.class]" 
         :style="styleObj"
         class="transition-all duration-500">
        
        <!-- Section Block -->
        <div v-if="block.type === 'section'" class="w-full relative transition-colors p-4"
             :class="{'min-h-[50px] border border-dashed editor-dashed-frame': isEditor}">
            <template v-if="isEditor">
                <draggable 
                    v-model="block.children" 
                    :group="'blocks'"
                    item-key="id"
                    handle=".drag-handle"
                    ghost-class="ghost-block"
                    class="min-h-[50px] flex flex-col gap-2 w-full">
                    <template #item="{ element }">
                        <div class="group/block relative w-full"
                             @click.stop="store.activeBlockId = element.id"
                             @mouseover.stop="store.hoveredBlockId = element.id"
                             @mouseout.stop="store.hoveredBlockId = null"
                             :class="{ 'editor-ring': store.activeBlockId === element.id }">
                            <div class="absolute left-2 top-1/2 -translate-y-1/2 z-50 transition-opacity"
                                 :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                <div class="drag-handle btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur cursor-move text-base-content/60 hover:text-primary hover:bg-base-200 shadow-sm rounded-box relative z-50 pointer-events-auto"><i class="fas fa-arrows-alt"></i></div>
                            </div>
                            <div class="absolute right-2 top-1/2 -translate-y-1/2 z-50 transition-opacity flex items-center gap-1"
                                 :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                <button type="button" @click.stop.prevent="store.duplicateBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-primary/80 hover:bg-primary hover:text-primary-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Duplicate Block"><i class="fas fa-copy"></i></button>
                                <button type="button" @click.stop.prevent="store.removeBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-error/80 hover:bg-error hover:text-error-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Delete Block"><i class="fas fa-trash"></i></button>
                            </div>
                            <DynamicBlock :block="element" />
                        </div>
                    </template>
                </draggable>
                <div v-if="!block.children?.length" class="absolute inset-0 flex items-center justify-center opacity-30 pointer-events-none text-xs font-bold uppercase tracking-widest border border-dashed border-base-content/10 m-2 rounded-lg bg-base-100/30 backdrop-blur-sm">Drop blocks here</div>
            </template>
            <template v-else>
                <div class="flex flex-col gap-2 w-full">
                    <DynamicBlock v-for="child in block.children" :key="child.id" :block="child" />
                </div>
            </template>
        </div>

        <!-- 1. Typography -->
        <div v-else-if="block.type === 'paragraph'" class="prose prose-lg" :class="{'opacity-80': !isEditor}" :style="textStyleObj" v-html="block.content.text"></div>
        
        <div v-else-if="block.type === 'heading'">
            <component :is="'h' + (block.content.level || 2)" 
                       :style="textStyleObj"
                       class="font-black italic uppercase tracking-tighter pr-[0.2em]"
                       :class="[
                           block.content.level === 1 ? 'text-6xl md:text-8xl' : (block.content.level === 2 ? 'text-4xl md:text-6xl' : 'text-2xl md:text-4xl'),
                           block.content.align === 'center' ? 'text-center' : ''
                       ]">
                <template v-if="block.settings?.animations?.preset === 'reveal-text'">
                    <span v-for="(char, i) in (block.content.text || 'Heading')" :key="i" class="inline-block whitespace-pre">{{ char }}</span>
                </template>
                <template v-else>
                    {{ block.content.text || 'Heading' }}
                </template>
            </component>
        </div>

        <div v-else-if="block.type === 'list'">
            <ul :class="[block.content.type === 'numbered' ? 'list-decimal' : 'list-disc', 'ml-6 opacity-80']">
                <li v-for="(item, i) in block.content.items" :key="i">{{ item }}</li>
            </ul>
        </div>

        <div v-else-if="block.type === 'quote'" class="pl-4 border-l-4 border-primary italic">
            <p class="text-xl mb-2">"{{ block.content.text }}"</p>
            <p v-if="block.content.author" class="text-sm font-bold opacity-70">— {{ block.content.author }}</p>
        </div>

        <div v-else-if="block.type === 'custom_code'" class="w-full">
            <div v-html="block.content.html"></div>
        </div>

        <!-- 2. Actions -->
        <div v-else-if="block.type === 'button'" :class="[block.content.align === 'center' ? 'text-center' : block.content.align === 'right' ? 'text-right' : 'text-left']">
            <a :href="block.content.url" class="btn" :class="`btn-${block.content.style || 'primary'}`">
                {{ block.content.label }}
            </a>
        </div>

        <div v-else-if="block.type === 'dropdown'" class="dropdown">
            <div tabindex="0" role="button" class="btn m-1">{{ block.content.label }}</div>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow border border-white/10">
                <li v-for="(item, i) in block.content.items" :key="i"><a>{{ item }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'modal'">
            <button class="btn" @click="isEditor ? null : document.getElementById(block.content.id).showModal()">{{ block.content.buttonLabel }}</button>
            <dialog :id="block.content.id" class="modal">
                <div class="modal-box">
                    <h3 class="font-bold text-lg">{{ block.content.title }}</h3>
                    <p class="py-4">{{ block.content.text }}</p>
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
                <div class="collapse-title text-xl font-medium">{{ item.title }}</div>
                <div class="collapse-content"><p>{{ item.content }}</p></div>
            </div>
        </div>

        <div v-else-if="block.type === 'avatar'" class="avatar" :class="block.content.online ? 'online' : 'offline'">
            <div class="w-24 rounded-full">
                <img :src="block.content.url || '/img/placeholder.png'" />
            </div>
        </div>

        <div v-else-if="block.type === 'badge'" class="badge" :class="`badge-${block.content.style || 'primary'}`">
            {{ block.content.text }}
        </div>

        <div v-else-if="block.type === 'card'" class="card bg-base-100 shadow-xl border border-white/5">
            <figure v-if="block.content.image"><img :src="block.content.image" alt="Image" /></figure>
            <div class="card-body">
                <h2 class="card-title">{{ block.content.title }}</h2>
                <p>{{ block.content.description }}</p>
                <div class="card-actions justify-end" v-if="block.content.buttonText">
                    <button class="btn btn-primary">{{ block.content.buttonText }}</button>
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
                <div class="chat-bubble" :class="msg.side === 'start' ? 'chat-bubble-primary' : ''">{{ msg.text }}</div>
            </div>
        </div>

        <div v-else-if="block.type === 'countdown'" class="grid auto-cols-max grid-flow-col gap-5 text-center mx-auto w-fit">
            <div class="flex flex-col">
                <span class="countdown font-mono text-5xl">
                    <span :style="`--value:${block.content.value};`"></span>
                </span>
                {{ block.content.unit }}
            </div>
        </div>

        <div v-else-if="block.type === 'diff'" class="diff aspect-[16/9] w-full max-w-2xl mx-auto rounded-box">
            <div class="diff-item-1">
                <img :src="block.content.img1 || '/img/placeholder.png'" alt="daisy" />
            </div>
            <div class="diff-item-2">
                <img :src="block.content.img2 || '/img/placeholder.png'" alt="daisy" />
            </div>
            <div class="diff-resizer"></div>
        </div>

        <!-- 3.5 Feedback -->
        <div v-else-if="block.type === 'alert'" role="alert" class="alert" :class="block.content.type || 'alert-info'">
            <i class="fas fa-info-circle"></i>
            <span>{{ block.content.text }}</span>
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
                <div class="stat-title">{{ block.content.title }}</div>
                <div class="stat-value text-primary">{{ block.content.value }}</div>
                <div class="stat-desc">{{ block.content.desc }}</div>
            </div>
        </div>

        <div v-else-if="block.type === 'table'" class="overflow-x-auto w-full">
            <table class="table table-zebra w-full border border-base-content/10">
                <tbody>
                    <tr v-for="(row, rIdx) in block.content.rows" :key="rIdx">
                        <td v-for="(cell, cIdx) in row" :key="cIdx" class="border border-base-content/5">{{ cell }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else-if="block.type === 'timeline'">
            <ul class="timeline timeline-vertical">
                <li v-for="(item, i) in block.content.items" :key="i">
                    <hr v-if="i > 0" class="bg-primary" />
                    <div class="timeline-start timeline-box">{{ item.year }}</div>
                    <div class="timeline-middle text-primary"><i class="fas fa-check-circle"></i></div>
                    <div class="timeline-end mb-10">
                        <div class="text-lg font-black">{{ item.title }}</div>
                        {{ item.content }}
                    </div>
                    <hr v-if="i < block.content.items.length - 1" class="bg-primary" />
                </li>
            </ul>
        </div>

        <!-- 4. Data Input -->
        <div v-else-if="block.type === 'text_input'" class="form-control w-full max-w-xs mx-auto">
            <label class="label"><span class="label-text">{{ block.content.label }}</span></label>
            <input type="text" :placeholder="block.content.placeholder" class="input input-bordered w-full" :disabled="isEditor" />
        </div>

        <div v-else-if="block.type === 'textarea'" class="form-control w-full max-w-2xl mx-auto">
            <label class="label"><span class="label-text">{{ block.content.label }}</span></label>
            <textarea class="textarea textarea-bordered h-24" :placeholder="block.content.placeholder" :disabled="isEditor"></textarea>
        </div>

        <div v-else-if="block.type === 'select'" class="form-control w-full max-w-xs mx-auto">
            <label class="label"><span class="label-text">{{ block.content.label }}</span></label>
            <select class="select select-bordered" :disabled="isEditor">
                <option disabled selected>Pick one</option>
                <option v-for="(opt, i) in (block.content.options?.split('\n') || [])" :key="i">{{ opt }}</option>
            </select>
        </div>

        <div v-else-if="block.type === 'checkbox'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <input type="checkbox" :checked="block.content.checked" class="checkbox" :disabled="isEditor" />
                <span class="label-text">{{ block.content.label }}</span>
            </label>
        </div>

        <div v-else-if="block.type === 'radio'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <input type="radio" :name="block.content.group" class="radio" checked :disabled="isEditor" />
                <span class="label-text">{{ block.content.label }}</span>
            </label>
        </div>

        <div v-else-if="block.type === 'toggle'" class="form-control mx-auto w-fit">
            <label class="cursor-pointer flex items-center gap-2">
                <span class="label-text">{{ block.content.label }}</span>
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
            <label class="label"><span class="label-text">{{ block.content.label }}</span></label>
            <input type="file" class="file-input file-input-bordered w-full max-w-xs" :disabled="isEditor" />
        </div>

        <!-- 6. Navigation -->
        <div v-else-if="block.type === 'breadcrumbs'" class="breadcrumbs text-sm">
            <ul>
                <li v-for="(item, i) in block.content.items" :key="i"><a>{{ item }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'menu'">
            <ul class="menu bg-base-200 rounded-box w-56">
                <li v-for="(item, i) in block.content.items" :key="i"><a>{{ item }}</a></li>
            </ul>
        </div>

        <div v-else-if="block.type === 'navbar'" class="navbar bg-base-200 shadow border border-white/5 rounded-box">
            <div class="flex-1">
                <a class="btn btn-ghost text-xl">{{ block.content.title }}</a>
            </div>
            <div class="flex-none hidden lg:flex">
                <ul class="menu menu-horizontal px-1">
                    <li v-for="(link, i) in block.content.links" :key="i"><a>{{ link }}</a></li>
                </ul>
            </div>
            <div class="flex-none">
                <a class="btn btn-primary btn-sm ml-4" v-if="block.content.actionButton">{{ block.content.actionButton }}</a>
            </div>
        </div>

        <div v-else-if="block.type === 'steps'">
            <ul class="steps w-full">
                <li v-for="(item, i) in block.content.items" :key="i" class="step" :class="i===0 ? 'step-primary' : ''">{{ item }}</li>
            </ul>
        </div>

        <div v-else-if="block.type === 'tabs'" role="tablist" class="tabs tabs-bordered">
            <template v-for="(t, i) in block.content.tabs" :key="i">
                <input type="radio" :name="'tab-' + block.id" role="tab" class="tab" :aria-label="t.title" :checked="i === 0" />
                <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">{{ t.content }}</div>
            </template>
        </div>

        <!-- 7. Mockup -->
        <div v-else-if="block.type === 'mockup_browser'" class="mockup-browser border border-white/10 bg-base-300">
            <div class="mockup-browser-toolbar">
                <div class="input">{{ block.content.url }}</div>
            </div>
            <div class="flex justify-center px-4 py-16 bg-base-200 border-t border-white/5">{{ block.content.content }}</div>
        </div>

        <div v-else-if="block.type === 'mockup_code'" class="mockup-code">
            <pre data-prefix="$"><code>{{ block.content.code }}</code></pre>
        </div>

        <div v-else-if="block.type === 'mockup_phone'" class="mockup-phone">
            <div class="camera"></div> 
            <div class="display">
                <div class="artboard artboard-demo phone-1 bg-base-200" :style="block.content.url ? `background-image: url(${block.content.url}); background-size: cover;` : ''">
                    <span v-if="!block.content.url">Hi.</span>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'mockup_window'" class="mockup-window border border-white/10 bg-base-300">
            <div class="flex justify-center px-4 py-16 bg-base-200 border-t border-white/5">{{ block.content.content }}</div>
        </div>

        <!-- 8. Extended -->
        <div v-else-if="block.type === 'posts_list'" class="space-y-4">
            <div :class="block.content.layout === 'grid' ? 'grid grid-cols-1 md:grid-cols-3 gap-6' : 'space-y-4'">
                <div v-for="i in (block.content.count || 3)" :key="i" class="card bg-base-200 shadow border border-white/5" :class="block.content.layout === 'list' ? 'card-side' : ''">
                    <figure v-if="block.content.layout === 'grid'"><img :src="'/img/placeholder.png'" alt="Post" /></figure>
                    <figure v-if="block.content.layout === 'list'" class="w-1/3"><img :src="'/img/placeholder.png'" class="h-full object-cover" alt="Post" /></figure>
                    <div class="card-body" :class="block.content.layout === 'list' ? 'w-2/3' : ''">
                        <h2 class="card-title">Blog Post Title #{{i}}</h2>
                        <p class="opacity-70 text-sm">Excerpt for blog post content goes here.</p>
                        <div class="card-actions justify-end mt-4">
                            <button class="btn btn-sm btn-outline">Read More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'projects_list'" class="space-y-4">
            <div :class="block.content.layout === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-4'">
                <div v-for="i in (block.content.count || 3)" :key="i" class="card bg-base-200 shadow border border-white/5 image-full">
                    <figure><img :src="'/img/placeholder.png'" alt="Project" /></figure>
                    <div class="card-body items-center text-center justify-center">
                        <h2 class="card-title text-3xl font-black italic uppercase">Project #{{i}}</h2>
                        <p class="flex-grow-0">Brief description of the project.</p>
                        <div class="card-actions mt-4">
                            <button class="btn btn-primary">Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'text_rotate'" class="text-3xl md:text-5xl font-bold text-center py-10">
            <span>{{ block.content.prefix }}</span>
            <span class="text-primary italic border-b-4 border-primary pb-1 mx-2">
                {{ block.content.words?.split('\n')?.[0] || 'Animated Word' }}
            </span>
            <span>{{ block.content.suffix }}</span>
        </div>

        <div v-else-if="block.type === 'divider'" class="divider">{{ block.content.text }}</div>

        <div v-else-if="block.type === 'hero'" class="hero min-h-screen bg-base-200" :style="block.content.bg_image ? `background-image: url(${block.content.bg_image});` : ''">
            <div v-if="block.content.bg_image" class="hero-overlay bg-opacity-60"></div>
            <div class="hero-content text-center" :class="block.content.bg_image ? 'text-neutral-content' : ''">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold">{{ block.content.headline }}</h1>
                    <p class="py-6">{{ block.content.subheadline }}</p>
                    <button class="btn btn-primary" v-if="block.content.primaryLabel">{{ block.content.primaryLabel }}</button>
                </div>
            </div>
        </div>

        <!-- Layout Blocks -->
        <div v-else-if="block.type === 'columns'" class="max-w-7xl mx-auto px-6 grid gap-8" :class="[`grid-cols-1 md:grid-cols-${block.content.count || 2}`, {'border border-dashed editor-dashed-frame p-4 rounded-xl group relative': isEditor}]">
             <template v-if="isEditor">
                 <div class="absolute -top-3 left-4 text-[10px] font-black uppercase tracking-widest bg-base-100 px-2 opacity-50 z-10 transition-opacity"
                      :class="{'!opacity-100': store.hoveredBlockId === block.id || store.activeBlockId === block.id}"
                      :style="{ color: store.hoveredBlockId === block.id || store.activeBlockId === block.id ? 'var(--admin-p)' : '' }">Columns Layout</div>
                 <div v-for="i in (block.content.count || 2)" :key="i" class="space-y-4 border border-dashed editor-dashed-frame-sub p-2 rounded-lg relative min-h-[50px]">
                     <div class="absolute -top-2 left-2 text-[8px] font-black uppercase tracking-widest bg-base-100 px-2 opacity-30 z-10">Col {{ i }}</div>
                     <draggable 
                        v-model="block.children" 
                        :group="'blocks'"
                        item-key="id"
                        handle=".drag-handle"
                        ghost-class="ghost-block"
                        class="min-h-[50px] w-full flex flex-col gap-2">
                        <template #item="{ element }">
                            <div v-if="element.column === i" class="group/block relative w-full"
                                 @click.stop="store.activeBlockId = element.id"
                                 @mouseover.stop="store.hoveredBlockId = element.id"
                                 @mouseout.stop="store.hoveredBlockId = null"
                                 :class="{ 'editor-ring': store.activeBlockId === element.id }">
                                <div class="absolute left-2 top-1/2 -translate-y-1/2 z-50 transition-opacity"
                                     :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                    <div class="drag-handle btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur cursor-move text-base-content/60 hover:text-primary hover:bg-base-200 shadow-sm rounded-box relative z-50 pointer-events-auto"><i class="fas fa-arrows-alt"></i></div>
                                </div>
                                <div class="absolute right-2 top-1/2 -translate-y-1/2 z-50 transition-opacity flex items-center gap-1"
                                     :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                    <button type="button" @click.stop.prevent="store.duplicateBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-primary/80 hover:bg-primary hover:text-primary-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Duplicate Block"><i class="fas fa-copy"></i></button>
                                    <button type="button" @click.stop.prevent="store.removeBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-error/80 hover:bg-error hover:text-error-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Delete Block"><i class="fas fa-trash"></i></button>
                                </div>
                                <DynamicBlock :block="element" />
                            </div>
                        </template>
                    </draggable>
                 </div>
             </template>
             <template v-else>
                 <div v-for="i in (block.content.count || 2)" :key="i" class="space-y-4">
                     <DynamicBlock v-for="child in block.children?.filter(c => c.column === i)" :key="child.id" :block="child" />
                 </div>
             </template>
        </div>

        <div v-else-if="block.type === 'group' || block.type === 'stack'" class="w-full relative transition-colors p-4" 
             :class="[block.type === 'stack' && !isEditor ? 'flex flex-col gap-4' : '', {'min-h-[50px] border border-dashed editor-dashed-frame': isEditor}]">
            <template v-if="isEditor">
                <div class="absolute -top-3 left-4 text-[10px] font-black uppercase tracking-widest bg-base-100 px-2 opacity-50 z-10 transition-opacity"
                     :class="{'!opacity-100': store.hoveredBlockId === block.id || store.activeBlockId === block.id}"
                     :style="{ color: store.hoveredBlockId === block.id || store.activeBlockId === block.id ? 'var(--admin-p)' : '' }">{{ block.type }}</div>
                <draggable 
                    v-model="block.children" 
                    :group="'blocks'"
                    item-key="id"
                    handle=".drag-handle"
                    ghost-class="ghost-block"
                    class="min-h-[50px] w-full"
                    :class="block.type === 'stack' ? 'flex flex-col gap-4' : ''">
                    <template #item="{ element }">
                        <div class="group/block relative w-full"
                             @click.stop="store.activeBlockId = element.id"
                             @mouseover.stop="store.hoveredBlockId = element.id"
                             @mouseout.stop="store.hoveredBlockId = null"
                             :class="{ 'editor-ring': store.activeBlockId === element.id }">
                            <div class="absolute left-2 top-1/2 -translate-y-1/2 z-50 transition-opacity"
                                 :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                <div class="drag-handle btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur cursor-move text-base-content/60 hover:text-primary hover:bg-base-200 shadow-sm rounded-box relative z-50 pointer-events-auto"><i class="fas fa-arrows-alt"></i></div>
                            </div>
                            <div class="absolute right-2 top-1/2 -translate-y-1/2 z-50 transition-opacity flex items-center gap-1"
                                 :class="{'opacity-100 pointer-events-auto': store.hoveredBlockId === element.id || store.activeBlockId === element.id, 'opacity-0 pointer-events-none': store.hoveredBlockId !== element.id && store.activeBlockId !== element.id}">
                                <button type="button" @click.stop.prevent="store.duplicateBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-primary/80 hover:bg-primary hover:text-primary-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Duplicate Block"><i class="fas fa-copy"></i></button>
                                <button type="button" @click.stop.prevent="store.removeBlock(element.id)" class="btn btn-square btn-xs btn-ghost bg-base-100 border border-base-content/10 backdrop-blur text-error/80 hover:bg-error hover:text-error-content shadow-sm rounded-box relative z-50 pointer-events-auto" title="Delete Block"><i class="fas fa-trash"></i></button>
                            </div>
                            <DynamicBlock :block="element" />
                        </div>
                    </template>
                </draggable>
                <div v-if="!block.children?.length" class="absolute inset-0 flex items-center justify-center opacity-30 pointer-events-none text-xs font-bold uppercase tracking-widest border border-dashed border-base-content/10 m-2 rounded-lg bg-base-100/30 backdrop-blur-sm">Drop here</div>
            </template>
            <template v-else>
                <div class="w-full" :class="block.type === 'stack' ? 'flex flex-col gap-4' : ''">
                    <DynamicBlock v-for="child in block.children" :key="child.id" :block="child" />
                </div>
            </template>
        </div>
    </div>
</template>
