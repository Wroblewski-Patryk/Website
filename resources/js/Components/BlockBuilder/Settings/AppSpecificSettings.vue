<template>
    <div class="space-y-4">
        <!-- Content Mode: Translatable text strings -->
        <div v-if="mode === 'content'" class="space-y-6">
            <div v-if="type === 'text_rotate'" class="space-y-3">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Prefix Text</span></label>
                    <input type="text" v-model="modelValue.prefix" placeholder="Prefix" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Rotating Words</span></label>
                    <textarea v-model="modelValue.words" placeholder="Words (one per line)" class="textarea textarea-sm textarea-bordered w-full h-32 focus:border-primary transition-all"></textarea>
                    <label class="label"><span class="label-text-alt opacity-40">One word per line</span></label>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Suffix Text</span></label>
                    <input type="text" v-model="modelValue.suffix" placeholder="Suffix" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                </div>
            </div>

            <div v-else class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No localized content for this block. Check Advanced tab for configuration.</span>
            </div>
        </div>

        <!-- Advanced Mode: Technical / Global settings -->
        <div v-if="mode === 'advanced'" class="space-y-6 animate-in fade-in slide-in-from-top-1">
            <div v-if="['posts_list', 'projects_list'].includes(type)" class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Items count to display</span></label>
                    <input type="number" v-model="modelValue.count" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                </div>
                
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Display Layout</span></label>
                    <select v-model="modelValue.layout" class="select select-bordered select-sm w-full">
                        <option value="grid">Grid Layout</option>
                        <option value="list">List Layout (Stacked)</option>
                    </select>
                </div>
            </div>

            <div v-else class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No global settings for this block.</span>
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
