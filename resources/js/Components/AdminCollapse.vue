<template>
    <div class="bg-base-200 rounded-box mb-3 overflow-hidden border border-base-content/5 transition-all shadow-sm">
        <div class="text-xs uppercase font-black tracking-widest flex items-center justify-between py-3 px-4 bg-base-200 hover:bg-base-300/50 transition-colors cursor-pointer select-none" @click="isOpen = !isOpen">
            <div class="flex items-center gap-3">
                <div v-if="icon" class="w-8 h-8 rounded-lg flex items-center justify-center text-primary bg-base-100 shadow-sm border border-base-content/5">
                    <component :is="iconComponent" weight="duotone" class="w-5 h-5" />
                </div>
                <span class="opacity-70">{{ title }}</span>
            </div>
            <div class="text-base-content/40 transition-transform duration-300" :class="{ 'rotate-180': isOpen }">
                <PhCaretDown weight="bold" class="w-4 h-4" />
            </div>
        </div>
        
        <!-- Grid transition trick for smooth height animation -->
        <div class="grid transition-all duration-300 ease-in-out" :class="isOpen ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
            <div class="overflow-hidden">
                <div class="px-4 pb-4 pt-1 bg-base-200 border-t border-base-content/5" :class="contentClass">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, shallowRef } from 'vue';
import { PhCaretDown } from '@phosphor-icons/vue';
import * as PhosphorIcons from '@phosphor-icons/vue';

const props = defineProps({
    title: { type: String, required: true },
    icon: { type: String, default: null },
    modelValue: { type: Boolean, default: false },
    contentClass: { type: String, default: '' },
    open: { type: Boolean, default: false }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(props.modelValue || props.open);

watch(() => props.modelValue, (newVal) => {
    isOpen.value = newVal;
});

watch(() => props.open, (newVal) => {
    isOpen.value = newVal;
});

watch(isOpen, (newVal) => {
    emit('update:modelValue', newVal);
});

const iconComponent = computed(() => {
    if (!props.icon) return PhosphorIcons.PhCube;
    return PhosphorIcons[props.icon] || PhosphorIcons.PhCube;
});
</script>
