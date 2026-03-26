<template>
    <div class="space-y-4">
        <div class="form-control">
            <label class="label pb-1">
                <span class="label-text text-[10px] uppercase font-bold opacity-50">Icon</span>
            </label>
            <IconPicker v-model="modelValue.icon" />
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="form-control">
                <label class="label py-0.5">
                    <span class="text-[10px] uppercase font-bold opacity-50">Size</span>
                </label>
                <UnitInput v-model="modelValue.size" placeholder="2rem" />
            </div>
            <div class="form-control">
                <label class="label py-0.5">
                    <span class="text-[10px] uppercase font-bold opacity-50">Color</span>
                </label>
                <input v-model="modelValue.color" type="text" class="input input-bordered input-sm w-full" placeholder="#000000" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { IconPicker } from '@/features/admin/icons';
import UnitInput from '@/features/admin/theme/components/UnitInput.vue';

defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
});
</script>
