<script setup>
import { ref, onMounted, computed } from 'vue';
import { useGsapRuntime } from '@/Composables/useGsapRuntime';
import { useForm } from '@inertiajs/vue3';

const props = defineProps(['block']);

const blockRef = ref(null);
const { animateBlock } = useGsapRuntime();

onMounted(() => {
    if (props.block.appearance?.animations?.enabled) {
        animateBlock(blockRef.value, props.block.appearance.animations);
    }
});

const styleObj = computed(() => {
    return {
        backgroundColor: props.block.appearance?.backgroundColor !== 'transparent' ? props.block.appearance?.backgroundColor : undefined,
        color: props.block.appearance?.textColor !== 'inherit' ? props.block.appearance?.textColor : undefined,
        paddingTop: props.block.appearance?.paddingTop,
        paddingBottom: props.block.appearance?.paddingBottom,
        marginTop: props.block.appearance?.marginTop,
        marginBottom: props.block.appearance?.marginBottom,
        fontSize: props.block.appearance?.fontSize,
        fontWeight: props.block.appearance?.fontWeight,
        textAlign: props.block.appearance?.textAlign,
        borderRadius: props.block.appearance?.borderRadius,
        borderWidth: props.block.appearance?.borderWidth,
        borderStyle: props.block.appearance?.borderWidth ? 'solid' : undefined,
        borderColor: props.block.appearance?.borderColor,
        boxShadow: props.block.appearance?.boxShadow,
    };
});
</script>

