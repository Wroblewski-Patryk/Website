<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
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

onMounted(() => {
    if (!blockRef.value) return;

    const data = props.block.data || {};
    const animationType = data.animation_type || 'none';
    
    if (animationType === 'none') return;

    const duration = parseFloat(data.duration) || 1.0;
    const delay = parseFloat(data.delay) || 0;
    const easing = data.easing || 'power2.out';
    const useScrollTrigger = data.scroll_trigger ?? true;

    // Define from/to arguments based on animation type
    let vars = {
        duration,
        delay,
        ease: easing,
    };

    if (useScrollTrigger) {
        vars.scrollTrigger = {
            trigger: blockRef.value,
            start: "top 85%", // adjust as needed
            toggleActions: "play none none reverse",
        };
    }

    // Set initial state based on type
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
    <div ref="blockRef" class="dynamic-block w-full">
        <!-- Hero Block -->
        <section v-if="block.type === 'hero'" class="py-20 flex flex-col items-center justify-center text-center">
            <h1 class="text-5xl font-bold mb-4">{{ block.data.heading }}</h1>
            <p v-if="block.data.subheading" class="text-xl text-gray-600 mb-8">{{ block.data.subheading }}</p>
            <img v-if="block.data.image" :src="`/storage/${block.data.image}`" class="rounded-xl shadow-lg max-w-4xl w-full object-cover">
        </section>

        <!-- Text Content Block -->
        <section v-if="block.type === 'text_content'" class="py-16 max-w-4xl mx-auto prose prose-lg dark:prose-invert">
            <div v-html="block.data.text"></div>
        </section>

        <!-- Portfolio Block -->
        <section v-if="block.type === 'portfolio_section'" class="py-16 max-w-6xl mx-auto">
            <h2 v-if="block.data.section_title" class="text-3xl font-bold mb-8 text-center">{{ block.data.section_title }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <a v-for="(item, index) in block.data.items" :key="index" :href="item.link_url || '#'" class="block relative overflow-hidden rounded-xl group">
                    <img :src="`/storage/${item.thumbnail}`" class="w-full h-64 object-cover transition duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                        <h3 class="text-white text-xl font-semibold">{{ item.title }}</h3>
                    </div>
                </a>
            </div>
        </section>

        <!-- Contact Form Block -->
        <section v-if="block.type === 'contact_form'" class="py-16 max-w-xl mx-auto bg-gray-50 rounded-2xl p-8 shadow-sm border border-gray-100">
            <h2 class="text-3xl font-bold mb-4 text-center">{{ block.data.heading || 'Contact Us' }}</h2>
            <p v-if="block.data.description" class="text-gray-600 text-center mb-8">{{ block.data.description }}</p>
            
            <div v-if="form.recentlySuccessful" class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg text-center">
                Message sent successfully!
            </div>

            <form @submit.prevent="submitContact" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-lg p-3" required>
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input v-model="form.email" type="email" class="w-full border-gray-300 rounded-lg p-3" required>
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Message</label>
                    <textarea v-model="form.message" class="w-full border-gray-300 rounded-lg p-3" rows="4" required></textarea>
                    <div v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</div>
                </div>
                <button type="submit" :disabled="form.processing" class="w-full bg-black text-white rounded-lg py-3 font-medium hover:bg-gray-800 transition disabled:opacity-50">
                    {{ form.processing ? 'Sending...' : 'Send Message' }}
                </button>
            </form>
        </section>
    </div>
</template>
