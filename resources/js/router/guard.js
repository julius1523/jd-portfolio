import { resolveAuthRedirect } from "@/middleware/auth";

export function historyGuard(router) {
    window.addEventListener("pageshow", async (event) => {
        if (!event.persisted) {
            return;
        }

        const redirect = resolveAuthRedirect(router.currentRoute.value);
        if (redirect) {
            await router.replace(redirect);
        }
    });
}
