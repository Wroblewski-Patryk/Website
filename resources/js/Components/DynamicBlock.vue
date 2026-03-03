<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useGsapRuntime } from '@/Composables/useGsapRuntime';
import { useForm, usePage } from '@inertiajs/vue3';
import DynamicBlock from '@/Components/DynamicBlock.vue';

const props = defineProps(['block']);
const page = usePage();

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
        
        // Spacing
        marginTop: st.marginTop,
        marginBottom: st.marginBottom,
        marginLeft: st.marginLeft,
        marginRight: st.marginRight,
        paddingTop: st.paddingTop,
        paddingBottom: st.paddingBottom,
        paddingLeft: st.paddingLeft,
        paddingRight: st.paddingRight,
        
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
        
        // Typography
        textAlign: st.textAlign,
        fontWeight: st.fontWeight,
        fontFamily: st.fontFamily,
        fontSize: st.fontSize,
        letterSpacing: st.letterSpacing,
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
        <div v-if="block.type === 'section'" class="w-full">
            <div>
                <DynamicBlock v-for="child in block.children" :key="child.id" :block="child" />
            </div>
        </div>

        <!-- Content Blocks -->
        <div v-else-if="block.type === 'paragraph'" class="prose prose-lg opacity-80" :style="textStyleObj" v-html="block.content.text"></div>
        
        <div v-else-if="block.type === 'heading'">
            <component :is="'h' + (block.content.level || 2)" 
                       :style="textStyleObj"
                       :class="[
                           'font-black italic uppercase tracking-tighter pr-[0.2em]', 
                           block.content.level === 1 ? 'text-6xl md:text-8xl' : 'text-4xl md:text-6xl',
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

        <div v-else-if="block.type === 'quote'">
            <blockquote class="border-l-4 border-primary pl-6 py-2 italic text-2xl font-serif opacity-90">
                "{{ block.content.text }}"
                <cite v-if="block.content.author" class="block not-italic text-sm font-sans mt-4 opacity-50">— {{ block.content.author }}</cite>
            </blockquote>
        </div>

        <div v-else-if="block.type === 'divider'" class="max-w-7xl mx-auto px-6">
            <hr :class="['border-base-content/20', block.content.style === 'dashed' ? 'border-dashed' : (block.content.style === 'dotted' ? 'border-dotted' : 'border-solid')]" />
        </div>

        <div v-else-if="block.type === 'table'" class="max-w-7xl mx-auto px-6 overflow-x-auto">
            <table class="table table-zebra w-full border border-base-content/10">
                <tbody>
                    <tr v-for="(row, rIdx) in block.content.rows" :key="rIdx">
                        <td v-for="(cell, cIdx) in row" :key="cIdx" class="border border-base-content/5">{{ cell }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else-if="block.type === 'spacer'" :class="block.content.height"></div>

        <!-- Marketing Blocks -->
        <div v-else-if="block.type === 'hero'" class="relative min-h-[80vh] flex items-center justify-center text-center px-6 overflow-hidden">
             <div v-if="block.content.bg_image" class="absolute inset-0 z-0">
                 <img :src="block.content.bg_image" class="w-full h-full object-cover opacity-30" />
                 <div class="absolute inset-0 bg-gradient-to-b from-base-100 via-transparent to-base-100"></div>
             </div>
             <div class="max-w-4xl relative z-10">
                <h1 class="text-7xl md:text-9xl font-black italic uppercase tracking-tighter mb-6 pr-[0.2em]" :style="textStyleObj">{{ block.content.headline }}</h1>
                <p class="text-xl md:text-2xl opacity-60 mb-12" :style="textStyleObj">{{ block.content.subheadline }}</p>
                <div v-if="block.content.primaryLabel" class="flex gap-4 justify-center">
                    <button class="btn btn-primary rounded-full px-8">{{ block.content.primaryLabel }}</button>
                    <button v-if="block.content.secondaryLabel" class="btn btn-outline rounded-full px-8">{{ block.content.secondaryLabel }}</button>
                </div>
             </div>
        </div>

        <div v-else-if="block.type === 'card'" class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-base-200 p-8 rounded-3xl border border-white/5 hover:border-primary/50 transition-colors group">
                <i v-if="block.content.icon" :class="block.content.icon" class="text-4xl text-primary mb-6 block group-hover:scale-110 transition-transform"></i>
                <h3 class="text-2xl font-black uppercase italic mb-4">{{ block.content.title }}</h3>
                <p class="opacity-60">{{ block.content.description }}</p>
            </div>
        </div>

        <div v-else-if="block.type === 'testimonial'" class="max-w-4xl mx-auto px-6 text-center">
            <i class="fas fa-quote-left text-6xl opacity-10 mb-8 block"></i>
            <p class="text-3xl font-serif italic mb-8">"{{ block.content.text }}"</p>
            <div class="flex flex-col items-center">
                <span class="font-black uppercase italic tracking-tighter text-xl">{{ block.content.author }}</span>
                <span class="text-xs opacity-40 uppercase tracking-widest mt-1">{{ block.content.company }}</span>
            </div>
        </div>

        <div v-else-if="block.type === 'faq'" class="max-w-3xl mx-auto px-6 space-y-4">
            <div v-for="(item, i) in block.content.items" :key="i" class="collapse collapse-plus bg-base-200 rounded-2xl border border-white/5">
                <input type="radio" :name="'faq-' + block.id" /> 
                <div class="collapse-title text-xl font-bold p-6">
                    {{ item.q }}
                </div>
                <div class="collapse-content px-6 pb-6 opacity-70"> 
                    <p>{{ item.a }}</p>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'pricing'" class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="(plan, i) in block.content.plans" :key="i" class="bg-base-200 p-8 rounded-3xl border border-white/5 flex flex-col items-center text-center">
                <h3 class="text-xl font-black uppercase italic italic mb-2">{{ plan.name }}</h3>
                <div class="text-4xl font-black italic tracking-tighter mb-6 text-primary">{{ plan.price }}</div>
                <ul class="space-y-2 mb-8 text-sm opacity-60">
                    <li v-for="(feature, f) in plan.features" :key="f">{{ feature }}</li>
                </ul>
                <button class="btn btn-primary btn-block rounded-full mt-auto">{{ plan.button }}</button>
            </div>
        </div>

        <div v-else-if="block.type === 'counter'" class="max-w-7xl mx-auto px-6 flex justify-center">
            <div class="text-center">
                <div class="text-8xl font-black italic tracking-tighter text-primary flex items-center justify-center">
                    {{ block.content.number }}<span class="text-4xl ml-2">{{ block.content.suffix }}</span>
                </div>
                <div class="text-xs opacity-40 uppercase font-black tracking-[0.3em] mt-4">{{ block.content.label }}</div>
            </div>
        </div>

        <div v-else-if="block.type === 'cta_box'" class="max-w-5xl mx-auto px-6">
            <div class="bg-primary text-primary-content p-12 rounded-box text-center space-y-6">
                <h2 class="text-4xl font-black uppercase italic">{{ block.content.title }}</h2>
                <button class="btn btn-lg btn-secondary rounded-full px-12">{{ block.content.button_label }}</button>
            </div>
        </div>

        <!-- Media Blocks -->
        <div v-else-if="block.type === 'image'" class="max-w-7xl mx-auto px-6 flex justify-center">
            <figure class="group overflow-hidden rounded-box shadow-2xl bg-base-200">
                <img :src="block.content.url || '/img/placeholder.png'" :alt="block.content.alt" class="max-w-full" />
                <figcaption v-if="block.content.caption" class="p-4 text-center text-xs opacity-50">{{ block.content.caption }}</figcaption>
            </figure>
        </div>

        <div v-else-if="block.type === 'gallery'" class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="(img, i) in block.content.images" :key="i" class="aspect-square rounded-2xl overflow-hidden bg-base-200 group">
                <img :src="img || '/img/placeholder.png'" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
            </div>
        </div>

        <div v-else-if="block.type === 'video'" class="max-w-7xl mx-auto px-6 aspect-video rounded-3xl overflow-hidden shadow-2xl">
            <iframe v-if="block.content.source === 'youtube'" :src="'https://www.youtube.com/embed/' + block.content.url.split('v=')[1]" class="w-full h-full border-0"></iframe>
            <video v-else-if="block.content.source === 'self'" :src="block.content.url" controls class="w-full h-full object-cover"></video>
        </div>

        <div v-else-if="block.type === 'media_text'" class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row gap-12 items-center" :class="block.content.media_position === 'right' ? 'md:flex-row-reverse' : ''">
            <div class="w-full md:w-1/2 aspect-video rounded-3xl overflow-hidden shadow-2xl">
                <img :src="block.content.media_url || '/img/placeholder.png'" class="w-full h-full object-cover" />
            </div>
            <div class="w-full md:w-1/2 space-y-6">
                <div class="prose prose-xl opacity-80" v-html="block.content.text"></div>
            </div>
        </div>

        <!-- Theme Blocks -->
        <div v-else-if="block.type === 'site_logo'" class="max-w-7xl mx-auto px-6">
             <div class="text-2xl font-black italic uppercase tracking-tighter flex items-center gap-2">
                 <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-primary-content"><i class="fas fa-bolt"></i></div>
                 <span>Brand<span class="text-primary">Name</span></span>
             </div>
        </div>

        <div v-else-if="block.type === 'breadcrumbs'" class="max-w-7xl mx-auto px-6 text-[10px] opacity-40 uppercase font-black tracking-widest flex items-center gap-2">
            <span>Home</span> <i class="fas fa-chevron-right text-[8px]"></i> <span>Pages</span> <i class="fas fa-chevron-right text-[8px]"></i> <span class="text-primary">Current Page</span>
        </div>

        <div v-else-if="block.type === 'navigation'" class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <div class="flex gap-8">
                <a v-for="item in menuItems" :key="item.id" :href="item.url" class="text-sm font-bold uppercase tracking-widest hover:text-primary transition-colors">{{ item.label }}</a>
            </div>
        </div>

        <div v-else-if="block.type === 'posts_list'" class="max-w-7xl mx-auto px-6">
            <div :class="block.content.layout === 'grid' ? 'grid grid-cols-1 md:grid-cols-3 gap-8' : 'space-y-8'">
                <div v-for="i in (block.content.count || 3)" :key="i" class="group bg-base-200 rounded-3xl overflow-hidden border border-white/5 hover:border-primary/30 transition-all">
                    <div class="aspect-video bg-base-300 relative overflow-hidden">
                        <img :src="'/api/placeholder/800/450'" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                        <div class="absolute top-4 left-4 badge badge-primary font-black italic">ARTIKEL</div>
                    </div>
                    <div class="p-8 space-y-4">
                        <div class="text-[10px] opacity-40 uppercase font-black tracking-widest">March 1, 2026</div>
                        <h3 class="text-2xl font-black italic uppercase tracking-tighter leading-none">Sample Post Title {{ i }}</h3>
                        <p class="text-sm opacity-60 line-clamp-2">This is a preview of the post content to demonstrate the layout engine.</p>
                        <div class="pt-4">
                            <button class="btn btn-ghost btn-sm p-0 hover:bg-transparent hover:text-primary transition-colors text-[10px] font-black uppercase tracking-widest group/btn">
                                Read More <i class="fas fa-arrow-right ml-2 group-hover/btn:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'google_maps'" class="max-w-7xl mx-auto px-6">
            <div class="w-full aspect-[21/9] rounded-3xl overflow-hidden shadow-2xl grayscale contrast-125 border border-white/10">
                <iframe 
                    width="100%" 
                    height="100%" 
                    frameborder="0" 
                    scrolling="no" 
                    marginheight="0" 
                    marginwidth="0" 
                    :src="'https://maps.google.com/maps?q=' + encodeURIComponent(block.content.address || 'London') + '&t=&z=' + (block.content.zoom || 14) + '&ie=UTF8&iwloc=&output=embed'">
                </iframe>
            </div>
        </div>

        <!-- Form Field Blocks (New) -->
        <div v-else-if="block.type === 'form_input'" class="max-w-xl mx-auto px-6 py-2">
            <div class="form-control">
                <label v-if="block.content.label" class="label"><span class="label-text font-black uppercase italic tracking-widest text-[10px] opacity-50">{{ block.content.label }}</span></label>
                <input :type="block.content.type || 'text'" 
                       :placeholder="block.content.placeholder" 
                       :required="block.content.required"
                       class="input input-bordered bg-base-200/50 rounded-2xl border-white/10 focus:border-primary/50 transition-all font-medium" />
            </div>
        </div>

        <div v-else-if="block.type === 'form_textarea'" class="max-w-xl mx-auto px-6 py-2">
            <div class="form-control">
                <label v-if="block.content.label" class="label"><span class="label-text font-black uppercase italic tracking-widest text-[10px] opacity-50">{{ block.content.label }}</span></label>
                <textarea :placeholder="block.content.placeholder" 
                          :required="block.content.required"
                          class="textarea textarea-bordered bg-base-200/50 rounded-2xl border-white/10 focus:border-primary/50 transition-all font-medium h-32"></textarea>
            </div>
        </div>

        <div v-else-if="block.type === 'form_select'" class="max-w-xl mx-auto px-6 py-2">
            <div class="form-control">
                <label v-if="block.content.label" class="label"><span class="label-text font-black uppercase italic tracking-widest text-[10px] opacity-50">{{ block.content.label }}</span></label>
                <select :required="block.content.required" class="select select-bordered bg-base-200/50 rounded-2xl border-white/10 focus:border-primary/50 transition-all font-medium">
                    <option v-for="(opt, oIdx) in (block.content.options?.split('\n') || [])" :key="oIdx" :value="opt">{{ opt }}</option>
                </select>
            </div>
        </div>

        <!-- Existing Blocks -->
        <div v-else-if="block.type === 'portfolio'" class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 gap-20">
                <div v-for="(project, idx) in displayedProjects" :key="idx" class="group flex flex-col md:flex-row gap-12 items-center">
                    <div class="w-full md:w-1/2 relative overflow-hidden rounded-box shadow-2xl aspect-video bg-base-200">
                        <img :src="project.desktop_image || '/img/placeholder.png'" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Desktop view" />
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <h3 class="text-3xl font-black italic uppercase tracking-tighter">{{ project.title }}</h3>
                        <p class="text-lg opacity-70 leading-relaxed">{{ project.description }}</p>
                        <a v-if="project.url" :href="project.url" target="_blank" class="btn btn-primary rounded-full px-8">View Live</a>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="block.type === 'button'" class="max-w-7xl mx-auto px-6 flex">
            <a :href="block.content.url" class="btn btn-lg rounded-full px-12 text-xs uppercase font-black tracking-widest" 
               :class="[
                    block.content.style === 'secondary' ? 'btn-secondary' : 'btn-primary',
                    block.content.align === 'center' ? 'mx-auto' : ''
               ]">
                {{ block.content.label || block.content.text }}
            </a>
        </div>

        <div v-else-if="block.type === 'contact_form'" class="max-w-xl mx-auto px-6 overflow-hidden bg-base-200 rounded-3xl border border-white/5 relative p-8">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 space-y-6">
                <div class="space-y-2">
                    <h3 class="text-3xl font-black italic uppercase tracking-tighter">Contact Us</h3>
                    <p class="text-sm opacity-50 uppercase tracking-widest font-bold">Leave a message</p>
                </div>
                <form @submit.prevent="submitContact" class="space-y-4">
                    <input v-model="contactForm.website" type="text" class="hidden" /> <!-- Honeypot -->
                    <div class="form-control">
                        <input v-model="contactForm.name" type="text" placeholder="Your Name" class="input input-bordered bg-base-100/50 rounded-2xl border-white/10 focus:border-primary/50 transition-colors" required />
                    </div>
                    <div class="form-control">
                        <input v-model="contactForm.email" type="email" placeholder="Email Address" class="input input-bordered bg-base-100/50 rounded-2xl border-white/10 focus:border-primary/50 transition-colors" required />
                    </div>
                    <div class="form-control">
                        <textarea v-model="contactForm.message" placeholder="How can we help?" class="textarea textarea-bordered bg-base-100/50 rounded-2xl border-white/10 focus:border-primary/50 transition-colors h-32" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block rounded-2xl font-black uppercase italic tracking-tighter text-lg group">
                        Send Message <i class="fas fa-paper-plane ml-2 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    </button>
                    <p v-if="contactForm.recentlySuccessful" class="text-center text-xs text-primary font-bold uppercase tracking-widest animate-pulse">Message Sent Successfully!</p>
                </form>
            </div>
        </div>

        <div v-else-if="block.type === 'custom_code'" class="w-full">
            <div v-html="block.content.html"></div>
        </div>

        <!-- Layout Blocks -->
        <div v-else-if="block.type === 'columns'" class="max-w-7xl mx-auto px-6 grid gap-8" :class="[`grid-cols-1 md:grid-cols-${block.content.count || 2}`]">
             <div v-for="i in (block.content.count || 2)" :key="i" class="space-y-4">
                 <DynamicBlock v-for="child in block.children?.filter(c => c.column === i)" :key="child.id" :block="child" />
             </div>
        </div>

        <div v-else-if="block.type === 'group' || block.type === 'stack'" class="w-full" :class="block.type === 'stack' ? 'flex flex-col gap-4' : ''">
            <DynamicBlock v-for="child in block.children" :key="child.id" :block="child" />
        </div>
    </div>
</template>
