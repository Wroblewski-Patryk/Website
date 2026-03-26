import "./bootstrap";
import "../css/app.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "flag-icons/css/flag-icons.min.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createPinia } from "pinia";
import { gsap } from "gsap";
import { ZiggyVue } from "ziggy-js";
import * as Sentry from "@sentry/vue";

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
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .use(ZiggyVue);

        if (import.meta.env.VITE_SENTRY_DSN) {
            Sentry.init({
                app: vueApp,
                dsn: import.meta.env.VITE_SENTRY_DSN,
                environment: import.meta.env.MODE,
                tracesSampleRate: Number(
                    import.meta.env.VITE_SENTRY_TRACES_SAMPLE_RATE ?? 0,
                ),
            });
        }

        vueApp.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
