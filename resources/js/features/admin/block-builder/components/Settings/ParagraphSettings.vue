<template>
    <div class="space-y-6">
        <!-- Content Mode -->
        <div v-if="mode === 'content'" class="form-control">
            <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Content</span></label>
            <textarea v-model="modelValue.text" class="textarea textarea-bordered h-48 w-full focus:border-primary transition-all"></textarea>
        </div>

        <!-- Advanced Mode -->
        <div v-if="mode === 'advanced'" class="space-y-6 animate-in fade-in slide-in-from-top-1">
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
