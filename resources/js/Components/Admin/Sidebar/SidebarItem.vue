<script setup>
import { Link } from '@inertiajs/vue3';
import { PhPlus, PhCaretDown } from '@phosphor-icons/vue';

const props = defineProps({
    item: Object,
    collapsed: Boolean,
    isActive: Boolean,
    isOpen: Boolean,
});

defineEmits(['toggle']);
</script>

<template>
    <li class="relative group/menu-item px-2">
        <!-- Main Item -->
        <div class="flex items-center justify-between group transition-all px-3 py-2 rounded-lg" 
             :class="[
                isActive ? `bg-${item.color || 'primary'}/10 text-${item.color || 'primary'} font-semibold` : `hover:bg-base-200/50 hover:text-${item.color || 'primary'}`
             ]">
            <!-- Vertical Active Indicator -->
            <div v-if="isActive" :class="`absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-${item.color || 'primary'}`"></div>

            <component :is="item.route ? Link : 'div'" 
                       :href="item.route ? route(item.route, item.params || {}) : null" 
                       @click="!item.route && item.children ? $emit('toggle') : null"
                       class="flex items-center flex-1 cursor-pointer min-w-0">
                <component :is="item.icon" weight="regular" class="w-5 h-5 shrink-0 transition-colors" 
                            :class="isActive ? `text-${item.color || 'primary'}` : 'text-base-content/60 group-hover/menu-item:text-inherit'" />
                <span v-show="!collapsed" class="ml-3 transition-opacity duration-300 text-sm truncate">{{ item.label }}</span>
            </component>

            <!-- Action Buttons (Create or Toggle) -->
            <div v-show="!collapsed" class="flex items-center pr-1 opacity-0 group-hover:opacity-100 transition-opacity gap-1 shrink-0">
                <Link v-if="item.createRoute" :href="route(item.createRoute)" 
                      class="p-1 rounded-md transition-all hover:bg-base-300"
                      :class="`text-${item.color || 'primary'}`" :title="`Add New ${item.label}`">
                    <PhPlus weight="bold" class="w-4 h-4" />
                </Link>
                <button v-if="item.children" @click.stop="$emit('toggle')" 
                        class="p-1 rounded-md transition-all hover:bg-base-300">
                    <PhCaretDown weight="bold" class="w-3.5 h-3.5 transition-transform duration-300" 
                                 :class="[{'rotate-180': isOpen}, `text-${item.color || 'primary'}`]" />
                </button>
            </div>
            
            <!-- Tooltip-like indicator for collapsed state if active -->
            <div v-if="collapsed && isActive" class="absolute right-2 w-1.5 h-1.5 rounded-full" :class="`bg-${item.color || 'primary'}`"></div>
        </div>

        <!-- Submenu Slot -->
        <slot v-if="isOpen && !collapsed" />
    </li>
</template>
