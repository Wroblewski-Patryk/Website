<template>
    <div class="flex items-center gap-2 w-full">
        <!-- ColorPicker native trigger -->
        <div class="color-trigger-wrap w-6 h-6 shrink-0 rounded-sm overflow-hidden shadow-inner border border-white/10">
            <ColorPicker v-model:pureColor="internalValue" :format="format === 'hex' ? 'hex8' : 'rgb'" shape="square" useType="pure" :isWidget="false" @pureColorChange="handlePickerChange" />
        </div>
        <input type="text" v-model="internalValue" @change="handleInputChange" class="input input-xs input-bordered flex-1 px-2 font-mono" />
        <button @click="toggleFormat" class="btn btn-xs btn-outline border-white/10 w-12 text-[9px] font-bold" title="Toggle Format">
            {{ format.toUpperCase() }}
        </button>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { ColorPicker } from 'vue3-colorpicker';
import 'vue3-colorpicker/style.css';

const props = defineProps({
    modelValue: { type: String, default: '#000000' }
});

const emit = defineEmits(['update:modelValue']);

// Always default to HEX as requested by user
const isRgba = (str) => String(str).toLowerCase().startsWith('rgb');
const format = ref('hex');
const internalValue = ref(props.modelValue);

onMounted(() => {
    if (format.value === 'hex' && isRgba(internalValue.value)) {
        internalValue.value = rgbToHex(internalValue.value);
        handleInputChange();
    }
});

watch(() => props.modelValue, (newVal) => {
    if (newVal !== internalValue.value) {
        internalValue.value = newVal;
    }
});

const handlePickerChange = () => {
    emit('update:modelValue', internalValue.value);
};

const handleInputChange = () => {
    emit('update:modelValue', internalValue.value);
};

const hexToRgb = (h) => {
    let r = 0, g = 0, b = 0, a = 1;
    let hex = h.replace('#', '');
    if (hex.length === 3) {
        r = parseInt(hex[0]+hex[0], 16);
        g = parseInt(hex[1]+hex[1], 16);
        b = parseInt(hex[2]+hex[2], 16);
    } else if (hex.length === 4) {
        r = parseInt(hex[0]+hex[0], 16);
        g = parseInt(hex[1]+hex[1], 16);
        b = parseInt(hex[2]+hex[2], 16);
        a = parseInt(hex[3]+hex[3], 16) / 255;
    } else if (hex.length === 6) {
        r = parseInt(hex.substring(0,2), 16);
        g = parseInt(hex.substring(2,4), 16);
        b = parseInt(hex.substring(4,6), 16);
    } else if (hex.length === 8) {
        r = parseInt(hex.substring(0,2), 16);
        g = parseInt(hex.substring(2,4), 16);
        b = parseInt(hex.substring(4,6), 16);
        a = parseInt(hex.substring(6,8), 16) / 255;
    }
    return a === 1 ? `rgb(${r}, ${g}, ${b})` : `rgba(${r}, ${g}, ${b}, ${a.toFixed(2)})`;
};

const rgbToHex = (rgba) => {
    const match = rgba.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
    if (match) {
        const r = parseInt(match[1]).toString(16).padStart(2, '0');
        const g = parseInt(match[2]).toString(16).padStart(2, '0');
        const b = parseInt(match[3]).toString(16).padStart(2, '0');
        let a = '';
        if (match[4] !== undefined) {
            a = Math.round(parseFloat(match[4]) * 255).toString(16).padStart(2, '0');
        }
        return `#${r}${g}${b}${a}`.toLowerCase();
    }
    return rgba;
};

const toggleFormat = () => {
    if (format.value === 'hex') {
        format.value = 'rgb';
        if (internalValue.value.startsWith('#')) {
            internalValue.value = hexToRgb(internalValue.value);
            handleInputChange();
        }
    } else {
        format.value = 'hex';
        if (isRgba(internalValue.value)) {
            internalValue.value = rgbToHex(internalValue.value);
            handleInputChange();
        }
    }
};
</script>

<style>
/* Override default vue3-colorpicker margins that break the 24x24 container */
.color-trigger-wrap .vc-color-wrap {
    margin: 0 !important;
    width: 100% !important;
    height: 100% !important;
    border-radius: 0 !important;
    display: block !important;
}
.color-trigger-wrap .current-color {
    width: 100% !important;
    height: 100% !important;
    border-radius: 0 !important;
    display: block !important;
}
</style>
