<script setup>
import ConfiguratorLayout from './ConfiguratorLayout.vue';

const breadcrumbs = [
    { label: 'Admin', url: '/admin', icon: 'fas fa-home' },
    { label: 'Theme', url: '/admin/theme-configurator/colors' },
    { label: 'Fonts' }
];

const googleFonts = [
    'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Oswald', 
    'Source Sans 3', 'Raleway', 'Ubuntu', 'Nunito', 'Playfair Display', 
    'Merriweather', 'PT Sans', 'Rubik', 'Noto Sans', 'Work Sans', 'Fira Sans',
    'Outfit', 'Space Grotesk', 'DM Sans', 'Syne', 'Plus Jakarta Sans', 'Manrope'
].sort();

</script>

<template>
    <ConfiguratorLayout 
        title="Fonts" 
        description="Select Google Fonts for Headings and Body text."
        :breadcrumbs="breadcrumbs">
        <template #default="{ form }">

            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-6 border-b border-base-200 pb-2"><i class="fas fa-font text-accent"></i> Google Fonts Configuration</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-lg">Heading Font</span></label>
                            <select class="select select-bordered select-lg" v-model="form.globals.fonts.heading">
                                <option v-for="font in googleFonts" :key="font" :value="font" :style="{ fontFamily: font + ', sans-serif' }">{{ font }}</option>
                            </select>
                            <label class="label"><span class="label-text-alt opacity-70">Used for h1, h2, h3, h4, etc.</span></label>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-lg">Body Font</span></label>
                            <select class="select select-bordered select-lg" v-model="form.globals.fonts.body">
                                <option v-for="font in googleFonts" :key="font" :value="font" :style="{ fontFamily: font + ', sans-serif' }">{{ font }}</option>
                            </select>
                            <label class="label"><span class="label-text-alt opacity-70">Used for paragraphs and general text.</span></label>
                        </div>
                    </div>

                    <div class="mt-8 p-8 bg-base-200 rounded-box border border-base-300 text-center space-y-4 shadow-inner">
                        <p class="text-sm font-bold tracking-widest uppercase opacity-50 mb-4">Live Font Preview</p>
                        <h1 class="text-4xl md:text-5xl font-bold" :style="{ fontFamily: form.globals.fonts.heading + ', sans-serif' }">
                            Headings convey the main idea
                        </h1>
                        <p class="text-lg opacity-80 max-w-2xl mx-auto leading-relaxed" :style="{ fontFamily: form.globals.fonts.body + ', sans-serif' }">
                            This is how the body text will look with the selected font. Clean, legible, and highly readable for long paragraphs. The right font choice establishes the entire mood of your website.
                        </p>
                    </div>
                </div>
            </div>

        </template>
    </ConfiguratorLayout>
</template>
