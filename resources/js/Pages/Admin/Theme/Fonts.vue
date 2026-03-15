<script setup>
import { markRaw } from 'vue';
import ConfiguratorLayout from './ConfiguratorLayout.vue';
import FontSelect from '@/features/admin/theme/components/FontSelect.vue';
import { PhStack, PhTextT, PhHouse, PhPaintRoller } from '@phosphor-icons/vue';
import { useTranslations } from '@/Composables/useTranslations';

const { t } = useTranslations();

const breadcrumbs = [
    { label: t('admin.dashboard.title', 'Dashboard'), url: route('admin.dashboard.index'), icon: markRaw(PhHouse) },
    { label: t('admin.menu.theme', 'Theme'), url: route('admin.theme.index') },
    { label: t('admin.menu.fonts', 'Fonts') }
];

const popularFonts = [
    'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Oswald', 
    'Source Sans 3', 'Raleway', 'Ubuntu', 'Nunito', 'Playfair Display', 
    'Merriweather', 'PT Sans', 'Rubik', 'Noto Sans', 'Work Sans', 'Fira Sans',
    'Outfit', 'Space Grotesk', 'DM Sans', 'Syne', 'Plus Jakarta Sans', 'Manrope', 'Fira Code', 'Inconsolata'
].sort();

const allGoogleFonts = [
    'Abel', 'Abril Fatface', 'Acme', 'Advent Pro', 'Alegreya', 'Alfa Slab One', 
    'Alice', 'Almarai', 'Amatic SC', 'Antic Slab', 'Anton', 'Archivo', 'Archivo Black',
    'Arimo', 'Arvo', 'Assistant', 'Asap', 'Barlow', 'Barlow Condensed', 'Bebas Neue',
    'Bitter', 'Blinker', 'Bokor', 'Cabin', 'Cairo', 'Cardo', 'Caveat', 'Chakra Petch',
    'Changa', 'Chivo', 'Cinzel', 'Comfortaa', 'Concert One', 'Cormorant Garamond',
    'Courgette', 'Crimson Text', 'Cuprum', 'Dancing Script', 'Dosis', 'EB Garamond',
    'Exo', 'Exo 2', 'Fascinate', 'Fjalla One', 'Francois One', 'Frank Ruhl Libre',
    'Fredoka One', 'Garcia', 'Gelasio', 'Glegoo', 'Heebo', 'Hind', 'Hind Madurai',
    'Hind Siliguri', 'IBM Plex Mono', 'IBM Plex Sans', 'IBM Plex Serif',
    'Indie Flower', 'Jost', 'Kanit', 'Karla', 'Khand', 'Krona One', 'Lalezar',
    'Lexend', 'Lexend Deca', 'Libre Baskerville', 'Libre Franklin', 'Lobster', 
    'Lora', 'Macondo', 'Mada', 'Martel', 'Mate', 'Maven Pro', 'Merriweather Sans',
    'Monda', 'Mukta', 'Mulish', 'Nanum Gothic', 'Noticia Text', 'Noto Serif',
    'Nunito Sans', 'Overpass', 'Oxygen', 'Pacifico', 'Pathway Gothic One', 
    'Patrick Hand', 'Paytone One', 'Philosopher', 'Prompt', 'Public Sans', 'Quattrocento',
    'Quattrocento Sans', 'Questrial', 'Quicksand', 'Rajdhani', 'Rambla', 'Righteous',
    'Rokkitt', 'Ropa Sans', 'Rubik Mono One', 'Ruda', 'Saira', 'Saira Condensed',
    'Sanchez', 'Satisfy', 'Secular One', 'Shadows Into Light', 'Signika', 'Sintony',
    'Slabo 27px', 'Sora', 'Source Code Pro', 'Source Serif 4', 'Spectral', 'Teko',
    'Tinos', 'Titillium Web', 'Trade Winds', 'Varela Round', 'Vollkorn', 'Yanone Kaffeesatz',
    'Yantramanav', 'Zilla Slab'
].sort();

</script>

