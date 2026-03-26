<template>
    <div ref="rootRef" class="dropdown dropdown-end" :class="{ 'dropdown-open': isOpen }">
        <button
            type="button"
            :tabindex="tabindex"
            :class="triggerClasses"
            @click.stop="toggleOpen"
        >
            <slot name="trigger" />
        </button>
        <ul
            v-show="isOpen"
            :tabindex="tabindex"
            :class="menuClasses"
            @click="handleMenuClick"
        >
            <slot />
        </ul>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    triggerClass: {
        type: String,
        default: ''
    },
    menuClass: {
        type: String,
        default: ''
    },
    tabindex: {
        type: Number,
        default: 0
    }
});

const isOpen = ref(false);
const rootRef = ref(null);

const triggerClasses = computed(() => [
    'btn btn-ghost border border-base-200 bg-base-100/70 shadow-sm transition-all hover:border-base-300 hover:bg-base-200/60 cursor-pointer',
    props.triggerClass
]);

const menuClasses = computed(() => [
    'mt-3 z-[60] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box border border-base-200',
    props.menuClass
]);

function closeOpen() {
    isOpen.value = false;
}

function toggleOpen() {
    isOpen.value = !isOpen.value;
}

function handleMenuClick(event) {
    const target = event?.target;
    if (target?.closest('a,button')) {
        closeOpen();
    }
}

function handleDocumentClick(event) {
    if (!isOpen.value || !rootRef.value) return;
    if (!rootRef.value.contains(event.target)) {
        closeOpen();
    }
}

function handleEscape(event) {
    if (event.key === 'Escape') {
        closeOpen();
    }
}

onMounted(() => {
    document.addEventListener('click', handleDocumentClick);
    document.addEventListener('keydown', handleEscape);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
    document.removeEventListener('keydown', handleEscape);
});
</script>
