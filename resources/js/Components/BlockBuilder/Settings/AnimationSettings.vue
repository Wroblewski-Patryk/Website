<template>
    <div class="space-y-6">
        <div class="form-control">
            <label class="label"><span class="label-text text-[10px] uppercase opacity-50">{{ t('admin.builder.animation_entrance', 'Entrance Animation') }}</span></label>
            <select v-model="settings.animation.type" class="select select-bordered select-sm w-full">
                <option :value="undefined">{{ t('admin.builder.animation_none', 'None') }}</option>
                <option value="fade">{{ t('admin.builder.animation_fade', 'Fade In') }}</option>
                <option value="slide-up">{{ t('admin.builder.animation_slide_up', 'Slide Up') }}</option>
                <option value="slide-down">{{ t('admin.builder.animation_slide_down', 'Slide Down') }}</option>
                <option value="slide-left">{{ t('admin.builder.animation_slide_left', 'Slide Left') }}</option>
                <option value="slide-right">{{ t('admin.builder.animation_slide_right', 'Slide Right') }}</option>
                <option value="zoom-in">{{ t('admin.builder.animation_zoom_in', 'Zoom In') }}</option>
                <option value="zoom-out">{{ t('admin.builder.animation_zoom_out', 'Zoom Out') }}</option>
                <option value="flip-up">{{ t('admin.builder.animation_flip_up', 'Flip Up') }}</option>
            </select>
        </div>

        <div v-if="settings.animation.type" class="p-4 bg-base-300/30 border border-white/5 rounded-2xl space-y-4 animate-in slide-in-from-top-2 duration-300">
            <div class="form-control">
                <label class="label flex justify-between">
                    <span class="label-text text-[10px] uppercase opacity-50">{{ t('admin.builder.animation_duration', 'Duration (ms)') }}</span>
                    <span class="text-[10px] font-mono opacity-50">{{ settings.animation.duration }}ms</span>
                </label>
                <input type="range" v-model="settings.animation.duration" min="100" max="3000" step="50" class="range range-xs range-primary" />
            </div>

            <div class="form-control">
                <label class="label flex justify-between">
                    <span class="label-text text-[10px] uppercase opacity-50">{{ t('admin.builder.animation_delay', 'Delay (ms)') }}</span>
                    <span class="text-[10px] font-mono opacity-50">{{ settings.animation.delay || 0 }}ms</span>
                </label>
                <input type="range" v-model="settings.animation.delay" min="0" max="2000" step="50" class="range range-xs" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text text-[10px] uppercase opacity-50">{{ t('admin.builder.animation_easing', 'Easing') }}</span></label>
                <select v-model="settings.animation.ease" class="select select-bordered select-xs w-full">
                    <option value="power1.out">{{ t('admin.builder.animation_ease_out', 'Ease Out') }}</option>
                    <option value="power2.out">{{ t('admin.builder.animation_strong_out', 'Strong Out') }}</option>
                    <option value="back.out(1.7)">{{ t('admin.builder.animation_elastic', 'Elastic') }}</option>
                    <option value="none">{{ t('admin.builder.animation_linear', 'Linear') }}</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-4">
                    <input type="checkbox" v-model="settings.animation.once" class="checkbox checkbox-primary checkbox-xs" />
                    <span class="text-[10px] uppercase font-bold opacity-50">{{ t('admin.builder.animation_once', 'Play once only') }}</span>
                </label>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();
const props = defineProps({
    settings: {
        type: Object,
        required: true
    }
});
</script>
