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
                <div v-if="modelValue.display === 'flex'" class="space-y-4">
                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Direction</span></label>
                        <div class="join w-full bg-base-200 rounded-md">
                            <button @click="modelValue.flexDirection = 'row'" class="btn btn-sm join-item min-h-0 h-8 flex-1 border-none" :class="modelValue.flexDirection === 'row' ? 'btn-primary' : 'bg-transparent opacity-60 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'" title="Row"><PhArrowRight class="w-4 h-4" /></button>
                            <button @click="modelValue.flexDirection = 'column'" class="btn btn-sm join-item min-h-0 h-8 flex-1 border-none" :class="modelValue.flexDirection === 'column' ? 'btn-primary' : 'bg-transparent opacity-60 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'" title="Column"><PhArrowDown class="w-4 h-4" /></button>
                            <button @click="modelValue.flexDirection = 'row-reverse'" class="btn btn-sm join-item min-h-0 h-8 flex-1 border-none" :class="modelValue.flexDirection === 'row-reverse' ? 'btn-primary' : 'bg-transparent opacity-60 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'" title="Row Reverse"><PhArrowLeft class="w-4 h-4" /></button>
                            <button @click="modelValue.flexDirection = 'column-reverse'" class="btn btn-sm join-item min-h-0 h-8 flex-1 border-none" :class="modelValue.flexDirection === 'column-reverse' ? 'btn-primary' : 'bg-transparent opacity-60 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'" title="Column Reverse"><PhArrowUp class="w-4 h-4" /></button>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1 flex justify-between items-center cursor-pointer">
                            <span class="text-[9px] uppercase font-bold opacity-40">Wrap Content</span>
                            <input type="checkbox" :checked="modelValue.flexWrap === 'wrap'" @change="modelValue.flexWrap = $event.target.checked ? 'wrap' : 'nowrap'" class="toggle toggle-sm toggle-primary" />
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Justify</span></label>
                        <div class="grid grid-cols-6 gap-1">
                            <button @click="modelValue.justifyContent = 'flex-start'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'flex-start' ? 'btn-primary' : 'bg-base-200'" title="Start"><PhAlignLeftSimple class="w-3 h-3" /></button>
                            <button @click="modelValue.justifyContent = 'center'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'center' ? 'btn-primary' : 'bg-base-200'" title="Center"><PhAlignCenterHorizontalSimple class="w-3 h-3" /></button>
                            <button @click="modelValue.justifyContent = 'flex-end'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'flex-end' ? 'btn-primary' : 'bg-base-200'" title="End"><PhAlignRightSimple class="w-3 h-3" /></button>
                            <button @click="modelValue.justifyContent = 'space-between'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'space-between' ? 'btn-primary' : 'bg-base-200'" title="Space Between"><PhArrowsOutLineHorizontal class="w-3 h-3" /></button>
                            <button @click="modelValue.justifyContent = 'space-around'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'space-around' ? 'btn-primary' : 'bg-base-200'" title="Space Around"><PhArrowsHorizontal class="w-3 h-3" /></button>
                            <button @click="modelValue.justifyContent = 'space-evenly'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'space-evenly' ? 'btn-primary' : 'bg-base-200'" title="Space Evenly"><PhDotsThreeOutline class="w-3 h-3" /></button>
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Align</span></label>
                        <div class="grid grid-cols-5 gap-1">
                            <button @click="modelValue.alignItems = 'flex-start'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'flex-start' ? 'btn-primary' : 'bg-base-200'" title="Start"><PhAlignTopSimple class="w-3 h-3" /></button>
                            <button @click="modelValue.alignItems = 'center'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'center' ? 'btn-primary' : 'bg-base-200'" title="Center"><PhAlignCenterVerticalSimple class="w-3 h-3" /></button>
                            <button @click="modelValue.alignItems = 'flex-end'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'flex-end' ? 'btn-primary' : 'bg-base-200'" title="End"><PhAlignBottomSimple class="w-3 h-3" /></button>
                            <button @click="modelValue.alignItems = 'baseline'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'baseline' ? 'btn-primary' : 'bg-base-200'" title="Baseline"><PhTextT class="w-3 h-3" /></button>
                            <button @click="modelValue.alignItems = 'stretch'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'stretch' ? 'btn-primary' : 'bg-base-200'" title="Stretch"><PhArrowsOutLineVertical class="w-3 h-3" /></button>
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
                <div v-if="modelValue.display === 'grid'" class="space-y-4">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="form-control col-span-2">
                            <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Template Columns</span></label>
                            <input type="text" v-model="modelValue.gridTemplateColumns" placeholder="e.g. repeat(3, 1fr)" class="input input-sm input-bordered w-full font-mono text-[10px]" />
                        </div>
                        <div class="form-control col-span-2">
                            <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Template Rows</span></label>
                            <input type="text" v-model="modelValue.gridTemplateRows" placeholder="e.g. auto 1fr auto" class="input input-sm input-bordered w-full font-mono text-[10px]" />
                        </div>
                        <div class="form-control col-span-2">
                            <label class="label py-1"><span class="text-[9px] uppercase font-bold opacity-40">Auto Flow</span></label>
                            <div class="grid grid-cols-5 gap-1">
                                <button @click="modelValue.gridAutoFlow = undefined" class="btn btn-sm px-0" :class="modelValue.gridAutoFlow === undefined ? 'btn-primary' : 'bg-base-200'" title="Default"><PhCircleDashed class="w-3 h-3" /></button>
                                <button @click="modelValue.gridAutoFlow = 'row'" class="btn btn-sm px-0" :class="modelValue.gridAutoFlow === 'row' ? 'btn-primary' : 'bg-base-200'" title="Row">
                                    <PhRows class="w-3 h-3" />
                                </button>
                                <button @click="modelValue.gridAutoFlow = 'column'" class="btn btn-sm px-0" :class="modelValue.gridAutoFlow === 'column' ? 'btn-primary' : 'bg-base-200'" title="Column">
                                    <PhColumns class="w-3 h-3" />
                                </button>
                                <button @click="modelValue.gridAutoFlow = 'row dense'" class="btn btn-sm px-0" :class="modelValue.gridAutoFlow === 'row dense' ? 'btn-primary' : 'bg-base-200'" title="Row Dense"><PhArrowsInLineHorizontal class="w-3 h-3" /></button>
                                <button @click="modelValue.gridAutoFlow = 'column dense'" class="btn btn-sm px-0" :class="modelValue.gridAutoFlow === 'column dense' ? 'btn-primary' : 'bg-base-200'" title="Column Dense"><PhArrowsInLineVertical class="w-3 h-3" /></button>
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Auto Columns</span></label>
                            <input type="text" v-model="modelValue.gridAutoColumns" placeholder="auto" class="input input-sm input-bordered w-full font-mono text-[10px]" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Auto Rows</span></label>
                            <input type="text" v-model="modelValue.gridAutoRows" placeholder="auto" class="input input-sm input-bordered w-full font-mono text-[10px]" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Gap</span></label>
                            <UnitInput v-model="modelValue.gap" placeholder="0px" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Column Gap</span></label>
                            <UnitInput v-model="modelValue.columnGap" placeholder="0px" />
                        </div>
                        <div class="form-control col-span-2">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Row Gap</span></label>
                            <UnitInput v-model="modelValue.rowGap" placeholder="0px" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Justify Items</span></label>
                            <div class="grid grid-cols-5 gap-1">
                                <button @click="modelValue.justifyItems = undefined" class="btn btn-sm px-0" :class="modelValue.justifyItems === undefined ? 'btn-primary' : 'bg-base-200'" title="Default"><PhCircleDashed class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyItems = 'start'" class="btn btn-sm px-0" :class="modelValue.justifyItems === 'start' ? 'btn-primary' : 'bg-base-200'" title="Start"><PhAlignLeftSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyItems = 'center'" class="btn btn-sm px-0" :class="modelValue.justifyItems === 'center' ? 'btn-primary' : 'bg-base-200'" title="Center"><PhAlignCenterHorizontalSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyItems = 'end'" class="btn btn-sm px-0" :class="modelValue.justifyItems === 'end' ? 'btn-primary' : 'bg-base-200'" title="End"><PhAlignRightSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyItems = 'stretch'" class="btn btn-sm px-0" :class="modelValue.justifyItems === 'stretch' ? 'btn-primary' : 'bg-base-200'" title="Stretch"><PhArrowsOutLineHorizontal class="w-3 h-3" /></button>
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Align Items</span></label>
                            <div class="grid grid-cols-5 gap-1">
                                <button @click="modelValue.alignItems = undefined" class="btn btn-sm px-0" :class="modelValue.alignItems === undefined ? 'btn-primary' : 'bg-base-200'" title="Default"><PhCircleDashed class="w-3 h-3" /></button>
                                <button @click="modelValue.alignItems = 'start'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'start' ? 'btn-primary' : 'bg-base-200'" title="Start"><PhAlignTopSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.alignItems = 'center'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'center' ? 'btn-primary' : 'bg-base-200'" title="Center"><PhAlignCenterVerticalSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.alignItems = 'end'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'end' ? 'btn-primary' : 'bg-base-200'" title="End"><PhAlignBottomSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.alignItems = 'stretch'" class="btn btn-sm px-0" :class="modelValue.alignItems === 'stretch' ? 'btn-primary' : 'bg-base-200'" title="Stretch"><PhArrowsOutLineVertical class="w-3 h-3" /></button>
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Justify Content</span></label>
                            <div class="grid grid-cols-4 gap-1">
                                <button @click="modelValue.justifyContent = undefined" class="btn btn-sm px-0" :class="modelValue.justifyContent === undefined ? 'btn-primary' : 'bg-base-200'" title="Default"><PhCircleDashed class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyContent = 'start'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'start' ? 'btn-primary' : 'bg-base-200'" title="Start"><PhAlignLeftSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyContent = 'center'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'center' ? 'btn-primary' : 'bg-base-200'" title="Center"><PhAlignCenterHorizontalSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyContent = 'end'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'end' ? 'btn-primary' : 'bg-base-200'" title="End"><PhAlignRightSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyContent = 'space-between'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'space-between' ? 'btn-primary' : 'bg-base-200'" title="Space Between"><PhArrowsOutLineHorizontal class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyContent = 'space-around'" class="btn btn-sm px-0" :class="modelValue.justifyContent === 'space-around' ? 'btn-primary' : 'bg-base-200'" title="Space Around"><PhArrowsHorizontal class="w-3 h-3" /></button>
                                <button @click="modelValue.justifyContent = 'space-evenly'" class="btn btn-sm px-0 col-span-2" :class="modelValue.justifyContent === 'space-evenly' ? 'btn-primary' : 'bg-base-200'" title="Space Evenly"><PhDotsThreeOutline class="w-3 h-3" /></button>
                            </div>
                        </div>
                        <div class="form-control">
                            <label class="label py-0.5"><span class="text-[8px] uppercase font-bold opacity-40">Align Content</span></label>
                            <div class="grid grid-cols-4 gap-1">
                                <button @click="modelValue.alignContent = undefined" class="btn btn-sm px-0" :class="modelValue.alignContent === undefined ? 'btn-primary' : 'bg-base-200'" title="Default"><PhCircleDashed class="w-3 h-3" /></button>
                                <button @click="modelValue.alignContent = 'start'" class="btn btn-sm px-0" :class="modelValue.alignContent === 'start' ? 'btn-primary' : 'bg-base-200'" title="Start"><PhAlignTopSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.alignContent = 'center'" class="btn btn-sm px-0" :class="modelValue.alignContent === 'center' ? 'btn-primary' : 'bg-base-200'" title="Center"><PhAlignCenterVerticalSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.alignContent = 'end'" class="btn btn-sm px-0" :class="modelValue.alignContent === 'end' ? 'btn-primary' : 'bg-base-200'" title="End"><PhAlignBottomSimple class="w-3 h-3" /></button>
                                <button @click="modelValue.alignContent = 'stretch'" class="btn btn-sm px-0" :class="modelValue.alignContent === 'stretch' ? 'btn-primary' : 'bg-base-200'" title="Stretch"><PhArrowsOutLineVertical class="w-3 h-3" /></button>
                                <button @click="modelValue.alignContent = 'space-between'" class="btn btn-sm px-0" :class="modelValue.alignContent === 'space-between' ? 'btn-primary' : 'bg-base-200'" title="Space Between"><PhArrowsOutLineVertical class="w-3 h-3" /></button>
                                <button @click="modelValue.alignContent = 'space-around'" class="btn btn-sm px-0" :class="modelValue.alignContent === 'space-around' ? 'btn-primary' : 'bg-base-200'" title="Space Around"><PhArrowsVertical class="w-3 h-3" /></button>
                                <button @click="modelValue.alignContent = 'space-evenly'" class="btn btn-sm px-0" :class="modelValue.alignContent === 'space-evenly' ? 'btn-primary' : 'bg-base-200'" title="Space Evenly"><PhDotsThreeOutlineVertical class="w-3 h-3" /></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 2. Sizing -->
        <AdminCollapse :title="t('admin.builder.tab_sizing', 'Sizing')" icon="PhBoundingBox" persistKey="style_sizing">
            <div class="space-y-6 pt-1">
                <div class="grid grid-cols-2 gap-4">
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
            </div>
        </AdminCollapse>

        <!-- 3. Position -->
        <AdminCollapse :title="t('admin.builder.tab_position', 'Position')" icon="PhArrowUDownRight" persistKey="style_position">
            <div class="space-y-6 pt-1">
                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Z-Index</span></label>
                    <input type="number" v-model="modelValue.zIndex" class="input input-sm input-bordered w-full" />
                </div>
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
                <div v-if="['absolute', 'fixed', 'sticky', 'relative'].includes(modelValue.position)" class="space-y-3">
                    <div class="flex flex-col gap-3">
                        <div class="flex justify-center gap-4">
                            <!-- Y Axis Toggle -->
                            <div class="join bg-base-200 rounded-md">
                                <button @click="toggleOffset('top')" class="btn btn-sm join-item min-h-0 h-8 w-10 border-none" :class="modelValue.top !== undefined ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'" title="Top"><PhArrowUp weight="bold" class="w-4 h-4" /></button>
                                <button @click="toggleOffset('bottom')" class="btn btn-sm join-item min-h-0 h-8 w-10 border-none" :class="modelValue.bottom !== undefined ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'" title="Bottom"><PhArrowDown weight="bold" class="w-4 h-4" /></button>
                            </div>
                            <!-- X Axis Toggle -->
                            <div class="join bg-base-200 rounded-md">
                                <button @click="toggleOffset('left')" class="btn btn-sm join-item min-h-0 h-8 w-10 border-none" :class="modelValue.left !== undefined ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'" title="Left"><PhArrowLeft weight="bold" class="w-4 h-4" /></button>
                                <button @click="toggleOffset('right')" class="btn btn-sm join-item min-h-0 h-8 w-10 border-none" :class="modelValue.right !== undefined ? 'btn-primary' : 'bg-transparent opacity-50 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'" title="Right"><PhArrowRight weight="bold" class="w-4 h-4" /></button>
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

        <!-- 4. Typography -->
        <AdminCollapse :title="t('admin.theme.typography', 'Typography')" icon="PhTextT" persistKey="style_typography" v-if="!['spacer', 'divider', 'image', 'video', 'gallery', 'carousel'].includes(blockType)">
            <div class="space-y-4 pt-1">
                <div class="form-control">
                    <FillControl v-model="textFill" :label="t('admin.builder.style_color', 'Color')" />
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
                    <label class="label flex justify-between">
                        <span class="label-text text-[10px] uppercase">Weight</span>
                        <span class="text-[10px] font-mono">{{ fontWeightSlider }}</span>
                    </label>
                    <input type="range" v-model.number="fontWeightSlider" min="100" max="900" step="100" class="range range-primary range-sm" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase">Alignment</span></label>
                    <div class="join w-full bg-base-200 rounded-md">
                        <button @click="modelValue.textAlign = 'left'" class="btn btn-sm join-item min-h-0 h-8 flex-1 border-none" :class="modelValue.textAlign === 'left' ? 'btn-primary' : 'bg-transparent opacity-60 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"><PhTextAlignLeft weight="bold" class="w-4 h-4" /></button>
                        <button @click="modelValue.textAlign = 'center'" class="btn btn-sm join-item min-h-0 h-8 flex-1 border-none" :class="modelValue.textAlign === 'center' ? 'btn-primary' : 'bg-transparent opacity-60 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"><PhTextAlignCenter weight="bold" class="w-4 h-4" /></button>
                        <button @click="modelValue.textAlign = 'right'" class="btn btn-sm join-item min-h-0 h-8 flex-1 border-none" :class="modelValue.textAlign === 'right' ? 'btn-primary' : 'bg-transparent opacity-60 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"><PhTextAlignRight weight="bold" class="w-4 h-4" /></button>
                        <button @click="modelValue.textAlign = 'justify'" class="btn btn-sm join-item min-h-0 h-8 flex-1 border-none" :class="modelValue.textAlign === 'justify' ? 'btn-primary' : 'bg-transparent opacity-60 text-base-content/70 hover:opacity-100 hover:bg-base-content/10'"><PhTextAlignJustify weight="bold" class="w-4 h-4" /></button>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 5. Background -->
        <AdminCollapse :title="t('admin.builder.style_background', 'Background')" icon="PhPalette" persistKey="style_background">
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
                <div v-if="backgroundFill?.type === 'image'" class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Background Size</span></label>
                        <select v-model="modelValue.backgroundSize" class="select select-bordered select-sm w-full">
                            <option value="cover">Cover</option>
                            <option value="contain">Contain</option>
                            <option value="auto">Auto</option>
                            <option value="100% 100%">Stretch</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text text-[10px] uppercase">Background Repeat</span></label>
                        <select v-model="modelValue.backgroundRepeat" class="select select-bordered select-sm w-full">
                            <option value="no-repeat">No Repeat</option>
                            <option value="repeat">Repeat</option>
                            <option value="repeat-x">Repeat X</option>
                            <option value="repeat-y">Repeat Y</option>
                            <option value="space">Space</option>
                            <option value="round">Round</option>
                        </select>
                    </div>
                    <div class="form-control col-span-2">
                        <label class="label"><span class="label-text text-[10px] uppercase">Background Position</span></label>
                        <select v-model="modelValue.backgroundPosition" class="select select-bordered select-sm w-full">
                            <option value="center">Center</option>
                            <option value="top">Top</option>
                            <option value="bottom">Bottom</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="top left">Top Left</option>
                            <option value="top right">Top Right</option>
                            <option value="bottom left">Bottom Left</option>
                            <option value="bottom right">Bottom Right</option>
                        </select>
                    </div>
                </div>
            </div>
        </AdminCollapse>

        <!-- 6. Effects -->
        <AdminCollapse :title="t('admin.builder.style_effects', 'Effects')" icon="PhSelectionBackground" persistKey="style_effects">
            <div class="space-y-6 pt-1">
                <div class="form-control">
                    <label class="label flex justify-between">
                        <span class="label-text text-[10px] uppercase font-bold opacity-50">Opacity</span>
                        <span class="text-[10px] font-mono">{{ Math.round((modelValue.opacity ?? 1) * 100) }}%</span>
                    </label>
                    <input type="range" v-model.number="modelValue.opacity" min="0" max="1" step="0.01" class="range range-primary range-sm" />
                </div>

                <!-- Filters -->
                <div class="space-y-4">
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
                                class="range range-sm" :class="modelValue[f[0]] !== undefined ? 'range-primary' : 'opacity-30'" />
                        </div>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text text-[10px] uppercase font-bold opacity-50">Shadow</span></label>
                    <input type="text" v-model="modelValue.boxShadow" placeholder="e.g. 0 10px 30px rgba(0,0,0,0.1)" class="input input-sm input-bordered w-full font-mono text-[10px]" />
                </div>
            </div>
        </AdminCollapse>

        <!-- 7. Borders -->
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

        <!-- 8. Spacing -->
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
 
        <!-- 9. Advanced -->
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
            <button @click="$emit('reset')" class="btn btn-outline btn-error btn-sm w-full opacity-50 hover:opacity-100 transition-opacity">
                <PhTrash class="w-3 h-3 mr-1" /> {{ t('admin.builder.action_reset_styles', 'Reset All Styles') }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTranslations } from '@/Composables/useTranslations';
