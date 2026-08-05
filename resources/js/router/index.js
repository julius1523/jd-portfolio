import {
    createRouter,
    createWebHistory,
    isNavigationFailure,
} from "vue-router";
import routes from "./routes";

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

router.beforeEach((to, from) => {
    const middleware = to.meta.middleware;

    if (!middleware) {
        return true;
    }

    return middleware(to, from);
});

router.onError((error, to) => {
    if (
        error.message.includes("Failed to fetch dynamically imported module") ||
        error.message.includes("Importing a module script failed")
    ) {
        if (!to?.fullPath) {
            window.location.reload();
        } else {
            window.location = to.fullPath;
        }
    }
});

router.afterEach((to, from, failure) => {
    if (isNavigationFailure(failure)) return;
    document.title = to.meta?.title ?? "Portfolio";
});

export default router;
