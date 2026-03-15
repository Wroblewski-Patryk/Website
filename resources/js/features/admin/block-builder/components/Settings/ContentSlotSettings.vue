<template>
    <div class="space-y-4">
        <!-- Content Mode: Localized Slot Label -->
        <div v-if="mode === 'content'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Placeholder / Slot Label</span></label>
                <input type="text" v-model="modelValue.label" placeholder="e.g. Page Content Area" class="input input-sm input-bordered w-full font-bold focus:border-primary transition-all" />
                <p class="text-[9px] opacity-40 italic mt-2 px-1 leading-relaxed decoration-primary underline-offset-4">This label is only visible in the editor to identify the slot purpose.</p>
            </div>
        </div>

        <!-- Advanced Mode -->
        <div v-if="mode === 'advanced'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
            <span class="text-xs opacity-30 italic">No global settings for slots. Label is localized.</span>
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

