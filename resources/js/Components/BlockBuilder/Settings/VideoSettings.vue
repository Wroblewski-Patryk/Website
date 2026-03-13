<template>
    <div class="space-y-4">
        <!-- Content Mode -->
        <div v-if="mode === 'content'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
            <span class="text-xs opacity-30 italic">No localized content for video. Go to Advanced tab to manage video source.</span>
        </div>

        <!-- Advanced Mode -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Video Pool</span></label>
                <select v-model="modelValue.source" class="select select-bordered select-sm w-full focus:border-primary transition-all">
                    <option value="youtube">YouTube</option>
                    <option value="vimeo">Vimeo</option>
                    <option value="self">Self-Hosted</option>
                </select>
            </div>
            
            <div class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Video URL / ID</span></label>
                <div class="relative group">
                    <input type="text" v-model="modelValue.url" placeholder="Video URL or ID..." class="input input-sm input-bordered w-full font-mono text-[10px] focus:border-primary transition-all pr-8" />
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-primary opacity-50 group-hover:opacity-100 transition-opacity">
                        <PhYoutubeLogo weight="bold" class="w-3 h-3" v-if="modelValue.source === 'youtube'" />
                        <PhVideo weight="bold" class="w-3 h-3" v-else />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { PhVideo, PhYoutubeLogo } from '@phosphor-icons/vue';

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

