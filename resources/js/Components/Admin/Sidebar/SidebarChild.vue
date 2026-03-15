<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    child: Object,
    isActive: Boolean,
    color: {
        type: String,
        default: 'primary'
    }
});
</script>

<template>
    <li>
        <Link :href="route(child.route, child.params || {})" class="py-1.5 px-3 text-[13px] rounded-md transition-colors block truncate" 
              :class="[
                isActive ? `bg-${color}/5 text-${color} font-medium` : `hover:text-${color} hover:bg-base-200/30 text-base-content/70`
              ]">
            {{ child.label }}
        </Link>
    </li>
</template>
