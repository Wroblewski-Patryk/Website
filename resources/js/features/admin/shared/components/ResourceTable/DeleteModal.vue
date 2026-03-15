<script setup>
import { PhWarning } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    show: Boolean,
    deleting: Boolean,
    title: {
        type: String,
        default: () => useTranslations().t('admin.common.are_you_sure', 'Are you sure?')
    },
    description: {
        type: String,
        default: () => useTranslations().t('admin.common.delete_warning', 'This action cannot be undone. All data associated with this record will be permanently removed.')
    }
});

const emit = defineEmits(['confirm', 'close']);
</script>

<template>
    <div v-if="show" class="modal modal-open z-[100]">
        <div class="modal-box rounded-3xl border border-white/10 shadow-2xl p-8 bg-base-100 max-w-sm text-center">
            <div class="w-16 h-16 bg-error/10 text-error rounded-full flex items-center justify-center mx-auto mb-6 text-2xl animate-bounce">
                <PhWarning class="w-8 h-8" />
            </div>
            <h3 class="font-black text-2xl mb-2 text-base-content">{{ title }}</h3>
            <p class="text-sm opacity-50 mb-8">{{ description }}</p>
            
            <div class="flex flex-col gap-2">
                <button @click="emit('confirm')" class="btn btn-error rounded-xl w-full shadow-lg shadow-error/20" :disabled="deleting">
                    <span v-if="deleting" class="loading loading-spinner loading-xs mr-2"></span>
                    {{ t('admin.common.confirm_delete', 'Yes, Delete Permanently') }}
                </button>
                <button @click="emit('close')" class="btn btn-ghost rounded-xl w-full" :disabled="deleting">{{ t('admin.common.cancel', 'Cancel') }}</button>
            </div>
        </div>
        <div class="modal-backdrop bg-black/60 backdrop-blur-sm" @click="emit('close')"></div>
    </div>
</template>
