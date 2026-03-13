<template>
    <div class="space-y-4">
        <!-- Content Mode: Localized Labels and Placeholders -->
        <div v-if="mode === 'content'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div v-if="['text_input', 'textarea', 'select', 'checkbox', 'radio', 'toggle', 'file_input'].includes(type)" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Field Label</span></label>
                <input type="text" v-model="modelValue.label" placeholder="e.g. Your Name" class="input input-sm input-bordered w-full font-bold focus:border-primary transition-all" />
            </div>
            
            <div v-if="['text_input', 'textarea'].includes(type)" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Placeholder Text</span></label>
                <input type="text" v-model="modelValue.placeholder" placeholder="e.g. Enter your name..." class="input input-sm input-bordered w-full focus:border-primary transition-all" />
            </div>
            
            <div v-if="type === 'select'" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Options (One per line)</span></label>
                <textarea v-model="modelValue.options" placeholder="Option 1\nOption 2" class="textarea textarea-sm textarea-bordered w-full h-32 focus:border-primary transition-all"></textarea>
            </div>

            <div v-if="['range', 'rating', 'file_input'].includes(type)" class="p-8 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No localized content for this specific input type.</span>
            </div>
        </div>

        <!-- Advanced Mode: Global Configuration -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div v-if="['checkbox', 'toggle'].includes(type)" class="form-control bg-base-200/50 p-3 rounded-2xl border border-white/5">
                <label class="label cursor-pointer flex justify-between">
                    <span class="label-text text-[10px] uppercase font-bold opacity-60">Default Checked</span>
                    <input type="checkbox" v-model="modelValue.checked" class="toggle toggle-primary toggle-xs" />
                </label>
            </div>

            <div v-if="type === 'radio'" class="form-control">
                <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Radio Group Name</span></label>
                <input type="text" v-model="modelValue.group" placeholder="e.g. color_selection" class="input input-sm input-bordered w-full font-mono text-xs focus:border-primary transition-all" />
            </div>

            <div v-if="['range', 'rating'].includes(type)" class="space-y-4">
                <div class="form-control">
                    <label class="label pb-1 flex justify-between">
                        <span class="label-text text-[10px] uppercase font-bold opacity-50">Initial Value</span>
                        <span class="text-xs font-mono font-bold text-primary">{{ modelValue.val }}</span>
                    </label>
                    <input type="range" v-model="modelValue.val" :min="modelValue.min || 1" :max="modelValue.max || 100" class="range range-xs range-primary" />
                </div>
                
                <div class="grid grid-cols-2 gap-3" v-if="type === 'range'">
                    <div class="form-control">
                        <label class="label py-0.5"><span class="text-[9px] uppercase font-bold opacity-40">Min</span></label>
                        <input type="number" v-model="modelValue.min" class="input input-xs input-bordered w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label py-0.5"><span class="text-[9px] uppercase font-bold opacity-40">Max</span></label>
                        <input type="number" v-model="modelValue.max" class="input input-xs input-bordered w-full" />
                    </div>
                </div>

                <div class="form-control" v-if="type === 'rating'">
                    <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Max Stars / Points</span></label>
                    <input type="number" v-model="modelValue.max" class="input input-sm input-bordered w-full focus:border-primary transition-all" max="10" />
                </div>
            </div>

            <div v-else-if="!['checkbox', 'toggle', 'radio', 'range', 'rating'].includes(type)" class="p-8 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No global settings for this specific input variant.</span>
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

