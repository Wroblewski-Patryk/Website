<template>
    <div class="dropdown dropdown-end">
        <button type="button" :tabindex="tabindex" :class="triggerClasses">
            <slot name="trigger" />
        </button>
        <ul :tabindex="tabindex" :class="menuClasses">
            <slot />
        </ul>
    </div>
</template>

<script setup>
import { computed } from 'vue';

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

const triggerClasses = computed(() => [
    'btn btn-ghost border border-base-200 bg-base-100/70 shadow-sm transition-all hover:border-base-300 hover:bg-base-200/60 cursor-pointer',
    props.triggerClass
]);

const menuClasses = computed(() => [
    'mt-3 z-[60] p-2 shadow-xl menu menu-sm dropdown-content bg-base-100 rounded-box border border-base-200',
    props.menuClass
]);
</script>
