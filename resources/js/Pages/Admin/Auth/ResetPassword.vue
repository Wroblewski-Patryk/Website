<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';
import FormFieldError from '@/features/admin/shared/components/forms/FormFieldError.vue';

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
    email: {
        type: String,
        default: '',
    },
});

const { t } = useTranslations();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('auth.password.update'));
};
</script>

<template>
    <Head :title="t('admin.reset_password.page_title', 'Set New Password')" />

    <div class="min-h-screen bg-base-200 flex items-center justify-center px-4">
        <div class="card w-full max-w-md bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="card-title text-2xl font-bold">
                    {{ t('admin.reset_password.title', 'Choose a new password') }}
                </h1>

                <form class="mt-2 space-y-4" @submit.prevent="submit">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ t('admin.login.email', 'Email Address') }}</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="input input-bordered w-full"
                            required
                            autofocus
                        />
                        <FormFieldError :message="form.errors.email" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ t('admin.login.password', 'Password') }}</span>
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="input input-bordered w-full"
                            required
                            autocomplete="new-password"
                        />
                        <FormFieldError :message="form.errors.password" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ t('admin.users.confirm_password', 'Confirm New Password') }}</span>
                        </label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="input input-bordered w-full"
                            required
                            autocomplete="new-password"
                        />
                        <FormFieldError :message="form.errors.password_confirmation" />
                    </div>

                    <button class="btn btn-primary w-full" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                        {{ t('admin.reset_password.submit', 'Reset password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
