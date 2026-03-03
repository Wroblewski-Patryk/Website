<script setup>
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { computed, ref, watch } from 'vue';
import UnitInput from '@/Components/UnitInput.vue';
import LinkedUnitInput from '@/Components/LinkedUnitInput.vue';
import LayerTreeItem from '@/Components/LayerTreeItem.vue';
import FillControl from '@/Components/FillControl.vue';

const props = defineProps(['menus']);

const store = useBlockBuilderStore();
const activeSidebarTab = ref('content');
const activeInspectorTab = ref('layers'); // For Document Inspector State

const createFillProxy = (newProp, legacyProp) => computed({
    get: () => {
        if (!store.activeBlock || !store.activeBlock.settings.style) return undefined;
        // Prefer the new advanced object
        if (store.activeBlock.settings.style[newProp] !== undefined) {
            return store.activeBlock.settings.style[newProp];
        }
        // Fallback to legacy string
        return store.activeBlock.settings.style[legacyProp];
    },
    set: (val) => {
        if (store.activeBlock && store.activeBlock.settings.style) {
            store.activeBlock.settings.style[legacyProp] = undefined;
            store.activeBlock.settings.style[newProp] = val;
        }
    }
});

const backgroundFill = createFillProxy('backgroundFill', 'backgroundColor');
const textFill = createFillProxy('textFill', 'textColor');
const borderFill = createFillProxy('borderFill', 'borderColor');

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
                padding: 'py-0',
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

