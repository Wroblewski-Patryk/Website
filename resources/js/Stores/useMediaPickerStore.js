import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMediaPickerStore = defineStore('mediaPicker', () => {
    const isOpen = ref(false);
    const resolveCallback = ref(null);
    const rejectCallback = ref(null);
    const options = ref({
        type: 'image', // image, video, document, etc. (later integration)
        multiple: false,
    });

    const open = (opts = {}) => {
        if (rejectCallback.value) {
            rejectCallback.value('replaced');
            rejectCallback.value = null;
            resolveCallback.value = null;
        }

        options.value = { ...options.value, ...opts };
        isOpen.value = true;

        return new Promise((resolve, reject) => {
            resolveCallback.value = resolve;
            rejectCallback.value = reject;
        });
    };

    const close = () => {
        isOpen.value = false;
        if (rejectCallback.value) {
            rejectCallback.value('cancelled');
            rejectCallback.value = null;
        }
        resolveCallback.value = null;
    };

    const select = (file) => {
        if (resolveCallback.value) {
            resolveCallback.value(file);
            resolveCallback.value = null;
            rejectCallback.value = null;
        }
        isOpen.value = false;
    };

    return {
        isOpen,
        options,
        open,
        close,
        select
    };
});
