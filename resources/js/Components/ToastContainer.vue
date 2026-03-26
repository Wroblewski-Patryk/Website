<template>
    <div class="toast toast-end toast-top z-[100] p-4 gap-2 pointer-events-none">
        <transition-group name="toast-fade">
            <div 
                v-for="toast in store.toasts" 
                :key="toast.id"
                class="alert shadow-lg border-l-4 min-w-[300px] flex justify-between items-start pointer-events-auto"
                :class="[
                    toast.type === 'success' ? 'alert-success border-l-success' : '',
                    toast.type === 'error' ? 'alert-error border-l-error' : '',
                    toast.type === 'warning' ? 'alert-warning border-l-warning' : '',
                    toast.type === 'info' ? 'alert-info border-l-info' : '',
                ]"
            >
                <div class="flex gap-3 items-center">
                    <!-- Icons based on type -->
                    <PhCheckCircle v-if="toast.type === 'success'" weight="bold" class="w-5 h-5" />
                    <PhWarningCircle v-else-if="toast.type === 'error'" weight="bold" class="w-5 h-5" />
                    <PhWarning v-else-if="toast.type === 'warning'" weight="bold" class="w-5 h-5" />
                    <PhInfo v-else weight="bold" class="w-5 h-5" />
                    
                    <div class="flex flex-col">
                        <span class="text-xs font-bold">{{ toast.message }}</span>
                    </div>
                </div>
                <button @click="store.remove(toast.id)" class="btn btn-ghost btn-xs btn-square opacity-50 hover:opacity-100">
                    <PhX weight="bold" class="w-3 h-3" />
                </button>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import { useToastStore } from '@/Stores/useToastStore';
import { PhCheckCircle, PhWarningCircle, PhWarning, PhInfo, PhX } from '@phosphor-icons/vue';

const store = useToastStore();
</script>

<style scoped>
.toast-fade-enter-active,
.toast-fade-leave-active {
    transition: all 0.3s ease;
}
.toast-fade-enter-from {
    opacity: 0;
    transform: translateX(30px);
}
.toast-fade-leave-to {
    opacity: 0;
    transform: translateX(30px) scale(0.9);
}
</style>
