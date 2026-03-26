<template>
    <i v-if="isFontAwesome" :class="faClass" aria-hidden="true"></i>
    <component v-else-if="phComponent" :is="phComponent" />
    <PhCircleDashed v-else />
</template>

<script setup>
import { computed } from 'vue';
import {
    PhCalendarBlank,
    PhCheckCircle,
    PhCircleDashed,
    PhClock,
    PhCreditCard,
    PhEnvelope,
    PhGear,
    PhGlobe,
    PhHeart,
    PhHouse,
    PhImage,
    PhInfo,
    PhMagnifyingGlass,
    PhMusicNote,
    PhPhone,
    PhShoppingCart,
    PhStar,
    PhUser,
    PhUsers,
    PhVideoCamera,
    PhWarningCircle,
} from '@phosphor-icons/vue';

const props = defineProps({
    icon: {
        type: String,
        default: '',
    },
});

const phosphorMap = {
    PhHouse,
    PhUser,
    PhUsers,
    PhEnvelope,
    PhPhone,
    PhGlobe,
    PhGear,
    PhCheckCircle,
    PhWarningCircle,
    PhInfo,
    PhStar,
    PhHeart,
    PhMagnifyingGlass,
    PhCalendarBlank,
    PhClock,
    PhImage,
    PhVideoCamera,
    PhMusicNote,
    PhShoppingCart,
    PhCreditCard,
};

const normalizedIcon = computed(() => String(props.icon || '').trim());
const isFontAwesome = computed(() => {
    if (!normalizedIcon.value) return false;
    if (normalizedIcon.value.startsWith('fa:')) return true;
    return /\bfa[srbld]?\s+fa-[a-z0-9-]+/i.test(normalizedIcon.value);
});
const faClass = computed(() => normalizedIcon.value.replace(/^fa:/, '').trim());
const phosphorName = computed(() => normalizedIcon.value.replace(/^ph:/, '').trim());
const phComponent = computed(() => phosphorMap[phosphorName.value] || null);
</script>
