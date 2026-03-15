<template>
    <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-ghost btn-sm gap-2 uppercase">
            <i class="ph ph-globe text-base"></i>
            {{ activeLocale }}
            <i class="ph ph-caret-down text-[10px] opacity-50"></i>
        </label>
        <ul tabindex="0" class="dropdown-content z-[50] menu p-2 shadow-2xl bg-base-200 rounded-box w-40 border border-white/5 backdrop-blur-xl">
            <li v-for="lang in page.props.languages" :key="lang.code">
                <button @click="switchLocale(lang.code)" 
                        :class="{ 'bg-primary/10 text-primary font-bold': activeLocale === lang.code }"
                        class="flex justify-between items-center px-3 py-2">
                    <span>{{ lang.name }}</span>
                    <span class="text-[10px] opacity-30 font-mono tracking-tighter">{{ lang.code.toUpperCase() }}</span>
                </button>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();
const activeLocale = computed(() => page.props.locale || 'pl');

const switchLocale = (lang) => {
    router.get(route('locale.switch', lang), {}, {
        preserveScroll: true,
        onSuccess: () => {
            window.location.reload();
        }
    });
};
</script>
