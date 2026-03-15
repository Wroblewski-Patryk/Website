<template>
    <div class="space-y-3">
        <label class="text-[10px] opacity-40 uppercase font-black">Images</label>
        <div v-for="(img, idx) in modelValue.images" :key="idx" class="flex gap-2">
            <input type="text" v-model="modelValue.images[idx]" class="input input-xs input-bordered flex-1" />
            <button @click="modelValue.images.splice(idx, 1)" class="btn btn-xs btn-error btn-ghost">
                <PhX weight="bold" class="w-3 h-3" />
            </button>
        </div>
        <button @click="modelValue.images.push('')" class="btn btn-xs btn-outline btn-block">Add Image</button>
    </div>
</template>

<script setup>
import { PhX } from '@phosphor-icons/vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    }
});
</script>
