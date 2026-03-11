<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { PhChartLineUp, PhFileText, PhArrowRight, PhFeather } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <template #header>
            <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
                <PhChartLineUp weight="regular" class="w-8 h-8 text-primary" />
                {{ t('admin.dashboard.title', 'Dashboard') }}
            </h1>
        </template>

        <div>
            <div class="alert alert-info shadow-sm mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ t('admin.dashboard.welcome_alert', 'Welcome to Featherly Administrator Panel. You have successfully authenticated.') }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card bg-base-100 shadow-sm border border-base-300 rounded-box overflow-hidden group hover:shadow-md transition-all">
                    <div class="card-body">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-primary/10 text-primary rounded-lg group-hover:bg-primary group-hover:text-primary-content transition-colors">
                                <PhFileText weight="regular" class="w-6 h-6" />
                            </div>
                            <h2 class="card-title text-lg">{{ t('admin.dashboard.pages', 'Pages') }}</h2>
                        </div>
                        <p class="text-sm opacity-60">{{ t('admin.dashboard.pages_desc', 'Manage your website pages and utilize the Visual Block Builder.') }}</p>
                        <div class="card-actions justify-end mt-4">
                            <Link :href="route('admin.pages.index')" class="btn btn-ghost btn-sm group-hover:btn-primary flex items-center gap-1">{{ t('admin.dashboard.manage_pages', 'Manage Pages') }} <PhArrowRight weight="regular" class="w-4 h-4 ml-1" /></Link>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm border border-base-300 rounded-box overflow-hidden group hover:shadow-md transition-all">
                    <div class="card-body">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-secondary/10 text-secondary rounded-lg group-hover:bg-secondary group-hover:text-secondary-content transition-colors">
                                <PhFeather weight="regular" class="w-6 h-6" />
                            </div>
                            <h2 class="card-title text-lg">{{ t('admin.dashboard.posts', 'Blog Posts') }}</h2>
                        </div>
                        <p class="text-sm opacity-60">{{ t('admin.dashboard.posts_desc', 'Write and publish blog articles with full SEO settings.') }}</p>
                        <div class="card-actions justify-end mt-4">
                            <Link :href="route('admin.posts.index')" class="btn btn-ghost btn-sm group-hover:btn-secondary flex items-center gap-1">{{ t('admin.dashboard.manage_posts', 'Manage Posts') }} <PhArrowRight weight="regular" class="w-4 h-4 ml-1" /></Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
