<template>
    <div class="flex flex-col gap-6">
        <!-- Categories -->
        <div v-if="categories.length > 0" class="space-y-3">
            <div class="flex items-center gap-2 px-1">
                <PhSquaresFour weight="bold" class="w-3 h-3 text-primary" />
                <span class="text-[10px] font-bold uppercase tracking-widest opacity-40">{{ t('admin.taxonomy.type_category', 'Categories') }}</span>
            </div>
            <div class="flex flex-col gap-1.5">
                <label 
                    v-for="cat in categories" 
                    :key="cat.id"
                    class="flex items-center justify-between p-2 rounded-lg border border-base-content/5 hover:border-primary/30 hover:bg-base-200/50 cursor-pointer transition-all group"
                    :class="{'border-primary bg-primary/5 shadow-sm': isSelected(cat.id)}"
                >
                    <div class="flex items-center gap-3">
                        <div 
                            class="w-2 h-2 rounded-full shadow-sm"
                            :style="{ backgroundColor: cat.color || 'var(--p)' }"
                        ></div>
                        <span class="text-xs font-medium" :class="{'text-primary': isSelected(cat.id)}">
                            {{ cat.title[activeLocale] || cat.title['en'] }}
                        </span>
                    </div>
                    <input 
                        type="checkbox" 
                        :value="cat.id" 
                        :checked="isSelected(cat.id)"
                        @change="toggle(cat.id)"
                        class="checkbox checkbox-primary checkbox-xs rounded-md" 
                    />
                </label>
            </div>
        </div>

        <!-- Tags -->
        <div v-if="tags.length > 0" class="space-y-3">
            <div class="flex items-center gap-2 px-1">
                <PhTag weight="bold" class="w-3 h-3 text-secondary" />
                <span class="text-[10px] font-bold uppercase tracking-widest opacity-40">{{ t('admin.taxonomy.type_tag', 'Tags') }}</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="tag in tags"
                    :key="tag.id"
                    type="button"
                    @click="toggle(tag.id)"
                    class="btn btn-xs rounded-full normal-case text-[10px] font-semibold border-base-content/10 transition-all hover:scale-105 active:scale-95"
                    :class="isSelected(tag.id) ? 'btn-secondary text-secondary-content' : 'btn-ghost bg-base-200/50 hover:bg-base-200'"
                >
                    {{ tag.title[activeLocale] || tag.title['en'] }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { PhSquaresFour, PhTag } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    availableTaxonomies: {
        type: Array,
        default: () => []
    },
    activeLocale: {
        type: String,
        default: 'en'
    }
});

const emit = defineEmits(['update:modelValue']);

const categories = computed(() => props.availableTaxonomies.filter(t => t.type === 'category'));
const tags = computed(() => props.availableTaxonomies.filter(t => t.type === 'tag'));

const isSelected = (id) => props.modelValue.includes(id);

const toggle = (id) => {
    const newValue = [...props.modelValue];
    const index = newValue.indexOf(id);
    if (index === -1) {
        newValue.push(id);
    } else {
        newValue.splice(index, 1);
    }
    emit('update:modelValue', newValue);
};
</script>
