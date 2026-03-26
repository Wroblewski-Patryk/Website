import { onMounted, onUnmounted } from 'vue';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function useGsapRuntime() {
    // Map of active timelines to allow sequencing by ID
    const timelines = {};

    const getTimeline = (id) => {
        if (!id) return null;
        if (!timelines[id]) {
            timelines[id] = gsap.timeline({
                scrollTrigger: {
                    trigger: `[data-timeline="${id}"]`,
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                }
            });
        }
        return timelines[id];
    };

    const animateBlock = (el, animation) => {
        if (!el || !animation || !animation.enabled) return;

        const { duration, delay, trigger, ease, timelineId, once } = animation;
        const preset = animation.preset || animation.type || 'fade-up';

        // 1. Define Advanced Presets
        const presets = {
            'fade-up': { from: { opacity: 0, y: 30 }, to: { opacity: 1, y: 0 } },
            'fade-in': { from: { opacity: 0 }, to: { opacity: 1 } },
            'slide-left': { from: { opacity: 0, x: -50 }, to: { opacity: 1, x: 0 } },
            'slide-right': { from: { opacity: 0, x: 50 }, to: { opacity: 1, x: 0 } },
            'zoom-in': { from: { opacity: 0, scale: 0.9 }, to: { opacity: 1, scale: 1 } },
            'clip-reveal': {
                from: { clipPath: 'inset(0 100% 0 0)' },
                to: { clipPath: 'inset(0 0% 0 0)' }
            },
            'reveal-text': { from: { opacity: 0, y: 20 }, to: { opacity: 1, y: 0, stagger: 0.02 } }
        };

        const customTween = animation.tween || {};
        const hasCustomTween = customTween
            && typeof customTween === 'object'
            && customTween.from
            && customTween.to
            && Object.keys(customTween.from).length
            && Object.keys(customTween.to).length;
        const config = hasCustomTween
            ? { from: customTween.from, to: customTween.to }
            : (presets[preset] || presets['fade-up']);

        const normalizedDuration = Number(duration ?? 0.8);
        const normalizedDelay = Number(delay ?? 0);
        const commonVars = {
            duration: normalizedDuration > 20 ? normalizedDuration / 1000 : (normalizedDuration || 0.8),
            delay: normalizedDelay > 20 ? normalizedDelay / 1000 : (normalizedDelay || 0),
            ease: ease || 'power2.out',
            overwrite: 'auto'
        };

        // 2. Handle Sequencing (Timeline ID)
        if (timelineId) {
            const tl = getTimeline(timelineId);
            tl.fromTo(el, config.from, { ...config.to, ...commonVars }, '>');
            return;
        }

        // 3. Handle Triggers
        if (trigger === 'onLoad') {
            gsap.fromTo(el, config.from, { ...config.to, ...commonVars });
        } else if (trigger === 'onScroll') {
            // Scrubbing animation based on scroll position
            gsap.fromTo(el, config.from, {
                ...config.to,
                scrollTrigger: {
                    trigger: el,
                    start: 'top bottom',
                    end: 'top center',
                    scrub: true,
                }
            });
        } else if (trigger === 'onEnter') {
            // Once-off entrance animation
            gsap.fromTo(el, config.from, {
                ...config.to,
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    toggleActions: once ? 'play none none none' : 'play none none reverse',
                }
            });
        }
        // onHover is handled via CSS/Native JS for better performance usually, 
        // but can be added here if complex GSAP hover is needed.
    };

    const cleanup = () => {
        ScrollTrigger.getAll().forEach(t => t.kill());
        Object.values(timelines).forEach(tl => tl.kill());
    };

    return { animateBlock, cleanup };
}
