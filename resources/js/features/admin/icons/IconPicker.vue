<template>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-2">
            <div class="form-control">
                <label class="label py-0.5">
                    <span class="text-[10px] uppercase font-bold opacity-50">Icon Set</span>
                </label>
                <select v-model="activeSet" class="select select-bordered select-sm w-full">
                    <option value="all">All</option>
                    <option v-for="set in ICON_SETS" :key="set.id" :value="set.id">{{ set.label }}</option>
                </select>
            </div>
            <div class="form-control">
                <label class="label py-0.5">
                    <span class="text-[10px] uppercase font-bold opacity-50">Search</span>
                </label>
                <input v-model="query" type="text" class="input input-bordered input-sm w-full" placeholder="Search icon..." />
            </div>
        </div>

        <div class="grid max-h-56 grid-cols-6 gap-2 overflow-y-auto rounded-xl border border-base-content/10 p-2">
            <button
                v-for="item in filteredIcons"
                :key="item.id"
                type="button"
                class="btn btn-sm btn-ghost h-10 min-h-0 rounded-lg border border-transparent p-0"
                :class="modelValue === item.value ? 'border-primary bg-primary/10 text-primary' : ''"
                :title="item.label"
                @click="$emit('update:modelValue', item.value)"
            >
                <IconRenderer :icon="item.value" class="text-lg" />
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import IconRenderer from '@/features/admin/icons/IconRenderer.vue';
import { ICON_SETS, searchIconLibrary } from '@/features/admin/icons/iconLibrary';

defineProps({
    modelValue: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);

const query = ref('');
const activeSet = ref('all');

const filteredIcons = computed(() => searchIconLibrary(query.value, activeSet.value).slice(0, 120));
</script>

