<script setup>
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { ref } from 'vue';

const props = defineProps(['menus']);

const store = useBlockBuilderStore();
const activeSidebarTab = ref('content');

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
                <!-- Heading -->
                <div v-if="store.activeBlock.type === 'heading'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Text</span></label>
                        <input type="text" v-model="store.activeBlock.content.text" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Level</span></label>
                        <select v-model="store.activeBlock.content.level" class="select select-bordered w-full">
                            <option :value="1">H1 - Main</option>
                            <option :value="2">H2 - Section</option>
                        </select>
                    </div>
                </div>

                <!-- Portfolio -->
                <div v-if="store.activeBlock.type === 'portfolio'" class="space-y-6">
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
            <div v-if="activeSidebarTab === 'animations'" class="space-y-6">
                 <div class="form-control p-4 bg-primary/5 rounded-2xl border border-primary/10">
                    <label class="label cursor-pointer justify-between">
                        <span class="text-sm font-black text-primary uppercase italic">Enable GSAP</span>
                        <input type="checkbox" v-model="store.activeBlock.settings.animations.enabled" class="toggle toggle-primary" />
                    </label>
                </div>

                <div v-if="store.activeBlock.settings.animations.enabled" class="space-y-4 animate-in fade-in slide-in-from-top-2">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Animation Type</span></label>
                        <select v-model="store.activeBlock.settings.animations.type" class="select select-bordered w-full">
                            <option value="fade-up">Fade Up</option>
                            <option value="slide-left">Slide Left</option>
                            <option value="reveal-text">Text Reveal (Chars)</option>
                            <option value="zoom-in">Zoom In</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] opacity-40 uppercase">Duration</span></label>
                            <input type="number" step="0.1" v-model="store.activeBlock.settings.animations.duration" class="input input-bordered" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text text-[10px] opacity-40 uppercase">Delay</span></label>
                            <input type="number" step="0.1" v-model="store.activeBlock.settings.animations.delay" class="input input-bordered" />
                        </div>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Easing</span></label>
                        <select v-model="store.activeBlock.settings.animations.ease" class="select select-bordered w-full">
                            <option value="power2.out">Smooth Out</option>
                            <option value="back.out(1.7)">Pop Out (Elastic)</option>
                            <option value="expo.out">Rapid Out</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- STYLE & ADVANCED TABS (Simplified for demo) -->
            <div v-if="activeSidebarTab === 'style'" class="space-y-4 opacity-50 italic text-xs">
                Color and typography overrides coming soon...
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
