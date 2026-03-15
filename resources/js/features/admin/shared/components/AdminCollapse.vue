<template>
    <div class="mb-3 overflow-hidden rounded-box border border-base-content/10 bg-base-100/50 transition-all">
        <button
            type="button"
            class="w-full flex items-center justify-between px-4 py-3 text-xs font-medium text-base-content/80 hover:bg-base-content/5 transition-colors select-none"
            @click="isOpen = !isOpen"
        >
            <span class="flex items-center gap-2.5 min-w-0">
                <component v-if="icon" :is="iconComponent" weight="duotone" class="w-4 h-4 text-primary shrink-0" />
                <span class="truncate">{{ title }}</span>
            </span>
            <PhCaretDown
                weight="bold"
                class="w-3.5 h-3.5 text-base-content/50 transition-transform duration-300 shrink-0"
                :class="{ 'rotate-180': isOpen }"
            />
        </button>

        <!-- Grid transition trick for smooth height animation -->
        <div class="grid transition-all duration-300 ease-in-out" :class="isOpen ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
            <div class="overflow-hidden">
                <div class="px-4 pb-4 pt-2 border-t border-base-content/10 bg-transparent" :class="contentClass">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { PhCaretDown } from '@phosphor-icons/vue';
import * as PhosphorIcons from '@phosphor-icons/vue';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';

const props = defineProps({
    title: { type: String, required: true },
    icon: { type: String, default: null },
    modelValue: { type: Boolean, default: false },
    contentClass: { type: String, default: '' },
    open: { type: Boolean, default: false },
    persistKey: { type: String, default: null }
});

const emit = defineEmits(['update:modelValue']);

const store = useBlockBuilderStore();

// Priority: persistKey from store > modelValue > open prop
const getInitialState = () => {
    if (props.persistKey && store.sidebarCollapses[props.persistKey] !== undefined) {
        return store.sidebarCollapses[props.persistKey];
    }
    return props.modelValue || props.open;
};

const isOpen = ref(getInitialState());

watch(() => props.modelValue, (newVal) => {
    isOpen.value = newVal;
});

watch(() => props.open, (newVal) => {
    isOpen.value = newVal;
});

watch(isOpen, (newVal) => {
    if (props.persistKey) {
        store.sidebarCollapses[props.persistKey] = newVal;
    }
    emit('update:modelValue', newVal);
});

// Update local state if store changes elsewhere
watch(() => store.sidebarCollapses[props.persistKey], (newVal) => {
    if (props.persistKey && newVal !== undefined && newVal !== isOpen.value) {
        isOpen.value = newVal;
    }
});

const iconComponent = computed(() => {
    if (!props.icon) return PhosphorIcons.PhCube;
    return PhosphorIcons[props.icon] || PhosphorIcons.PhCube;
});
</script>
