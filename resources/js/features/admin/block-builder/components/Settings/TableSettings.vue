<template>
    <div class="space-y-4">
        <!-- Content Mode: Localized Cell Values -->
        <div v-if="mode === 'content'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="flex flex-col gap-2">
                <div v-for="(row, rIdx) in modelValue.rows" :key="rIdx" class="flex gap-2 items-center bg-base-200/50 p-2 rounded-2xl border border-white/5 overflow-hidden">
                    <div class="flex-none font-mono text-[9px] opacity-20 w-4">#{{ rIdx + 1 }}</div>
                    <div class="flex gap-2 flex-1 overflow-x-auto custom-scrollbar pb-1">
                        <input v-for="(cell, cIdx) in row" :key="cIdx" type="text" v-model="modelValue.rows[rIdx][cIdx]" class="input input-xs input-bordered min-w-[100px] flex-1 focus:border-primary transition-all rounded-lg" :placeholder="`Cell ${rIdx+1},${cIdx+1}`" />
                    </div>
                </div>
            </div>
            
            <div class="p-4 border border-dashed border-base-content/10 rounded-2xl text-center bg-base-200/20">
                <span class="text-[10px] opacity-40 italic">Editing values for current language. Go to Advanced tab to modify table structure.</span>
            </div>
        </div>

        <!-- Advanced Mode: Global Table Structure -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="bg-base-200/50 p-4 rounded-3xl border border-white/5 space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] opacity-40 uppercase font-black">Columns</label>
                    <div class="join">
                        <button @click="modelValue.rows.forEach(r => r.push(''))" class="btn btn-xs join-item btn-ghost bg-base-300 hover:bg-primary hover:text-white px-3 transition-colors" title="Add Column">+ Col</button>
                        <button @click="modelValue.rows.forEach(r => { if(r.length > 1) r.pop() })" class="btn btn-xs join-item btn-ghost bg-base-300 hover:bg-error hover:text-white px-3 transition-colors" title="Remove Last Column">- Col</button>
                    </div>
                </div>

                <div class="divider my-0 opacity-10"></div>

                <div class="flex items-center justify-between">
                    <label class="text-[10px] opacity-40 uppercase font-black">Rows</label>
                    <div class="flex gap-2">
                         <button @click="modelValue.rows.push(new Array(modelValue.rows[0].length).fill(''))" class="btn btn-xs btn-primary rounded-xl px-4">
                            <PhPlus weight="bold" class="w-2.5 h-2.5 mr-1.5" />Add Row
                        </button>
                    </div>
                </div>

                <div class="space-y-2 mt-4">
                     <div v-for="(row, rIdx) in modelValue.rows" :key="rIdx" class="flex items-center justify-between p-2 bg-base-300/50 rounded-xl border border-white/5 group">
                        <span class="text-[10px] font-bold opacity-30 ml-2">Row #{{ rIdx + 1 }}</span>
                        <div class="flex gap-1">
                            <button @click="modelValue.rows.splice(rIdx, 1)" class="btn btn-square btn-xs btn-ghost text-error opacity-0 group-hover:opacity-100 transition-opacity" :disabled="modelValue.rows.length <= 1" title="Remove Row">
                                <PhTrash weight="bold" class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { PhTrash, PhPlus } from '@phosphor-icons/vue';

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

