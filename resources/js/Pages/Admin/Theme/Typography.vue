<script setup>
import { markRaw } from 'vue';
import ConfiguratorLayout from './ConfiguratorLayout.vue';
import UnitInput from '../../../Components/UnitInput.vue';
import { PhTextAa, PhWarning, PhHouse, PhPaintRoller } from '@phosphor-icons/vue';

const breadcrumbs = [
    { label: 'Dashboard', url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Theme', url: route('admin.theme.index') },
    { label: 'Typography' }
];

const weightOptions = {
    '100': '100 - Thin',
    '200': '200 - Extra Light',
    '300': '300 - Light',
    '400': '400 - Normal',
    '500': '500 - Medium',
    '600': '600 - Semi Bold',
    '700': '700 - Bold',
    '800': '800 - Extra Bold',
    '900': '900 - Black'
};
</script>

<template>
    <ConfiguratorLayout 
        title="Typography" 
        description="Fine-tune typography weights, line heights, and letter spacing."
        :breadcrumbs="breadcrumbs">
        <template #default="{ form }">

            <div v-if="form.globals.advanced" class="space-y-6">
                <!-- Typography -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 border-b border-base-200 pb-2">
                            <PhTextAa weight="regular" class="w-6 h-6 text-secondary inline-block align-text-bottom" /> Typography Metrics
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                            <!-- Font Sizes -->
                            <div>
                                <h3 class="font-bold mb-4 opacity-70 uppercase tracking-wider text-sm">Font Sizes</h3>
                                <div class="space-y-4">
                                    <div v-for="size in ['xs', 'sm', 'base', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl']" :key="size" class="form-control">
                                        <label class="label pb-1"><span class="label-text text-xs font-bold">text-{{ size }}</span></label>
                                        <UnitInput v-model="form.globals.advanced[`text-${size}`]" placeholder="e.g. 1rem" />
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Font Weights -->
                            <div>
                                <h3 class="font-bold mb-4 opacity-70 uppercase tracking-wider text-sm">Font Weights</h3>
                                <div class="space-y-4">
                                    <div v-for="weight in ['thin', 'extralight', 'light', 'normal', 'medium', 'semibold', 'bold', 'extrabold', 'black']" :key="weight" class="form-control">
                                        <label class="label pb-1"><span class="label-text text-xs font-bold">font-{{ weight }}</span></label>
                                        <select class="select select-sm select-bordered w-full bg-base-100" v-model="form.globals.advanced[`font-weight-${weight}`]">
                                            <option v-for="(label, val) in weightOptions" :key="val" :value="val">{{ label }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Line Heights -->
                            <div>
                                <h3 class="font-bold mb-4 opacity-70 uppercase tracking-wider text-sm">Line Heights</h3>
                                <div class="space-y-4">
                                    <div v-for="lh in ['none', 'tight', 'snug', 'normal', 'relaxed', 'loose']" :key="lh" class="form-control">
                                        <label class="label pb-1 flex justify-between">
                                            <span class="label-text text-xs font-bold">leading-{{ lh }}</span>
                                            <span class="text-xs opacity-50">{{ form.globals.advanced[`leading-${lh}`] }}</span>
                                        </label>
                                        <div class="flex gap-2 items-center">
                                            <input type="range" min="0.5" max="2.5" step="0.05" class="range range-xs range-primary flex-1" v-model="form.globals.advanced[`leading-${lh}`]" />
                                            <input type="text" class="input input-sm input-bordered w-16 text-center" v-model="form.globals.advanced[`leading-${lh}`]" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tracking -->
                            <div>
                                <h3 class="font-bold mb-4 opacity-70 uppercase tracking-wider text-sm">Letter Spacing</h3>
                                <div class="space-y-4">
                                    <div v-for="track in ['tighter', 'tight', 'normal', 'wide', 'wider', 'widest']" :key="track" class="form-control">
                                        <label class="label pb-1"><span class="label-text text-xs font-bold">tracking-{{ track }}</span></label>
                                        <UnitInput v-model="form.globals.advanced[`tracking-${track}`]" placeholder="e.g. 0.05em" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else class="alert alert-warning shadow-sm">
                <PhWarning weight="fill" class="w-6 h-6 text-warning" />
                <div>
                    <h3 class="font-bold">Missing Config</h3>
                    <div class="text-xs">The configuration data was not found. Please re-save the theme settings to generate it.</div>
                </div>
            </div>

        </template>
    </ConfiguratorLayout>
</template>
