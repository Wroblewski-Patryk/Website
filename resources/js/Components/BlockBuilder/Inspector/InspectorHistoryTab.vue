<template>
    <div class="flex-1 overflow-y-auto px-4 pb-4 custom-scrollbar">
        <div v-if="history.length === 0" class="text-center py-20 opacity-20">
            <PhClockCounterClockwise weight="thin" class="w-12 h-12 mx-auto mb-2" />
            <p class="text-xs">{{ t('admin.builder.inspector_history_empty', 'No history yet') }}</p>
        </div>
        <div v-else class="space-y-2">
            <button 
                v-for="(item, idx) in history" 
                :key="idx"
                @click="$emit('restore', idx)"
                class="w-full text-left p-3 rounded-xl border transition-all duration-300"
                :class="currentIndex === idx ? 'bg-primary/20 border-primary/50 text-primary' : 'bg-base-200/50 border-white/5 hover:bg-base-200'"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold">{{ item.label }}</span>
                    <span class="text-[10px] opacity-40">{{ item.time }}</span>
                </div>
            </button>
        </div>
    </div>
</template>

<script setup>
import { PhClockCounterClockwise } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    history: {
        type: Array,
        default: () => []
    },
    currentIndex: {
        type: Number,
        default: -1
    }
});

const emit = defineEmits(['restore']);
</script>
