<script setup>
import { markRaw } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModuleHeader from '@/Components/Admin/ModuleHeader.vue';
import { PhChartLineUp, PhFileText, PhArrowRight, PhFeather, PhHouse } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();
</script>

<template>

    <AdminLayout>
        <div class="space-y-6">
            <div class="bg-base-100 p-6 rounded-box shadow-sm border border-base-300">
                <ModuleHeader 
                    :title="t('admin.dashboard.title', 'Pulpit')" 
                    :description="t('admin.dashboard.welcome_desc', 'Zarządzaj swoją stroną, mediami i ustawieniami z jednego miejsca.')" 
                    :breadcrumbs="[
                        { label: t('admin.dashboard.title', 'Pulpit'), icon: markRaw(PhHouse) }
                    ]"
                    :icon="markRaw(PhChartLineUp)"
                />
            </div>

            <div>
                <div class="alert alert-info shadow-sm mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ t('admin.dashboard.welcome_title', 'Witaj w Panelu Administratora Featherly.') }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="card bg-base-100 shadow-sm border border-base-300 rounded-box overflow-hidden group hover:shadow-md transition-all">
                        <div class="card-body">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-primary/10 text-primary rounded-lg group-hover:bg-primary group-hover:text-primary-content transition-colors">
                                    <PhFileText weight="regular" class="w-6 h-6" />
                                </div>
                                <h2 class="card-title text-lg">{{ t('admin.dashboard.pages_card_title', 'Strony') }}</h2>
                            </div>
                            <p class="text-sm opacity-60">{{ t('admin.dashboard.pages_card_desc', 'Twórz i edytuj zawartość swojej witryny.') }}</p>
                            <div class="card-actions justify-end mt-4">
                                <Link :href="route('admin.pages.index')" class="btn btn-ghost btn-sm group-hover:btn-primary flex items-center gap-1">{{ t('admin.dashboard.go_to_pages', 'Przejdź do stron') }} <PhArrowRight weight="regular" class="w-4 h-4 ml-1" /></Link>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-sm border border-base-300 rounded-box overflow-hidden group hover:shadow-md transition-all">
                        <div class="card-body">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-secondary/10 text-secondary rounded-lg group-hover:bg-secondary group-hover:text-secondary-content transition-colors">
                                    <PhFeather weight="regular" class="w-6 h-6" />
                                </div>
                                <h2 class="card-title text-lg">{{ t('admin.menu.posts', 'Wpisy') }}</h2>
                            </div>
                            <p class="text-sm opacity-60">{{ t('admin.dashboard.posts_desc', 'Pisz i publikuj artykuły na blogu z pełnymi ustawieniami SEO.') }}</p>
                            <div class="card-actions justify-end mt-4">
                                <Link :href="route('admin.posts.index')" class="btn btn-ghost btn-sm group-hover:btn-secondary flex items-center gap-1">{{ t('admin.dashboard.manage_posts', 'Zarządzaj wpisami') }} <PhArrowRight weight="regular" class="w-4 h-4 ml-1" /></Link>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow-sm border border-base-300 rounded-box overflow-hidden group hover:shadow-md transition-all">
                        <div class="card-body">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="p-2 bg-accent/10 text-accent rounded-lg group-hover:bg-accent group-hover:text-accent-content transition-colors">
                                    <PhChartLineUp weight="regular" class="w-6 h-6" />
                                </div>
                                <h2 class="card-title text-lg">{{ t('admin.dashboard.media_card_title', 'Media') }}</h2>
                            </div>
                            <p class="text-sm opacity-60">{{ t('admin.dashboard.media_card_desc', 'Przesyłaj i organizuj zdjęcia oraz pliki.') }}</p>
                            <div class="card-actions justify-end mt-4">
                                <Link :href="route('admin.media.index')" class="btn btn-ghost btn-sm group-hover:btn-accent flex items-center gap-1">{{ t('admin.dashboard.go_to_media', 'Biblioteka mediów') }} <PhArrowRight weight="regular" class="w-4 h-4 ml-1" /></Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
