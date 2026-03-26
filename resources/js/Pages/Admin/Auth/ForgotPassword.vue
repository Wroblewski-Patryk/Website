<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';
import FormFieldError from '@/features/admin/shared/components/forms/FormFieldError.vue';
import FormStatusAlert from '@/features/admin/shared/components/forms/FormStatusAlert.vue';

const { t } = useTranslations();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('auth.password.email'));
};
</script>

<template>
    <Head :title="t('admin.forgot_password.page_title', 'Forgot Password')" />

    <div class="min-h-screen bg-base-200 flex items-center justify-center px-4">
        <div class="card w-full max-w-md bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="card-title text-2xl font-bold">
                    {{ t('admin.forgot_password.title', 'Reset your password') }}
                </h1>
                <p class="text-sm opacity-70">
                    {{ t('admin.forgot_password.description', 'Enter your admin email and we will send you a password reset link.') }}
                </p>

                <FormStatusAlert :message="$page.props.flash?.status" variant="success" />

                <form class="mt-2 space-y-4" @submit.prevent="submit">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ t('admin.login.email', 'Email Address') }}</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="input input-bordered w-full"
                            placeholder="admin@example.com"
                            required
                            autofocus
                        />
                        <FormFieldError :message="form.errors.email" />
                    </div>

                    <button class="btn btn-primary w-full" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                        {{ t('admin.forgot_password.send_link', 'Send reset link') }}
                    </button>
                </form>

                <div class="text-center mt-2">
                    <Link :href="route('auth.login')" class="link link-hover text-sm">
                        {{ t('admin.forgot_password.back_to_login', 'Back to login') }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
