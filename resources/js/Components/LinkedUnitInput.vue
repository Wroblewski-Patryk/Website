<template>
    <div class="space-y-4 w-full">
        <!-- Header -->
        <div class="flex items-center justify-between mb-3">
            <h4 v-if="label" class="text-[10px] uppercase font-bold tracking-widest opacity-30">{{ label }}</h4>
            <div v-else></div>

            <div class="flex items-center gap-2">
                <!-- Link Toggles with DaisyUI Join -->
                <div class="join bg-base-300 rounded-md p-0.5">
                    <button @click="toggleLinkAll" class="btn btn-xs join-item min-h-0 h-6 w-8 border-none" :class="linkAll ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" title="Link All">
                        <i class="fas fa-link"></i>
                    </button>
                    <button @click="toggleLinkY" class="btn btn-xs join-item min-h-0 h-6 w-8 border-none" :class="linkY ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" title="Link Top & Bottom">
                        <i class="fas fa-arrows-alt-v"></i>
                    </button>
                    <button @click="toggleLinkX" class="btn btn-xs join-item min-h-0 h-6 w-8 border-none" :class="linkX ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" title="Link Left & Right">
                        <i class="fas fa-arrows-alt-h"></i>
                    </button>
                </div>
                
                <!-- Global Unit -->
                <select v-model="globalUnit" class="select select-bordered select-xs w-20 px-1 h-6 min-h-0 text-[10px] uppercase bg-base-300 border-none opacity-50 hover:opacity-100 transition-opacity text-center text-center-last flex justify-center appearance-none">
                    <option value="px">PX</option>
                    <option value="%">%</option>
                    <option value="em">EM</option>
                    <option value="rem">REM</option>
                    <option value="vh">VH</option>
                    <option value="vw">VW</option>
                </select>
            </div>
        </div>

        <!-- Spatial Layout (Box Model Style) -->
        <div class="relative bg-base-200/50 rounded-2xl p-4 border border-white/5 flex flex-col items-center justify-center gap-1 group overflow-hidden">
            
            <!-- Top Input -->
            <div class="w-20 z-10 px-1">
                <input type="number" step="any" :value="internal.top" @input="updateTop($event.target.value)" class="input input-xs input-bordered w-full text-center bg-base-100/90 shadow-sm hover:border-primary focus:border-primary transition-colors focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" placeholder="-" title="Top" />
                <div class="text-[8px] uppercase font-bold tracking-widest text-center opacity-30 mt-1 cursor-default select-none">Top</div>
            </div>

            <!-- Middle Row: Left - Center - Right -->
            <div class="flex items-center w-full justify-between z-10 -my-1">
                <!-- Left Input -->
                <!-- If LinkAll OR LinkX is ON, Left takes value from Right (or Top if LinkAll) and is disabled -->
                <div class="w-20 px-1">
                    <div class="text-[8px] uppercase font-bold tracking-widest text-center opacity-30 mb-1 cursor-default select-none" :class="{'opacity-10': linkAll || linkX}">Left</div>
                    <input type="number" step="any" :value="internal.left" @input="updateLeft($event.target.value)" :disabled="linkAll || linkX" :class="{'opacity-50 cursor-not-allowed': linkAll || linkX}" class="input input-xs input-bordered w-full text-center bg-base-100/90 shadow-sm hover:border-primary focus:border-primary transition-colors focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" placeholder="-" title="Left" />
                </div>

                <!-- Center visual indicator -->
                <div class="opacity-10 text-xl transition-all duration-300 group-hover:text-primary group-hover:opacity-40 select-none pointer-events-none">
                    <i class="fas fa-vector-square"></i>
                </div>

                <!-- Right Input -->
                <!-- If LinkAll is ON, Right takes value from Top and is disabled -->
                <div class="w-20 px-1">
                    <div class="text-[8px] uppercase font-bold tracking-widest text-center opacity-30 mb-1 cursor-default select-none" :class="{'opacity-10': linkAll}">Right</div>
                    <input type="number" step="any" :value="internal.right" @input="updateRight($event.target.value)" :disabled="linkAll" :class="{'opacity-50 cursor-not-allowed': linkAll}" class="input input-xs input-bordered w-full text-center bg-base-100/90 shadow-sm hover:border-primary focus:border-primary transition-colors focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" placeholder="-" title="Right" />
                </div>
            </div>

            <!-- Bottom Input -->
            <!-- If LinkAll OR LinkY is ON, Bottom takes value from Top and is disabled -->
            <div class="w-20 z-10 px-1">
                <div class="text-[8px] uppercase font-bold tracking-widest text-center opacity-30 mb-1 cursor-default select-none" :class="{'opacity-10': linkAll || linkY}">Bottom</div>
                <input type="number" step="any" :value="internal.bottom" @input="updateBottom($event.target.value)" :disabled="linkAll || linkY" :class="{'opacity-50 cursor-not-allowed': linkAll || linkY}" class="input input-xs input-bordered w-full text-center bg-base-100/90 shadow-sm hover:border-primary focus:border-primary transition-colors focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" placeholder="-" title="Bottom" />
            </div>
            
        </div>
    </div>
