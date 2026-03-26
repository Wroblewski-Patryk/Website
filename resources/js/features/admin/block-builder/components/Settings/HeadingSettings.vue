<template>
    <div class="space-y-6">
        <!-- Content Mode: Text Editing -->
        <div v-if="mode === 'content'" class="form-control">
            <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Heading Text</span></label>
            <LightweightWysiwyg v-model="modelValue.text" />
        </div>

        <!-- Advanced Mode: Structural Settings -->
        <div v-if="mode === 'advanced'" class="space-y-6 animate-in fade-in slide-in-from-top-1">
            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Level</span></label>
                <div class="join w-full bg-base-300/50 p-1 rounded-lg">
                    <button v-for="h in [1,2,3,4,5,6]" :key="h" 
                            @click="modelValue.level = h"
                            type="button"
                            class="btn btn-xs join-item flex-1 border-none transition-all"
                            :class="modelValue.level === h ? 'btn-primary shadow-lg' : 'btn-ghost opacity-50 hover:opacity-100'">H{{h}}</button>
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Alignment</span></label>
                <div class="join w-full bg-base-300/50 p-1 rounded-lg">
                    <button v-for="align in ['left', 'center', 'right', 'justify']" :key="align" 
                            @click="modelValue.align = align"
                            type="button"
                            class="btn btn-xs join-item flex-1 border-none transition-all"
                            :class="modelValue.align === align ? 'btn-primary shadow-lg' : 'btn-ghost opacity-50 hover:opacity-100'">
                        <component :is="getAlignIcon(align)" class="w-3 h-3" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { PhTextAlignLeft, PhTextAlignCenter, PhTextAlignRight, PhTextAlignJustify } from '@phosphor-icons/vue';
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

const getAlignIcon = (align) => {
    switch (align) {
        case 'center': return PhTextAlignCenter;
        case 'right': return PhTextAlignRight;
        case 'justify': return PhTextAlignJustify;
        default: return PhTextAlignLeft;
    }
};
</script>
