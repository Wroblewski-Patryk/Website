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
    <li>
        <!-- Item with children -->
        <details v-if="item.children" :open="isOpen || isActive" class="group relative">
            <summary class="flex items-center transition-all py-2 rounded-lg list-none cursor-pointer group-focus-visible:ring-1 group-focus-visible:ring-primary"
                     style="padding-left: 20px; padding-right: 20px;"
                     :class="[
                        isActive ? `bg-${item.color || 'primary'}/10 text-${item.color || 'primary'} font-semibold` : `hover:bg-base-200/50 hover:text-${item.color || 'primary'}`,
                        collapsed ? 'justify-center !px-0' : 'justify-between'
                     ]">
                <div class="flex items-center min-w-0">
                    <div class="flex items-center justify-center shrink-0" :class="collapsed ? 'w-5 h-5' : 'w-10'">
                        <component :is="item.icon" weight="regular" class="w-5 h-5 transition-colors" 
                                    :class="isActive ? `text-${item.color || 'primary'}` : 'text-base-content/60 group-hover:text-inherit'" />
                    </div>
                    <span v-show="!collapsed" class="ml-3 transition-opacity duration-300 text-sm truncate">{{ item.label }}</span>
                </div>

                <!-- Action Buttons & Chevron -->
                <div v-show="!collapsed" class="flex items-center shrink-0 pr-1">
                    <PhCaretDown weight="bold" class="w-2.5 h-2.5 transition-transform duration-300 opacity-40 group-hover:opacity-100 group-open:rotate-180" 
                                 :class="`text-${item.color || 'primary'}`" />
                </div>
            </summary>
            
            <!-- Submenu Content -->
            <ul class="mt-0.5 flex flex-col gap-0.5 transition-all overflow-hidden !border-none !p-0 !ml-0" 
                style="padding-left: 0 !important; margin-left: 0 !important;"
                :class="[collapsed ? '' : 'py-1']">
                <slot />
            </ul>
        </details>

        <!-- Main Item without children -->
        <Link v-else 
              :href="item.route ? route(item.route, item.params || {}) : '#'" 
              class="flex items-center justify-between transition-all py-2 rounded-lg"
              style="padding-left: 20px; padding-right: 20px;"
              :class="[
                 isActive ? `bg-${item.color || 'primary'}/10 text-${item.color || 'primary'} font-semibold` : `hover:bg-base-200/50 hover:text-${item.color || 'primary'}`,
                 collapsed ? 'justify-center !px-0' : ''
              ]">
            <div class="flex items-center" :class="collapsed ? 'justify-center' : 'flex-1 min-w-0'">
                <div class="flex items-center justify-center shrink-0" :class="collapsed ? 'w-5 h-5' : 'w-10'">
                    <component :is="item.icon" weight="regular" class="w-5 h-5 shrink-0 transition-colors" 
                                :class="isActive ? `text-${item.color || 'primary'}` : 'text-base-content/60'" />
                </div>
                <span v-show="!collapsed" class="ml-3 transition-opacity duration-300 text-sm truncate">{{ item.label }}</span>
            </div>
            
            <!-- Active indicator for collapsed state -->
            <div v-if="collapsed && isActive" class="absolute right-1 w-1 h-4 rounded-full" :class="`bg-${item.color || 'primary'}`"></div>
        </Link>
    </li>
</template>

<style scoped>
/* Hide the default marker for <details> */
summary::-webkit-details-marker {
    display: none;
}
summary {
    list-style: none;
}
/* Hide DaisyUI's default chevron to use Phosphor icons instead */
summary::after {
    display: none !important;
}
/* Hide DaisyUI's menu lines */
ul::before {
    display: none !important;
}
</style>
