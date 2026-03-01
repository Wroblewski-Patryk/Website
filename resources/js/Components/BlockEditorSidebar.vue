<script setup>
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import { ref } from 'vue';

const store = useBlockBuilderStore();
const activeSidebarTab = ref('content');
</script>

<template>
    <div v-if="store.activeBlock" class="h-full flex flex-col bg-base-100 border-l border-white/5 animate-in slide-in-from-right-4 fade-in duration-300">
        <!-- Sidebar Header -->
        <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between sticky top-0 bg-base-100/80 backdrop-blur-xl z-20">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                    <i v-if="store.activeBlock.type === 'heading'" class="fas fa-heading"></i>
                    <i v-else-if="store.activeBlock.type === 'text'" class="fas fa-align-left"></i>
                    <i v-else-if="store.activeBlock.type === 'image'" class="fas fa-image"></i>
                    <i v-else-if="store.activeBlock.type === 'button'" class="fas fa-mouse-pointer"></i>
                    <i v-else-if="store.activeBlock.type === 'hero'" class="fas fa-star"></i>
                    <i v-else-if="store.activeBlock.type === 'section'" class="fas fa-layer-group"></i>
                    <i v-else-if="store.activeBlock.type === 'columns'" class="fas fa-columns"></i>
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
            <button v-for="tab in ['content', 'style', 'advanced']" :key="tab"
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
                <!-- Heading Content -->
                <div v-if="store.activeBlock.type === 'heading'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Heading Text</span></label>
                        <input type="text" v-model="store.activeBlock.content.text" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Level</span></label>
                        <select v-model="store.activeBlock.content.level" class="select select-bordered w-full">
                            <option value="h1">H1 - Main Title</option>
                            <option value="h2">H2 - Section Title</option>
                            <option value="h3">H3 - Subsection</option>
                            <option value="h4">H4 - Small Heading</option>
                        </select>
                    </div>
                </div>

                <!-- Text Content -->
                <div v-if="store.activeBlock.type === 'text'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Rich Text</span></label>
                        <textarea v-model="store.activeBlock.content.text" class="textarea textarea-bordered h-48 w-full"></textarea>
                    </div>
                </div>

                <!-- Hero Content -->
                <div v-if="store.activeBlock.type === 'hero'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Headline</span></label>
                        <textarea v-model="store.activeBlock.content.headline" class="textarea textarea-bordered w-full" rows="2"></textarea>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Subheadline</span></label>
                        <textarea v-model="store.activeBlock.content.subheadline" class="textarea textarea-bordered w-full" rows="3"></textarea>
                    </div>
                </div>

                <!-- Image Content -->
                <div v-if="store.activeBlock.type === 'image'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Image URL</span></label>
                        <input type="text" v-model="store.activeBlock.content.url" class="input input-bordered w-full" placeholder="/storage/..." />
                        <div class="mt-2 text-[10px] opacity-40">Paste a local storage URL or external image link.</div>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Alt Description</span></label>
                        <input type="text" v-model="store.activeBlock.content.alt" class="input input-bordered w-full" placeholder="Describe for accessibility" />
                    </div>
                </div>

                <!-- Button Content -->
                <div v-if="store.activeBlock.type === 'button'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Label</span></label>
                        <input type="text" v-model="store.activeBlock.content.label" class="input input-bordered w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">URL</span></label>
                        <input type="text" v-model="store.activeBlock.content.url" class="input input-bordered w-full" placeholder="https://..." />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Variant</span></label>
                        <select v-model="store.activeBlock.content.style" class="select select-bordered w-full">
                            <option value="primary">Solid Primary</option>
                            <option value="outline">Outline Primary</option>
                            <option value="ghost">Ghost (Glass)</option>
                        </select>
                    </div>
                </div>

                <!-- Section Content -->
                <div v-if="store.activeBlock.type === 'section'" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Width Layout</span></label>
                        <select v-model="store.activeBlock.content.width" class="select select-bordered w-full">
                            <option value="boxed">Boxed (Max 1280px)</option>
                            <option value="full">Full Width</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-xs opacity-50">Background Image URL</span></label>
                        <input type="text" v-model="store.activeBlock.content.bg_image" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>

            <!-- STYLE TAB -->
            <div v-if="activeSidebarTab === 'style'" class="space-y-6">
                <!-- Spacing -->
                <section>
                    <h4 class="text-[10px] font-bold opacity-30 uppercase tracking-widest mb-4">Spacing</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-[10px] opacity-40">Padding Top</span></label>
                            <input type="text" v-model="store.activeBlock.appearance.paddingTop" class="input input-sm input-bordered font-mono text-xs" />
                        </div>
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-[10px] opacity-40">Padding Bottom</span></label>
                            <input type="text" v-model="store.activeBlock.appearance.paddingBottom" class="input input-sm input-bordered font-mono text-xs" />
                        </div>
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-[10px] opacity-40">Margin Top</span></label>
                            <input type="text" v-model="store.activeBlock.appearance.marginTop" class="input input-sm input-bordered font-mono text-xs" />
                        </div>
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-[10px] opacity-40">Margin Bottom</span></label>
                            <input type="text" v-model="store.activeBlock.appearance.marginBottom" class="input input-sm input-bordered font-mono text-xs" />
                        </div>
                    </div>
                </section>

                <!-- Colors -->
                <section>
                    <h4 class="text-[10px] font-bold opacity-30 uppercase tracking-widest mb-4">Appearance</h4>
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-[10px] opacity-40">Background Color</span></label>
                            <div class="flex gap-2">
                                <input type="color" v-model="store.activeBlock.appearance.backgroundColor" class="w-8 h-8 rounded-lg bg-transparent border border-white/10" />
                                <input type="text" v-model="store.activeBlock.appearance.backgroundColor" class="input input-xs input-bordered flex-1 font-mono" />
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-[10px] opacity-40">Text Color</span></label>
                            <div class="flex gap-2">
                                <input type="color" v-model="store.activeBlock.appearance.textColor" class="w-8 h-8 rounded-lg bg-transparent border border-white/10" />
                                <input type="text" v-model="store.activeBlock.appearance.textColor" class="input input-xs input-bordered flex-1 font-mono" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Typography -->
                <section>
                    <h4 class="text-[10px] font-bold opacity-30 uppercase tracking-widest mb-4">Typography</h4>
                    <div class="flex gap-2">
                         <button v-for="align in ['left', 'center', 'right', 'justify']" :key="align"
                                 @click="store.activeBlock.appearance.textAlign = align"
                                 class="btn btn-xs flex-1" :class="store.activeBlock.appearance.textAlign === align ? 'btn-primary' : 'btn-ghost bg-base-300/50'">
                             <i :class="`fas fa-align-${align}`"></i>
                         </button>
                    </div>
                </section>
            </div>

            <!-- ADVANCED TAB -->
            <div v-if="activeSidebarTab === 'advanced'" class="space-y-6">
                <!-- GSAP Animations -->
                <section class="p-4 bg-primary/5 rounded-2xl border border-primary/10">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-xs font-bold text-primary">GSAP Animation</h4>
                        <input type="checkbox" v-model="store.activeBlock.appearance.animations.enabled" class="toggle toggle-primary toggle-sm" />
                    </div>

                    <div v-show="store.activeBlock.appearance.animations.enabled" class="space-y-4 animate-in fade-in zoom-in-95 duration-200">
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text text-[10px] opacity-40">Preset</span></label>
                            <select v-model="store.activeBlock.appearance.animations.preset" class="select select-sm select-bordered w-full">
                                <option value="fadeUp">Fade Up</option>
                                <option value="fadeIn">Fade In</option>
                                <option value="slideLeft">Slide Left</option>
                                <option value="slideRight">Slide Right</option>
                                <option value="scaleIn">Scale Up</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-[10px] opacity-40">Duration</span></label>
                                <input type="number" step="0.1" v-model="store.activeBlock.appearance.animations.duration" class="input input-sm input-bordered" />
                            </div>
                            <div class="form-control">
                                <label class="label py-1"><span class="label-text text-[10px] opacity-40">Delay</span></label>
                                <input type="number" step="0.1" v-model="store.activeBlock.appearance.animations.delay" class="input input-sm input-bordered" />
                            </div>
                        </div>
                    </div>
                </section>

                <div class="form-control">
                    <label class="label"><span class="label-text text-xs opacity-50">Custom CSS Classes</span></label>
                    <input type="text" v-model="store.activeBlock.appearance.customClasses" placeholder="e.g. glass shadow-xl" class="input input-bordered w-full font-mono text-xs" />
                </div>
            </div>
        </div>
    </div>
    
    <!-- Empty State -->
    <div v-else class="h-full flex flex-col items-center justify-center p-8 text-center opacity-20">
        <i class="fas fa-mouse-pointer text-4xl mb-4"></i>
        <p class="text-sm font-bold uppercase tracking-widest">Select a Block</p>
        <p class="text-xs mt-2">to edit its settings and style</p>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
</style>
