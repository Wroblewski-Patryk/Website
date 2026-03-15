<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { PhCaretDown, PhMagnifyingGlass, PhCheck } from '@phosphor-icons/vue';

const props = defineProps({
    modelValue: String,
    popularFonts: {
        type: Array,
        default: () => []
    },
    allFonts: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref(null);
const searchInput = ref(null);

const filteredPopular = computed(() => {
    if (!searchQuery.value) return props.popularFonts;
    return props.popularFonts.filter(f => f.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const filteredAll = computed(() => {
    if (!searchQuery.value) return props.allFonts;
    return props.allFonts.filter(f => f.toLowerCase().includes(searchQuery.value.toLowerCase()) && !props.popularFonts.includes(f));
});

const selectFont = (font) => {
    emit('update:modelValue', font);
    isOpen.value = false;
    searchQuery.value = '';
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

const toggleDropdown = async () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        await nextTick();
        if (searchInput.value) {
            searchInput.value.focus();
        }
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <!-- Main Button -->
        <button 
            type="button"
            class="input input-bordered input-lg w-full flex items-center justify-between cursor-pointer"
            @click="toggleDropdown"
        >
            <span :style="{ fontFamily: modelValue + ', sans-serif' }">{{ modelValue || 'Select a font' }}</span>
            <PhCaretDown weight="bold" class="opacity-50 w-4 h-4 transition-transform duration-200" :class="{'rotate-180': isOpen}" />
        </button>

        <!-- Dropdown Menu -->
        <div v-if="isOpen" class="absolute z-50 mt-1 w-full bg-base-100 rounded-box shadow-xl border border-base-200 top-full left-0 overflow-hidden">
            <!-- Search Bar -->
            <div class="p-3 border-b border-base-200 bg-base-100 relative z-10">
                <label class="input input-sm input-bordered flex items-center gap-2 w-full">
                    <PhMagnifyingGlass weight="bold" class="opacity-50 w-4 h-4" />
                    <input 
                        type="text" 
                        class="grow" 
                        placeholder="Search fonts..."
                        v-model="searchQuery"
                        ref="searchInput"
                        @click.stop
                    />
                </label>
            </div>

            <!-- List -->
            <ul class="max-h-60 overflow-y-auto p-2">
                <template v-if="filteredPopular.length > 0">
                    <li class="px-4 py-2 text-xs opacity-50 font-bold uppercase tracking-wider">Popular Fonts</li>
                    <li v-for="font in filteredPopular" :key="'pop-'+font">
                        <button 
                            type="button"
                            class="w-full text-left px-4 py-2 mt-1 rounded-btn hover:bg-base-200 transition-colors flex items-center justify-between"
                            :class="{'bg-primary/10 text-primary font-bold': modelValue === font}"
                            @click.stop="selectFont(font)"
                        >
                            <span :style="{ fontFamily: font + ', sans-serif' }">{{ font }}</span>
                            <PhCheck weight="bold" v-if="modelValue === font" class="w-4 h-4" />
                        </button>
                    </li>
                </template>

                <template v-if="filteredAll.length > 0">
                    <div v-if="filteredPopular.length > 0" class="divider my-1"></div>
                    <li class="px-4 py-2 text-xs opacity-50 font-bold uppercase tracking-wider">All Fonts</li>
                    <li v-for="font in filteredAll" :key="'all-'+font">
                        <button 
                            type="button"
                            class="w-full text-left px-4 py-2 mt-1 rounded-btn hover:bg-base-200 transition-colors flex items-center justify-between"
                            :class="{'bg-primary/10 text-primary font-bold': modelValue === font}"
                            @click.stop="selectFont(font)"
                        >
                            <span :style="{ fontFamily: font + ', sans-serif' }">{{ font }}</span>
                            <PhCheck weight="bold" v-if="modelValue === font" class="w-4 h-4" />
                        </button>
                    </li>
                </template>
                
                <li v-if="filteredPopular.length === 0 && filteredAll.length === 0" class="p-4 text-center opacity-50 text-sm">
                    No fonts found matching "{{ searchQuery }}"
                </li>
            </ul>
        </div>
    </div>
</template>
