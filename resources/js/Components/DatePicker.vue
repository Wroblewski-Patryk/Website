<template>
    <div class="flex gap-2 w-full">
        <!-- Date Part -->
        <div class="relative flex-1 group cursor-pointer" @click="openPicker('dateInputRef')">
            <input 
                ref="dateInputRef"
                type="date" 
                v-model="datePart"
                class="input input-bordered input-sm w-full transition-all pr-8 cursor-pointer focus:input-primary bg-base-100/30"
            />
            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none opacity-40 group-hover:opacity-100 transition-opacity">
                <PhCalendar weight="bold" class="w-3.5 h-3.5" />
            </div>
        </div>

        <!-- Time Part -->
        <div class="relative w-32 group cursor-pointer" @click="openPicker('timeInputRef')">
            <input 
                ref="timeInputRef"
                type="time" 
                v-model="timePart"
                class="input input-bordered input-sm w-full transition-all pr-8 cursor-pointer focus:input-primary bg-base-100/30"
            />
            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none opacity-40 group-hover:opacity-100 transition-opacity">
                <PhClock weight="bold" class="w-3.5 h-3.5" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, useTemplateRef } from 'vue';
import { PhCalendar, PhClock } from '@phosphor-icons/vue';

const props = defineProps({
    modelValue: String
});

const emit = defineEmits(['update:modelValue']);

const dateInputRef = useTemplateRef('dateInputRef');
const timeInputRef = useTemplateRef('timeInputRef');

const datePart = computed({
    get: () => {
        if (!props.modelValue) return '';
        return props.modelValue.split(' ')[0] || '';
    },
    set: (val) => {
        const time = timePart.value || '00:00';
        if (!val) {
            emit('update:modelValue', null);
            return;
        }
        emit('update:modelValue', `${val} ${time}:00`);
    }
});

const timePart = computed({
    get: () => {
        if (!props.modelValue) return '00:00';
        const parts = props.modelValue.split(' ');
        if (parts.length < 2) return '00:00';
        return parts[1].substring(0, 5);
    },
    set: (val) => {
        const date = datePart.value;
        if (!date) return;
        emit('update:modelValue', `${date} ${val}:00`);
    }
});

const openPicker = (refName) => {
    const el = refName === 'dateInputRef' ? dateInputRef.value : timeInputRef.value;
    if (el && typeof el.showPicker === 'function') {
        el.showPicker();
    }
};
</script>

<style scoped>
/* Total elimination of native browser UI elements inside the inputs */

/* Chrome, Edge, Safari */
input::-webkit-calendar-picker-indicator {
    display: none !important;
    -webkit-appearance: none !important;
}

input::-webkit-inner-spin-button,
input::-webkit-clear-button {
    display: none !important;
    -webkit-appearance: none !important;
}

/* Firefox */
input[type="date"], 
input[type="time"] {
    appearance: none;
    -moz-appearance: textfield;
}

/* Ensure the text is properly aligned and doesn't reserve space for hidden native icons */
input::-webkit-datetime-edit {
    display: inline-flex;
}

input::-webkit-datetime-edit-fields-wrapper {
    padding: 0;
}
</style>
