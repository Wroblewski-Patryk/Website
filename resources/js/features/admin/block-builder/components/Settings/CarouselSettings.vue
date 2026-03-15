<template>
    <div class="space-y-4">
        <!-- Content Mode: Placeholder for now -->
        <div v-if="mode === 'content'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
            <span class="text-xs opacity-30 italic">No localized content for carousel. Go to Advanced tab to manage images.</span>
        </div>

        <!-- Advanced Mode: Global Image URLs -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="flex items-center justify-between">
                <label class="text-[10px] opacity-40 uppercase font-black ml-1">Images (URLs)</label>
                <div class="flex items-center gap-1 text-[9px] opacity-30 font-bold uppercase">
                    <PhImage weight="bold" class="w-2.5 h-2.5" />
                    {{ modelValue.images?.length || 0 }} images
                </div>
            </div>

            <div class="space-y-2">
                <div v-for="(url, idx) in modelValue.images" :key="idx" class="flex gap-2 group items-center bg-base-200/50 p-2 rounded-2xl border border-white/5">
                    <div class="w-8 h-8 rounded-lg overflow-hidden bg-base-300 shadow-inner flex-none">
                        <img :src="url" class="w-full h-full object-cover opacity-50 group-hover:opacity-100 transition-opacity" alt="" />
                    </div>
                    <input type="text" v-model="modelValue.images[idx]" placeholder="https://..." class="input input-xs input-bordered flex-1 font-mono text-[10px] focus:border-primary transition-all" />
                    <button @click="modelValue.images.splice(idx, 1)" class="btn btn-xs btn-circle btn-ghost text-error opacity-0 group-hover:opacity-100 transition-opacity">
                        <PhX weight="bold" class="w-3 h-3" />
                    </button>
                </div>
            </div>
            
            <button @click="modelValue.images = modelValue.images || []; modelValue.images.push('')" class="btn btn-xs btn-outline btn-block rounded-xl border-dashed opacity-50 hover:opacity-100">
                <PhPlus weight="bold" class="mr-1 w-3 h-3" /> Add Image URL
            </button>
        </div>
    </div>
</template>

<script setup>
import { PhX, PhPlus, PhImage } from '@phosphor-icons/vue';

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

