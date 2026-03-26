<template>
    <div class="space-y-2">
        <div class="join w-full flex-wrap rounded-lg border border-base-content/10 bg-base-200/40 p-1">
            <button type="button" class="btn btn-xs join-item" @click="exec('bold')" title="Bold">
                <strong>B</strong>
            </button>
            <button type="button" class="btn btn-xs join-item italic" @click="exec('italic')" title="Italic">
                I
            </button>
            <button type="button" class="btn btn-xs join-item underline" @click="exec('underline')" title="Underline">
                U
            </button>
            <button type="button" class="btn btn-xs join-item" @click="exec('insertUnorderedList')" title="Bullet List">
                • List
            </button>
            <button type="button" class="btn btn-xs join-item" @click="exec('insertOrderedList')" title="Numbered List">
                1. List
            </button>
            <button type="button" class="btn btn-xs join-item" @click="insertLink" title="Insert Link">
                Link
            </button>
            <button type="button" class="btn btn-xs join-item" @click="exec('removeFormat')" title="Clear Formatting">
                Clear
            </button>
        </div>

        <div
            ref="editorRef"
            class="min-h-48 w-full rounded-xl border border-base-content/10 bg-base-100 px-3 py-2 text-sm leading-relaxed outline-none focus:border-primary"
            contenteditable="true"
            :data-placeholder="placeholder"
            @input="emitContent"
            @blur="emitContent"
        ></div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Type here...',
    },
});

const emit = defineEmits(['update:modelValue']);
const editorRef = ref(null);

const normalize = (value) => String(value || '');

const emitContent = () => {
    if (!editorRef.value) return;
    emit('update:modelValue', editorRef.value.innerHTML);
};

const exec = (command) => {
    if (!editorRef.value) return;
    editorRef.value.focus();
    document.execCommand(command, false, null);
    emitContent();
};

const insertLink = () => {
    if (!editorRef.value) return;

    const url = window.prompt('Enter URL (https://...)');
    if (!url) return;

    editorRef.value.focus();
    document.execCommand('createLink', false, url);
    emitContent();
};

onMounted(() => {
    if (!editorRef.value) return;
    editorRef.value.innerHTML = normalize(props.modelValue);
});

watch(
    () => props.modelValue,
    (newValue) => {
        if (!editorRef.value) return;
        const normalized = normalize(newValue);
        if (editorRef.value.innerHTML !== normalized) {
            editorRef.value.innerHTML = normalized;
        }
    }
);
</script>

<style scoped>
[contenteditable='true']:empty::before {
    content: attr(data-placeholder);
    color: color-mix(in srgb, currentColor 35%, transparent);
}
</style>

