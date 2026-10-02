import { createRouter, createWebHistory, START_LOCATION } from "vue-router";
import routes from "./routes";
import { resolveAuthRedirect } from "@/middleware/auth";
import { scrollGate, trackScroll, readReloadScroll } from "./scrollGate";

const router = createRouter({
    history: createWebHistory(),
    routes,

    scrollBehavior(to, from, savedPosition) {
        let position = savedPosition;

        if (from === START_LOCATION) {
            position = readReloadScroll(to) ?? position;
        }

        const target = {
            ...(position ?? { left: 0, top: 0 }),
            behavior: "instant",
        };

        if (to.name === from.name) return target;

        return scrollGate.wait().then(() => target);
    },
});

trackScroll(router);

router.beforeEach(() => {
    scrollGate.reset();
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
    } catch (err) {}
});

export default router;
