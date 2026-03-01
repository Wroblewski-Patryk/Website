<template>
    <AdminLayout>
        <div class="h-[calc(100vh-64px)] flex flex-col overflow-hidden bg-base-300">
            <!-- Top Bar: Viewport Toggles & Actions -->
            <div class="flex items-center justify-between px-6 py-2 bg-base-100 border-b border-white/5 shadow-md z-20">
                <div class="flex items-center gap-4">
                    <button @click="$inertia.visit(route('admin.posts.index'))" class="btn btn-ghost btn-sm">
                        <i class="fas fa-chevron-left mr-2"></i> Back
                    </button>
                    <div class="h-6 w-[1px] bg-white/10 mx-2"></div>
                    <div class="join bg-base-200 p-1 rounded-xl">
                        <button @click="viewport = 'desktop'" class="btn btn-xs join-item" :class="{ 'btn-primary': viewport === 'desktop' }">
                            <i class="fas fa-desktop"></i>
                        </button>
                        <button @click="viewport = 'tablet'" class="btn btn-xs join-item" :class="{ 'btn-primary': viewport === 'tablet' }">
                            <i class="fas fa-tablet-alt"></i>
                        </button>
                        <button @click="viewport = 'mobile'" class="btn btn-xs join-item" :class="{ 'btn-primary': viewport === 'mobile' }">
                            <i class="fas fa-mobile-alt"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <span v-if="store.isDirty" class="text-xs opacity-50 italic mr-2">Unsaved changes...</span>
                    <button @click="save" class="btn btn-primary btn-sm px-6 rounded-full shadow-lg shadow-primary/20">
                        <i class="fas fa-save mr-2"></i> Save Post
                    </button>
                </div>
            </div>

            <div class="flex-1 flex overflow-hidden">
                <!-- Left Sidebar: Palette & Post Settings -->
                <div class="w-80 bg-base-100 border-r border-white/5 flex flex-col z-10 shadow-xl">
                    <div class="tabs tabs-boxed bg-transparent p-2 m-2">
                        <button @click="leftTab = 'blocks'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'blocks' }">Blocks</button>
                        <button @click="leftTab = 'settings'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'settings' }">Info</button>
                        <button @click="leftTab = 'revisions'" class="tab tab-sm flex-1" :class="{ 'tab-active': leftTab === 'revisions' }">History</button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                        <!-- Block Palette -->
                        <div v-show="leftTab === 'blocks'" class="space-y-6">
                            <section>
                                <h3 class="text-xs font-bold opacity-40 uppercase tracking-widest mb-3">Layout</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    <button @click="store.addBlock('section')" class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/10 hover:bg-white/5">
                                        <i class="fas fa-layer-group text-lg"></i>
                                        <span class="text-[10px]">Section</span>
                                    </button>
                                    <button @click="store.addBlock('columns')" class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/10 hover:bg-white/5">
                                        <i class="fas fa-columns text-lg"></i>
                                        <span class="text-[10px]">Columns</span>
                                    </button>
                                </div>
                            </section>

                            <section>
                                <h3 class="text-xs font-bold opacity-40 uppercase tracking-widest mb-3">Content</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    <button @click="store.addBlock('heading')" class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/10 hover:bg-white/5">
                                        <i class="fas fa-heading text-lg"></i>
                                        <span class="text-[10px]">Heading</span>
                                    </button>
                                    <button @click="store.addBlock('text')" class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/10 hover:bg-white/5">
                                        <i class="fas fa-align-left text-lg"></i>
                                        <span class="text-[10px]">Text</span>
                                    </button>
                                    <button @click="store.addBlock('image')" class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/10 hover:bg-white/5">
                                        <i class="fas fa-image text-lg"></i>
                                        <span class="text-[10px]">Image</span>
                                    </button>
                                    <button @click="store.addBlock('button')" class="btn btn-outline btn-sm h-16 flex flex-col gap-1 border-white/10 hover:bg-white/5">
                                        <i class="fas fa-mouse-pointer text-lg"></i>
                                        <span class="text-[10px]">Button</span>
                                    </button>
                                </div>
                            </section>
                        </div>

                        <!-- Post Settings -->
                        <div v-show="leftTab === 'settings'" class="space-y-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text">Post Title</span></label>
                                <input type="text" v-model="form.title.pl" class="input input-bordered input-sm" placeholder="Title (PL)" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Slug</span></label>
                                <input type="text" v-model="form.slug.pl" class="input input-bordered input-sm" placeholder="slug-pl" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Excerpt</span></label>
                                <textarea v-model="form.excerpt.pl" class="textarea textarea-bordered textarea-sm h-24" placeholder="Short summary..."></textarea>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Featured Image</span></label>
                                <input type="text" v-model="form.featured_image.pl" class="input input-bordered input-sm" placeholder="/storage/media/..." />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Status</span></label>
                                <select v-model="form.is_published" class="select select-bordered select-sm">
                                    <option :value="false">Draft</option>
                                    <option :value="true">Published</option>
                                </select>
                            </div>
                        </div>

                        <!-- Revisions List -->
                        <div v-show="leftTab === 'revisions'" class="space-y-4">
                            <h3 class="text-xs font-bold opacity-40 uppercase tracking-widest mb-3">Revisions</h3>
                            <div v-if="!post.revisions || post.revisions.length === 0" class="text-center py-10 opacity-30 text-xs italic">
                                No history yet. Revisions are created when you save changes.
                            </div>
                            <div v-for="rev in post.revisions" :key="rev.id" class="p-3 bg-base-200/50 rounded-xl border border-white/5 flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold">{{ new Date(rev.created_at).toLocaleString() }}</span>
                                    <button @click="restoreRevision(rev)" class="btn btn-xs btn-outline btn-primary">Restore</button>
                                </div>
                                <span class="text-[10px] opacity-50">{{ rev.content?.length || 0 }} blocks total</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center: Canvas -->
                <div class="flex-1 bg-base-300 overflow-y-auto p-8 flex justify-center custom-scrollbar">
                    <div :class="[
                        'bg-white min-h-[1000px] shadow-2xl transition-all duration-500 rounded-sm overflow-x-hidden',
                        viewport === 'desktop' ? 'w-full max-w-4xl' : (viewport === 'tablet' ? 'w-[768px]' : 'w-[375px]')
                    ]">
                        <!-- Post Header Preview -->
                        <div class="p-12 border-b border-black/5 bg-base-100/50 select-none opacity-40">
                            <h1 class="text-4xl font-black mb-4">{{ form.title.pl || 'Post Title' }}</h1>
                            <p class="text-lg opacity-60">{{ form.excerpt.pl || 'Post excerpt will be shown here...' }}</p>
                        </div>

                        <!-- Reorderable Block Canvas -->
                        <div class="p-0">
                            <div v-for="(block, index) in store.blocks" 
                                 :key="block.id" 
                                 class="group relative"
                                 @click="store.activeBlockId = block.id"
                                 :class="{ 'ring-2 ring-primary ring-inset': store.activeBlockId === block.id }">
                                
                                <!-- Block Controls Overlays -->
                                <div class="absolute right-2 top-2 z-30 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                    <button @click.stop="store.moveBlock(index, -1)" class="btn btn-square btn-xs btn-ghost bg-white/50 backdrop-blur" title="Move Up"><i class="fas fa-arrow-up"></i></button>
                                    <button @click.stop="store.moveBlock(index, 1)" class="btn btn-square btn-xs btn-ghost bg-white/50 backdrop-blur" title="Move Down"><i class="fas fa-arrow-down"></i></button>
                                    <button @click.stop="store.removeBlock(block.id)" class="btn btn-square btn-xs btn-error btn-ghost bg-red-500/50 backdrop-blur" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>

                                <DynamicBlock :block="block" />
                            </div>

                            <div v-if="store.blocks.length === 0" class="h-96 flex flex-col items-center justify-center border-2 border-dashed border-base-content/10 m-10 rounded-2xl">
                                <i class="fas fa-feather-alt text-4xl mb-4 opacity-20 text-primary"></i>
                                <p class="opacity-40">Start writing your post content</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Settings -->
                <div class="w-80 bg-base-100 border-l border-white/5 overflow-y-auto z-10 shadow-2xl custom-scrollbar">
                    <BlockEditorSidebar />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { useBlockBuilderStore } from '@/Stores/useBlockBuilderStore';
