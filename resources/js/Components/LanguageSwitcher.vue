<template>
    <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-ghost btn-sm gap-2 lowercase">
            <i class="fas fa-globe"></i>
            {{ activeLocale }}
            <i class="fas fa-chevron-down text-[10px] opacity-50"></i>
        </label>
        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow-2xl bg-base-200 rounded-box w-32 border border-white/5 backdrop-blur-xl">
            <li>
                <button @click="switchLocale('pl')" :class="{ 'active': activeLocale === 'pl' }">Polski</button>
            </li>
            <li>
                <button @click="switchLocale('en')" :class="{ 'active': activeLocale === 'en' }">English</button>
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
