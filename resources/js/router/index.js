import {
    createRouter,
    createWebHistory,
    isNavigationFailure,
} from "vue-router";
import routes from "./routes";
import { resolveAuthRedirect } from "@/middleware/auth";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { useSystemSettingsStore } from "@/stores/systemSettings";

const router = createRouter({
    history: createWebHistory(),
    routes,

    scrollBehavior(savedPosition) {
        if (savedPosition) return savedPosition;
        return new Promise((resolve) => {
            setTimeout(() => resolve({ left: 0, top: 0 }), 150);
        });
    },
});

router.beforeEach((to) => {
    const redirect = resolveAuthRedirect(to);
    return redirect ? { ...redirect, replace: true } : true;
});

router.beforeEach(async (to) => {
    const storeHook = to.meta?.store;
    if (!storeHook) return;

    const store = storeHook();
    try {
        await store.fetch();
    } catch (err) {
        console.error(
            `[router] Failed to prefetch data for "${to.name}":`,
            err,
        );
    }
});

router.afterEach((to, from, failure) => {
    if (isNavigationFailure(failure)) return;
    requestAnimationFrame(() => ScrollTrigger.refresh());

    const settingsStore = useSystemSettingsStore();
    const systemName =
        settingsStore.systemName ?? window.__APP_NAME__ ?? "Portfolio";
    const pageTitle = to.meta?.title;

    document.title = pageTitle ? `${pageTitle} | ${systemName}` : systemName;
});

export default router;
