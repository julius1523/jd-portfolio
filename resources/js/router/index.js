import {
    createRouter,
    createWebHistory,
    isNavigationFailure,
} from "vue-router";
import routes from "./routes";
import { resolveAuthRedirect } from "@/middleware/auth";
import { ScrollTrigger } from "gsap/ScrollTrigger";

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

router.afterEach((to, from, failure) => {
    if (isNavigationFailure(failure)) return;
    requestAnimationFrame(() => ScrollTrigger.refresh());
    document.title = to.meta?.title ?? "Portfolio";
});

export default router;
