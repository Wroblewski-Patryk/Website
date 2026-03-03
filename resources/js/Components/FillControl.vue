<template>
    <div class="form-control w-full space-y-3">
        <!-- Header & Top Toggles -->
        <div class="flex items-center justify-between">
            <label class="label-text text-[10px] uppercase font-bold tracking-widest opacity-50">{{ label }}</label>
            <div class="join bg-base-300 rounded-md p-1 items-center">
                <button 
                    @click="setType('none')" 
                    class="btn btn-xs join-item min-h-0 h-6 w-8 border-none text-[10px]" 
                    :class="type === 'none' ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"
                    title="None">
                    <i class="fas fa-ban"></i>
                </button>
                <button 
                    @click="setType('color')" 
                    class="btn btn-xs join-item min-h-0 h-6 w-8 border-none text-[10px]" 
                    :class="type === 'color' ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"
                    title="Solid Color">
                    <i class="fas fa-fill-drip"></i>
                </button>
                <button 
                    @click="setType('gradient')" 
                    class="btn btn-xs join-item min-h-0 h-6 w-8 border-none text-[10px]" 
                    :class="type === 'gradient' ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"
                    title="Gradient">
                    <i class="fas fa-swatchbook"></i>
                </button>
                <button 
                    @click="setType('image')" 
                    class="btn btn-xs join-item min-h-0 h-6 w-8 border-none text-[10px]" 
                    :class="type === 'image' ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"
                    title="Image">
                    <i class="fas fa-image"></i>
                </button>
            </div>
        </div>

        <!-- Mode-Specific Input Area -->
        <div v-if="type !== 'none'" class="bg-base-200/50 rounded-xl p-3 border border-white/5 relative flex justify-center mt-1">
            
            <!-- Solid Color Picker -->
            <div v-if="type === 'color'" class="w-full">
                <ColorInput v-model="solidColor" @update:modelValue="updateSolid" />
            </div>

            <!-- Gradient Picker -->
            <div v-else-if="type === 'gradient'" class="w-full flex flex-col gap-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] uppercase opacity-50 font-bold w-12">Type</span>
                    <select v-model="gradType" @change="updateGradient" class="select select-xs select-bordered w-full max-w-[120px] focus:outline-none">
                        <option value="linear">Linear</option>
                        <option value="radial">Radial</option>
                    </select>
                </div>

                <div v-if="gradType === 'linear'" class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] uppercase opacity-50 font-bold w-12">Angle</span>
                    <input type="range" min="0" max="360" v-model="gradAngle" @input="updateGradient" class="range range-xs flex-1" />
                    <div class="flex items-center relative">
                        <input type="number" min="0" max="360" v-model="gradAngle" @input="updateGradient" class="input input-xs input-bordered w-14 px-1 text-center pr-3 font-mono" />
                        <span class="absolute right-2 text-[8px] opacity-50 pointer-events-none">&deg;</span>
                    </div>
                </div>

                <!-- Stop 1 -->
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] uppercase opacity-50 font-bold">Start Color</span>
                    <ColorInput v-model="gradColor1" @update:modelValue="updateGradient" />
                </div>

                <!-- Stop 2 -->
                <div class="flex flex-col gap-1 mt-1">
                    <span class="text-[10px] uppercase opacity-50 font-bold">End Color</span>
                    <ColorInput v-model="gradColor2" @update:modelValue="updateGradient" />
                </div>
            </div>

            <!-- Image URL Input -->
            <div v-else-if="type === 'image'" class="w-full form-control gap-2">
                <input 
                    type="text" 
                    v-model="internalImage" 
                    @input="emitUpdate"
                    placeholder="https://example.com/image.jpg" 
                    class="input input-sm input-bordered w-full"
                />
                <button @click="openMediaLibrary" class="btn btn-sm btn-outline border-white/10 hover:border-primary/50 text-[10px] uppercase tracking-wider">
                    <i class="fas fa-image mr-1"></i> Browse Media
                </button>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import ColorInput from './ColorInput.vue';

