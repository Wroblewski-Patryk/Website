<script setup>
defineProps({
    title: String,
    collapsed: Boolean,
    color: {
        type: String,
        default: 'primary'
    }
});
</script>

<template>
    <li class="menu-title mt-4 mb-1 px-4 pointer-events-none">
        <span class="text-[10px] uppercase font-bold tracking-widest whitespace-nowrap overflow-hidden transition-all duration-300" 
              :class="[`text-${color}`, { 'opacity-0 w-0': collapsed }]"
              v-show="!collapsed">
            {{ title }}
        </span>
    </li>
</template>
