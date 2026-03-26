<template>
    <details
        ref="rootRef"
        class="dropdown dropdown-end"
        @toggle="handleToggle"
    >
        <summary
            :tabindex="tabindex"
            :class="triggerClasses"
            role="button"
        >
            <slot name="trigger" />
        </summary>
        <ul :tabindex="tabindex" :class="menuClasses" @click="handleMenuClick">
            <slot />
        </ul>
    </details>
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

const rootRef = ref(null);

const triggerClasses = computed(() => [
    'btn btn-ghost border border-base-200 bg-base-100/70 shadow-sm transition-all hover:border-base-300 hover:bg-base-200/60 cursor-pointer list-none pointer-events-auto',
    props.triggerClass
]);

const menuClasses = computed(() => [
    'mt-3 z-[60] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box border border-base-200',
    props.menuClass
]);

function closeDropdown() {
    if (rootRef.value?.open) {
        rootRef.value.open = false;
    }
}

function closeOtherDropdowns() {
    const all = document.querySelectorAll('details.dropdown.dropdown-end[open]');
    all.forEach((el) => {
        if (el !== rootRef.value) {
            el.open = false;
        }
    });
}

function handleToggle() {
    if (rootRef.value?.open) {
        closeOtherDropdowns();
    }
}

function handleDocumentPointerDown(event) {
    if (!rootRef.value?.open) return;
    if (!rootRef.value.contains(event.target)) {
        closeDropdown();
    }
}

function handleEscape(event) {
    if (event.key === 'Escape') {
        closeDropdown();
    }
}

function handleMenuClick(event) {
    const target = event?.target;
    if (target?.closest('a,button')) {
        closeDropdown();
    }
}

onMounted(() => {
    document.addEventListener('pointerdown', handleDocumentPointerDown);
    document.addEventListener('keydown', handleEscape);
});

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', handleDocumentPointerDown);
    document.removeEventListener('keydown', handleEscape);
});
</script>

<style scoped>
summary::-webkit-details-marker {
    display: none;
}
</style>
