<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const props = defineProps({
    block: {
        type: Object,
        required: true,
    }
});

const blockRef = ref(null);

const form = useForm({
    name: '',
    email: '',
    message: '',
});

const submitContact = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const appearance = computed(() => {
    const data = props.block.data || {};
    return {
        marginTop: data.margin_top || '',
        marginBottom: data.margin_bottom || '',
        paddingTop: data.padding_top || '',
        paddingBottom: data.padding_bottom || '',
        backgroundColor: data.background_color || '',
        color: data.text_color || '',
    };
});

const customClasses = computed(() => {
    return props.block.data?.custom_css_classes || '';
});

onMounted(() => {
    if (!blockRef.value) return;

    const data = props.block.data || {};
    const animationType = data.animation_type || 'none';
    
    if (animationType === 'none') return;

    const duration = parseFloat(data.duration) || 1.0;
    const delay = parseFloat(data.delay) || 0;
    const easing = data.easing || 'power2.out';
    const useScrollTrigger = data.scroll_trigger ?? true;

    let vars = {
        duration,
        delay,
        ease: easing,
    };

    if (useScrollTrigger) {
        vars.scrollTrigger = {
            trigger: blockRef.value,
            start: "top 85%",
            toggleActions: "play none none reverse",
        };
    }

    switch (animationType) {
        case 'fade':
            gsap.fromTo(blockRef.value, { opacity: 0 }, { opacity: 1, ...vars });
            break;
        case 'slide_up':
            gsap.fromTo(blockRef.value, { opacity: 0, y: 50 }, { opacity: 1, y: 0, ...vars });
            break;
        case 'slide_left':
            gsap.fromTo(blockRef.value, { opacity: 0, x: 50 }, { opacity: 1, x: 0, ...vars });
            break;
        case 'slide_right':
            gsap.fromTo(blockRef.value, { opacity: 0, x: -50 }, { opacity: 1, x: 0, ...vars });
            break;
        case 'scale_up':
            gsap.fromTo(blockRef.value, { opacity: 0, scale: 0.8 }, { opacity: 1, scale: 1, ...vars });
            break;
    }
});
</script>

