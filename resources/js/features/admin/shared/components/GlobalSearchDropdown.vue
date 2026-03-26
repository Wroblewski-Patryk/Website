<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { PhMagnifyingGlass } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const query = ref('');
const results = ref([]);
const isOpen = ref(false);
const isLoading = ref(false);
const hasError = ref(false);
const activeIndex = ref(-1);
let debounceId = null;

const typeBadgeClass = (type) => {
    if (type === 'page') return 'badge-info';
    if (type === 'post') return 'badge-success';
    if (type === 'project') return 'badge-warning';
    return 'badge-ghost';
};

const closeDropdown = () => {
    isOpen.value = false;
    activeIndex.value = -1;
};

const openDropdown = () => {
    if (!query.value.trim()) return;
    isOpen.value = true;
};

const search = async () => {
    const term = query.value.trim();
    hasError.value = false;

    if (term.length < 2) {
        results.value = [];
        closeDropdown();
        return;
    }

    isLoading.value = true;
    openDropdown();

    try {
        const searchUrl = `${route('admin.search.index')}?query=${encodeURIComponent(term)}&limit=8`;
        const response = await fetch(searchUrl, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`Search failed with status ${response.status}`);
        }

        const payload = await response.json();
        results.value = payload?.data?.results ?? payload?.results ?? [];
        activeIndex.value = results.value.length > 0 ? 0 : -1;
    } catch (error) {
        results.value = [];
        hasError.value = true;
    } finally {
        isLoading.value = false;
    }
};

const navigateToResult = (result) => {
    if (!result?.url) return;
    window.location.assign(result.url);
};

const onKeyDown = (event) => {
    if (!isOpen.value) {
        if (event.key === 'ArrowDown' && results.value.length > 0) {
            isOpen.value = true;
            activeIndex.value = 0;
            event.preventDefault();
        }
        return;
    }

    if (event.key === 'Escape') {
        closeDropdown();
        return;
    }

    if (event.key === 'ArrowDown') {
        if (results.value.length > 0) {
            activeIndex.value = (activeIndex.value + 1) % results.value.length;
        }
        event.preventDefault();
        return;
    }

    if (event.key === 'ArrowUp') {
        if (results.value.length > 0) {
            activeIndex.value = (activeIndex.value - 1 + results.value.length) % results.value.length;
        }
        event.preventDefault();
        return;
    }

    if (event.key === 'Enter') {
        if (activeIndex.value >= 0 && results.value[activeIndex.value]) {
            navigateToResult(results.value[activeIndex.value]);
            event.preventDefault();
        }
    }
};

const onClickOutside = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;
    if (target.closest('[data-global-search]')) return;
    closeDropdown();
};

watch(query, () => {
    if (debounceId) clearTimeout(debounceId);
    debounceId = setTimeout(() => {
        search();
    }, 250);
});

onMounted(() => {
    window.addEventListener('click', onClickOutside);
});

onBeforeUnmount(() => {
    window.removeEventListener('click', onClickOutside);
    if (debounceId) clearTimeout(debounceId);
});
</script>

<template>
    <div class="relative w-full max-w-xl" data-global-search>
        <label class="input input-bordered w-full flex items-center gap-2">
            <PhMagnifyingGlass class="w-4 h-4 opacity-60" />
            <input
                v-model="query"
                type="search"
                class="grow"
                :placeholder="t('admin.search.placeholder', 'Search pages, posts, projects...')"
                @focus="openDropdown"
                @keydown="onKeyDown"
            />
            <span v-if="isLoading" class="loading loading-spinner loading-xs"></span>
        </label>

        <div v-if="isOpen" class="absolute z-[60] mt-2 w-full rounded-box border border-base-300 bg-base-100 shadow-xl">
            <div v-if="hasError" class="p-3 text-sm text-error">
                {{ t('admin.search.error', 'Search is temporarily unavailable.') }}
            </div>
            <div v-else-if="isLoading" class="p-3 text-sm opacity-70">
                {{ t('admin.search.loading', 'Searching...') }}
            </div>
            <div v-else-if="results.length === 0" class="p-3 text-sm opacity-70">
                {{ t('admin.search.empty', 'No matching results.') }}
            </div>
            <ul v-else class="menu p-1">
                <li
                    v-for="(result, index) in results"
                    :key="`${result.type}-${result.id}`"
                >
                    <button
                        type="button"
                        class="flex items-center justify-between gap-3"
                        :class="{ 'active': index === activeIndex }"
                        @mouseenter="activeIndex = index"
                        @click="navigateToResult(result)"
                    >
                        <div class="min-w-0 text-left">
                            <div class="font-medium truncate">{{ result.title }}</div>
                            <div class="text-xs opacity-60 truncate">{{ result.subtitle }}</div>
                        </div>
                        <span class="badge badge-sm" :class="typeBadgeClass(result.type)">
                            {{ result.type }}
                        </span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>
