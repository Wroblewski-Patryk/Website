<script setup>
import { computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';
import { useToastStore } from '@/Stores/useToastStore';

const props = defineProps({
    animation_preset: Object,
});

const { t } = useTranslations();
const toast = useToastStore();

const presetOptions = [
    { value: 'fade-up', label: 'Fade Up' },
    { value: 'fade-in', label: 'Fade In' },
    { value: 'slide-left', label: 'Slide Left' },
    { value: 'slide-right', label: 'Slide Right' },
    { value: 'zoom-in', label: 'Zoom In' },
    { value: 'clip-reveal', label: 'Clip Reveal' },
    { value: 'reveal-text', label: 'Reveal Text' },
];

const form = useForm({
    name: props.animation_preset?.name || '',
    slug: props.animation_preset?.slug || '',
    is_active: props.animation_preset?.is_active ?? true,
    definition: {
        trigger: props.animation_preset?.definition?.trigger || 'onEnter',
        preset: props.animation_preset?.definition?.preset || 'fade-up',
        duration: props.animation_preset?.definition?.duration ?? 0.8,
        delay: props.animation_preset?.definition?.delay ?? 0,
        ease: props.animation_preset?.definition?.ease || 'power2.out',
        once: props.animation_preset?.definition?.once ?? true,
        timeline_id: props.animation_preset?.definition?.timeline_id || '',
        tween: {
            from: props.animation_preset?.definition?.tween?.from || {},
            to: props.animation_preset?.definition?.tween?.to || {},
        },
    },
});

const pageTitle = computed(() => (
    props.animation_preset?.id
        ? t('admin.builder.animation_preset_edit', 'Edit Animation Preset')
        : t('admin.builder.animation_preset_create', 'Create Animation Preset')
));

const generateSlug = (text) => String(text || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-+|-+$/g, '');

watch(() => form.name, (value) => {
    if (!props.animation_preset?.id || !form.slug) {
        form.slug = generateSlug(value);
    }
});

const save = () => {
    if (props.animation_preset?.id) {
        form.put(route('admin.animation-presets.update', props.animation_preset.id), {
            preserveScroll: true,
            onSuccess: () => toast.success(t('admin.common.updated', 'Updated successfully.')),
            onError: () => toast.error(t('admin.common.update_failed', 'Update failed.')),
        });
        return;
    }

    form.post(route('admin.animation-presets.store'), {
        preserveScroll: true,
        onSuccess: () => toast.success(t('admin.common.created', 'Created successfully.')),
        onError: () => toast.error(t('admin.common.create_failed', 'Create failed.')),
    });
};
</script>

<template>
    <Head :title="pageTitle" />
    <AdminLayout>
        <div class="mx-auto w-full max-w-4xl space-y-6">
            <div class="rounded-2xl border border-base-content/10 bg-base-100 p-6">
                <h1 class="text-lg font-bold">{{ pageTitle }}</h1>
                <p class="mt-1 text-xs opacity-60">
                    {{ t('admin.builder.animation_presets_desc', 'Reusable GSAP animation definitions for block settings.') }}
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-base-content/10 bg-base-100 p-6 space-y-4">
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ t('admin.common.name', 'Name') }}</span></label>
                        <input v-model="form.name" type="text" class="input input-bordered input-sm w-full" />
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">{{ t('admin.common.url_slug', 'Slug') }}</span></label>
                        <div class="join w-full">
                            <input v-model="form.slug" type="text" class="input input-bordered input-sm join-item w-full font-mono text-xs" />
                            <button type="button" class="btn btn-sm btn-ghost join-item" @click="form.slug = generateSlug(form.name)">
                                {{ t('admin.common.regenerate', 'Regenerate') }}
                            </button>
                        </div>
                    </div>

                    <label class="label cursor-pointer justify-between rounded-xl border border-base-content/10 bg-base-200/30 px-3 py-2">
                        <span class="label-text text-xs font-bold opacity-70">{{ t('admin.common.active', 'Active') }}</span>
                        <input v-model="form.is_active" type="checkbox" class="toggle toggle-primary toggle-sm" />
                    </label>
                </div>

                <div class="rounded-2xl border border-base-content/10 bg-base-100 p-6 space-y-4">
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">Preset</span></label>
                        <select v-model="form.definition.preset" class="select select-bordered select-sm w-full">
                            <option v-for="option in presetOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">Trigger</span></label>
                        <select v-model="form.definition.trigger" class="select select-bordered select-sm w-full">
                            <option value="onEnter">On Enter</option>
                            <option value="onLoad">On Load</option>
                            <option value="onScroll">On Scroll</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text">Duration (s)</span></label>
                            <input v-model.number="form.definition.duration" type="number" min="0" max="30" step="0.1" class="input input-bordered input-sm w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text">Delay (s)</span></label>
                            <input v-model.number="form.definition.delay" type="number" min="0" max="30" step="0.1" class="input input-bordered input-sm w-full" />
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">Ease</span></label>
                        <input v-model="form.definition.ease" type="text" class="input input-bordered input-sm w-full" placeholder="power2.out" />
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text">Timeline ID (optional)</span></label>
                        <input v-model="form.definition.timeline_id" type="text" class="input input-bordered input-sm w-full" placeholder="main-sequence" />
                    </div>

                    <label class="label cursor-pointer justify-between rounded-xl border border-base-content/10 bg-base-200/30 px-3 py-2">
                        <span class="label-text text-xs font-bold opacity-70">Play once</span>
                        <input v-model="form.definition.once" type="checkbox" class="toggle toggle-primary toggle-sm" />
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link :href="route('admin.animation-presets.index')" class="btn btn-ghost">
                    {{ t('admin.common.cancel', 'Cancel') }}
                </Link>
                <button class="btn btn-primary" :disabled="form.processing" @click="save">
                    {{ t('admin.common.save', 'Save') }}
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