<template>
    <div ref="blockRef" :class="[block.appearance?.customClasses]" :style="styleObj">
        <!-- Section Block (Layout Wrapper) -->
        <div v-if="block.type === 'section'" 
             :class="[
                'w-full py-1 transition-all duration-700',
                block.content?.width === 'boxed' ? 'max-w-7xl mx-auto px-6' : 'px-0',
                block.content?.bg_color || ''
             ]"
             :style="{ 
                backgroundImage: block.content?.bg_image ? `url(${block.content.bg_image})` : 'none',
                backgroundSize: 'cover',
                backgroundPosition: 'center',
                backgroundColor: block.appearance?.backgroundColor || 'transparent',
                paddingTop: block.appearance?.paddingTop || '3rem',
                paddingBottom: block.appearance?.paddingBottom || '3rem'
             }">
            <div class="space-y-4">
                <DynamicBlock v-for="child in block.children" :key="child.id" :block="child" />
            </div>
        </div>

        <!-- Button Block -->
        <div v-else-if="block.type === 'button'" class="py-4" :style="{ textAlign: block.content.align || 'left' }">
            <a :href="block.content.url || '#'" 
               :target="block.content.newTab ? '_blank' : '_self'"
               :class="[
                    'btn shadow-lg transition-all hover:scale-105 active:scale-95',
                    block.content.style === 'secondary' ? 'btn-secondary' : 'btn-primary',
                    block.content.size === 'lg' ? 'btn-lg' : (block.content.size === 'sm' ? 'btn-sm' : '')
               ]">
                {{ block.content.label || 'Click Here' }}
            </a>
        </div>

        <!-- Contact Form Block -->
        <div v-else-if="block.type === 'contact_form'" class="max-w-xl mx-auto p-8 bg-base-100 rounded-3xl shadow-2xl border border-white/5">
            <h3 class="text-2xl font-bold mb-6 text-primary">{{ block.content.title || 'Contact Us' }}</h3>
            <form @submit.prevent="submitContact" class="space-y-4">
                <!-- Honeypot -->
                <div class="hidden">
                    <input type="text" v-model="contactForm.website" tabindex="-1" autocomplete="off" />
                </div>

                <div class="form-control">
                    <input type="text" v-model="contactForm.name" placeholder="Your Name" class="input input-bordered w-full" :class="{ 'input-error': contactForm.errors.name }" required />
                    <div v-if="contactForm.errors.name" class="text-error text-sm mt-1">{{ contactForm.errors.name }}</div>
                </div>
                <div class="form-control">
                    <input type="email" v-model="contactForm.email" placeholder="Your Email" class="input input-bordered w-full" :class="{ 'input-error': contactForm.errors.email }" required />
                    <div v-if="contactForm.errors.email" class="text-error text-sm mt-1">{{ contactForm.errors.email }}</div>
                </div>
                <div class="form-control">
                    <textarea v-model="contactForm.message" placeholder="How can we help?" class="textarea textarea-bordered h-32 w-full" :class="{ 'textarea-error': contactForm.errors.message }" required></textarea>
                    <div v-if="contactForm.errors.message" class="text-error text-sm mt-1">{{ contactForm.errors.message }}</div>
                </div>
                
                <button type="submit" class="btn btn-primary w-full shadow-lg shadow-primary/20" :disabled="contactForm.processing">
                    <span v-if="contactForm.processing" class="loading loading-spinner"></span>
                    {{ block.content?.button_text || 'Send Message' }}
                </button>

                <div v-if="contactForm.wasSuccessful" class="alert alert-success mt-4">
                    {{ block.content?.success_message || 'Thank you! Message sent.' }}
                </div>
            </form>
        </div>

        <!-- Hero Block -->
        <div v-else-if="block.type === 'hero'" 
             class="relative py-32 px-10 text-center overflow-hidden"
             :style="{
                backgroundImage: block.content.bgImage ? `url(/storage/${block.content.bgImage})` : 'none',
                backgroundSize: 'cover',
                backgroundPosition: 'center',
             }">
            <div v-if="block.content.bgImage" class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
            <div class="relative z-10">
                <h1 class="text-6xl font-black mb-6 tracking-tight text-white drop-shadow-2xl italic">{{ block.content.headline || 'Your Premium Headline' }}</h1>
                <p class="text-xl mb-10 text-white/90 max-w-2xl mx-auto drop-shadow-md">{{ block.content.subheadline || 'Elevate your digital presence with artisan-crafted designs.' }}</p>
                <div class="flex justify-center gap-4">
                    <button v-if="block.content.primaryLabel" class="btn btn-primary px-8 rounded-full shadow-xl shadow-primary/30">{{ block.content.primaryLabel }}</button>
                    <button v-if="block.content.secondaryLabel" class="btn btn-outline btn-primary px-8 rounded-full backdrop-blur-md text-white border-white/30">{{ block.content.secondaryLabel }}</button>
                </div>
            </div>
        </div>

        <!-- Heading Block -->
        <div v-else-if="block.type === 'heading'" class="py-4">
            <component :is="block.content.level || 'h2'" 
                       :class="['font-bold', block.content.level === 'h1' ? 'text-6xl' : 'text-4xl']">
                {{ block.content.text || 'Your Heading Here' }}
            </component>
        </div>

        <!-- Text Block -->
        <div v-else-if="block.type === 'text'" class="prose prose-lg max-w-none p-8" v-html="block.content.text || '<p>Start typing your content here...</p>'"></div>

        <!-- Columns Block -->
        <div v-else-if="block.type === 'columns'" class="grid gap-8 p-8" :class="[`grid-cols-${block.content.columns || 2}`]">
            <div v-for="(col, idx) in block.content.data" :key="idx" class="flex flex-col gap-4">
                <DynamicBlock v-for="subBlock in col.blocks" :key="subBlock.id" :block="subBlock" />
            </div>
            <!-- Empty state for columns if no data -->
            <div v-if="!block.content.data || block.content.data.length === 0" class="col-span-full border-2 border-dashed border-base-300 p-8 text-center opacity-50">
                Columns Placeholder (Add elements in sidebar)
            </div>
        </div>

        <!-- Image Block -->
        <div v-else-if="block.type === 'image'" class="flex justify-center p-8">
            <template v-if="block.content.url">
                <img :src="block.content.url" :alt="block.content.alt" class="max-w-full rounded-box shadow-xl" />
            </template>
            <template v-else>
                <div class="w-full max-w-lg aspect-video bg-base-300 rounded-box flex items-center justify-center text-base-content/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
            </template>
        </div>
        
        <!-- Fallback Block -->
        <div v-else class="p-8 text-center text-error border border-error bg-error/10 m-4 rounded">
            Unknown block type: {{ block.type }}
        </div>
    </div>
</template>
