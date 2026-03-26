<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';
import FormFieldError from '@/features/admin/shared/components/forms/FormFieldError.vue';

const { t } = useTranslations();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('auth.login.post'));
};
</script>

<template>
    <Head :title="t('admin.login.page_title', 'Admin Login')" />

    <div class="min-h-screen bg-base-200 flex items-center justify-center">
        <div class="card w-96 bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl font-bold justify-center mb-4">Featherly Login</h2>

                <form @submit.prevent="submit">
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">{{ t('admin.login.email', 'Email Address') }}</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="admin@example.com"
                            class="input input-bordered w-full"
                            required
                            autofocus
                        />
                        <FormFieldError :message="form.errors.email" />
                    </div>

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text">{{ t('admin.login.password', 'Password') }}</span>
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="********"
                            class="input input-bordered w-full"
                            required
                        />
                        <FormFieldError :message="form.errors.password" />
                    </div>

                    <div class="form-control mb-6">
                        <label class="cursor-pointer label justify-start gap-4">
                            <input type="checkbox" v-model="form.remember" class="checkbox checkbox-primary" />
                            <span class="label-text">{{ t('admin.login.remember_me', 'Remember me') }}</span>
                        </label>
                    </div>

                    <div class="text-right mb-4">
                        <a :href="route('auth.password.request')" class="link link-hover text-sm">
                            {{ t('admin.login.forgot_password', 'Forgot password?') }}
                        </a>
                    </div>

                    <div class="form-control mt-6">
                        <button class="btn btn-primary w-full" :disabled="form.processing">
                            <span v-if="form.processing" class="loading loading-spinner"></span>
                            {{ t('admin.login.submit', 'Login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
