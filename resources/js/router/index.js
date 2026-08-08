import {
    createRouter,
    createWebHistory,
    isNavigationFailure,
} from "vue-router";
import routes from "./routes";
import { useAuthStore } from "@/stores/auth";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

const router = createRouter({
    history: createWebHistory(),
    routes,

    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) return savedPosition;
        return new Promise((resolve) => {
            setTimeout(() => resolve({ left: 0, top: 0 }), 300);
        });
    },
});

router.beforeEach((to) => {
    const auth = useAuthStore();
    switch (to.meta.middleware) {
        case "guest":
            if (auth.isAuthenticated) {
                return {
                    name: "manage-content",
                    replace: true,
                };
            }
            break;
        case "auth":
            if (!auth.isAuthenticated) {
                return {
                    name: "login",
                    replace: true,
                };
            }
            break;
    }
    return true;
});

router.afterEach((to, from, failure) => {
    if (isNavigationFailure(failure)) return;
    requestAnimationFrame(() => ScrollTrigger.refresh());
    document.title = to.meta?.title ?? "Portfolio";
});

export default router;
