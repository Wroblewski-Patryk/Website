<script setup>
import { markRaw } from 'vue';
import ConfiguratorLayout from './ConfiguratorLayout.vue';
import { PhRuler, PhShapes, PhHouse, PhPaintRoller } from '@phosphor-icons/vue';

const breadcrumbs = [
    { label: 'Dashboard', url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: 'Theme', url: route('admin.theme.index') },
    { label: 'Sizes & Metrics' }
];
</script>

<template>
    <ConfiguratorLayout 
        title="Sizes & Metrics" 
        description="Configure border radius for UI elements and the maximum width for the website container."
        :breadcrumbs="breadcrumbs">
        <template #default="{ form }">
            
            <div class="space-y-6">
                <!-- Spacing -->
                <div v-if="form.globals.advanced" class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4 border-b border-base-200 pb-2">
                            <PhRuler weight="regular" class="w-6 h-6 text-primary inline-block align-text-bottom" /> Base Spacing Scale
                        </h2>
                        
                        <div class="form-control max-w-md">
                            <label class="label">
                                <span class="label-text font-bold">--spacing</span>
                                <span class="label-text-alt opacity-70">Multiplier for all padding/margin</span>
                            </label>
                            <input type="text" class="input input-bordered" v-model="form.globals.advanced['spacing']" placeholder="0.25rem" />
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-6 border-b border-base-200 pb-2"><PhShapes weight="regular" class="w-6 h-6 text-secondary inline-block align-text-bottom" /> Global Shapes & Layout</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="form-control">
                            <label class="label" title="Border radius for Cards, Modals, Images"><span class="label-text font-bold text-lg">Box Radius</span></label>
                            <input type="text" class="input input-bordered input-lg" v-model="form.globals.borderRadius.box" placeholder="e.g. 1rem" />
                            <label class="label"><span class="label-text-alt opacity-70">Applies to Cards, Modals, and large containers.</span></label>
                        </div>
                        
                        <div class="form-control">
                            <label class="label" title="Maximum width for containers and sections"><span class="label-text font-bold text-lg">Container Max Width</span></label>
                            <input type="text" class="input input-bordered input-lg" v-model="form.globals.layout.maxWidth" placeholder="e.g. 1280px" />
                            <label class="label"><span class="label-text-alt opacity-70">The maximum width for boxed sections (Columns, boxed blocks).</span></label>
                        </div>

                        <div class="form-control">
                            <label class="label" title="Border radius for Buttons"><span class="label-text font-bold text-lg">Button Radius</span></label>
                            <input type="text" class="input input-bordered input-lg" v-model="form.globals.borderRadius.btn" placeholder="e.g. 0.5rem" />
                            <label class="label"><span class="label-text-alt opacity-70">Applies to all standard buttons.</span></label>
                        </div>

                        <div class="form-control">
                            <label class="label" title="Border radius for Badges"><span class="label-text font-bold text-lg">Badge Radius</span></label>
                            <input type="text" class="input input-bordered input-lg" v-model="form.globals.borderRadius.badge" placeholder="e.g. 1.9rem" />
                            <label class="label"><span class="label-text-alt opacity-70">Applies to pills and small badges.</span></label>
                        </div>
                    </div>
                    
                    <div class="mt-8 p-10 bg-base-200 rounded-box border border-base-300 shadow-inner flex flex-col items-center gap-6">
                        <p class="text-sm font-bold tracking-widest uppercase opacity-50 mb-2">Live Radius Preview</p>
                        
                        <div class="flex flex-wrap gap-8 justify-center items-center">
                            <div class="bg-base-100 p-6 shadow-md border border-base-300 w-40 h-40 flex flex-col items-center justify-center text-center leading-tight font-semibold" 
                                 :style="{ borderRadius: form.globals.borderRadius.box }">
                                Box<br/><span class="text-xs font-normal opacity-50 mt-1">Cards & Modals</span>
                            </div>
                            
                            <div class="flex flex-col gap-6 items-center">
                                <button class="btn btn-primary btn-lg shadow-md" :style="{ borderRadius: form.globals.borderRadius.btn }">
                                    Primary Button
                                </button>
                                
                                <div class="badge badge-secondary badge-lg shadow-sm px-4 py-4" :style="{ borderRadius: form.globals.borderRadius.badge }">
                                    Notification Badge
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </div>

        </template>
    </ConfiguratorLayout>
</template>
