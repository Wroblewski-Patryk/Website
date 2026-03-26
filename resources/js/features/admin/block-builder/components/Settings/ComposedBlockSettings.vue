<template>
    <div class="space-y-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text text-[10px] uppercase font-bold opacity-50">Composed Block</span>
            </label>
            <select v-model="selectedId" class="select select-bordered select-sm w-full">
                <option :value="null">Select composed block</option>
                <option v-for="item in availableComposedBlocks" :key="item.id" :value="item.id">
                    {{ getBlockTitle(item) }} ({{ item.slug }})
                </option>
            </select>
        </div>

        <div v-if="selectedComposedBlock" class="rounded-xl border border-base-content/10 bg-base-200/30 p-3">
            <p class="text-[10px] uppercase font-bold opacity-50">Selected</p>
            <p class="font-semibold text-sm mt-1">{{ getBlockTitle(selectedComposedBlock) }}</p>
            <p class="font-mono text-[11px] opacity-60 mt-1">#{{ selectedComposedBlock.id }} / {{ selectedComposedBlock.slug }}</p>
            <p class="text-[11px] opacity-60 mt-2">
                Blocks: {{ Array.isArray(selectedComposedBlock.content) ? selectedComposedBlock.content.length : 0 }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
});

const page = usePage();

const availableComposedBlocks = computed(() => {
    return Array.isArray(page.props.composed_blocks_library)
        ? page.props.composed_blocks_library
        : [];
});

const selectedId = computed({
    get: () => props.modelValue?.composed_block_id ?? null,
    set: (value) => {
        props.modelValue.composed_block_id = value ? Number(value) : null;
        const selected = availableComposedBlocks.value.find((item) => item.id === props.modelValue.composed_block_id);
        props.modelValue.snapshot_title = selected ? getBlockTitle(selected) : '';
    },
});

const selectedComposedBlock = computed(() => {
    return availableComposedBlocks.value.find((item) => item.id === Number(selectedId.value)) || null;
});

const getBlockTitle = (item) => {
    if (!item?.title) return 'Untitled';
    if (typeof item.title === 'string') return item.title;
    return item.title.en || item.title.pl || Object.values(item.title)[0] || 'Untitled';
};
</script>

