<template>
    <div class="flex flex-col h-full relative">
        <!-- Controls Header -->
        <div class="flex items-center gap-4 mb-4 bg-base-200/50 p-2 rounded-lg border border-white/5">
            <div class="flex gap-2">
                <button @click="restart" class="btn btn-sm btn-ghost btn-square" :title="t('admin.builder.action_restart', 'Restart')">
                    <PhSkipBack weight="bold" class="w-4 h-4" />
                </button>
                <button @click="togglePlay" class="btn btn-sm btn-primary btn-square" :title="isPlaying ? t('admin.builder.action_pause', 'Pause') : t('admin.builder.action_play', 'Play')">
                    <PhPause weight="fill" v-if="isPlaying" class="w-4 h-4" />
                    <PhPlay weight="fill" v-else class="w-4 h-4" />
                </button>
            </div>
            
            <div class="text-xs font-mono opacity-60 w-16 text-right">
                {{ currentTime.toFixed(1) }}s / {{ totalDuration.toFixed(1) }}s
            </div>

            <!-- Scrubber -->
            <div class="flex-1 relative h-6 flex items-center cursor-pointer group" @mousedown="startScrubbing" ref="scrubberTrack">
                <div class="w-full h-2 bg-base-300 rounded-full overflow-hidden relative">
                    <div class="absolute top-0 bottom-0 bg-primary/30" :style="{ width: `${progress * 100}%` }"></div>
                </div>
                <!-- Playhead -->
                <div class="absolute top-0 bottom-0 w-px bg-primary pointer-events-none z-10 before:content-[''] before:absolute before:top-0 before:-translate-x-1/2 before:w-3 before:h-3 before:bg-primary before:border-2 before:border-base-100 before:rounded-full" 
                     :style="{ left: `${progress * 100}%` }">
                </div>
            </div>
        </div>

        <!-- Tracks Area -->
        <div class="flex-1 overflow-y-auto custom-scrollbar relative border border-white/5 rounded-lg bg-base-200/20" ref="tracksArea">
            
            <!-- Grid Lines (Background) -->
            <div class="absolute inset-0 pointer-events-none flex" v-if="totalDuration > 0">
                <div v-for="tick in gridTicks" :key="tick" class="h-full border-r border-white/5" :style="{ width: `${(1 / Math.max(1, totalDuration)) * 100}%` }">
                    <span class="text-[8px] opacity-20 absolute mt-1 -translate-x-1/2">{{ tick }}s</span>
                </div>
            </div>

            <!-- Blocks List -->
            <div class="relative z-10 p-2 space-y-2">
                <div v-for="(block, index) in blocks" :key="block.id" 
                     class="group flex items-center gap-2 relative h-10 border border-transparent hover:border-white/10 rounded overflow-hidden"
                     @click="store.activeBlockId = block.id"
                     :class="{ 'bg-primary/5 ring-1 ring-primary ring-inset': store.activeBlockId === block.id }">
                     
                     <!-- Block Info -->
                     <div class="w-48 shrink-0 flex items-center gap-2 pl-2 bg-base-100/50 h-full border-r border-white/10 relative z-20 backdrop-blur-sm">
                        <div class="w-4 text-center text-[10px] font-mono opacity-40">{{ index + 1 }}</div>
                        <component :is="PhCube" weight="bold" class="w-3 h-3 opacity-50" />
                        <div class="text-xs truncate font-semibold" :title="block.type">{{ block.type.charAt(0).toUpperCase() + block.type.slice(1) }}</div>
                     </div>

                     <!-- Track Timeline -->
                     <div class="flex-1 h-full relative group-hover:bg-white/5 transition-colors">
                        <div class="absolute top-1/2 -translate-y-1/2 h-6 bg-primary/60 rounded cursor-ew-resize border border-primary/40 shadow-sm hover:brightness-125 transition-all"
                             :style="getTrackStyle(block)"
                             :title="t('admin.builder.action_drag_delay', 'Drag to change delay')">
                            <div class="px-2 text-[10px] font-mono font-bold text-white/90 leading-6 truncate mix-blend-overlay">
                                {{ block.settings.animations.preset }}
                            </div>
                        </div>
                     </div>
                </div>

                <div v-if="blocks.length === 0" class="flex flex-col items-center justify-center opacity-30 py-12 pointer-events-none">
                    <PhTimer weight="bold" class="w-12 h-12 mb-4" />
                    <p class="text-xs text-center max-w-sm">{{ t('admin.builder.no_blocks_timeline', 'No blocks attached to the Main Timeline.') }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { PhSkipBack, PhPlay, PhPause, PhTimer, PhCube } from '@phosphor-icons/vue';
import { useBlockBuilderStore } from '@/features/admin/block-builder/store/useBlockBuilderStore';
import { useTranslations } from '@/Composables/useTranslations';
import gsap from 'gsap';

const props = defineProps({
    blocks: {
        type: Array,
        required: true
    }
});

const store = useBlockBuilderStore();
const { t } = useTranslations();

const isPlaying = ref(false);
const progress = ref(0);
const currentTime = ref(0);
const totalDuration = ref(0);
const scrubberTrack = ref(null);
const timeline = ref(null);

const gridTicks = computed(() => {
    const duration = Math.max(1, Math.ceil(totalDuration.value));
    return Array.from({ length: duration + 1 }, (_, i) => i);
});

// Rebuild timeline when blocks or their animation settings change
watch(() => props.blocks, buildTimeline, { deep: true, immediate: true });

function buildTimeline() {
    // Kill existing timeline to avoid memory leaks
    if (timeline.value) {
        timeline.value.kill();
    }

    // Create a new GSAP timeline. It won't actually animate DOM elements here,
    // it's a "dummy" timeline using empty objects just to track time accurately 
    // simulating the animation sequence.
    timeline.value = gsap.timeline({
        paused: !isPlaying.value,
        onUpdate: () => {
            progress.value = timeline.value.progress();
            currentTime.value = timeline.value.time();
        },
        onComplete: () => {
            isPlaying.value = false;
        }
    });

    // Populate timeline with dummy tweens representing the blocks
    props.blocks.forEach(block => {
        const anim = block.settings?.animations || {};
        const duration = Number(anim.duration) || 1;
        const delay = Number(anim.delay) || 0;
        
        const dummyTarget = { val: 0 };
        // Add a dummy tween to the timeline
        timeline.value.to(dummyTarget, {
            val: 1,
            duration: duration,
            ease: "none"
        }, delay);
    });

    totalDuration.value = timeline.value.duration() || 1; // Fallback to 1 to avoid division by zero visually
    
    // Resume correct progress
    if (progress.value > 0 && progress.value < 1) {
        timeline.value.progress(progress.value);
    } else {
        progress.value = timeline.value.progress();
        currentTime.value = timeline.value.time();
    }
}

function getTrackStyle(block) {
    const anim = block.settings?.animations || {};
    const duration = Number(anim.duration) || 1;
    const delay = Number(anim.delay) || 0;
    
    // Calculate percentages based on total timeline duration
    const maxDur = Math.max(1, totalDuration.value);
    const leftPct = (delay / maxDur) * 100;
    const widthPct = (duration / maxDur) * 100;

    return {
        left: `${leftPct}%`,
        width: `${widthPct}%`,
        minWidth: '4px'
    };
}

function togglePlay() {
    if (!timeline.value) return;
    
    if (progress.value === 1) {
        timeline.value.restart();
        isPlaying.value = true;
    } else if (isPlaying.value) {
        timeline.value.pause();
        isPlaying.value = false;
    } else {
        timeline.value.play();
        isPlaying.value = true;
    }
}

function restart() {
    if (!timeline.value) return;
    timeline.value.pause(0);
    isPlaying.value = false;
    progress.value = 0;
    currentTime.value = 0;
}

// Scrubber logic
let isScrubbing = false;

function startScrubbing(e) {
    if (!timeline.value || totalDuration.value === 0) return;
    isScrubbing = true;
    
    // Pause during scrub
    if (isPlaying.value) {
        timeline.value.pause();
    }

    updateScrubber(e);
    window.addEventListener('mousemove', updateScrubber);
    window.addEventListener('mouseup', stopScrubbing);
}

function updateScrubber(e) {
    if (!isScrubbing || !scrubberTrack.value) return;
    
    const rect = scrubberTrack.value.getBoundingClientRect();
    let x = e.clientX - rect.left;
    x = Math.max(0, Math.min(x, rect.width)); // Clamp between 0 and width
    
    const scrubProgress = x / rect.width;
    timeline.value.progress(scrubProgress);
    progress.value = scrubProgress;
    currentTime.value = timeline.value.time();
}

function stopScrubbing() {
    isScrubbing = false;
    window.removeEventListener('mousemove', updateScrubber);
    window.removeEventListener('mouseup', stopScrubbing);
    
    if (isPlaying.value) {
        timeline.value.play();
    }
}

onUnmounted(() => {
    if (timeline.value) {
        timeline.value.kill();
    }
    window.removeEventListener('mousemove', updateScrubber);
    window.removeEventListener('mouseup', stopScrubbing);
});
</script>
