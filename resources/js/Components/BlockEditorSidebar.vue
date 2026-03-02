<script setup>
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { computed, ref, watch } from 'vue';
import UnitInput from '@/Components/UnitInput.vue';
import { ColorPicker } from 'vue3-colorpicker';
import 'vue3-colorpicker/style.css';

const props = defineProps(['menus']);

const store = useBlockBuilderStore();
const activeSidebarTab = ref('content');

const getBlockStyleColor = (prop) => computed({
    get: () => {
        if (!store.activeBlock || !store.activeBlock.settings.style) return 'rgba(0,0,0,0)';
        return store.activeBlock.settings.style[prop] || 'rgba(0,0,0,0)';
    },
    set: (val) => {
        if (store.activeBlock && store.activeBlock.settings.style) {
            store.activeBlock.settings.style[prop] = val === 'rgba(0,0,0,0)' ? '' : val;
        }
    }
});

const bgColor = getBlockStyleColor('backgroundColor');
const textColor = getBlockStyleColor('textColor');
const borderColor = getBlockStyleColor('borderColor');

watch(() => store.activeBlock, (newBlock) => {
    if (newBlock) {
        if (!newBlock.settings) newBlock.settings = {};
        if (!newBlock.settings.style) newBlock.settings.style = {};
        if (!newBlock.settings.animations) {
            newBlock.settings.animations = {
                enabled: false,
                trigger: 'onEnter',
                preset: 'fade-up',
                duration: 0.8,
                delay: 0,
                ease: 'power2.out',
                bindToTimeline: false,
                once: true,
            };
        }
        if (!newBlock.settings.layout) {
            newBlock.settings.layout = {
                fullHeight: false,
                fixedBg: false,
                padding: 'py-20',
                zIndex: 1
            };
        }
    }
}, { immediate: true, deep: true });

const addProject = () => {
    store.activeBlock.content.projects.push({
        title: 'New Project',
        date: new Date().getFullYear().toString(),
        description: '',
        desktop_image: '',
        mobile_image: '',
        url: ''
    });
};

const removeProject = (idx) => {
    store.activeBlock.content.projects.splice(idx, 1);
};
</script>

