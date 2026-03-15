<template>
    <div class="space-y-4">
        <!-- Content Mode: Localized Labels, Items and Tabs -->
        <div v-if="mode === 'content'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div v-if="type === 'tabs'" class="space-y-3">
                <div v-for="(t, idx) in modelValue.tabs" :key="idx" class="p-3 bg-base-200/50 rounded-2xl border border-white/5 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] uppercase font-black opacity-30">Tab #{{ idx + 1 }}</span>
                        <button @click="modelValue.tabs.splice(idx, 1)" class="btn btn-xs btn-circle btn-ghost text-error opacity-50 hover:opacity-100"><PhX weight="bold" class="w-3 h-3" /></button>
                    </div>
                    <input type="text" v-model="t.title" placeholder="Tab Title" class="input input-xs input-bordered w-full font-bold focus:border-primary transition-all" />
                    <textarea v-model="t.content" placeholder="Tab Content" class="textarea textarea-xs textarea-bordered w-full h-20 focus:border-primary transition-all"></textarea>
                </div>
                <button @click="modelValue.tabs.push({title:'New Tab', content:''})" class="btn btn-xs btn-outline btn-block rounded-xl border-dashed opacity-50 hover:opacity-100">
                    <PhPlus weight="bold" class="mr-1 w-3 h-3" /> Add Tab
                </button>
            </div>
            
            <div v-if="['breadcrumbs', 'menu', 'steps'].includes(type)" class="space-y-3">
                <label class="text-[10px] opacity-40 uppercase font-black ml-1">List Items</label>
                <div v-for="(item, idx) in modelValue.items" :key="idx" class="flex gap-2 group">
                     <input type="text" v-model="modelValue.items[idx]" class="input input-sm input-bordered flex-1 focus:border-primary transition-all rounded-xl" />
                     <button @click="modelValue.items.splice(idx, 1)" class="btn btn-sm btn-circle btn-ghost text-error opacity-0 group-hover:opacity-100 transition-opacity"><PhX weight="bold" class="w-3 h-3" /></button>
                </div>
                <button @click="modelValue.items.push('New Item')" class="btn btn-xs btn-outline btn-block rounded-xl border-dashed opacity-50 hover:opacity-100 mt-2">
                    <PhPlus weight="bold" class="mr-1 w-3 h-3" /> Add Item
                </button>
            </div>

            <div v-if="type === 'navbar'" class="space-y-4">
                <div class="form-control">
                    <label class="label py-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Brand Title</span></label>
                    <input type="text" v-model="modelValue.title" placeholder="Brand Title" class="input input-sm input-bordered w-full font-bold focus:border-primary transition-all" />
                </div>
                <div class="form-control">
                    <label class="label py-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Action Button Text</span></label>
                    <input type="text" v-model="modelValue.actionButton" placeholder="Action Button Text" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                </div>
                
                <div class="divider my-1 opacity-10"></div>
                
                <div class="space-y-3">
                    <label class="text-[10px] opacity-40 uppercase font-black ml-1">Navigation Links</label>
                    <div v-for="(link, idx) in modelValue.links" :key="idx" class="flex gap-2 group">
                         <input type="text" v-model="modelValue.links[idx]" class="input input-sm input-bordered flex-1 focus:border-primary transition-all rounded-xl" />
                         <button @click="modelValue.links.splice(idx, 1)" class="btn btn-sm btn-circle btn-ghost text-error opacity-0 group-hover:opacity-100 transition-opacity"><PhX weight="bold" class="w-3 h-3" /></button>
                    </div>
                    <button @click="modelValue.links.push('New Link')" class="btn btn-xs btn-outline btn-block rounded-xl border-dashed opacity-50 hover:opacity-100 mt-2">
                        <PhPlus weight="bold" class="mr-1 w-3 h-3" /> Add Link
                    </button>
                </div>
            </div>
        </div>

        <!-- Advanced Mode: Global Configuration -->
        <div v-if="mode === 'advanced'" class="space-y-4 animate-in fade-in slide-in-from-top-1">
            <div class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No global settings for this specific block type.</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { PhX, PhPlus } from '@phosphor-icons/vue';

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