const toggleOffset = (direction) => {
    if (!store.activeBlock || !store.activeBlock.settings.style) return;
    const style = store.activeBlock.settings.style;
    
    if (direction === 'top') {
        const val = style.bottom ?? style.top ?? '0px';
        style.bottom = undefined;
        style.top = val;
    } else if (direction === 'bottom') {
        const val = style.top ?? style.bottom ?? '0px';
        style.top = undefined;
        style.bottom = val;
    } else if (direction === 'left') {
        const val = style.right ?? style.left ?? '0px';
        style.right = undefined;
        style.left = val;
    } else if (direction === 'right') {
        const val = style.left ?? style.right ?? '0px';
        style.left = undefined;
        style.right = val;
    }
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
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] opacity-40 uppercase font-black">Table Layout</label>
                        <div class="join">
                            <button @click="store.activeBlock.content.rows.forEach(r => r.push(''))" class="btn btn-xs join-item btn-ghost bg-base-200 hover:bg-base-300 px-3" title="Add Column">+ Col</button>
                            <button @click="store.activeBlock.content.rows.forEach(r => { if(r.length > 1) r.pop() })" class="btn btn-xs join-item btn-ghost bg-base-200 hover:bg-base-300 px-3" title="Remove Last Column">- Col</button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div v-for="(row, rIdx) in store.activeBlock.content.rows" :key="rIdx" class="flex gap-2 items-center bg-base-200/50 p-2 rounded-xl border border-white/5 overflow-hidden">
                            <div class="flex gap-2 flex-1 overflow-x-auto custom-scrollbar pb-1">
                                <input v-for="(cell, cIdx) in row" :key="cIdx" type="text" v-model="store.activeBlock.content.rows[rIdx][cIdx]" class="input input-xs input-bordered min-w-[80px] flex-1" />
                            </div>
                            <button @click="store.activeBlock.content.rows.splice(rIdx, 1)" class="btn btn-square btn-xs btn-ghost text-error shrink-0" :disabled="store.activeBlock.content.rows.length <= 1" title="Remove Row"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    
                    <button @click="store.activeBlock.content.rows.push(new Array(store.activeBlock.content.rows[0].length).fill(''))" class="btn btn-xs btn-outline btn-block border-dashed border-white/20"><i class="fas fa-plus mr-2"></i>Add Row</button>
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
                <div class="collapse collapse-arrow bg-base-200/50 border border-white/5 rounded-2xl">
                    <input type="checkbox" /> 
                    <div class="collapse-title text-[10px] uppercase font-black tracking-widest opacity-50">Colors</div>
                    <div class="collapse-content space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control items-center w-full col-span-2">
                                <FillControl v-model="backgroundFill" label="Background" />
                            </div>
                            <div class="divider my-0 opacity-10 col-span-2"></div>
                            <div class="form-control items-center w-full col-span-2">
                                <FillControl v-model="textFill" label="Text" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Spacing (Margin & Padding) -->
                <div class="collapse collapse-arrow bg-base-200/50 border border-white/5 rounded-2xl">
                    <input type="checkbox" /> 
                    <div class="collapse-title text-[10px] uppercase font-black tracking-widest opacity-50">Spacing</div>
                    <div class="collapse-content space-y-6">
                        <LinkedUnitInput 
                            v-model:top="store.activeBlock.settings.style.marginTop"
                            v-model:right="store.activeBlock.settings.style.marginRight"
                            v-model:bottom="store.activeBlock.settings.style.marginBottom"
                            v-model:left="store.activeBlock.settings.style.marginLeft"
                            label="Margin" 
                        />
                        <div class="divider my-0 opacity-10"></div>
                        <LinkedUnitInput 
                            v-model:top="store.activeBlock.settings.style.paddingTop"
                            v-model:right="store.activeBlock.settings.style.paddingRight"
                            v-model:bottom="store.activeBlock.settings.style.paddingBottom"
                            v-model:left="store.activeBlock.settings.style.paddingLeft"
                            label="Padding" 
                        />
                    </div>
                </div>

                <!-- Typography -->
                <div class="collapse collapse-arrow bg-base-200/50 border border-white/5 rounded-2xl">
                    <input type="checkbox" checked /> 
                    <div class="collapse-title text-[10px] uppercase font-black tracking-widest opacity-50">Typography</div>
                    <div class="collapse-content space-y-4">
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
                            <div class="form-control col-span-2">
                                <label class="label"><span class="label-text text-[10px] uppercase">Alignment</span></label>
                                <div class="join w-full">
                                    <button @click="store.activeBlock.settings.style.textAlign = 'left'" class="btn btn-sm join-item flex-1" :class="store.activeBlock.settings.style.textAlign === 'left' ? 'btn-primary' : 'bg-base-300'"><i class="fas fa-align-left"></i></button>
                                    <button @click="store.activeBlock.settings.style.textAlign = 'center'" class="btn btn-sm join-item flex-1" :class="store.activeBlock.settings.style.textAlign === 'center' ? 'btn-primary' : 'bg-base-300'"><i class="fas fa-align-center"></i></button>
                                    <button @click="store.activeBlock.settings.style.textAlign = 'right'" class="btn btn-sm join-item flex-1" :class="store.activeBlock.settings.style.textAlign === 'right' ? 'btn-primary' : 'bg-base-300'"><i class="fas fa-align-right"></i></button>
                                    <button @click="store.activeBlock.settings.style.textAlign = 'justify'" class="btn btn-sm join-item flex-1" :class="store.activeBlock.settings.style.textAlign === 'justify' ? 'btn-primary' : 'bg-base-300'"><i class="fas fa-align-justify"></i></button>
                                </div>
                            </div>
                            <div class="form-control col-span-2">
                                <label class="label"><span class="label-text text-[10px] uppercase">Weight</span></label>
                                <div class="join w-full">
                                    <button @click="store.activeBlock.settings.style.fontWeight = '300'" class="btn btn-sm join-item flex-1 font-light" :class="store.activeBlock.settings.style.fontWeight === '300' ? 'btn-primary' : 'bg-base-300'">L</button>
                                    <button @click="store.activeBlock.settings.style.fontWeight = 'normal'" class="btn btn-sm join-item flex-1 font-normal" :class="(!store.activeBlock.settings.style.fontWeight || store.activeBlock.settings.style.fontWeight === 'normal') ? 'btn-primary' : 'bg-base-300'">R</button>
                                    <button @click="store.activeBlock.settings.style.fontWeight = '500'" class="btn btn-sm join-item flex-1 font-medium" :class="store.activeBlock.settings.style.fontWeight === '500' ? 'btn-primary' : 'bg-base-300'">M</button>
                                    <button @click="store.activeBlock.settings.style.fontWeight = 'bold'" class="btn btn-sm join-item flex-1 font-bold" :class="store.activeBlock.settings.style.fontWeight === 'bold' ? 'btn-primary' : 'bg-base-300'">B</button>
                                    <button @click="store.activeBlock.settings.style.fontWeight = '900'" class="btn btn-sm join-item flex-1 font-black" :class="store.activeBlock.settings.style.fontWeight === '900' ? 'btn-primary' : 'bg-base-300'">H</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Borders -->
                <div class="collapse collapse-arrow bg-base-200/50 border border-white/5 rounded-2xl">
                    <input type="checkbox" /> 
                    <div class="collapse-title text-[10px] uppercase font-black tracking-widest opacity-50">Border & Appearance</div>
                    <div class="collapse-content space-y-6">
                        <!-- Border Radius Linked Input -->
                        <LinkedUnitInput 
                            v-model:top="store.activeBlock.settings.style.borderTopLeftRadius"
                            v-model:right="store.activeBlock.settings.style.borderTopRightRadius"
                            v-model:bottom="store.activeBlock.settings.style.borderBottomRightRadius"
                            v-model:left="store.activeBlock.settings.style.borderBottomLeftRadius"
                            label="Radius" 
                        />

                        <div class="divider my-0 opacity-10"></div>

                        <!-- Border Width Linked Input -->
                        <LinkedUnitInput 
                            v-model:top="store.activeBlock.settings.style.borderTopWidth"
                            v-model:right="store.activeBlock.settings.style.borderRightWidth"
                            v-model:bottom="store.activeBlock.settings.style.borderBottomWidth"
                            v-model:left="store.activeBlock.settings.style.borderLeftWidth"
                            label="Width" 
                        />

                        <div class="divider my-0 opacity-10"></div>

                        <div class="grid grid-cols-1">
                            <div class="form-control w-full">
                                <FillControl v-model="borderFill" label="Border" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Layout & Position -->
                <div class="collapse collapse-arrow bg-base-200/50 border border-white/5 rounded-2xl">
                    <input type="checkbox" /> 
                    <div class="collapse-title text-[10px] uppercase font-black tracking-widest opacity-50">Layout & Position</div>
                    <div class="collapse-content space-y-4">
                        <!-- 1. Z-Index and Position -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text text-[10px] uppercase">Z-Index</span></label>
                                <input type="number" v-model="store.activeBlock.settings.style.zIndex" class="input input-sm input-bordered w-full" />
                            </div>
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
                        </div>
                        
                        <!-- 2. Offsets (Only visible if not static) -->
                        <div v-if="['absolute', 'fixed', 'sticky', 'relative'].includes(store.activeBlock.settings.style.position)" class="bg-base-300/50 p-4 rounded-xl mt-4 space-y-4">
                            <div class="text-[10px] uppercase font-bold opacity-50 mb-2 border-b border-white/10 pb-1">Offsets</div>

                            <div class="flex items-center gap-4">
                                <div class="join">
                                    <button @click="toggleOffset('top')" class="btn btn-xs join-item min-h-0 h-6 w-8 border-none" :class="store.activeBlock.settings.style.top !== undefined || store.activeBlock.settings.style.bottom === undefined ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" title="Top Offset"><i class="fas fa-arrow-up"></i></button>
                                    <button @click="toggleOffset('bottom')" class="btn btn-xs join-item min-h-0 h-6 w-8 border-none" :class="store.activeBlock.settings.style.bottom !== undefined ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" title="Bottom Offset"><i class="fas fa-arrow-down"></i></button>
                                </div>
                                <UnitInput v-if="store.activeBlock.settings.style.bottom !== undefined" v-model="store.activeBlock.settings.style.bottom" placeholder="Bottom value" class="flex-1" />
                                <UnitInput v-else v-model="store.activeBlock.settings.style.top" placeholder="Top value" class="flex-1" />
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <div class="join">
                                    <button @click="toggleOffset('left')" class="btn btn-xs join-item min-h-0 h-6 w-8 border-none" :class="store.activeBlock.settings.style.left !== undefined || store.activeBlock.settings.style.right === undefined ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" title="Left Offset"><i class="fas fa-arrow-left"></i></button>
                                    <button @click="toggleOffset('right')" class="btn btn-xs join-item min-h-0 h-6 w-8 border-none" :class="store.activeBlock.settings.style.right !== undefined ? 'btn-primary' : 'bg-transparent text-base-content/50 hover:bg-base-content/10'" title="Right Offset"><i class="fas fa-arrow-right"></i></button>
                                </div>
                                <UnitInput v-if="store.activeBlock.settings.style.right !== undefined" v-model="store.activeBlock.settings.style.right" placeholder="Right value" class="flex-1" />
                                <UnitInput v-else v-model="store.activeBlock.settings.style.left" placeholder="Left value" class="flex-1" />
                            </div>
                        </div>

                        <div class="divider my-0 opacity-10"></div>

                        <!-- 3. Display -->
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] uppercase">Display</span></label>
                            <select v-model="store.activeBlock.settings.style.display" class="select select-bordered select-sm w-full">
                                <option :value="undefined">Default</option>
                                <option value="block">Block</option>
                                <option value="inline-block">Inline Block</option>
                                <option value="flex">Flex</option>
                                <option value="grid">Grid</option>
                                <option value="inline-flex">Inline Flex</option>
                                <option value="inline-grid">Inline Grid</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                        
                        <!-- 4. Width / Height -->
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
    
    <!-- Empty State: Document Inspector -->
    <div v-else class="h-full flex flex-col bg-base-100 animate-in fade-in duration-300">
        <!-- Inspector Header -->
        <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between sticky top-0 bg-base-100/80 backdrop-blur-xl z-20">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-base-content/5 flex items-center justify-center text-base-content/70 flex-shrink-0">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold capitalize">Document Inspector</h3>
                    <p class="text-[10px] opacity-40 uppercase tracking-widest leading-none">Global Settings</p>
                </div>
            </div>
        </div>

        <!-- Inspector Tabs -->
        <div class="flex border-b border-white/5 bg-base-200/30">
            <button v-for="tab in ['layers', 'info', 'history']" :key="tab"
                    @click="activeInspectorTab = tab"
                    class="flex-1 py-3 text-[10px] font-bold uppercase tracking-widest transition-all relative"
                    :class="activeInspectorTab === tab ? 'text-primary' : 'opacity-40 hover:opacity-100'">
                {{ tab }}
                <div v-if="activeInspectorTab === tab" class="absolute bottom-0 left-4 right-4 h-0.5 bg-primary rounded-full"></div>
            </button>
        </div>

        <!-- Inspector Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">
            
            <!-- Layers -->
            <div v-if="activeInspectorTab === 'layers'" class="space-y-4 animate-in fade-in">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-30 mb-2">Canvas Graph</p>
                
                <LayerTreeItem 
                    :blocks="store.blocks" 
                    @change="store.isDirty = true" 
                />
                
                <div v-if="store.blocks.length === 0" class="text-xs opacity-40 italic mt-2 text-center py-8">
                    <i class="fas fa-cubes text-2xl mb-2 opacity-20 block"></i>
                    No blocks on canvas.
                </div>
            </div>

            <!-- Global Info Slot -->
            <div v-if="activeInspectorTab === 'info'" class="space-y-4 animate-in fade-in">
                <slot name="info"></slot>
            </div>

            <!-- History Slot -->
            <div v-if="activeInspectorTab === 'history'" class="space-y-4 animate-in fade-in">
                <slot name="history"></slot>
            </div>

        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
</style>
