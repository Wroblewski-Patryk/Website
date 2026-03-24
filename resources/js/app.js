import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createPinia } from "pinia";
import { gsap } from "gsap";
import { ZiggyVue } from "ziggy-js";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Featherly";

// Reduced Motion Handling for GSAP
if (
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches
) {
    gsap.globalTimeline.timeScale(1000); // Instantly finish animations
}

createInertiaApp({
    title: (title) => {
        // This is a bit tricky since we don't have easy access to props here
        // without an active component. However, many users use a simple
        // string or object as a fallback.
        return title;
    },
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
