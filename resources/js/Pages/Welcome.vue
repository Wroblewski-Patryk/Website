<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome" />
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="p-8 bg-white shadow-lg rounded-lg">
            <h1 class="text-3xl font-bold text-gray-800">Welcome to Your New Portfolio</h1>
            <p class="mt-4 text-gray-600">Laravel 12 + Vue 3 + Inertia.js + Tailwind + Filament</p>
            <div class="mt-6">
                <a href="/admin" class="text-blue-500 hover:underline">Go to Admin Panel</a>
            </div>
        </div>
    </div>
</template>