import AdminCollapse from '@/features/admin/shared/components/AdminCollapse.vue';
import FillControl from '@/features/admin/theme/components/FillControl.vue';
import LinkedUnitInput from '@/features/admin/theme/components/LinkedUnitInput.vue';
import UnitInput from '@/features/admin/theme/components/UnitInput.vue';
import { 
    PhTextAlignLeft, PhTextAlignCenter, PhTextAlignRight, PhTextAlignJustify, 
    PhAlignLeftSimple, PhAlignCenterHorizontalSimple, PhAlignRightSimple,
    PhAlignTopSimple, PhAlignCenterVerticalSimple, PhAlignBottomSimple,
    PhPalette, PhFrameCorners, PhTextT, PhSquareHalf, 
    PhArrowUp, PhArrowDown, PhArrowLeft, PhArrowRight,
    PhArrowsHorizontal, PhArrowsVertical, PhArrowsOutLineHorizontal, PhArrowsOutLineVertical,
    PhArrowsInLineHorizontal, PhArrowsInLineVertical, PhCircleDashed,
    PhDotsThreeOutline, PhDotsThreeOutlineVertical,
    PhBoundingBox, PhSelectionBackground, PhSlidersHorizontal, PhTrash,
    PhLayout, PhColumns, PhRows, PhArrowUDownRight
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

const fontWeightSlider = computed({
    get: () => {
        const raw = props.modelValue?.fontWeight;
        if (raw === undefined || raw === null || raw === '') return 400;
        if (raw === 'normal') return 400;
        if (raw === 'bold') return 700;

        const parsed = Number(raw);
        if (!Number.isFinite(parsed)) return 400;
        return Math.min(900, Math.max(100, Math.round(parsed / 100) * 100));
    },
    set: (val) => {
        if (!props.modelValue) return;
        const parsed = Number(val);
        const clamped = Number.isFinite(parsed) ? Math.min(900, Math.max(100, Math.round(parsed / 100) * 100)) : 400;
        emit('update:modelValue', { ...props.modelValue, fontWeight: String(clamped) });
    }
});

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

