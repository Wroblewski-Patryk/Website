<template>
    <div class="form-control w-full space-y-3">
        <!-- Header & Top Toggles -->
        <div class="flex items-center justify-between">
            <label class="label-text text-[10px] uppercase font-bold tracking-widest opacity-50">{{ label }}</label>
            <div class="join bg-base-200 rounded-md items-center">
                <button 
                    @click="setType('none')" 
                    class="btn btn-sm join-item min-h-0 h-8 w-10 border-none text-[10px]"
                    :class="type === 'none' ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"
                    :title="t('admin.builder.fill_none', 'None')">
                    <PhProhibit weight="bold" class="w-4 h-4" />
                </button>
                <button 
                    @click="setType('color')" 
                    class="btn btn-sm join-item min-h-0 h-8 w-10 border-none text-[10px]"
                    :class="type === 'color' ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"
                    :title="t('admin.builder.fill_color', 'Solid Color')">
                    <PhPaintBucket weight="bold" class="w-4 h-4" />
                </button>
                <button 
                    @click="setType('gradient')" 
                    class="btn btn-sm join-item min-h-0 h-8 w-10 border-none text-[10px]"
                    :class="type === 'gradient' ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"
                    :title="t('admin.builder.fill_gradient', 'Gradient')">
                    <PhSwatches weight="bold" class="w-4 h-4" />
                </button>
                <button 
                    @click="setType('image')" 
                    class="btn btn-sm join-item min-h-0 h-8 w-10 border-none text-[10px]"
                    :class="type === 'image' ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"
                    :title="t('admin.builder.fill_image', 'Image')">
                    <PhImage weight="bold" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Mode-Specific Input Area -->
        <div v-if="type !== 'none'" class="mt-1">
            
            <!-- Solid Color Picker -->
            <div v-if="type === 'color'" class="w-full">
                <ColorInput v-model="solidColor" @update:modelValue="updateSolid" />
            </div>

            <!-- Gradient Picker -->
            <div v-else-if="type === 'gradient'" class="w-full flex flex-col gap-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] uppercase opacity-50 font-bold w-12">{{ t('admin.builder.fill_grad_type', 'Type') }}</span>
                    <select v-model="gradType" @change="updateGradient" class="select select-bordered select-sm h-8 min-h-0 w-full max-w-[120px]">
                        <option value="linear">{{ t('admin.builder.fill_grad_linear', 'Linear') }}</option>
                        <option value="radial">{{ t('admin.builder.fill_grad_radial', 'Radial') }}</option>
                    </select>
                </div>

                <div v-if="gradType === 'linear'" class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] uppercase opacity-50 font-bold w-12">{{ t('admin.builder.fill_grad_angle', 'Angle') }}</span>
                    <input type="range" min="0" max="360" v-model="gradAngle" @input="updateGradient" class="range range-primary range-sm flex-1" />
                    <div class="flex items-center relative">
                        <input type="number" min="0" max="360" v-model="gradAngle" @input="updateGradient" class="input input-sm input-bordered h-8 min-h-0 w-14 px-1 text-center pr-3 font-mono" />
                        <span class="absolute right-2 text-[8px] opacity-50 pointer-events-none">&deg;</span>
                    </div>
                </div>

                <!-- Stop 1 -->
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] uppercase opacity-50 font-bold">{{ t('admin.builder.fill_grad_start', 'Start Color') }}</span>
                    <ColorInput v-model="gradColor1" @update:modelValue="updateGradient" />
                </div>

                <!-- Stop 2 -->
                <div class="flex flex-col gap-1 mt-1">
                    <span class="text-[10px] uppercase opacity-50 font-bold">{{ t('admin.builder.fill_grad_end', 'End Color') }}</span>
                    <ColorInput v-model="gradColor2" @update:modelValue="updateGradient" />
                </div>
            </div>

            <!-- Image Selection Area -->
            <div v-else-if="type === 'image'" class="w-full flex flex-col gap-3">
                <div v-if="internalImage" class="relative group/img overflow-hidden rounded-xl aspect-video w-full">
                    <img :src="resolveMediaSrc(internalImage)" class="w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-110" />
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-[2px]">
                        <button @click="openMediaLibrary" class="btn btn-circle btn-sm btn-primary shadow-lg" :title="t('admin.builder.fill_image_change', 'Change Image')">
                            <PhImage weight="bold" class="w-3 h-3" />
                        </button>
                        <button @click="internalImage = ''; emitUpdate()" class="btn btn-circle btn-sm btn-error shadow-lg" :title="t('admin.builder.fill_image_remove', 'Remove Image')">
                            <PhTrash weight="bold" class="w-3 h-3" />
                        </button>
                    </div>
                </div>
                
                <button v-else @click="openMediaLibrary" class="btn btn-sm btn-outline border-dashed border-white/10 hover:border-primary/50 text-[10px] uppercase tracking-widest gap-2 w-full h-24 flex flex-col items-center justify-center bg-base-200/50">
                    <PhImage weight="bold" class="w-6 h-6 opacity-30" /> 
                    <span>{{ t('admin.builder.fill_image_browse', 'Browse Media') }}</span>
                </button>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import ColorInput from './ColorInput.vue';
import { useMediaPickerStore } from '@/Stores/useMediaPickerStore';
import { useTranslations } from '@/Composables/useTranslations';
import { PhProhibit, PhPaintBucket, PhSwatches, PhImage, PhTrash } from '@phosphor-icons/vue';

const { t } = useTranslations();

const props = defineProps({
    modelValue: {
        type: [Object, String],
        default: () => ({ type: 'none', color: 'rgba(0,0,0,0)', gradient: 'linear-gradient(0deg, rgba(0,0,0,1) 0%, rgba(255,255,255,1) 100%)', image: '' })
    },
    label: { type: String, default: 'Fill' }
});

const emit = defineEmits(['update:modelValue']);
const mediaPicker = useMediaPickerStore();

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

const resolveMediaSrc = (value) => {
    if (!value) return '';
    if (typeof value !== 'string') return '';
    if (/^https?:\/\//i.test(value)) return value;
    if (value.startsWith('/')) return `${window.location.origin}${value}`;
    return `${window.location.origin}/storage/${value.replace(/^\/+/, '')}`;
};

const openMediaLibrary = async () => {
    try {
        const file = await mediaPicker.open({ type: 'image' });
        if (file) {
            internalImage.value = file.url || resolveMediaSrc(file.path);
            emitUpdate();
        }
    } catch (e) {
        // Cancelled
    }
};
</script>

