<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
    countdown_to: {
        type: String,
        default: null,
    },
});

const { t } = useTranslations();
const now = ref(Date.now());
let timerId = null;

const targetTimestamp = computed(() => {
    if (!props.countdown_to) return null;
    const date = new Date(props.countdown_to);
    const time = date.getTime();
    return Number.isFinite(time) ? time : null;
});

const remaining = computed(() => {
    if (!targetTimestamp.value) {
        return { days: 0, hours: 0, minutes: 0, seconds: 0 };
    }

    const diff = Math.max(0, targetTimestamp.value - now.value);
    const days = Math.floor(diff / 86400000);
    const hours = Math.floor((diff % 86400000) / 3600000);
    const minutes = Math.floor((diff % 3600000) / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);

    return { days, hours, minutes, seconds };
});

onMounted(() => {
    timerId = window.setInterval(() => {
        now.value = Date.now();
    }, 1000);
});

onUnmounted(() => {
    if (timerId) {
        window.clearInterval(timerId);
    }
});
</script>

<template>
    <Head :title="t('admin.settings.coming_soon', 'Coming Soon')" />

    <main class="min-h-screen bg-gradient-to-b from-base-200 to-base-100 flex items-center justify-center px-6">
        <div class="w-full max-w-2xl rounded-3xl border border-base-content/10 bg-base-100/80 backdrop-blur p-8 md:p-12 text-center space-y-6">
            <img
                v-if="settings.brand_logo_light"
                :src="settings.brand_logo_light"
                alt="Brand"
                class="mx-auto h-14 w-auto object-contain"
            />
            <h1 class="text-3xl md:text-4xl font-black tracking-tight">
                {{ t('admin.settings.coming_soon', 'Coming Soon') }}
            </h1>
            <p class="opacity-70">
                {{ t('admin.settings.coming_soon_desc', 'This page is scheduled and will be published soon.') }}
            </p>

            <div class="grid grid-cols-4 gap-3">
                <div class="rounded-2xl bg-base-200/70 p-3">
                    <p class="text-2xl font-black">{{ remaining.days }}</p>
                    <p class="text-[10px] uppercase opacity-50">Days</p>
                </div>
                <div class="rounded-2xl bg-base-200/70 p-3">
                    <p class="text-2xl font-black">{{ remaining.hours }}</p>
                    <p class="text-[10px] uppercase opacity-50">Hours</p>
                </div>
                <div class="rounded-2xl bg-base-200/70 p-3">
                    <p class="text-2xl font-black">{{ remaining.minutes }}</p>
                    <p class="text-[10px] uppercase opacity-50">Minutes</p>
                </div>
                <div class="rounded-2xl bg-base-200/70 p-3">
                    <p class="text-2xl font-black">{{ remaining.seconds }}</p>
                    <p class="text-[10px] uppercase opacity-50">Seconds</p>
                </div>
            </div>
        </div>
    </main>
</template>
