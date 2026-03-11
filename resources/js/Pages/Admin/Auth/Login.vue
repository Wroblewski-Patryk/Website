<script setup>
import { useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('auth.login'));
};
</script>

<template>
    <Head title="Admin Login" />
    
    <div class="min-h-screen bg-base-200 flex items-center justify-center">
        <div class="card w-96 bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl font-bold justify-center mb-4">Featherly Login</h2>
                
                <form @submit.prevent="submit">
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input 
                            type="email" 
                            v-model="form.email" 
                            placeholder="admin@example.com" 
                            class="input input-bordered w-full" 
                            required 
                            autofocus 
                        />
                        <label class="label" v-if="form.errors.email">
                            <span class="label-text-alt text-error">{{ form.errors.email }}</span>
                        </label>
                    </div>
                    
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input 
                            type="password" 
                            v-model="form.password" 
                            placeholder="••••••••" 
                            class="input input-bordered w-full" 
                            required 
                        />
                    </div>
                    
                    <div class="form-control mb-6">
                        <label class="cursor-pointer label justify-start gap-4">
                            <input type="checkbox" v-model="form.remember" class="checkbox checkbox-primary" />
                            <span class="label-text">Remember me</span>
                        </label>
                    </div>

                    <div class="form-control mt-6">
                        <button class="btn btn-primary w-full" :disabled="form.processing">
                            <span v-if="form.processing" class="loading loading-spinner"></span>
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
