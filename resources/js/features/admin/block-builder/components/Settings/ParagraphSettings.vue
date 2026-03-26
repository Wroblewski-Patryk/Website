<template>
    <div class="space-y-6">
        <!-- Content Mode -->
        <div v-if="mode === 'content'" class="form-control">
            <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Content</span></label>
            <LightweightWysiwyg v-model="modelValue.text" />
        </div>

        <!-- Advanced Mode -->
        <div v-if="mode === 'advanced'" class="space-y-6 animate-in fade-in slide-in-from-top-1">
            <div class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No global settings for this block.</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import LightweightWysiwyg from '@/features/admin/shared/components/LightweightWysiwyg.vue';

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