<template>
    <div v-if="store.activeBlock" class="h-full flex flex-col bg-base-100 border-l border-white/5 animate-in slide-in-from-right-4 fade-in duration-300">
        <!-- Sidebar Header -->
        <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between sticky top-0 bg-base-100/80 backdrop-blur-xl z-20">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                    <i v-if="store.activeBlock.type === 'heading'" class="fas fa-heading"></i>
                    <i v-else-if="store.activeBlock.type === 'portfolio'" class="fas fa-briefcase"></i>
                    <i v-else-if="store.activeBlock.type === 'custom_code'" class="fas fa-code"></i>
                    <i v-else-if="store.activeBlock.type === 'section'" class="fas fa-layer-group"></i>
                    <i v-else class="fas fa-cube"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold capitalize">{{ store.activeBlock.type.replace('_', ' ') }}</h3>
                    <p class="text-[10px] opacity-40 uppercase tracking-widest leading-none">Settings</p>
                </div>
            </div>
            <button @click="store.activeBlockId = null" class="btn btn-ghost btn-xs btn-circle">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Sidebar Tabs -->
        <div class="flex border-b border-white/5 bg-base-200/30">
            <button v-for="tab in ['content', 'style', 'animations', 'advanced']" :key="tab"
                    @click="activeSidebarTab = tab"
                    class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-all relative"
                    :class="activeSidebarTab === tab ? 'text-primary' : 'opacity-40 hover:opacity-100'">
                {{ tab }}
                <div v-if="activeSidebarTab === tab" class="absolute bottom-0 left-4 right-4 h-0.5 bg-primary rounded-full"></div>
            </button>
        </div>

        <!-- Sidebar Content Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">
            
            <!-- CONTENT TAB -->
            <div v-if="activeSidebarTab === 'content'" class="space-y-6">
                <!-- Paragraph / Text -->
                <div v-if="['paragraph', 'text'].includes(store.activeBlock.type)" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Content</span></label>
                        <textarea v-model="store.activeBlock.content.text" class="textarea textarea-bordered h-32 w-full"></textarea>
                    </div>
                </div>

                <!-- Heading -->
                <div v-if="store.activeBlock.type === 'heading'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Text</span></label>
                        <input type="text" v-model="store.activeBlock.content.text" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Level</span></label>
                        <div class="join w-full">
                            <button v-for="h in [1,2,3,4,5,6]" :key="h" 
                                    @click="store.activeBlock.content.level = h"
                                    class="btn btn-xs join-item flex-1"
                                    :class="store.activeBlock.content.level === h ? 'btn-primary' : ''">H{{h}}</button>
                        </div>
                    </div>
                </div>

                <!-- List -->
                <div v-if="store.activeBlock.type === 'list'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Items</span></label>
                        <div v-for="(item, i) in store.activeBlock.content.items" :key="i" class="flex gap-2 mb-2">
                            <input type="text" v-model="store.activeBlock.content.items[i]" class="input input-sm input-bordered flex-1" />
                            <button @click="store.activeBlock.content.items.splice(i, 1)" class="btn btn-square btn-xs btn-ghost text-error"><i class="fas fa-times"></i></button>
                        </div>
                        <button @click="store.activeBlock.content.items.push('New item')" class="btn btn-sm btn-outline btn-block mt-2">Add Item</button>
                    </div>
                </div>

                <!-- Quote -->
                <div v-if="store.activeBlock.type === 'quote'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Quote Text</span></label>
                        <textarea v-model="store.activeBlock.content.text" class="textarea textarea-bordered h-24 w-full"></textarea>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Author</span></label>
                        <input type="text" v-model="store.activeBlock.content.author" class="input input-bordered w-full" />
                    </div>
                </div>

                <!-- Spacer / Divider -->
                <div v-if="['spacer', 'divider'].includes(store.activeBlock.type)" class="space-y-4">
                    <div v-if="store.activeBlock.type === 'spacer'" class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Height</span></label>
                        <select v-model="store.activeBlock.content.height" class="select select-bordered w-full">
                            <option value="py-5">X-Small</option>
                            <option value="py-10">Small</option>
                            <option value="py-20">Medium</option>
                            <option value="py-40">Large</option>
                            <option value="py-60">X-Large</option>
                        </select>
                    </div>
                    <div v-if="store.activeBlock.type === 'divider'" class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Style</span></label>
                        <select v-model="store.activeBlock.content.style" class="select select-bordered w-full">
                            <option value="solid">Solid</option>
                            <option value="dashed">Dashed</option>
                            <option value="dotted">Dotted</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div v-if="store.activeBlock.type === 'table'" class="space-y-4">
                    <label class="text-[10px] opacity-40 uppercase font-black">Table Rows</label>
                    <div v-for="(row, rIdx) in store.activeBlock.content.rows" :key="rIdx" class="grid grid-cols-2 gap-2">
                        <input v-for="(cell, cIdx) in row" :key="cIdx" type="text" v-model="store.activeBlock.content.rows[rIdx][cIdx]" class="input input-xs input-bordered" />
                        <button @click="store.activeBlock.content.rows.splice(rIdx, 1)" class="btn btn-xs btn-error btn-ghost col-span-2">Remove Row</button>
                    </div>
                    <button @click="store.activeBlock.content.rows.push(['New Cell', 'New Cell'])" class="btn btn-xs btn-outline btn-block">Add Row</button>
                </div>

                <!-- Media Blocks -->
                <div v-if="['image', 'gallery', 'video', 'audio', 'file', 'media_text', 'carousel'].includes(store.activeBlock.type)" class="space-y-4">
                    <div v-if="store.activeBlock.type === 'image'" class="space-y-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-xs opacity-50">Image URL</span></label>
                            <input type="text" v-model="store.activeBlock.content.url" class="input input-bordered w-full font-mono text-xs" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-xs opacity-50">Alt Text</span></label>
                            <input type="text" v-model="store.activeBlock.content.alt" class="input input-bordered w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-xs opacity-50">Caption</span></label>
                            <input type="text" v-model="store.activeBlock.content.caption" class="input input-bordered w-full" />
                        </div>
                    </div>

                    <div v-if="store.activeBlock.type === 'gallery' || store.activeBlock.type === 'carousel'" class="space-y-3">
                         <label class="text-[10px] opacity-40 uppercase font-black">Images</label>
                         <div v-for="(img, idx) in store.activeBlock.content.images || store.activeBlock.content.items" :key="idx" class="flex gap-2">
                             <input type="text" v-model="(store.activeBlock.content.images || store.activeBlock.content.items)[idx]" class="input input-xs input-bordered flex-1" />
                             <button @click="(store.activeBlock.content.images || store.activeBlock.content.items).splice(idx, 1)" class="btn btn-xs btn-error btn-ghost"><i class="fas fa-times"></i></button>
                         </div>
                         <button @click="(store.activeBlock.content.images || store.activeBlock.content.items).push('')" class="btn btn-xs btn-outline btn-block">Add Image</button>
                    </div>

                    <div v-if="store.activeBlock.type === 'video'" class="space-y-3">
                        <select v-model="store.activeBlock.content.source" class="select select-bordered select-sm w-full">
                            <option value="youtube">YouTube</option>
                            <option value="vimeo">Vimeo</option>
                            <option value="self">Self-Hosted</option>
                        </select>
                        <input type="text" v-model="store.activeBlock.content.url" placeholder="Video URL" class="input input-sm input-bordered w-full" />
                    </div>

                    <div v-if="store.activeBlock.type === 'media_text'" class="space-y-3">
                        <input type="text" v-model="store.activeBlock.content.media_url" placeholder="Media URL" class="input input-sm input-bordered w-full" />
                        <textarea v-model="store.activeBlock.content.text" placeholder="Text Content" class="textarea textarea-sm textarea-bordered w-full"></textarea>
                        <select v-model="store.activeBlock.content.media_position" class="select select-bordered select-sm w-full">
                            <option value="left">Media Left</option>
                            <option value="right">Media Right</option>
                        </select>
                    </div>
                </div>

                <!-- Marketing Blocks -->
                <div v-if="['hero', 'cta_box', 'card', 'testimonial', 'faq', 'pricing', 'counter'].includes(store.activeBlock.type)" class="space-y-4">
                    <div v-if="store.activeBlock.type === 'hero'" class="space-y-3">
                        <input type="text" v-model="store.activeBlock.content.headline" placeholder="Headline" class="input input-sm input-bordered w-full font-bold" />
                        <textarea v-model="store.activeBlock.content.subheadline" placeholder="Sub-headline" class="textarea textarea-sm textarea-bordered w-full"></textarea>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" v-model="store.activeBlock.content.primaryLabel" placeholder="Primary BTN" class="input input-xs input-bordered w-full" />
                            <input type="text" v-model="store.activeBlock.content.secondaryLabel" placeholder="Secondary BTN" class="input input-xs input-bordered w-full" />
                        </div>
                    </div>

                    <div v-if="store.activeBlock.type === 'cta_box'" class="space-y-3">
                         <input type="text" v-model="store.activeBlock.content.title" placeholder="CTA Title" class="input input-sm input-bordered w-full font-bold" />
                         <input type="text" v-model="store.activeBlock.content.button_label" placeholder="Button Label" class="input input-sm input-bordered w-full" />
                    </div>

                    <div v-if="store.activeBlock.type === 'testimonial'" class="space-y-3">
                        <textarea v-model="store.activeBlock.content.text" placeholder="Review Text" class="textarea textarea-sm textarea-bordered w-full"></textarea>
                        <input type="text" v-model="store.activeBlock.content.author" placeholder="Author Name" class="input input-sm input-bordered w-full" />
                        <input type="text" v-model="store.activeBlock.content.company" placeholder="Company" class="input input-sm input-bordered w-full" />
                    </div>

                    <div v-if="store.activeBlock.type === 'faq'" class="space-y-3">
                        <div v-for="(item, idx) in store.activeBlock.content.items" :key="idx" class="p-2 bg-base-200 rounded-lg">
                            <input type="text" v-model="item.q" placeholder="Question" class="input input-xs input-bordered w-full mb-1" />
                            <textarea v-model="item.a" placeholder="Answer" class="textarea textarea-xs textarea-bordered w-full"></textarea>
                        </div>
                        <button @click="store.activeBlock.content.items.push({q:'', a:''})" class="btn btn-xs btn-outline btn-block">Add FAQ</button>
                    </div>

                    <div v-else-if="store.activeBlock.type === 'pricing'" class="space-y-4">
                <div v-for="(plan, i) in store.activeBlock.content.plans" :key="i" class="p-4 bg-base-200 rounded-2xl border border-white/5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-widest opacity-40">Plan {{ i + 1 }}</span>
                        <button @click="store.activeBlock.content.plans.splice(i, 1)" class="btn btn-ghost btn-xs text-error"><i class="fas fa-times"></i></button>
                    </div>
                    <input type="text" v-model="plan.name" class="input input-bordered input-sm w-full" placeholder="Plan Name" />
                    <input type="text" v-model="plan.price" class="input input-bordered input-sm w-full" placeholder="Price (e.g. $29/mo)" />
                    <input type="text" v-model="plan.button" class="input input-bordered input-sm w-full" placeholder="Button Text" />
                    <div class="space-y-1">
                        <label class="label"><span class="label-text text-[10px] opacity-40 uppercase">Features</span></label>
                        <div v-for="(feat, f) in plan.features" :key="f" class="flex gap-1">
                            <input type="text" v-model="plan.features[f]" class="input input-bordered input-xs w-full" />
                            <button @click="plan.features.splice(f, 1)" class="btn btn-ghost btn-xs text-error"><i class="fas fa-minus"></i></button>
                        </div>
                        <button @click="plan.features.push('New Feature')" class="btn btn-ghost btn-xs btn-block border-dashed border-white/10 mt-1">+ Add Feature</button>
                    </div>
                </div>
                <button @click="store.activeBlock.content.plans.push({ name: 'Pro', price: '$49/mo', button: 'Get Started', features: ['All Basic features', 'Priority Support'] })" class="btn btn-outline btn-sm btn-block border-dashed border-white/20">+ Add Plan</button>
            </div>

            <div v-else-if="store.activeBlock.type === 'counter'" class="space-y-3">
                        <input type="number" v-model="store.activeBlock.content.number" class="input input-sm input-bordered w-full" />
                        <input type="text" v-model="store.activeBlock.content.suffix" placeholder="Suffix (+, %)" class="input input-sm input-bordered w-full" />
                        <input type="text" v-model="store.activeBlock.content.label" placeholder="Label" class="input input-sm input-bordered w-full" />
                    </div>
                </div>

                <!-- Theme / Site Blocks -->
                <div v-if="['site_logo', 'navigation', 'breadcrumbs', 'posts_list', 'google_maps'].includes(store.activeBlock.type)" class="space-y-4">
                    <div v-if="store.activeBlock.type === 'navigation'" class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Select Menu</span></label>
                        <select v-model="store.activeBlock.content.menu_id" class="select select-bordered w-full">
                            <option :value="null">None</option>
                            <option v-for="menu in menus" :key="menu.id" :value="menu.id">{{ menu.name }}</option>
                        </select>
                    </div>
                    <div v-if="store.activeBlock.type === 'posts_list'" class="space-y-3">
                        <input type="number" v-model="store.activeBlock.content.count" class="input input-sm input-bordered w-full" />
                        <select v-model="store.activeBlock.content.layout" class="select select-bordered select-sm w-full">
                            <option value="grid">Grid</option>
                            <option value="list">List</option>
                        </select>
                    </div>
                    <div v-if="store.activeBlock.type === 'google_maps'" class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Address</span></label>
                        <input type="text" v-model="store.activeBlock.content.address" class="input input-bordered w-full" />
                    </div>
                </div>

                <!-- Portfolio -->
                <div v-if="store.activeBlock.type === 'portfolio'" class="space-y-6">
                    <div class="form-control p-4 bg-primary/5 rounded-2xl border border-primary/10">
                        <label class="label cursor-pointer justify-start gap-4">
                            <input type="checkbox" v-model="store.activeBlock.content.use_projects_module" class="checkbox checkbox-primary" />
                            <div>
                                <span class="text-sm font-bold block">Dynamic Projects</span>
                                <span class="text-[10px] opacity-50">Fetch all projects from Projects module</span>
                            </div>
                        </label>
                    </div>

                    <div v-if="!store.activeBlock.content.use_projects_module" class="space-y-6">
                        <div v-for="(project, idx) in store.activeBlock.content.projects" :key="idx" class="p-4 bg-base-200 rounded-2xl relative group/item">
                            <button @click="removeProject(idx)" class="absolute -right-2 -top-2 btn btn-circle btn-xs btn-error opacity-0 group-hover/item:opacity-100 transition-opacity">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="space-y-3">
                                <input type="text" v-model="project.title" placeholder="Project Title" class="input input-sm input-bordered w-full font-bold" />
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" v-model="project.date" placeholder="Date" class="input input-sm input-bordered w-full text-xs" />
                                    <input type="text" v-model="project.url" placeholder="Live URL" class="input input-sm input-bordered w-full text-xs" />
                                </div>
                                <textarea v-model="project.description" placeholder="Description" class="textarea textarea-sm textarea-bordered w-full" rows="2"></textarea>
                                <div class="space-y-2">
                                    <label class="text-[10px] opacity-40 uppercase font-black">Images</label>
                                    <input type="text" v-model="project.desktop_image" placeholder="Desktop Image URL" class="input input-xs input-bordered w-full font-mono" />
                                    <input type="text" v-model="project.mobile_image" placeholder="Mobile Image URL" class="input input-xs input-bordered w-full font-mono" />
                                </div>
                            </div>
                        </div>
                        <button @click="addProject" class="btn btn-outline btn-primary btn-sm btn-block rounded-xl border-dashed">
                            <i class="fas fa-plus mr-2"></i> Add Project
                        </button>
                    </div>
                    <div v-else class="p-8 text-center opacity-40 italic text-xs">
                        <i class="fas fa-magic mb-2 text-xl block"></i>
                        Showing all projects from module.
                    </div>
                </div>

                <!-- Custom Code -->
                <div v-if="store.activeBlock.type === 'custom_code'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50 font-mono">HTML</span></label>
                        <textarea v-model="store.activeBlock.content.html" class="textarea textarea-bordered h-48 w-full font-mono text-xs"></textarea>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50 font-mono">JS (Executed on mount)</span></label>
                        <textarea v-model="store.activeBlock.content.js" class="textarea textarea-bordered h-32 w-full font-mono text-xs"></textarea>
                    </div>
                </div>

                <!-- Section -->
                <div v-if="store.activeBlock.type === 'section'" class="space-y-4">
                     <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-4 p-4 bg-base-200 rounded-2xl">
                            <input type="checkbox" v-model="store.activeBlock.settings.layout.fullHeight" class="checkbox checkbox-primary" />
                            <span class="text-sm font-bold">Full Screen Height (100vh)</span>
                        </label>
                    </div>
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-4 p-4 bg-base-200 rounded-2xl">
                            <input type="checkbox" v-model="store.activeBlock.settings.layout.fixedBg" class="checkbox checkbox-primary" />
                            <span class="text-sm font-bold">Fixed Background (Parallax)</span>
                        </label>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Padding Preset</span></label>
                        <select v-model="store.activeBlock.settings.layout.padding" class="select select-bordered w-full">
                            <option value="py-0">No Padding</option>
                            <option value="py-10">Small</option>
                            <option value="py-20">Medium</option>
                            <option value="py-40">Large</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ANIMATIONS TAB -->
            <div v-if="activeSidebarTab === 'animations'" class="space-y-6 animate-in fade-in">
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-between bg-base-200/50 p-3 rounded-xl border border-white/5">
                        <span class="label-text font-bold text-xs uppercase tracking-widest opacity-70">Enable Animations</span>
                        <input type="checkbox" v-model="store.activeBlock.settings.animations.enabled" class="toggle toggle-primary toggle-sm" />
                    </label>
                </div>

                <div v-if="store.activeBlock.settings.animations.enabled" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] opacity-40 uppercase font-bold">Trigger Event</span></label>
                        <select v-model="store.activeBlock.settings.animations.trigger" class="select select-bordered select-sm w-full">
                            <option value="onEnter">On Enter (Viewport)</option>
                            <option value="onLoad">On Page Load</option>
                            <option value="onScroll">On Scroll (Scrub)</option>
                            <option value="timeline">Main GSAP Timeline sequence</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] opacity-40 uppercase font-bold">Animation Preset</span></label>
                        <select v-model="store.activeBlock.settings.animations.preset" class="select select-bordered select-sm w-full">
                            <option value="fade-up">Fade Up</option>
                            <option value="fade-in">Fade In</option>
                            <option value="slide-left">Slide Left</option>
                            <option value="slide-right">Slide Right</option>
                            <option value="zoom-in">Zoom In</option>
                            <option value="clip-reveal">Clip Reveal</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] opacity-40 uppercase">Duration (s)</span></label>
                            <input type="number" step="0.1" v-model="store.activeBlock.settings.animations.duration" class="input input-bordered input-sm" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] opacity-40 uppercase">Delay (s)</span></label>
                            <input type="number" step="0.1" v-model="store.activeBlock.settings.animations.delay" class="input input-bordered input-sm" />
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] opacity-40 uppercase">Easing Curve</span></label>
                        <select v-model="store.activeBlock.settings.animations.ease" class="select select-bordered select-sm w-full">
                            <option value="power2.out">Smooth Out (Standard)</option>
                            <option value="back.out(1.7)">Pop Out (Elastic)</option>
                            <option value="expo.out">Rapid Out (High-End)</option>
                            <option value="none">Linear</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- STYLE & ADVANCED TABS -->
            <div v-if="activeSidebarTab === 'style'" class="space-y-6 animate-in fade-in">
                
                <!-- Colors -->
                <div class="space-y-4">
                    <h4 class="text-[10px] uppercase font-black tracking-widest opacity-50 border-b border-white/5 pb-2">Colors</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control items-center">
                            <label class="label self-start"><span class="label-text text-[10px] uppercase">Background</span></label>
                            <ColorPicker v-model:pureColor="bgColor" format="rgb" shape="square" useType="pure" />
                        </div>
                        <div class="form-control items-center">
                            <label class="label self-start"><span class="label-text text-[10px] uppercase">Text Color</span></label>
                            <ColorPicker v-model:pureColor="textColor" format="rgb" shape="square" useType="pure" />
                        </div>
                    </div>
                </div>

                <!-- Spacing (Margin) -->
                <div class="space-y-4">
                    <h4 class="text-[10px] uppercase font-black tracking-widest opacity-50 border-b border-white/5 pb-2">Margin</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <UnitInput v-model="store.activeBlock.settings.style.marginTop" placeholder="Top" />
                        <UnitInput v-model="store.activeBlock.settings.style.marginBottom" placeholder="Bottom" />
                        <UnitInput v-model="store.activeBlock.settings.style.marginLeft" placeholder="Left" />
                        <UnitInput v-model="store.activeBlock.settings.style.marginRight" placeholder="Right" />
                    </div>
                </div>

                <!-- Spacing (Padding) -->
                <div class="space-y-4">
                    <h4 class="text-[10px] uppercase font-black tracking-widest opacity-50 border-b border-white/5 pb-2">Padding</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <UnitInput v-model="store.activeBlock.settings.style.paddingTop" placeholder="Top" />
                        <UnitInput v-model="store.activeBlock.settings.style.paddingBottom" placeholder="Bottom" />
                        <UnitInput v-model="store.activeBlock.settings.style.paddingLeft" placeholder="Left" />
                        <UnitInput v-model="store.activeBlock.settings.style.paddingRight" placeholder="Right" />
                    </div>
                </div>

                <!-- Typography -->
                <div class="space-y-4">
                    <h4 class="text-[10px] uppercase font-black tracking-widest opacity-50 border-b border-white/5 pb-2">Typography</h4>
                    
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Font Family</span></label>
                        <select v-model="store.activeBlock.settings.style.fontFamily" class="select select-bordered select-sm w-full">
                            <option :value="undefined">Default</option>
                            <option value="sans-serif">Sans Serif</option>
                            <option value="serif">Serif</option>
                            <option value="monospace">Monospace</option>
                            <option value="'Inter', sans-serif">Inter</option>
                            <option value="'Titillium Web', sans-serif">Titillium Web</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Font Size</span></label>
                            <UnitInput v-model="store.activeBlock.settings.style.fontSize" placeholder="Size" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Letter Spacing</span></label>
                            <UnitInput v-model="store.activeBlock.settings.style.letterSpacing" placeholder="Spacing" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Alignment</span></label>
                            <select v-model="store.activeBlock.settings.style.textAlign" class="select select-bordered select-sm w-full">
                                <option :value="undefined">Default</option>
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                                <option value="justify">Justify</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Weight</span></label>
                            <select v-model="store.activeBlock.settings.style.fontWeight" class="select select-bordered select-sm w-full">
                                <option :value="undefined">Default</option>
                                <option value="300">Light</option>
                                <option value="normal">Normal</option>
                                <option value="500">Medium</option>
                                <option value="bold">Bold</option>
                                <option value="900">Black/Heavy</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Borders -->
                <div class="space-y-4">
                    <h4 class="text-[10px] uppercase font-black tracking-widest opacity-50 border-b border-white/5 pb-2">Border & Appearance</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Radius</span></label>
                            <UnitInput v-model="store.activeBlock.settings.style.borderRadius" placeholder="Radius" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Width</span></label>
                            <UnitInput v-model="store.activeBlock.settings.style.borderWidth" placeholder="Width" />
                        </div>
                        <div class="form-control col-span-2 items-center">
                            <label class="label self-start"><span class="label-text text-[10px] uppercase">Border Color</span></label>
                            <ColorPicker v-model:pureColor="borderColor" format="rgb" shape="square" useType="pure" />
                        </div>
                    </div>
                </div>

                <!-- Layout & Position -->
                <div class="space-y-4">
                    <h4 class="text-[10px] uppercase font-black tracking-widest opacity-50 border-b border-white/5 pb-2">Layout & Position</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Position</span></label>
                            <select v-model="store.activeBlock.settings.style.position" class="select select-bordered select-sm w-full">
                                <option :value="undefined">Static</option>
                                <option value="relative">Relative</option>
                                <option value="absolute">Absolute</option>
                                <option value="fixed">Fixed</option>
                                <option value="sticky">Sticky</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Z-Index</span></label>
                            <input type="number" v-model="store.activeBlock.settings.style.zIndex" class="input input-sm input-bordered w-full" />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <UnitInput v-model="store.activeBlock.settings.style.top" placeholder="Top" />
                        <UnitInput v-model="store.activeBlock.settings.style.bottom" placeholder="Bottom" />
                        <UnitInput v-model="store.activeBlock.settings.style.left" placeholder="Left" />
                        <UnitInput v-model="store.activeBlock.settings.style.right" placeholder="Right" />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Width</span></label>
                            <UnitInput v-model="store.activeBlock.settings.style.width" placeholder="Width" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Height</span></label>
                            <UnitInput v-model="store.activeBlock.settings.style.height" placeholder="Height" />
                        </div>
                    </div>
                </div>
                
                <div class="form-control mt-6">
                    <button @click="store.activeBlock.settings.style = {}" class="btn btn-outline btn-error btn-sm w-full">Reset Styles</button>
                </div>
            </div>

            <div v-if="activeSidebarTab === 'advanced'" class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs opacity-50">HTML ID</span></label>
                    <input type="text" v-model="store.activeBlock.settings.id" placeholder="e.g. contact-anchor" class="input input-bordered font-mono text-xs" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-xs opacity-50">Custom Classes</span></label>
                    <input type="text" v-model="store.activeBlock.settings.class" placeholder="e.g. premium-shadow lg:p-20" class="input input-bordered font-mono text-xs" />
                </div>
            </div>
        </div>
    </div>
    
    <!-- Empty State -->
    <div v-else class="h-full flex flex-col items-center justify-center p-8 text-center opacity-20">
        <i class="fas fa-mouse-pointer text-4xl mb-4"></i>
        <p class="text-sm font-bold uppercase tracking-widest leading-tight">Artisan Editor</p>
        <p class="text-[10px] mt-2">Select a block to refine its aesthetic.</p>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
</style>
