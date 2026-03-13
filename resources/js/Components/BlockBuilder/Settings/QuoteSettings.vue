<template>
    <div class="space-y-6">
        <div v-if="mode === 'content'" class="space-y-6 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Quote Text</span></label>
                <textarea v-model="modelValue.text" class="textarea textarea-bordered h-24 w-full focus:border-primary transition-all italic font-serif" placeholder="Wpisz treść cytatu..."></textarea>
            </div>

            <div class="divider my-0 opacity-10"></div>

            <div class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Author Name</span></label>
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 opacity-30"><PhUser weight="bold" class="w-3 h-3" /></div>
                        <input type="text" v-model="modelValue.author" placeholder="Jan Kowalski" class="input input-sm input-bordered w-full pl-8 font-bold text-[10px]" />
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Author Title / Role</span></label>
                    <input type="text" v-model="modelValue.authorTitle" placeholder="np. CEO of Acme Corp" class="input input-sm input-bordered w-full text-[10px]" />
                </div>
            </div>
        </div>

        <div v-else-if="mode === 'advanced'" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
            <span class="text-xs opacity-30 italic">No global settings for this specific block type.</span>
        </div>
    </div>
</template>

<script setup>
import { PhUser } from '@phosphor-icons/vue';

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

