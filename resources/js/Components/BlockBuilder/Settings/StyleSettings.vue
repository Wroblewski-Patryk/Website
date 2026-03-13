<template>
    <div class="space-y-6">
        <!-- 1. Layout -->
        <AdminCollapse :title="t('admin.theme.layout', 'Layout')" icon="PhLayout" persistKey="style_layout">
            <div class="space-y-4 pt-1">
                <!-- Display -->
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-[10px] uppercase font-bold opacity-50">Display</span></label>
                    <select v-model="modelValue.display" class="select select-bordered select-sm w-full">
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
                <div v-if="modelValue.display === 'flex'" class="p-3 bg-base-300/30 rounded-xl space-y-4 border border-white/5 shadow-inner animate-in fade-in slide-in-from-top-1 duration-200">
                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Direction</span></label>
                        <div class="join w-full">
                            <button @click="modelValue.flexDirection = 'row'" class="btn btn-xs join-item flex-1" :class="modelValue.flexDirection === 'row' ? 'btn-primary' : 'bg-base-200'" title="Row"><PhColumns class="w-3 h-3" /></button>
                            <button @click="modelValue.flexDirection = 'column'" class="btn btn-xs join-item flex-1" :class="modelValue.flexDirection === 'column' ? 'btn-primary' : 'bg-base-200'" title="Column"><PhRows class="w-3 h-3" /></button>
                            <button @click="modelValue.flexDirection = 'row-reverse'" class="btn btn-xs join-item flex-1" :class="modelValue.flexDirection === 'row-reverse' ? 'btn-primary' : 'bg-base-200'" title="Row Reverse"><i class="fas fa-right-left text-[10px]"></i></button>
                            <button @click="modelValue.flexDirection = 'column-reverse'" class="btn btn-xs join-item flex-1" :class="modelValue.flexDirection === 'column-reverse' ? 'btn-primary' : 'bg-base-200'" title="Column Reverse"><i class="fas fa-up-down text-[10px]"></i></button>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1 flex justify-between items-center cursor-pointer">
                            <span class="text-[9px] uppercase font-bold opacity-40">Wrap Content</span>
                            <input type="checkbox" :checked="modelValue.flexWrap === 'wrap'" @change="modelValue.flexWrap = $event.target.checked ? 'wrap' : 'nowrap'" class="toggle toggle-xs toggle-primary" />
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Justify</span></label>
                        <div class="grid grid-cols-6 gap-1">
                            <button v-for="j in ['flex-start', 'center', 'flex-end', 'space-between', 'space-around', 'space-evenly']" :key="j"
                                @click="modelValue.justifyContent = j" class="btn btn-xs px-0 bg-base-200" :class="modelValue.justifyContent === j ? 'btn-primary' : ''" :title="j">
                                <span class="text-[8px] uppercase font-bold">{{ j.split('-').pop()[0] }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Align</span></label>
                        <div class="grid grid-cols-5 gap-1">
                            <button v-for="a in ['flex-start', 'center', 'flex-end', 'baseline', 'stretch']" :key="a"
                                @click="modelValue.alignItems = a" class="btn btn-xs px-0 bg-base-200" :class="modelValue.alignItems === a ? 'btn-primary' : ''" :title="a">
                                <span class="text-[8px] uppercase font-bold">{{ a.split('-').pop()[0] }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Gap</span></label>
                            <UnitInput v-model="modelValue.gap" placeholder="0px" />
                        </div>
                    </div>
                </div>

                <!-- Grid Settings -->
                <div v-if="modelValue.display === 'grid'" class="p-3 bg-base-300/30 rounded-xl space-y-4 border border-white/5 shadow-inner animate-in fade-in slide-in-from-top-1 duration-200">
                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Columns</span></label>
                        <input type="text" v-model="modelValue.gridTemplateColumns" placeholder="e.g. 1fr 1fr" class="input input-xs input-bordered w-full font-mono text-[10px]" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Column Gap</span></label>
                            <UnitInput v-model="modelValue.columnGap" placeholder="0px" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Row Gap</span></label>
                            <UnitInput v-model="modelValue.rowGap" placeholder="0px" />
                        </div>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 2. Sizing & Positioning -->
        <AdminCollapse :title="t('admin.builder.tab_sizing', 'Sizing & Position')" icon="PhBoundingBox" persistKey="style_sizing">
            <div class="space-y-6 pt-1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Z-Index</span></label>
                        <input type="number" v-model="modelValue.zIndex" class="input input-sm input-bordered w-full" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Aspect Ratio</span></label>
                        <select v-model="modelValue.aspectRatio" class="select select-bordered select-sm w-full">
                            <option :value="undefined">Auto</option>
                            <option value="1/1">1:1 Square</option>
                            <option value="16/9">16:9 Video</option>
                            <option value="4/3">4:3 Photo</option>
                            <option value="21/9">21:9 Ultra</option>
                        </select>
                    </div>
                </div>

                <!-- Width -->
                <div class="space-y-1">
                    <label class="label pb-0"><span class="label-text text-[10px] uppercase font-bold opacity-50">Width</span></label>
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <div class="form-control col-span-2">
                            <UnitInput v-model="modelValue.width" :placeholder="t('admin.builder.style_width', 'Width') + ' (auto)'" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase opacity-40">Min</span></label>
                            <UnitInput v-model="modelValue.minWidth" placeholder="none" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase opacity-40">Max</span></label>
                            <UnitInput v-model="modelValue.maxWidth" placeholder="none" />
                        </div>
                    </div>
                </div>

                <!-- Height -->
                <div class="space-y-1">
                    <label class="label pb-0"><span class="label-text text-[10px] uppercase font-bold opacity-50">Height</span></label>
                    <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                        <div class="form-control col-span-2">
                            <UnitInput v-model="modelValue.height" :placeholder="t('admin.builder.style_height', 'Height') + ' (auto)'" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase opacity-40">Min</span></label>
                            <UnitInput v-model="modelValue.minHeight" placeholder="none" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase opacity-40">Max</span></label>
                            <UnitInput v-model="modelValue.maxHeight" placeholder="none" />
                        </div>
                    </div>
                </div>

                <div class="divider my-0 opacity-10"></div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Positioning</span></label>
                    <select v-model="modelValue.position" class="select select-bordered select-sm w-full">
                        <option :value="undefined">Static</option>
                        <option value="relative">Relative</option>
                        <option value="absolute">Absolute</option>
                        <option value="fixed">Fixed</option>
                        <option value="sticky">Sticky</option>
                    </select>
                </div>

                <!-- Offsets -->
                <div v-if="['absolute', 'fixed', 'sticky', 'relative'].includes(modelValue.position)" class="bg-base-300/30 p-3 rounded-xl space-y-3 border border-white/5 shadow-inner">
                    <div class="flex flex-col gap-3">
                        <div class="flex justify-center gap-4">
                            <!-- Y Axis Toggle -->
                            <div class="join border border-white/10 overflow-hidden shadow-lg">
                                <button @click="toggleOffset('top')" class="btn btn-xs join-item h-7 w-8" :class="modelValue.top !== undefined ? 'btn-primary' : 'bg-base-200 opacity-50'" title="Top"><PhArrowUp weight="bold" class="w-3 h-3" /></button>
                                <button @click="toggleOffset('bottom')" class="btn btn-xs join-item h-7 w-8" :class="modelValue.bottom !== undefined ? 'btn-primary' : 'bg-base-200 opacity-50'" title="Bottom"><PhArrowDown weight="bold" class="w-3 h-3" /></button>
                            </div>
                            <!-- X Axis Toggle -->
                            <div class="join border border-white/10 overflow-hidden shadow-lg">
                                <button @click="toggleOffset('left')" class="btn btn-xs join-item h-7 w-8" :class="modelValue.left !== undefined ? 'btn-primary' : 'bg-base-200 opacity-50'" title="Left"><PhArrowLeft weight="bold" class="w-3 h-3" /></button>
                                <button @click="toggleOffset('right')" class="btn btn-xs join-item h-7 w-8" :class="modelValue.right !== undefined ? 'btn-primary' : 'bg-base-200 opacity-50'" title="Right"><PhArrowRight weight="bold" class="w-3 h-3" /></button>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div v-if="modelValue.top !== undefined" class="form-control">
                                <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Top</span></label>
                                <UnitInput v-model="modelValue.top" />
                            </div>
                            <div v-if="modelValue.bottom !== undefined" class="form-control">
                                <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Bottom</span></label>
                                <UnitInput v-model="modelValue.bottom" />
                            </div>
                            <div v-if="modelValue.left !== undefined" class="form-control">
                                <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Left</span></label>
                                <UnitInput v-model="modelValue.left" />
                            </div>
                            <div v-if="modelValue.right !== undefined" class="form-control">
                                <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Right</span></label>
                                <UnitInput v-model="modelValue.right" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 2. Typography -->
        <AdminCollapse :title="t('admin.theme.typography', 'Typography')" icon="PhTextT" persistKey="style_typography" v-if="!['spacer', 'divider', 'image', 'video', 'gallery', 'carousel'].includes(blockType)">
            <div class="space-y-4 pt-1">
                <div class="form-control">
                    <FillControl v-model="textFill" :label="t('admin.builder.style_text_color', 'Text Color')" />
                </div>

                <div class="divider my-0 opacity-10"></div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Font Family</span></label>
                    <select v-model="modelValue.fontFamily" class="select select-bordered select-sm w-full font-mono text-[11px]">
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
                        <UnitInput v-model="modelValue.fontSize" placeholder="16px" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Line Height</span></label>
                        <UnitInput v-model="modelValue.lineHeight" placeholder="1.5" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Letter Spacing</span></label>
                        <UnitInput v-model="modelValue.letterSpacing" placeholder="0px" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Transform</span></label>
                        <select v-model="modelValue.textTransform" class="select select-bordered select-sm w-full">
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
                            @click="modelValue.fontWeight = w[0]" 
                            class="btn btn-sm join-item flex-1" 
                            :class="((!modelValue.fontWeight && w[0] === 'normal') || modelValue.fontWeight === w[0]) ? 'btn-primary' : 'bg-base-300 border-white/5'">
                            {{ w[1] }}
                        </button>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Alignment</span></label>
                    <div class="join w-full">
                        <button @click="modelValue.textAlign = 'left'" class="btn btn-sm join-item flex-1" :class="modelValue.textAlign === 'left' ? 'btn-primary' : 'bg-base-300 border-white/5'"><PhTextAlignLeft weight="bold" class="w-4 h-4" /></button>
                        <button @click="modelValue.textAlign = 'center'" class="btn btn-sm join-item flex-1" :class="modelValue.textAlign === 'center' ? 'btn-primary' : 'bg-base-300 border-white/5'"><PhTextAlignCenter weight="bold" class="w-4 h-4" /></button>
                        <button @click="modelValue.textAlign = 'right'" class="btn btn-sm join-item flex-1" :class="modelValue.textAlign === 'right' ? 'btn-primary' : 'bg-base-300 border-white/5'"><PhTextAlignRight weight="bold" class="w-4 h-4" /></button>
                        <button @click="modelValue.textAlign = 'justify'" class="btn btn-sm join-item flex-1" :class="modelValue.textAlign === 'justify' ? 'btn-primary' : 'bg-base-300 border-white/5'"><PhTextAlignJustify weight="bold" class="w-4 h-4" /></button>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 3. Background & Effects -->
        <AdminCollapse :title="t('admin.builder.style_background', 'Background & Effects')" icon="PhPalette" persistKey="style_background">
            <div class="space-y-6 pt-1">
                <div class="form-control">
                    <FillControl v-model="backgroundFill" :label="t('admin.builder.style_background', 'Background')" />
                </div>
                
                <div v-if="backgroundFill?.type === 'image'" class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Attachment</span></label>
                    <select v-model="modelValue.backgroundAttachment" class="select select-bordered select-sm w-full">
                        <option value="scroll">Scroll (Default)</option>
                        <option value="fixed">Fixed (Parallax)</option>
                    </select>
                </div>

                <div class="divider my-0 opacity-10"></div>

                <div class="form-control">
                    <label class="label flex justify-between">
                        <span class="label-text text-[10px] uppercase font-bold opacity-50">Opacity</span>
                        <span class="text-[10px] font-mono">{{ Math.round((modelValue.opacity ?? 1) * 100) }}%</span>
                    </label>
                    <input type="range" v-model.number="modelValue.opacity" min="0" max="1" step="0.01" class="range range-primary range-xs" />
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
                                <span class="font-mono">{{ modelValue[f[0]] ?? (f[0] === 'blur' ? 0 : 100) }}{{ f[2] }}</span>
                            </label>
                            <input type="range" v-model.number="modelValue[f[0]]" 
                                :min="0" :max="f[3]" :step="f[0] === 'blur' ? 1 : 5" 
                                class="range range-xs" :class="modelValue[f[0]] !== undefined ? 'range-primary' : 'opacity-30'" />
                        </div>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Shadow</span></label>
                    <input type="text" v-model="modelValue.boxShadow" placeholder="e.g. 0 10px 30px rgba(0,0,0,0.1)" class="input input-sm input-bordered w-full font-mono text-[10px]" />
                </div>
            </div>
        </AdminCollapse>

        <!-- 4. Borders -->
        <AdminCollapse :title="t('admin.builder.tab_borders', 'Borders')" icon="PhSquareHalf" persistKey="style_borders">
            <div class="space-y-6 pt-1">
                <LinkedUnitInput 
                    v-model:top="modelValue.borderTopLeftRadius"
                    v-model:right="modelValue.borderTopRightRadius"
                    v-model:bottom="modelValue.borderBottomRightRadius"
                    v-model:left="modelValue.borderBottomLeftRadius"
                    :label="t('admin.builder.style_radius', 'Radius')" 
                />
                <div class="divider my-0 opacity-10"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control col-span-2">
                        <LinkedUnitInput 
                            v-model:top="modelValue.borderTopWidth"
                            v-model:right="modelValue.borderRightWidth"
                            v-model:bottom="modelValue.borderBottomWidth"
                            v-model:left="modelValue.borderLeftWidth"
                            :label="t('admin.builder.style_width', 'Width')" 
                        />
                    </div>
                    <div class="form-control col-span-2">
                        <label class="label"><span class="label-text text-[10px] uppercase">Style</span></label>
                        <select v-model="modelValue.borderStyle" class="select select-bordered select-sm w-full">
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
        <AdminCollapse :title="t('admin.theme.spacing', 'Spacing')" icon="PhFrameCorners" persistKey="style_spacing">
            <div class="space-y-6 pt-1">
                <LinkedUnitInput 
                    v-model:top="modelValue.marginTop"
                    v-model:right="modelValue.marginRight"
                    v-model:bottom="modelValue.marginBottom"
                    v-model:left="modelValue.marginLeft"
                    :label="t('admin.builder.style_margin', 'Margin')" 
                />
                <div class="divider my-0 opacity-10"></div>
                <LinkedUnitInput 
                    v-model:top="modelValue.paddingTop"
                    v-model:right="modelValue.paddingRight"
                    v-model:bottom="modelValue.paddingBottom"
                    v-model:left="modelValue.paddingLeft"
                    :label="t('admin.builder.style_padding', 'Padding')" 
                />
            </div>
        </AdminCollapse>
 
        <!-- 6. Advanced -->
        <AdminCollapse :title="t('admin.builder.tab_advanced', 'Advanced')" icon="PhSlidersHorizontal" persistKey="style_advanced">
            <div class="space-y-4 pt-1">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Overflow</span></label>
                    <select v-model="modelValue.overflow" class="select select-bordered select-sm w-full">
                        <option :value="undefined">Default</option>
                        <option value="visible">Visible</option>
                        <option value="hidden">Hidden</option>
                        <option value="auto">Auto</option>
                        <option value="scroll">Scroll</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">HTML ID</span></label>
                    <input type="text" v-model="modelValue.htmlId" placeholder="element-id" class="input input-sm input-bordered w-full font-mono" />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Custom CSS Class</span></label>
                    <input type="text" v-model="modelValue.customClass" placeholder="my-custom-class" class="input input-sm input-bordered w-full font-mono" />
                </div>
            </div>
        </AdminCollapse>

        <div class="form-control px-2 pt-4">
            <button @click="$emit('reset')" class="btn btn-outline btn-error btn-xs w-full opacity-50 hover:opacity-100 transition-opacity">
                <PhTrash class="w-3 h-3 mr-1" /> {{ t('admin.builder.action_reset_styles', 'Reset All Styles') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';
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
    modelValue: {
        type: Object,
        required: true
    },
    blockType: String
});

const emit = defineEmits(['update:modelValue', 'reset']);

const { t } = useTranslations();

const createFillProxy = (legacyProp, newProp) => computed({
    get: () => {
        if (!props.modelValue) return undefined;
        if (props.modelValue[newProp] !== undefined) return props.modelValue[newProp];
        return props.modelValue[legacyProp];
    },
    set: (val) => {
        if (props.modelValue) {
            const newModelValue = { ...props.modelValue };
            newModelValue[legacyProp] = undefined;
            newModelValue[newProp] = val;
            emit('update:modelValue', newModelValue);
        }
    }
});

const backgroundFill = createFillProxy('backgroundColor', 'backgroundFill');
const textFill = createFillProxy('textColor', 'textFill');
const borderFill = createFillProxy('borderColor', 'borderFill');

const toggleOffset = (direction) => {
    if (!props.modelValue) return;
    const newModelValue = { ...props.modelValue };
    
    if (direction === 'top') {
        const val = newModelValue.bottom ?? newModelValue.top ?? '0px';
        newModelValue.bottom = undefined;
        newModelValue.top = val;
    } else if (direction === 'bottom') {
        const val = newModelValue.top ?? newModelValue.bottom ?? '0px';
        newModelValue.top = undefined;
        newModelValue.bottom = val;
    } else if (direction === 'left') {
        const val = newModelValue.right ?? newModelValue.left ?? '0px';
        newModelValue.right = undefined;
        newModelValue.left = val;
    } else if (direction === 'right') {
        const val = newModelValue.left ?? newModelValue.right ?? '0px';
        newModelValue.left = undefined;
        newModelValue.right = val;
    }
    emit('update:modelValue', newModelValue);
};
</script>
