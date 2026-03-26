<template>
    <div class="space-y-4">
        <!-- Content Mode: Localized Content -->
        <div v-if="mode === 'content'" class="space-y-6">
            <div v-if="type === 'card'" class="space-y-3">
                <input type="text" v-model="modelValue.title" placeholder="Card Title" class="input input-sm input-bordered w-full font-bold focus:border-primary transition-all" />
                <textarea v-model="modelValue.description" placeholder="Description" class="textarea textarea-sm textarea-bordered w-full focus:border-primary transition-all"></textarea>
                <input type="text" v-model="modelValue.buttonText" placeholder="Button Text" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                <input type="text" v-model="modelValue.image" placeholder="Image URL / Media" class="input input-sm input-bordered w-full text-xs focus:border-primary transition-all" />
            </div>
            
            <div v-if="type === 'stat'" class="space-y-3">
                <input type="text" v-model="modelValue.title" placeholder="Title" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                <input type="text" v-model="modelValue.value" placeholder="Value (e.g., 34K)" class="input input-sm input-bordered w-full font-bold focus:border-primary transition-all" />
                <input type="text" v-model="modelValue.desc" placeholder="Description" class="input input-sm input-bordered w-full text-xs focus:border-primary transition-all" />
            </div>

            <div v-if="type === 'accordion'" class="space-y-3">
                <div v-for="(item, idx) in modelValue.items" :key="idx" class="p-4 bg-base-200/50 rounded-2xl border border-white/5 space-y-2 group">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold opacity-30 uppercase">Item #{{idx+1}}</span>
                        <button @click="modelValue.items.splice(idx, 1)" class="btn btn-ghost btn-xs btn-square text-error opacity-0 group-hover:opacity-100 transition-opacity">
                            <PhTrash weight="bold" class="w-3 h-3" />
                        </button>
                    </div>
                    <input type="text" v-model="item.title" placeholder="Title" class="input input-xs input-bordered w-full font-bold focus:border-primary transition-all" />
                    <textarea v-model="item.content" placeholder="Content" class="textarea textarea-xs textarea-bordered w-full focus:border-primary transition-all"></textarea>
                </div>
                <button @click="modelValue.items.push({title:'New Item', content:'Content here'})" class="btn btn-xs btn-outline btn-block border-dashed opacity-60 hover:opacity-100">
                    <PhPlus weight="bold" class="w-3 h-3 mr-2" /> Add Item
                </button>
            </div>

            <div v-if="type === 'timeline'" class="space-y-3">
                <div v-for="(item, idx) in modelValue.items" :key="idx" class="p-4 bg-base-200/50 rounded-2xl border border-white/5 space-y-2 group">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold opacity-30 uppercase">Event #{{idx+1}}</span>
                        <button @click="modelValue.items.splice(idx, 1)" class="btn btn-ghost btn-xs btn-square text-error opacity-0 group-hover:opacity-100 transition-opacity">
                            <PhTrash weight="bold" class="w-3 h-3" />
                        </button>
                    </div>
                    <input type="text" v-model="item.year" placeholder="Year / Time" class="input input-xs input-bordered w-full font-bold focus:border-primary transition-all" />
                    <input type="text" v-model="item.title" placeholder="Title" class="input input-xs input-bordered w-full font-bold text-primary focus:border-primary transition-all" />
                    <textarea v-model="item.content" placeholder="Details" class="textarea textarea-xs textarea-bordered w-full focus:border-primary transition-all"></textarea>
                </div>
                <button @click="modelValue.items.push({year:'2026', title:'New Milestone', content:''})" class="btn btn-xs btn-outline btn-block border-dashed opacity-60 hover:opacity-100">
                    <PhPlus weight="bold" class="w-3 h-3 mr-2" /> Add Milestone
                </button>
            </div>

            <div v-if="type === 'chat'" class="space-y-3">
                <label class="text-[10px] opacity-40 uppercase font-black">Messages</label>
                <div v-for="(msg, idx) in modelValue.messages" :key="idx" class="p-3 bg-base-200/50 rounded-2xl border border-white/5 space-y-2 group">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] font-bold opacity-30 uppercase">Msg #{{idx+1}} ({{msg.side}})</span>
                        <button @click="modelValue.messages.splice(idx, 1)" class="btn btn-ghost btn-xs btn-square text-error opacity-0 group-hover:opacity-100 transition-opacity">
                            <PhTrash weight="bold" class="w-3 h-3" />
                        </button>
                    </div>
                    <input type="text" v-model="msg.text" placeholder="Message text" class="input input-xs input-bordered w-full focus:border-primary transition-all" />
                </div>
                <button @click="modelValue.messages.push({side:'start', text:''})" class="btn btn-xs btn-outline btn-block border-dashed opacity-60 hover:opacity-100">
                    <PhPlus weight="bold" class="w-3 h-3 mr-2" /> Add Message
                </button>
            </div>

            <div v-if="type === 'countdown'" class="space-y-3">
                <input type="number" v-model="modelValue.value" placeholder="Target Value" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                <input type="text" v-model="modelValue.unit" placeholder="Unit (e.g. days, hours)" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
            </div>

            <div v-if="type === 'alert'" class="space-y-3">
                <textarea v-model="modelValue.text" placeholder="Alert text" class="textarea textarea-sm textarea-bordered w-full h-24 focus:border-primary transition-all"></textarea>
            </div>

            <div v-if="['diff', 'progress', 'radial_progress'].includes(type)" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">Purely technical block. Check Advanced tab for configuration.</span>
            </div>
        </div>

        <!-- Advanced Mode: Structural / Technical Settings -->
        <div v-if="mode === 'advanced'" class="space-y-6 animate-in fade-in slide-in-from-top-1">
            <div v-if="type === 'stat'" class="space-y-3">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Icon</span></label>
                    <IconPicker v-model="modelValue.icon" />
                </div>
            </div>

            <div v-if="type === 'chat'" class="space-y-4">
                <label class="text-[10px] opacity-40 uppercase font-black">Message Flow</label>
                <div v-for="(msg, idx) in modelValue.messages" :key="idx" class="flex items-center gap-2 p-2 bg-base-200 rounded-lg">
                    <span class="text-[10px] opacity-30 min-w-[30px]">#{{idx+1}}</span>
                    <select v-model="msg.side" class="select select-xs select-bordered w-full">
                        <option value="start">Left (Start)</option>
                        <option value="end">Right (End)</option>
                    </select>
                </div>
            </div>

            <div v-if="type === 'diff'" class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Before Image</span></label>
                    <input type="text" v-model="modelValue.img1" placeholder="URL" class="input input-sm input-bordered w-full font-mono text-xs focus:border-primary transition-all" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">After Image</span></label>
                    <input type="text" v-model="modelValue.img2" placeholder="URL" class="input input-sm input-bordered w-full font-mono text-xs focus:border-primary transition-all" />
                </div>
            </div>

            <div v-if="type === 'alert'" class="space-y-3">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Alert Type</span></label>
                    <select v-model="modelValue.type" class="select select-bordered select-sm w-full">
                        <option value="alert-info">Info</option>
                        <option value="alert-success">Success</option>
                        <option value="alert-warning">Warning</option>
                        <option value="alert-error">Error</option>
                    </select>
                </div>
            </div>
            
            <div v-if="['progress', 'radial_progress'].includes(type)" class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Value</span></label>
                    <input type="number" v-model="modelValue.value" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                </div>
                <div v-if="type === 'progress'" class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Max Value</span></label>
                    <input type="number" v-model="modelValue.max" class="input input-sm input-bordered w-full focus:border-primary transition-all" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Color Theme</span></label>
                    <select v-model="modelValue.color" class="select select-bordered select-sm w-full">
                        <option value="progress-primary">Primary</option>
                        <option value="progress-secondary">Secondary</option>
                        <option value="progress-accent">Accent</option>
                        <option value="progress-success">Success</option>
                        <option value="progress-warning">Warning</option>
                        <option value="progress-error">Error</option>
                        <option v-if="type === 'radial_progress'" value="text-primary">Radial Primary</option>
                    </select>
                </div>
            </div>

            <div v-else-if="!['stat', 'chat', 'diff', 'alert'].includes(type)" class="p-10 border-2 border-dashed border-base-content/10 rounded-3xl text-center">
                <span class="text-xs opacity-30 italic">No global settings for this specific block variant.</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { PhPlus, PhTrash } from '@phosphor-icons/vue';
import { IconPicker } from '@/features/admin/icons';

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
