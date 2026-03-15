<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    child: Object,
    isActive: Boolean,
    collapsed: Boolean,
    color: {
        type: String,
        default: 'primary'
    }
});
</script>

<template>
    <li>
        <Link :href="route(child.route, child.params || {})" class="py-1.5 rounded-md transition-all flex items-center group/child w-full" 
              style="padding-left: 20px; padding-right: 20px;"
              :class="[
                isActive ? `bg-${color}/5 text-${color} font-medium` : `hover:text-${color} hover:bg-base-200/30 text-base-content/70`,
                collapsed ? 'justify-center !px-0' : ''
              ]">
            <div class="flex items-center justify-center shrink-0" :class="collapsed ? 'w-5 h-5' : 'w-10'">
                <component v-if="child.icon" :is="child.icon" weight="regular" class="w-4 h-4 transition-colors"
                           :class="isActive ? `text-${color}` : 'text-base-content/40 group-hover/child:text-inherit'" />
            </div>
            
            <span v-show="!collapsed" class="ml-3 text-[13px] truncate transition-opacity duration-300">
                {{ child.label }}
            </span>
        </Link>
    </li>
</template>