const props = defineProps({
    modelValue: {
        type: [Object, String],
        default: () => ({ type: 'none', color: 'rgba(0,0,0,0)', gradient: 'linear-gradient(0deg, rgba(0,0,0,1) 0%, rgba(255,255,255,1) 100%)', image: '' })
    },
    label: { type: String, default: 'Fill' }
});

const emit = defineEmits(['update:modelValue']);

// Local state for the UI
const type = ref('none');
const internalColor = ref('rgba(0,0,0,0)');
const internalGradient = ref('linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(255,255,255,1) 100%)');
const internalImage = ref('');

// Shared states
const solidColor = ref('rgba(0,0,0,0)');

const gradType = ref('linear');
const gradAngle = ref(90);
const gradColor1 = ref('#000000');
const gradColor2 = ref('#ffffff');

const extractColors = (str) => {
    return str.match(/(?:rgba?\([^)]+\)|#[a-fA-F0-9]{3,8})/gi) || [];
};

const updateSolid = () => {
    internalColor.value = solidColor.value;
    emitUpdate();
};

const updateGradient = () => {
    if (gradType.value === 'radial') {
        internalGradient.value = `radial-gradient(circle, ${gradColor1.value} 0%, ${gradColor2.value} 100%)`;
    } else {
        internalGradient.value = `linear-gradient(${gradAngle.value}deg, ${gradColor1.value} 0%, ${gradColor2.value} 100%)`;
    }
    emitUpdate();
};

const applyIncomingInternalStates = () => {
    solidColor.value = internalColor.value;

    if (internalGradient.value.startsWith('radial-gradient')) {
        gradType.value = 'radial';
    } else {
        gradType.value = 'linear';
        const angleMatch = internalGradient.value.match(/linear-gradient\((-?\d+)deg/);
        if(angleMatch) gradAngle.value = parseInt(angleMatch[1]);
    }
    
    const colors = extractColors(internalGradient.value);
    if(colors.length >= 1) gradColor1.value = colors[0];
    if(colors.length >= 2) gradColor2.value = colors[1];
};

// Parse incoming value, because historically colors were just stored as strings like 'rgba(255,0,0,1)'.
// If it's a string, we auto-convert it to our object shape for backward compatibility.
watch(() => props.modelValue, (newVal) => {
    if (!newVal) {
        type.value = 'none';
        return;
    }
    
    // Legacy String Support
    if (typeof newVal === 'string') {
        if (newVal.trim() === '' || newVal === 'rgba(0,0,0,0)' || newVal === 'transparent') {
            type.value = 'none';
        } else if (newVal.includes('gradient')) {
            type.value = 'gradient';
            internalGradient.value = newVal;
        } else if (newVal.includes('url(')) {
            type.value = 'image';
            internalImage.value = newVal.replace(/url\(['"]?(.*?)['"]?\)/, '$1');
        } else {
            type.value = 'color';
            internalColor.value = newVal;
        }
    } 
    // Object Support (New Format)
    else if (typeof newVal === 'object') {
        type.value = newVal.type || 'none';
        internalColor.value = newVal.color || 'rgba(0,0,0,0)';
        internalGradient.value = newVal.gradient || 'linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(255,255,255,1) 100%)';
        internalImage.value = newVal.image || '';
    }

    applyIncomingInternalStates();
}, { immediate: true, deep: true });

const setType = (newType) => {
    type.value = newType;
    emitUpdate();
};

const emitUpdate = () => {
    // Construct the payload
    const payload = {
        type: type.value,
        color: internalColor.value,
        gradient: internalGradient.value,
        image: internalImage.value
    };
    emit('update:modelValue', payload);
};

const openMediaLibrary = () => {
    // Scaffolded for later integration with a proper media manager modal
    alert("Media library integration goes here.");
};
</script>
