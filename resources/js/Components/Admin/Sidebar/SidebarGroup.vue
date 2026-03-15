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
    <li class="mt-4 mb-1 pointer-events-none" style="padding-left: 20px; padding-right: 20px;">
        <div v-show="!collapsed" class="flex items-center gap-2 opacity-30">
            <div class="h-[1px] w-4 bg-current" :class="`text-${color}`"></div>
            <span class="text-[9px] uppercase font-bold tracking-[0.2em] whitespace-nowrap overflow-hidden transition-all duration-300" 
                  :class="`text-${color}`">
                {{ title }}
            </span>
        </div>
        <div v-show="collapsed" class="h-[1px] w-full bg-base-200 mx-auto my-2 opacity-50"></div>
    </li>
</template>