<template>
    <div 
        ref="blockRef" 
        class="dynamic-block w-full relative z-20"
        :class="customClasses"
        :style="appearance"
    >
        <!-- Layout / Structural Blocks -->
        <section v-if="block.type === 'section_wrapper'" class="w-full" :class="{'max-w-7xl mx-auto px-4': !block.data.full_width}">
            <DynamicBlock v-for="(subBlock, idx) in block.data.content" :key="idx" :block="subBlock" />
        </section>

        <div v-else-if="block.type === 'grid_columns'" :class="['grid gap-' + (block.data.gap === 'none' ? '0' : (block.data.gap === 'small' ? '4' : (block.data.gap === 'large' ? '12' : '8'))), 'grid-cols-1', 'md:grid-cols-' + block.data.columns]">
            <div v-for="(colBlock, idx) in block.data.column_content" :key="idx">
                <DynamicBlock :block="colBlock" />
            </div>
        </div>

        <div v-else-if="block.type === 'spacing_block'" :style="{ height: block.data.height }" class="w-full flex items-center justify-center">
            <hr v-if="block.data.show_divider" class="w-1/2 border-white/20" />
        </div>

        <!-- Typography & Single Assets -->
        <div v-else-if="block.type === 'heading'" :class="{'text-center': block.data.alignment === 'center', 'text-right': block.data.alignment === 'right', 'text-left': block.data.alignment === 'left'}">
            <component :is="block.data.level || 'h2'" class="font-bold tracking-tight mb-4 uppercase" :class="{'text-6xl md:text-7xl': block.data.level === 'h1', 'text-4xl md:text-5xl': block.data.level === 'h2', 'text-3xl': block.data.level === 'h3', 'text-2xl': block.data.level === 'h4'}">
                {{ block.data.text }}
            </component>
        </div>

        <div v-else-if="block.type === 'image_block'" class="w-full flex" :class="{'justify-center': block.data.alignment === 'center', 'justify-end': block.data.alignment === 'right', 'justify-start': block.data.alignment === 'left'}">
            <img :src="`/storage/${block.data.image}`" :alt="block.data.alt_text" class="w-full h-auto rounded-lg shadow-xl" :style="{ objectFit: block.data.object_fit || 'cover' }">
        </div>

        <div v-else-if="block.type === 'button_block'" class="flex" :class="{'justify-center': block.data.alignment === 'center', 'justify-end': block.data.alignment === 'right', 'justify-start': block.data.alignment === 'left'}">
            <a :href="block.data.url" class="px-8 py-4 font-bold tracking-widest uppercase transition-colors" :class="{
                'bg-white text-black hover:bg-gray-200': block.data.style === 'primary',
                'bg-transparent border-2 border-white text-white hover:bg-white hover:text-black': block.data.style === 'outline',
                'bg-gray-800 text-white hover:bg-gray-700': block.data.style === 'secondary'
            }">
                {{ block.data.label }}
            </a>
        </div>

        <!-- Legacy / Specific Blocks -->
        <nav v-else-if="block.type === 'nav'" class="py-4 px-8 flex justify-between items-center z-50 relative">
            <div class="text-2xl font-bold tracking-widest"><Link href="/">PORTFOLIO</Link></div>
            <ul class="flex gap-6">
                <li v-for="(link, index) in block.data.links" :key="index">
                    <a :href="link.url" class="hover:text-gray-400 transition">{{ link.label }}</a>
                </li>
            </ul>
        </nav>

        <section v-else-if="block.type === 'hero'" class="py-32 flex flex-col items-center justify-center text-center relative z-20">
            <h1 class="text-6xl md:text-8xl font-black mb-6 uppercase tracking-tight">{{ block.data.heading }}</h1>
            <p v-if="block.data.subheading" class="text-2xl md:text-3xl font-light mb-12 max-w-3xl mx-auto">{{ block.data.subheading }}</p>
            <img v-if="block.data.image" :src="`/storage/${block.data.image}`" class="rounded-xl shadow-2xl max-w-4xl w-full object-cover">
        </section>

        <section v-else-if="block.type === 'text_content'" class="py-20 max-w-3xl mx-auto prose prose-xl dark:prose-invert relative z-20">
            <div v-html="block.data.text" class="leading-relaxed"></div>
        </section>

        <section v-else-if="block.type === 'portfolio_section'" class="py-20 max-w-7xl mx-auto px-4 relative z-20">
            <h2 v-if="block.data.section_title" class="text-4xl md:text-5xl font-bold mb-16 text-center uppercase">{{ block.data.section_title }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                <a v-for="(item, index) in block.data.items" :key="index" :href="item.link_url || '#'" class="block relative overflow-hidden group">
                    <img :src="`/storage/${item.thumbnail}`" class="w-full h-80 object-cover transition-transform duration-700 group-hover:scale-105 filter grayscale group-hover:grayscale-0">
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                        <h3 class="text-white text-2xl font-bold tracking-wider uppercase border-2 border-white px-6 py-2">{{ item.title }}</h3>
                    </div>
                </a>
            </div>
        </section>

        <section v-else-if="block.type === 'contact_form'" class="py-24 max-w-2xl mx-auto relative z-20 px-4">
            <h2 class="text-4xl md:text-5xl font-bold mb-6 text-center uppercase tracking-wider">{{ block.data.heading || 'Contact' }}</h2>
            <p v-if="block.data.description" class="text-center mb-12 text-lg opacity-80">{{ block.data.description }}</p>
            
            <div v-if="form.recentlySuccessful" class="mb-8 p-6 bg-white/10 border border-white/20 text-white rounded text-center text-xl">
                Thanks for your message. We'll be in touch!
            </div>

            <form @submit.prevent="submitContact" class="space-y-6">
                <div>
                    <input v-model="form.name" type="text" placeholder="Name" class="w-full bg-transparent border-0 border-b-2 border-white/30 focus:border-white focus:ring-0 px-0 py-4 text-xl placeholder-white/50 transition" required>
                    <div v-if="form.errors.name" class="text-red-400 text-sm mt-2">{{ form.errors.name }}</div>
                </div>
                <div>
                    <input v-model="form.email" type="email" placeholder="Email" class="w-full bg-transparent border-0 border-b-2 border-white/30 focus:border-white focus:ring-0 px-0 py-4 text-xl placeholder-white/50 transition" required>
                    <div v-if="form.errors.email" class="text-red-400 text-sm mt-2">{{ form.errors.email }}</div>
                </div>
                <div>
                    <textarea v-model="form.message" placeholder="Message" class="w-full bg-transparent border-0 border-b-2 border-white/30 focus:border-white focus:ring-0 px-0 py-4 text-xl placeholder-white/50 transition" rows="4" required></textarea>
                    <div v-if="form.errors.message" class="text-red-400 text-sm mt-2">{{ form.errors.message }}</div>
                </div>
                <button type="submit" :disabled="form.processing" class="w-full border-2 border-white hover:bg-white hover:text-black text-white py-4 text-xl font-bold tracking-widest uppercase transition-colors disabled:opacity-50 mt-8">
                    {{ form.processing ? 'Sending...' : 'Send Message' }}
                </button>
            </form>
        </section>
    </div>
</template>
