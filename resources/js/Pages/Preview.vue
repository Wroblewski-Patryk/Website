<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import DynamicBlock from '@/features/admin/block-builder/components/DynamicBlock.vue';

const props = defineProps({
    page: {
        type: Object,
        required: true,
    }
});

const livePageInfo = ref(props.page);
const liveBlocks = ref(props.page.content || []);

const messageHandler = (event) => {
    // Basic security check: ensure it comes from the admin panel (same origin for now)
    if (event.origin !== window.location.origin) return;
    
    if (event.data && event.data.type === 'filament-block-update') {
        liveBlocks.value = event.data.payload.content || [];
        // Optionally update title, etc. if passed
        livePageInfo.value.title = event.data.payload.title || 'Live Preview';
    }
};

onMounted(() => {
    window.addEventListener('message', messageHandler);
    // Tell the parent iframe we are ready to receive data
    window.parent.postMessage({ type: 'preview-ready' }, window.location.origin);
});

onUnmounted(() => {
    window.removeEventListener('message', messageHandler);
});
</script>

<template>
    <SeoHead :title="livePageInfo.title ? livePageInfo.title + ' (Live Preview)' : 'Live Preview'" />

    <AppLayout>
        <template v-if="liveBlocks && liveBlocks.length > 0">
            <DynamicBlock 
                v-for="(block, index) in liveBlocks" 
                :key="'live-block-' + index" 
                :block="block" 
            />
        </template>
        <div v-else class="min-h-screen flex items-center justify-center text-white/50 text-2xl font-light">
            Dodaj nowe bloki w kreatorze aby zobaczyć podgląd. (Add blocks to preview)
        </div>
    </AppLayout>
</template>
