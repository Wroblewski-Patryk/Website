<script setup>
import ConfiguratorLayout from './ConfiguratorLayout.vue';

const breadcrumbs = [
    { label: 'Admin', url: '/admin', icon: 'fas fa-home' },
    { label: 'Theme', url: '/admin/theme-configurator/colors' },
    { label: 'Block Defaults' }
];

</script>

<template>
    <ConfiguratorLayout 
        title="Block Defaults" 
        description="Set up default styling (like margins and paddings) that apply whenever a blank block is dropped onto the page."
        :breadcrumbs="breadcrumbs">
        <template #default="{ form }">

            <div class="alert bg-base-100 border border-base-200 shadow-sm mb-8">
                <i class="fas fa-info-circle text-info text-2xl"></i>
                <div>
                    <h3 class="font-bold text-lg">Priority System</h3>
                    <div class="text-base mt-1">These settings act as base fallbacks. Local settings chosen visually within the Block Editor on a specific page will <strong>always override</strong> these global defaults.</div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8">
                
                <!-- Section Defaults -->
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-xl border-b border-base-200 pb-2"><i class="fas fa-vector-square opacity-70 text-primary w-8"></i> Section Block</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Default Top Padding</span></label>
                                <input type="text" class="input input-bordered" v-model="(form.block_defaults['section'] = form.block_defaults['section'] || {}).paddingTop" placeholder="e.g. 4rem or 64px" />
                                <label class="label"><span class="label-text-alt opacity-70">Recommended: 4rem or more for breathing room.</span></label>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Default Bottom Padding</span></label>
                                <input type="text" class="input input-bordered" v-model="form.block_defaults['section'].paddingBottom" placeholder="e.g. 4rem or 64px" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Heading Defaults -->
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-xl border-b border-base-200 pb-2"><i class="fas fa-heading opacity-70 text-accent w-8"></i> Heading Block</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Default Bottom Margin</span></label>
                                <input type="text" class="input input-bordered" v-model="(form.block_defaults['heading'] = form.block_defaults['heading'] || {}).marginBottom" placeholder="e.g. 1.5rem" />
                                <label class="label"><span class="label-text-alt opacity-70">Space between heading and the block below it.</span></label>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Default Font Weight</span></label>
                                <select class="select select-bordered" v-model="form.block_defaults['heading'].fontWeight">
                                    <option value="">Theme Default</option>
                                    <option value="300">Light (300)</option>
                                    <option value="400">Normal (400)</option>
                                    <option value="500">Medium (500)</option>
                                    <option value="600">Semi Bold (600)</option>
                                    <option value="700">Bold (700)</option>
                                    <option value="800">Extra Bold (800)</option>
                                    <option value="900">Black (900)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paragraph Defaults -->
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-xl border-b border-base-200 pb-2"><i class="fas fa-paragraph opacity-70 text-secondary w-8"></i> Paragraph Block</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Default Bottom Margin</span></label>
                                <input type="text" class="input input-bordered" v-model="(form.block_defaults['paragraph'] = form.block_defaults['paragraph'] || {}).marginBottom" placeholder="e.g. 1rem" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Default Font Size</span></label>
                                <input type="text" class="input input-bordered" v-model="form.block_defaults['paragraph'].fontSize" placeholder="e.g. 1.125rem" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-semibold">Default Text Opacity</span></label>
                                <input type="text" class="input input-bordered" v-model="form.block_defaults['paragraph'].opacity" placeholder="e.g. 0.8" />
                                <label class="label"><span class="label-text-alt opacity-70">1 is solid, 0.8 is slightly transparent.</span></label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </template>
    </ConfiguratorLayout>
</template>
