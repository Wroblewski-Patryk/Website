<template>
    <div class="space-y-6">
        <!-- 1. Layout -->
        <AdminCollapse title="Layout" icon="PhLayout" persistKey="style_layout">
            <div class="space-y-4 pt-1">
                <!-- Display -->
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Display</span></label>
                    <select v-model="settings.style.display" class="select select-bordered select-sm w-full">
                        <option :value="undefined">Default</option>
                        <option value="block">Block</option>
                        <option value="flex">Flex</option>
                        <option value="grid">Grid</option>
                        <option value="inline-block">Inline Block</option>
                        <option value="inline">Inline</option>
                        <option value="none">None</option>
                    </select>
                </div>

                <!-- Flex Settings -->
                <div v-if="settings.style.display === 'flex'" class="p-3 bg-base-300/30 rounded-xl space-y-4 border border-white/5 shadow-inner animate-in fade-in slide-in-from-top-1 duration-200">
                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Direction</span></label>
                        <div class="join w-full">
                            <button @click="settings.style.flexDirection = 'row'" class="btn btn-xs join-item flex-1" :class="settings.style.flexDirection === 'row' ? 'btn-primary' : 'bg-base-200'" title="Row"><PhColumns class="w-3 h-3" /></button>
                            <button @click="settings.style.flexDirection = 'column'" class="btn btn-xs join-item flex-1" :class="settings.style.flexDirection === 'column' ? 'btn-primary' : 'bg-base-200'" title="Column"><PhRows class="w-3 h-3" /></button>
                            <button @click="settings.style.flexDirection = 'row-reverse'" class="btn btn-xs join-item flex-1" :class="settings.style.flexDirection === 'row-reverse' ? 'btn-primary' : 'bg-base-200'" title="Row Reverse"><i class="fas fa-right-left text-[10px]"></i></button>
                            <button @click="settings.style.flexDirection = 'column-reverse'" class="btn btn-xs join-item flex-1" :class="settings.style.flexDirection === 'column-reverse' ? 'btn-primary' : 'bg-base-200'" title="Column Reverse"><i class="fas fa-up-down text-[10px]"></i></button>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1 flex justify-between items-center cursor-pointer">
                            <span class="text-[9px] uppercase font-bold opacity-40">Wrap Content</span>
                            <input type="checkbox" :checked="settings.style.flexWrap === 'wrap'" @change="settings.style.flexWrap = $event.target.checked ? 'wrap' : 'nowrap'" class="toggle toggle-xs toggle-primary" />
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Justify</span></label>
                        <div class="grid grid-cols-6 gap-1">
                            <button v-for="j in ['flex-start', 'center', 'flex-end', 'space-between', 'space-around', 'space-evenly']" :key="j"
                                @click="settings.style.justifyContent = j" class="btn btn-xs px-0 bg-base-200" :class="settings.style.justifyContent === j ? 'btn-primary' : ''" :title="j">
                                <span class="text-[8px] uppercase font-bold">{{ j.split('-').pop()[0] }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Align</span></label>
                        <div class="grid grid-cols-5 gap-1">
                            <button v-for="a in ['flex-start', 'center', 'flex-end', 'baseline', 'stretch']" :key="a"
                                @click="settings.style.alignItems = a" class="btn btn-xs px-0 bg-base-200" :class="settings.style.alignItems === a ? 'btn-primary' : ''" :title="a">
                                <span class="text-[8px] uppercase font-bold">{{ a.split('-').pop()[0] }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Gap</span></label>
                            <UnitInput v-model="settings.style.gap" placeholder="0px" />
                        </div>
                    </div>
                </div>

                <!-- Grid Settings -->
                <div v-if="settings.style.display === 'grid'" class="p-3 bg-base-300/30 rounded-xl space-y-4 border border-white/5 shadow-inner animate-in fade-in slide-in-from-top-1 duration-200">
                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Columns</span></label>
                        <input type="text" v-model="settings.style.gridTemplateColumns" placeholder="e.g. 1fr 1fr" class="input input-xs input-bordered w-full font-mono text-[10px]" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Column Gap</span></label>
                            <UnitInput v-model="settings.style.columnGap" placeholder="0px" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Row Gap</span></label>
                            <UnitInput v-model="settings.style.rowGap" placeholder="0px" />
                        </div>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 2. Sizing & Positioning -->
        <AdminCollapse title="Sizing & Position" icon="PhBoundingBox" persistKey="style_sizing">
            <div class="space-y-6 pt-1">
                <div class="form-control mb-4">
                    <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Z-Index</span></label>
                    <input type="number" v-model.number="settings.style.zIndex" class="input input-sm input-bordered w-full" placeholder="Auto" />
                </div>

                <!-- Width -->
                <div class="space-y-1">
                    <label class="label pb-0"><span class="label-text text-[10px] uppercase font-bold opacity-50">Width</span></label>
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <div class="form-control col-span-2">
                            <UnitInput v-model="settings.style.width" placeholder="Width (auto)" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase opacity-40">Min</span></label>
                            <UnitInput v-model="settings.style.minWidth" placeholder="none" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase opacity-40">Max</span></label>
                            <UnitInput v-model="settings.style.maxWidth" placeholder="none" />
                        </div>
                    </div>
                </div>

                <!-- Height -->
                <div class="space-y-1">
                    <label class="label pb-0"><span class="label-text text-[10px] uppercase font-bold opacity-50">Height</span></label>
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <div class="form-control col-span-2">
                            <UnitInput v-model="settings.style.height" placeholder="Height (auto)" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase opacity-40">Min</span></label>
                            <UnitInput v-model="settings.style.minHeight" placeholder="none" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase opacity-40">Max</span></label>
                            <UnitInput v-model="settings.style.maxHeight" placeholder="none" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Z-Index</span></label>
                        <input type="number" v-model="settings.style.zIndex" class="input input-sm input-bordered w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Aspect Ratio</span></label>
                        <select v-model="settings.style.aspectRatio" class="select select-bordered select-sm w-full">
                            <option :value="undefined">Auto</option>
                            <option value="1/1">1:1 Square</option>
                            <option value="16/9">16:9 Video</option>
                            <option value="4/3">4:3 Photo</option>
                            <option value="21/9">21:9 Ultra</option>
                        </select>
                    </div>
                </div>

                <div class="divider my-0 opacity-10"></div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Positioning</span></label>
                    <select v-model="settings.style.position" class="select select-bordered select-sm w-full">
                        <option :value="undefined">Static</option>
                        <option value="relative">Relative</option>
                        <option value="absolute">Absolute</option>
                        <option value="fixed">Fixed</option>
                        <option value="sticky">Sticky</option>
                    </select>
                </div>

                <!-- Offsets -->
                <div v-if="['absolute', 'fixed', 'sticky', 'relative'].includes(settings.style.position)" class="bg-base-300/30 p-3 rounded-xl space-y-3 border border-white/5 shadow-inner">
                    <div class="flex flex-col gap-3">
                        <div class="flex justify-center gap-4">
                            <!-- Y Axis Toggle -->
                            <div class="join border border-white/10 overflow-hidden shadow-lg">
                                <button @click="toggleOffset('top')" class="btn btn-xs join-item h-7 w-8" :class="settings.style.top !== undefined ? 'btn-primary' : 'bg-base-200 opacity-50'" title="Top"><PhArrowUp weight="bold" class="w-3 h-3" /></button>
                                <button @click="toggleOffset('bottom')" class="btn btn-xs join-item h-7 w-8" :class="settings.style.bottom !== undefined ? 'btn-primary' : 'bg-base-200 opacity-50'" title="Bottom"><PhArrowDown weight="bold" class="w-3 h-3" /></button>
                            </div>
                            <!-- X Axis Toggle -->
                            <div class="join border border-white/10 overflow-hidden shadow-lg">
                                <button @click="toggleOffset('left')" class="btn btn-xs join-item h-7 w-8" :class="settings.style.left !== undefined ? 'btn-primary' : 'bg-base-200 opacity-50'" title="Left"><PhArrowLeft weight="bold" class="w-3 h-3" /></button>
                                <button @click="toggleOffset('right')" class="btn btn-xs join-item h-7 w-8" :class="settings.style.right !== undefined ? 'btn-primary' : 'bg-base-200 opacity-50'" title="Right"><PhArrowRight weight="bold" class="w-3 h-3" /></button>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div v-if="settings.style.top !== undefined" class="form-control">
                                <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Top</span></label>
                                <UnitInput v-model="settings.style.top" />
                            </div>
                            <div v-if="settings.style.bottom !== undefined" class="form-control">
                                <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Bottom</span></label>
                                <UnitInput v-model="settings.style.bottom" />
                            </div>
                            <div v-if="settings.style.left !== undefined" class="form-control">
                                <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Left</span></label>
                                <UnitInput v-model="settings.style.left" />
                            </div>
                            <div v-if="settings.style.right !== undefined" class="form-control">
                                <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Right</span></label>
                                <UnitInput v-model="settings.style.right" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 2. Typography -->
        <AdminCollapse title="Typography" icon="PhTextT" persistKey="style_typography" v-if="!['spacer', 'divider', 'image', 'video', 'gallery', 'carousel'].includes(blockType)">
            <div class="space-y-4 pt-1">
                <div class="form-control">
                    <FillControl v-model="textFill" label="Text Color" />
                </div>

                <div class="divider my-0 opacity-10"></div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Font Family</span></label>
                    <select v-model="settings.style.fontFamily" class="select select-bordered select-sm w-full font-mono text-[11px]">
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
                        <label class="label"><span class="label-text text-[10px] uppercase">Size</span></label>
                        <UnitInput v-model="settings.style.fontSize" placeholder="16px" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Line Height</span></label>
                        <UnitInput v-model="settings.style.lineHeight" placeholder="1.5" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Letter Spacing</span></label>
                        <UnitInput v-model="settings.style.letterSpacing" placeholder="0px" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Transform</span></label>
                        <select v-model="settings.style.textTransform" class="select select-bordered select-sm w-full">
                            <option :value="undefined">None</option>
                            <option value="uppercase">Uppercase</option>
                            <option value="lowercase">Lowercase</option>
                            <option value="capitalize">Capitalize</option>
                        </select>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Weight</span></label>
                    <div class="join w-full">
                        <button v-for="w in [['300','L'],['normal','R'],['500','M'],['bold','B'],['900','H']]" 
                            :key="w[0]"
                            @click="settings.style.fontWeight = w[0]" 
                            class="btn btn-sm join-item flex-1" 
                            :class="((!settings.style.fontWeight && w[0] === 'normal') || settings.style.fontWeight === w[0]) ? 'btn-primary' : 'bg-base-300 border-white/5'">
                            {{ w[1] }}
                        </button>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Alignment</span></label>
                    <div class="join w-full">
                        <button @click="settings.style.textAlign = 'left'" class="btn btn-sm join-item flex-1" :class="settings.style.textAlign === 'left' ? 'btn-primary' : 'bg-base-300 border-white/5'"><PhTextAlignLeft weight="bold" class="w-4 h-4" /></button>
                        <button @click="settings.style.textAlign = 'center'" class="btn btn-sm join-item flex-1" :class="settings.style.textAlign === 'center' ? 'btn-primary' : 'bg-base-300 border-white/5'"><PhTextAlignCenter weight="bold" class="w-4 h-4" /></button>
                        <button @click="settings.style.textAlign = 'right'" class="btn btn-sm join-item flex-1" :class="settings.style.textAlign === 'right' ? 'btn-primary' : 'bg-base-300 border-white/5'"><PhTextAlignRight weight="bold" class="w-4 h-4" /></button>
                        <button @click="settings.style.textAlign = 'justify'" class="btn btn-sm join-item flex-1" :class="settings.style.textAlign === 'justify' ? 'btn-primary' : 'bg-base-300 border-white/5'"><PhTextAlignJustify weight="bold" class="w-4 h-4" /></button>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 3. Background & Effects -->
        <AdminCollapse title="Background & Effects" icon="PhPalette" persistKey="style_background">
            <div class="space-y-6 pt-1">
                <div class="form-control">
                    <FillControl v-model="backgroundFill" label="Background" />
                </div>
                
                <div v-if="backgroundFill?.type === 'image'" class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Attachment</span></label>
                    <select v-model="settings.style.backgroundAttachment" class="select select-bordered select-sm w-full">
                        <option value="scroll">Scroll (Default)</option>
                        <option value="fixed">Fixed (Parallax)</option>
                    </select>
                </div>

                <div class="divider my-0 opacity-10"></div>

                <div class="form-control">
                    <label class="label flex justify-between">
                        <span class="label-text text-[10px] uppercase font-bold opacity-50">Opacity</span>
                        <span class="text-[10px] font-mono">{{ Math.round((settings.style.opacity ?? 1) * 100) }}%</span>
                    </label>
                    <input type="range" v-model.number="settings.style.opacity" min="0" max="1" step="0.01" class="range range-primary range-xs" />
                </div>

                <!-- Filters -->
                <div class="space-y-4 bg-base-300/30 p-4 rounded-xl border border-white/5">
                    <div class="text-[10px] uppercase font-bold opacity-40 border-b border-white/10 pb-2 flex items-center gap-2">
                        <PhSelectionBackground class="w-3 h-3" /> Filters
                    </div>
                    
                    <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                        <div v-for="f in [['blur','Blur','px',20], ['brightness','Bright','%',200], ['contrast','Contrast','%',200], ['saturate','Saturate','%',200]]" :key="f[0]" class="space-y-1">
                            <label class="flex justify-between items-center text-[9px] uppercase font-bold opacity-50">
                                <span>{{ f[1] }}</span>
                                <span class="font-mono">{{ settings.style[f[0]] ?? (f[0] === 'blur' ? 0 : 100) }}{{ f[2] }}</span>
                            </label>
                            <input type="range" v-model.number="settings.style[f[0]]" 
                                :min="0" :max="f[3]" :step="f[0] === 'blur' ? 1 : 5" 
                                class="range range-xs" :class="settings.style[f[0]] !== undefined ? 'range-primary' : 'opacity-30'" />
                        </div>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Shadow</span></label>
                    <input type="text" v-model="settings.style.boxShadow" placeholder="e.g. 0 10px 30px rgba(0,0,0,0.1)" class="input input-sm input-bordered w-full font-mono text-[10px]" />
                </div>
            </div>
        </AdminCollapse>

        <!-- 4. Borders -->
        <AdminCollapse title="Borders" icon="PhSquareHalf" persistKey="style_borders">
            <div class="space-y-6 pt-1">
                <LinkedUnitInput 
                    v-model:top="settings.style.borderTopLeftRadius"
                    v-model:right="settings.style.borderTopRightRadius"
                    v-model:bottom="settings.style.borderBottomRightRadius"
                    v-model:left="settings.style.borderBottomLeftRadius"
                    label="Radius" 
                />
                <div class="divider my-0 opacity-10"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control col-span-2">
                        <LinkedUnitInput 
                            v-model:top="settings.style.borderTopWidth"
                            v-model:right="settings.style.borderRightWidth"
                            v-model:bottom="settings.style.borderBottomWidth"
                            v-model:left="settings.style.borderLeftWidth"
                            label="Width" 
                        />
                    </div>
                    <div class="form-control col-span-2">
                        <label class="label"><span class="label-text text-[10px] uppercase">Style</span></label>
                        <select v-model="settings.style.borderStyle" class="select select-bordered select-sm w-full">
                            <option :value="undefined">Default</option>
                            <option value="solid">Solid</option>
                            <option value="dashed">Dashed</option>
                            <option value="dotted">Dotted</option>
                            <option value="double">Double</option>
                        </select>
                    </div>
                </div>
                <div class="form-control w-full">
                    <FillControl v-model="borderFill" label="Border Color" />
                </div>
            </div>
        </AdminCollapse>

        <!-- 5. Spacing -->
        <AdminCollapse title="Spacing" icon="PhFrameCorners" persistKey="style_spacing">
            <div class="space-y-6 pt-1">
                <LinkedUnitInput 
                    v-model:top="settings.style.marginTop"
                    v-model:right="settings.style.marginRight"
                    v-model:bottom="settings.style.marginBottom"
                    v-model:left="settings.style.marginLeft"
                    label="Margin" 
                />
                <div class="divider my-0 opacity-10"></div>
                <LinkedUnitInput 
                    v-model:top="settings.style.paddingTop"
                    v-model:right="settings.style.paddingRight"
                    v-model:bottom="settings.style.paddingBottom"
                    v-model:left="settings.style.paddingLeft"
                    label="Padding" 
                />
            </div>
        </AdminCollapse>

        <!-- 6. Advanced -->
        <AdminCollapse title="Advanced" icon="PhSlidersHorizontal" persistKey="style_advanced">
            <div class="space-y-4 pt-1">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Overflow</span></label>
                    <select v-model="settings.style.overflow" class="select select-bordered select-sm w-full">
                        <option :value="undefined">Default</option>
                        <option value="visible">Visible</option>
                        <option value="hidden">Hidden</option>
                        <option value="auto">Auto</option>
                        <option value="scroll">Scroll</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">HTML ID</span></label>
                    <input type="text" v-model="settings.style.htmlId" placeholder="element-id" class="input input-sm input-bordered w-full font-mono" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Custom CSS Class</span></label>
                    <input type="text" v-model="settings.style.customClass" placeholder="my-custom-class" class="input input-sm input-bordered w-full font-mono" />
                </div>
            </div>
        </AdminCollapse>

        <div class="form-control px-2 pt-4">
            <button @click="settings.style = {}" class="btn btn-outline btn-error btn-xs w-full opacity-50 hover:opacity-100 transition-opacity">
                <PhTrash class="w-3 h-3 mr-1" /> Reset All Styles
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import AdminCollapse from '@/Components/AdminCollapse.vue';
import FillControl from '@/Components/FillControl.vue';
import LinkedUnitInput from '@/Components/LinkedUnitInput.vue';
import UnitInput from '@/Components/UnitInput.vue';
import { 
    PhTextAlignLeft, PhTextAlignCenter, PhTextAlignRight, PhTextAlignJustify, 
    PhPalette, PhFrameCorners, PhTextT, PhSquareHalf, 
    PhArrowUp, PhArrowDown, PhArrowLeft, PhArrowRight,
    PhBoundingBox, PhSelectionBackground, PhSlidersHorizontal, PhTrash,
    PhLayout, PhSquaresFour, PhColumns, PhRows, PhArrowUDownRight, PhListDashes
} from '@phosphor-icons/vue';

const props = defineProps({
    settings: {
        type: Object,
        required: true
    },
    blockType: String
});

const createFillProxy = (newProp, legacyProp) => computed({
    get: () => {
        if (!props.settings || !props.settings.style) return undefined;
        if (props.settings.style[newProp] !== undefined) return props.settings.style[newProp];
        return props.settings.style[legacyProp];
    },
    set: (val) => {
        if (props.settings && props.settings.style) {
            props.settings.style[legacyProp] = undefined;
            props.settings.style[newProp] = val;
        }
    }
});

const backgroundFill = createFillProxy('backgroundFill', 'backgroundColor');
const textFill = createFillProxy('textFill', 'textColor');
const borderFill = createFillProxy('borderFill', 'borderColor');

const toggleOffset = (direction) => {
    if (!props.settings || !props.settings.style) return;
    const style = props.settings.style;
    
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
