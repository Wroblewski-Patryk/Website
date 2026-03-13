<template>
    <div class="space-y-6">
        <!-- Content Mode: List Items -->
        <div v-if="mode === 'content'" class="form-control">
            <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">List Items</span></label>
            <div class="space-y-2 max-h-60 overflow-y-auto pr-1 custom-scrollbar">
                <div v-for="(item, i) in modelValue.items" :key="i" class="group flex gap-2 animate-in slide-in-from-right-2 duration-300" :style="{ 'animation-delay': `${i * 50}ms` }">
                    <div class="flex-shrink-0 w-6 flex items-center justify-center opacity-30 group-hover:opacity-100 transition-opacity">
                        <span v-if="modelValue.listType === 'ol'" class="text-[10px] font-black font-mono">{{ i + 1 }}.</span>
                        <PhDotsSixVertical v-else class="w-3 h-3 cursor-grab" />
                    </div>
                    <!-- Direct binding works for non-localized, but for localized we might need a helper if we want to store as {pl: '', en: ''} -->
                    <input type="text" v-model="modelValue.items[i]" class="input input-xs input-bordered flex-1 focus:border-primary transition-all text-[11px]" placeholder="Element listy..." />
                    <button @click="modelValue.items.splice(i, 1)" class="btn btn-square btn-xs btn-ghost text-error opacity-0 group-hover:opacity-100 transition-opacity">
                        <PhTrash weight="bold" class="w-3 h-3" />
                    </button>
                </div>
            </div>
            
            <button @click="modelValue.items.push('New item')" class="btn btn-xs btn-outline btn-block mt-4 gap-2 border-dashed opacity-60 hover:opacity-100">
                <PhPlus weight="bold" class="w-3 h-3" /> Add New Item
            </button>
        </div>

        <!-- Advanced Mode: Structural Settings -->
        <div v-if="mode === 'advanced'" class="space-y-6 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">List Type</span></label>
                <div class="join w-full bg-base-300/50 p-1 rounded-lg">
                    <button @click="modelValue.listType = 'ul'" type="button" class="btn btn-xs join-item flex-1 border-none" :class="modelValue.listType === 'ul' || !modelValue.listType ? 'btn-primary shadow-sm' : 'btn-ghost opacity-50'"><PhListBullets class="w-4 h-4" /></button>
                    <button @click="modelValue.listType = 'ol'" type="button" class="btn btn-xs join-item flex-1 border-none" :class="modelValue.listType === 'ol' ? 'btn-primary shadow-sm' : 'btn-ghost opacity-50'"><PhListNumbers class="w-4 h-4" /></button>
                    <button @click="modelValue.listType = 'tasks'" type="button" class="btn btn-xs join-item flex-1 border-none" :class="modelValue.listType === 'tasks' ? 'btn-primary shadow-sm' : 'btn-ghost opacity-50'"><PhListChecks class="w-4 h-4" /></button>
                </div>
            </div>

            <div class="form-control">
                <label class="label flex justify-between">
                    <span class="label-text text-[10px] uppercase font-bold opacity-50">Spacing (Gap)</span>
                    <span class="text-[10px] font-mono opacity-50">{{ modelValue.gap || 0 }}px</span>
                </label>
                <input type="range" v-model.number="modelValue.gap" min="0" max="64" class="range range-xs range-primary" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { PhListBullets, PhListNumbers, PhListChecks, PhTrash, PhPlus, PhDotsSixVertical } from '@phosphor-icons/vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    },
    mode: {
        type: String,
        default: 'content'
    }
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 3px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
</style>

