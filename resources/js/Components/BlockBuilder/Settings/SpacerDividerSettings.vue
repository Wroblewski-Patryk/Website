<template>
    <div class="space-y-4">
        <!-- Content Mode -->
        <div v-if="mode === 'content'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
            <span class="text-xs opacity-30 italic">No localized content for this element. Go to Advanced tab for layout settings.</span>
        </div>

        <!-- Advanced Mode -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div v-if="type === 'spacer'" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Height / Padding</span></label>
                <select v-model="modelValue.height" class="select select-bordered select-sm w-full focus:border-primary transition-all">
                    <option value="py-5">X-Small (20px)</option>
                    <option value="py-10">Small (40px)</option>
                    <option value="py-20">Medium (80px)</option>
                    <option value="py-40">Large (160px)</option>
                    <option value="py-60">X-Large (240px)</option>
                </select>
            </div>
            
            <div v-if="type === 'divider'" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Line Style</span></label>
                <select v-model="modelValue.style" class="select select-bordered select-sm w-full focus:border-primary transition-all">
                    <option value="solid">Solid Line</option>
                    <option value="dashed">Dashed Line</option>
                    <option value="dotted">Dotted Line</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    },
    type: {
        type: String,
        required: true
    },
    mode: {
        type: String,
        default: 'content'
    }
});
</script>