<template>
    <ConfiguratorLayout 
        :title="t('admin.theme.fonts_title', 'Fonts')" 
        :description="t('admin.theme.fonts_desc', 'Select Google Fonts to populate the 3 core Tailwind font stacks.')"
        :breadcrumbs="breadcrumbs">
        <template #default="{ form }">

            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-6 border-b border-base-200 pb-2">
                        <PhTextT weight="regular" class="w-6 h-6 text-accent inline-block align-text-bottom" /> 
                        {{ t('admin.theme.fonts_title', 'Google Fonts Configuration') }}
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-lg">Sans-Serif Font</span></label>
                            <FontSelect 
                                v-model="form.globals.fonts.sans" 
                                :popularFonts="popularFonts" 
                                :allFonts="allGoogleFonts" 
                            />
                            <label class="label"><span class="label-text-alt opacity-70">Tailwind's <code>font-sans</code> utility.</span></label>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-lg">Serif Font</span></label>
                            <FontSelect 
                                v-model="form.globals.fonts.serif" 
                                :popularFonts="popularFonts" 
                                :allFonts="allGoogleFonts" 
                            />
                            <label class="label"><span class="label-text-alt opacity-70">Tailwind's <code>font-serif</code> utility.</span></label>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold text-lg">Monospace Font</span></label>
                            <FontSelect 
                                v-model="form.globals.fonts.mono" 
                                :popularFonts="popularFonts" 
                                :allFonts="allGoogleFonts" 
                            />
                            <label class="label"><span class="label-text-alt opacity-70">Tailwind's <code>font-mono</code> utility.</span></label>
                        </div>
                    </div>

                    <div class="mt-8 p-8 bg-base-200 rounded-box border border-base-300 text-center space-y-6 shadow-inner">
                        <p class="text-sm font-bold tracking-widest uppercase opacity-50 mb-2">{{ t('admin.theme.live_preview', 'Live Font Preview') }}</p>
                        
                        <h1 class="text-3xl md:text-5xl font-bold" :style="{ fontFamily: form.globals.fonts.sans + ', sans-serif' }">
                            Sans-Serif: Clean & Modern
                        </h1>
                        
                        <h1 class="text-3xl md:text-5xl font-bold" :style="{ fontFamily: form.globals.fonts.serif + ', serif' }">
                            Serif: Elegant & Classic
                        </h1>

                        <p class="text-lg opacity-80 max-w-2xl mx-auto leading-relaxed" :style="{ fontFamily: form.globals.fonts.sans + ', sans-serif' }">
                            This is an example paragraph to show how the selected sans-serif font looks at a smaller size. Readability is key here.
                        </p>

                        <div class="mockup-code text-left max-w-2xl mx-auto mt-4" :style="{ fontFamily: form.globals.fonts.mono + ', monospace' }">
                            <pre data-prefix="$"><code>npm install tailwindcss @tailwindcss/vite</code></pre> 
                            <pre data-prefix=">"><code class="text-warning">Configuring fonts perfectly...</code></pre> 
                            <pre data-prefix=">" class="text-success"><code>Done!</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Core Font Stacks -->
            <div class="card bg-base-100 shadow-sm border border-base-200 mt-8">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4 border-b border-base-200 pb-2">
                        <PhStack weight="regular" class="w-6 h-6 text-info inline-block align-text-bottom" /> 
                        {{ t('admin.theme.fallback_stacks', 'Tailwind Fallback Stacks') }}
                    </h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-bold">--font-sans Fallbacks</span>
                                <span class="label-text-alt opacity-70">Added after your Google Font</span>
                            </label>
                            <textarea class="textarea textarea-bordered h-20" v-model="form.globals.advanced['font-sans']" placeholder="ui-sans-serif, system-ui..."></textarea>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-bold">--font-serif Fallbacks</span>
                                <span class="label-text-alt opacity-70">Added after your Google Font</span>
                            </label>
                            <textarea class="textarea textarea-bordered h-20" v-model="form.globals.advanced['font-serif']" placeholder="ui-serif, Georgia..."></textarea>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-bold">--font-mono Fallbacks</span>
                                <span class="label-text-alt opacity-70">Added after your Google Font</span>
                            </label>
                            <textarea class="textarea textarea-bordered h-20" v-model="form.globals.advanced['font-mono']" placeholder="ui-monospace, SFMono-Regular..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </template>
    </ConfiguratorLayout>
</template>
