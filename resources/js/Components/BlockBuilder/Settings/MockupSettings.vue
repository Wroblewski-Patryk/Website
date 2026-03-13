<template>
    <div class="space-y-4">
        <!-- Content Mode: Localized Content and Code -->
        <div v-if="mode === 'content'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div v-if="['mockup_browser', 'mockup_window'].includes(type)" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Mockup Content</span></label>
                <textarea v-model="modelValue.content" placeholder="Content text..." class="textarea textarea-sm textarea-bordered w-full h-32 focus:border-primary transition-all"></textarea>
            </div>

            <div v-if="type === 'mockup_code'" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Code Snippet</span></label>
                <textarea v-model="modelValue.code" placeholder="npm install..." class="textarea textarea-sm textarea-bordered w-full h-48 font-mono text-xs bg-base-300 text-success leading-relaxed focus:border-primary transition-all"></textarea>
            </div>

            <div v-if="type === 'mockup_phone'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No localized content for phone mockup. Go to Advanced tab for image URL.</span>
            </div>
        </div>

        <!-- Advanced Mode: Global URL -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div v-if="['mockup_browser', 'mockup_phone'].includes(type)" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">{{ type === 'mockup_browser' ? 'Browser URL バー' : 'Mockup Image URL' }}</span></label>
                <input type="text" v-model="modelValue.url" :placeholder="type === 'mockup_browser' ? 'https://example.com' : 'https://...'" class="input input-sm input-bordered w-full font-mono text-[10px] focus:border-primary transition-all" />
            </div>

            <div v-if="type === 'mockup_window'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No global settings for window mockup.</span>
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
