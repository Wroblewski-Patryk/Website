<template>
    <div class="space-y-4">
        <!-- Content Mode: Localized Alt and Caption -->
        <div v-if="mode === 'content'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Alt Text (Accessibility)</span></label>
                <input type="text" v-model="modelValue.alt" placeholder="Describe the image..." class="input input-sm input-bordered w-full focus:border-primary transition-all" />
            </div>
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Caption</span></label>
                <input type="text" v-model="modelValue.caption" placeholder="Image caption..." class="input input-sm input-bordered w-full focus:border-primary transition-all" />
            </div>
        </div>

        <!-- Advanced Mode: Global Image URL -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Image URL</span></label>
                <div class="relative group">
                    <input type="text" v-model="modelValue.url" class="input input-sm input-bordered w-full font-mono text-[10px] focus:border-primary transition-all pr-8" />
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-primary opacity-50 group-hover:opacity-100 transition-opacity">
                        <PhLink weight="bold" class="w-3 h-3" />
                    </div>
                </div>
            </div>
            
            <div v-if="modelValue.url" class="mt-2 rounded-2xl overflow-hidden shadow-lg border border-white/10 aspect-video bg-base-300 flex items-center justify-center">
                <img :src="modelValue.url" alt="Preview" class="w-full h-full object-cover" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { PhLink } from '@phosphor-icons/vue';

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