import DynamicBlock from '@/Components/DynamicBlock.vue';
import BlockEditorSidebar from '@/Components/BlockEditorSidebar.vue';
import { onMounted, watch, ref } from 'vue';

const props = defineProps({
    post: Object
});

const store = useBlockBuilderStore();
const viewport = ref('desktop');
const leftTab = ref('blocks');

const form = useForm({
    title: props.post?.title || { pl: '', en: '' },
    slug: props.post?.slug || { pl: '', en: '' },
    excerpt: props.post?.excerpt || { pl: '', en: '' },
    content: props.post?.content || [],
    is_published: props.post?.is_published ?? false,
    featured_image: props.post?.featured_image || { pl: '', en: '' },
});

onMounted(() => {
    store.init(props.post?.content || []);
});

const generateSlug = (text) => {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
};

// Auto-slug generation
watch(() => form.title.pl, (newTitle) => {
    if (!props.post?.id || !form.slug.pl) {
        form.slug.pl = generateSlug(newTitle);
    }
});

const restoreRevision = (rev) => {
    if (confirm('Are you sure you want to restore this version? Current unsaved changes will be lost.')) {
        store.init(rev.content);
        store.isDirty = true;
    }
};

const save = () => {
    form.content = store.blocks;
    if (props.post?.id) {
        form.put(route('admin.posts.update', props.post.id), {
            onSuccess: () => store.isDirty = false
        });
    } else {
        form.post(route('admin.posts.store'), {
            onSuccess: () => store.isDirty = false
        });
    }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}
</style>
