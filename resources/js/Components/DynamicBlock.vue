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

const styleObj = computed(() => {
    const s = props.block.settings || {};
    const l = s.layout || {};
    return {
        minHeight: l.fullHeight ? '100vh' : undefined,
        backgroundAttachment: l.fixedBg ? 'fixed' : undefined,
    };
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
         :class="[block.settings?.class, block.settings?.layout?.padding]" 
         :style="styleObj"
         class="transition-all duration-500">
        
        <!-- Section Block -->
        <div v-if="block.type === 'section'" class="w-full">
            <div class="space-y-4">
                <DynamicBlock v-for="child in block.children" :key="child.id" :block="child" />
            </div>
        </div>

        <!-- Portfolio / Projects Block -->
        <div v-else-if="block.type === 'portfolio'" class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 gap-20">
                <div v-for="(project, idx) in block.content.projects" :key="idx" class="group flex flex-col md:flex-row gap-12 items-center">
                    <div class="w-full md:w-1/2 relative overflow-hidden rounded-3xl shadow-2xl aspect-video bg-base-200">
                        <img :src="project.desktop_image || '/img/placeholder.png'" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Desktop view" />
                        <div v-if="project.mobile_image" class="absolute bottom-4 right-4 w-1/4 aspect-[9/16] rounded-xl border-4 border-base-100 shadow-2xl overflow-hidden hidden md:block">
                            <img :src="project.mobile_image" class="w-full h-full object-cover" alt="Mobile view" />
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <div class="flex items-center gap-4">
                            <h3 class="text-3xl font-black italic uppercase tracking-tighter">{{ project.title }}</h3>
                            <span class="text-xs opacity-40 font-mono">{{ project.date }}</span>
                        </div>
                        <p class="text-lg opacity-70 leading-relaxed">{{ project.description || 'Project description goes here...' }}</p>
                        <a v-if="project.url" :href="project.url" target="_blank" class="btn btn-outline btn-primary rounded-full px-8">
                            View Live <i class="fas fa-external-link-alt ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Code Block -->
        <div v-else-if="block.type === 'custom_code'" class="w-full">
            <div v-html="block.content.html"></div>
            <component :is="'script'" v-if="block.content.js">
                {{ block.content.js }}
            </component>
        </div>

        <!-- Heading Block -->
        <div v-else-if="block.type === 'heading'" class="max-w-7xl mx-auto px-6">
            <component :is="block.content.level === 1 ? 'h1' : 'h2'" 
                       :class="[
                           'font-black italic uppercase tracking-tighter', 
                           block.content.level === 1 ? 'text-6xl md:text-8xl' : 'text-4xl md:text-6xl'
                       ]">
                <template v-if="block.settings?.animations?.type === 'reveal-text'">
                    <span v-for="(char, i) in (block.content.text || 'Heading')" :key="i" class="inline-block whitespace-pre">{{ char }}</span>
                </template>
                <template v-else>
                    {{ block.content.text || 'Heading' }}
                </template>
            </component>
        </div>

        <!-- Text Block -->
        <div v-else-if="block.type === 'text'" class="max-w-4xl mx-auto px-6 prose prose-xl opacity-80" v-html="block.content.text"></div>

        <!-- Hero Block -->
        <div v-else-if="block.type === 'hero'" class="relative min-h-[80vh] flex items-center justify-center text-center px-6 overflow-hidden">
             <!-- Simplified for now -->
             <div class="max-w-4xl">
                <h1 class="text-7xl md:text-9xl font-black italic uppercase tracking-tighter mb-6">{{ block.content.headline }}</h1>
                <p class="text-xl md:text-2xl opacity-60 mb-12">{{ block.content.subheadline }}</p>
             </div>
        </div>

        <!-- Columns Block -->
        <div v-else-if="block.type === 'columns'" class="max-w-7xl mx-auto px-6 grid gap-8" :class="[`grid-cols-1 md:grid-cols-${block.content.count || 2}`]">
             <div v-for="i in (block.content.count || 2)" :key="i" class="space-y-4">
                 <DynamicBlock v-for="child in block.children?.filter(c => c.column === i)" :key="child.id" :block="child" />
             </div>
        </div>

        <!-- Image Block -->
        <div v-else-if="block.type === 'image'" class="max-w-7xl mx-auto px-6 flex justify-center">
            <img :src="block.content.url" :alt="block.content.alt" class="max-w-full rounded-3xl shadow-2xl" />
        </div>

        <!-- Button Block -->
        <div v-else-if="block.type === 'button'" class="max-w-7xl mx-auto px-6 flex py-4">
            <a :href="block.content.url" class="btn btn-lg rounded-full px-12" :class="block.content.style === 'secondary' ? 'btn-secondary' : 'btn-primary'">
                {{ block.content.text }}
            </a>
        </div>

        <!-- Fallback -->
        <div v-else class="p-4 border border-dashed opacity-50 text-center rounded-xl">
            Block: {{ block.type }}
        </div>
    </div>
</template>