</template>

<script setup>
import { reactive, watch, ref } from 'vue';

const props = defineProps({
    top: { type: [String, Number], default: '' },
    right: { type: [String, Number], default: '' },
    bottom: { type: [String, Number], default: '' },
    left: { type: [String, Number], default: '' },
    label: { type: String, default: '' },
});

const emit = defineEmits(['update:top', 'update:right', 'update:bottom', 'update:left']);

// Toggles logic
const linkAll = ref(true); 
const linkX = ref(false);
const linkY = ref(false);

const globalUnit = ref('px');

const internal = reactive({
    top: '',
    right: '',
    bottom: '',
    left: ''
});

// Watch incoming value to sync local state
watch([() => props.top, () => props.right, () => props.bottom, () => props.left], ([t, r, b, l]) => {
    // Parse raw values like '10px' or '-5rem' into unit ('px') and number ('10')
    const parseValue = (raw) => {
        if (!raw && raw !== 0) return '';
        const match = String(raw).match(/^(-?\d*\.?\d+)(.*)$/);
        return match ? parseFloat(match[1]) : '';
    };

    const rawTop = t || '';
    const rawRight = r || '';
    const rawBottom = b || '';
    const rawLeft = l || '';

    // Try to guess global unit from existing values
    const units = [rawTop, rawRight, rawBottom, rawLeft]
        .map(v => v ? String(v).replace(/[\d\.\-]/g, '') : null)
        .filter(v => v && v.length > 0);
    
    if (units.length > 0) {
        globalUnit.value = units[0] || 'px';
    }

    internal.top = parseValue(rawTop);
    internal.right = parseValue(rawRight);
    internal.bottom = parseValue(rawBottom);
    internal.left = parseValue(rawLeft);
    
    // Auto-detect link state originally
    if (internal.top !== '' && internal.top === internal.bottom && internal.top === internal.left && internal.top === internal.right) {
        linkAll.value = true;
        linkX.value = false;
        linkY.value = false;
    } else {
        linkAll.value = false;
        if (internal.left === internal.right && (internal.left !== '' || internal.right !== '')) {
            linkX.value = true;
        }
        if (internal.top === internal.bottom && (internal.top !== '' || internal.bottom !== '')) {
            linkY.value = true;
        }
    }

}, { immediate: true, deep: true });

const emitChanges = () => {
    const formatValue = (num) => {
        if (num === '' || num === null || num === undefined) return undefined;
        if (num === '0' || num === 0) return '0'; // Allow naked zero
        return `${num}${globalUnit.value}`;
    };

    emit('update:top', formatValue(internal.top));
    emit('update:right', formatValue(internal.right));
    emit('update:bottom', formatValue(internal.bottom));
    emit('update:left', formatValue(internal.left));
};

// Re-emit immediately if unit changes so all suffixes update
watch(globalUnit, () => {
    emitChanges();
});

// Toggles Mutators
const toggleLinkAll = () => {
    linkAll.value = !linkAll.value;
    if (linkAll.value) {
        linkX.value = false;
        linkY.value = false;
        if (internal.top !== '') {
            internal.right = internal.top;
            internal.bottom = internal.top;
            internal.left = internal.top;
            emitChanges();
        }
    }
};

const toggleLinkX = () => {
    linkX.value = !linkX.value;
    if (linkX.value) {
        linkAll.value = false;
        if (internal.right !== '') {
            internal.left = internal.right;
            emitChanges();
        }
    }
};

const toggleLinkY = () => {
    linkY.value = !linkY.value;
    if (linkY.value) {
        linkAll.value = false;
        if (internal.top !== '') {
            internal.bottom = internal.top;
            emitChanges();
        }
    }
};

// Inputs Mutators
const updateTop = (val) => {
    internal.top = val;
    if (linkAll.value) {
        internal.right = val;
        internal.bottom = val;
        internal.left = val;
    } else if (linkY.value) {
        internal.bottom = val;
    }
    emitChanges();
};

const updateRight = (val) => {
    internal.right = val;
    if (!linkAll.value && linkX.value) {
        internal.left = val;
    }
    emitChanges();
};

const updateBottom = (val) => {
    internal.bottom = val;
    emitChanges();
};

const updateLeft = (val) => {
    internal.left = val;
    emitChanges();
};
</script>
