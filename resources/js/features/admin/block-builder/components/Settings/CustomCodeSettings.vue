<template>
    <div class="space-y-4">
        <!-- Content Mode: Localized HTML/JS -->
        <div v-if="mode === 'content'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50 font-mono">HTML Content</span></label>
                <textarea v-model="modelValue.html" class="textarea textarea-bordered h-48 w-full font-mono text-xs focus:border-primary transition-all shadow-inner"></textarea>
            </div>
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50 font-mono">JS (Executed on mount)</span></label>
                <textarea v-model="modelValue.js" class="textarea textarea-bordered h-32 w-full font-mono text-xs focus:border-primary transition-all shadow-inner"></textarea>
            </div>
        </div>

        <!-- Advanced Mode -->
        <div v-if="mode === 'advanced'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
            <span class="text-xs opacity-30 italic">No global settings for custom code. All code is localized.</span>
        </div>
    </div>
</template>

<script setup>
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

