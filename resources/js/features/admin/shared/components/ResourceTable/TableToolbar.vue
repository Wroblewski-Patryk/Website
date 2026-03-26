<script setup>
import { Link } from '@inertiajs/vue3';
import { PhSlidersHorizontal, PhColumns, PhPlus } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    search: String,
    searchPlaceholder: String,
    toggleableColumns: {
        type: Array,
        default: () => []
    },
    visibleColumns: {
        type: Array,
        default: () => []
    },
    createRoute: String,
    createLabel: String,
    persistenceKey: String
});

const emit = defineEmits(['update:search', 'update:visibleColumns']);

function toggleColumn(key) {
    let newVisible = [...props.visibleColumns];
    if (newVisible.includes(key)) {
        newVisible = newVisible.filter(k => k !== key);
    } else {
        newVisible.push(key);
    }

    emit('update:visibleColumns', newVisible);

    if (props.persistenceKey) {
        localStorage.setItem(`resource-table-columns-${props.persistenceKey}`, JSON.stringify(newVisible));
    }
}
</script>

<template>
    <div class="flex items-center gap-3 w-full md:w-auto mt-4 md:mt-0 justify-end flex-wrap">
        <!-- Column Toggle -->
        <div v-if="toggleableColumns.length > 0" class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-square bg-base-100 border-base-200 shadow-sm hover:shadow-md hover:border-primary/40 text-base-content/60 hover:text-primary transition-all">
                <PhSlidersHorizontal class="text-lg" />
            </label>
            <div tabindex="0" class="dropdown-content z-[20] p-4 shadow-2xl bg-base-100 rounded-box w-64 mt-3 border border-base-200">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-base-200">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <PhColumns class="text-sm" />
                    </div>
                    <div>
                        <h3 class="font-bold text-sm leading-tight text-base-content">{{ t('admin.common.layout', 'Layout') }}</h3>
                        <p class="text-[10px] opacity-50 uppercase tracking-widest font-bold">{{ t('admin.common.toggle_columns', 'Toggle Columns') }}</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <label v-for="col in toggleableColumns" :key="col.key" class="label cursor-pointer hover:bg-base-200/50 px-3 py-2.5 rounded-xl transition-all border border-transparent hover:border-base-300 group/item">
                        <span class="label-text font-semibold group-hover/item:text-primary transition-colors">{{ col.label }}</span>
                        <input
                            type="checkbox"
                            :checked="visibleColumns.includes(col.key)"
                            @change="toggleColumn(col.key)"
                            class="toggle toggle-primary toggle-sm"
                        />
                    </label>
                </div>
            </div>
        </div>

        <slot name="actions"></slot>

        <Link v-if="createRoute" :href="createRoute" class="btn btn-primary shadow-lg shadow-primary/20 hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <PhPlus weight="bold" class="mr-1" /> {{ createLabel || t('admin.common.create', 'Create') }}
        </Link>
    </div>
</template>
