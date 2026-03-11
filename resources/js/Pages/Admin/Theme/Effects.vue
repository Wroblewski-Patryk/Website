<script setup>
import { markRaw } from 'vue';
import ConfiguratorLayout from './ConfiguratorLayout.vue';
import { PhMagicWand, PhWarning, PhHouse, PhPaintRoller } from '@phosphor-icons/vue';

const breadcrumbs = [
    { label: 'Dashboard', url: route('dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Theme', url: route('dashboard.theme.index') },
    { label: 'Effects' }
];
</script>

<template>
    <ConfiguratorLayout 
        title="Effects" 
        description="Fine-tune visual effects like shadows and blurs."
        :breadcrumbs="breadcrumbs">
        <template #default="{ form }">

            <div v-if="form.globals.advanced" class="space-y-6">
                <!-- Visuals (Shadows & Blurs) -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 border-b border-base-200 pb-2">
                            <PhMagicWand weight="regular" class="w-6 h-6 text-accent inline-block align-text-bottom" /> Visual Effects
                        </h2>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Shadows -->
                            <div>
                                <h3 class="font-bold mb-4 opacity-70 uppercase tracking-wider text-sm">Shadows</h3>
                                <div class="space-y-2">
                                    <div v-for="shadow in ['xs', 'sm', 'md', 'lg', 'xl', '2xl', 'inner']" :key="shadow" class="form-control">
                                        <label class="label pb-0"><span class="label-text text-xs">shadow-{{ shadow }}</span></label>
                                        <input type="text" class="input input-sm input-bordered" v-model="form.globals.advanced[`shadow-${shadow}`]" />
                                    </div>
                                </div>
                            </div>

                            <!-- Blurs -->
                            <div>
                                <h3 class="font-bold mb-4 opacity-70 uppercase tracking-wider text-sm">Blurs</h3>
                                <div class="space-y-2">
                                    <div v-for="blur in ['sm', '', 'md', 'lg', 'xl', '2xl', '3xl']" :key="blur" class="form-control">
                                        <label class="label pb-0"><span class="label-text text-xs">blur-{{ blur || 'default' }}</span></label>
                                        <input type="text" class="input input-sm input-bordered" v-model="form.globals.advanced[`blur${blur ? '-' + blur : ''}`]" />
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
