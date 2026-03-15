<template>
    <div class="space-y-4">
        <!-- Content Mode -->
        <div v-if="mode === 'content'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
            <span class="text-xs opacity-30 italic">No localized content for template reference. Go to Advanced tab to selection.</span>
        </div>

        <!-- Advanced Mode -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Select Template Part</span></label>
                <select v-model="modelValue.template_id" class="select select-bordered select-sm w-full focus:border-primary transition-all">
                    <option :value="null">None (Disconnected)</option>
                    <option v-for="t in templates" :key="t.id" :value="t.id">
                        {{ typeof t.title === 'object' ? (t.title[store.editingLocale] || t.title['pl'] || Object.values(t.title)[0]) : t.title }} ({{ t.type }})
                    </option>
                </select>
                <p class="text-[9px] opacity-40 italic mt-2 px-1 leading-relaxed">Note: Common template parts like Headers or Footers are shared globally across all languages.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';

const store = useBlockBuilderStore();

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    },
    templates: {
        type: Array,
        default: () => []
    },
    mode: {
        type: String,
        default: 'content'
    }
});
</script>

