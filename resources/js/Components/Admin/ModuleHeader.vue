<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    breadcrumbs: {
        type: Array,
        default: () => []
    },
    icon: {
        type: String,
        default: null
    }
});
</script>

<template>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Title, Description, and Breadcrumbs -->
        <div class="flex items-center gap-4">
            <div v-if="icon" class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-xl shadow-inner">
                <i :class="icon"></i>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black tracking-tight flex items-center gap-2">
                        {{ title }}
                        <slot name="title-append"></slot>
                    </h1>
                    <span v-if="description" class="text-base-content/30 text-xl font-light">|</span>
                    <p v-if="description" class="text-sm opacity-60 m-0 pt-1">{{ description }}</p>
                </div>
                
                <div class="flex flex-col gap-1 mt-0.5">
                    <!-- Breadcrumbs stacked below title/description -->
                    <div v-if="breadcrumbs && breadcrumbs.length > 0" class="breadcrumbs text-xs text-base-content/50 m-0 pt-2 p-0">
                        <ul>
                            <li v-for="(crumb, index) in breadcrumbs" :key="index">
                                <Link v-if="crumb.url" :href="crumb.url" class="hover:text-primary transition-colors">
                                    <i :class="crumb.icon" v-if="crumb.icon" class="mr-1"></i>
                                    {{ crumb.label }}
                                </Link>
                                <span v-else class="text-base-content/70 font-medium tracking-wide">
                                    <i :class="crumb.icon" v-if="crumb.icon" class="mr-1"></i>
                                    {{ crumb.label }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Slot -->
        <div v-if="$slots.actions" class="flex items-center gap-3">
            <slot name="actions"></slot>
        </div>
    </div>
</template>
