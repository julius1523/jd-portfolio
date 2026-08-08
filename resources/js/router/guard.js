import { useAuthStore } from "@/stores/auth";

export function historyGuard(router) {
    window.addEventListener("pageshow", async (event) => {
        if (!event.persisted) {
            return;
        }

        const auth = useAuthStore();
        const route = router.currentRoute.value;

        switch (route.meta.middleware) {
            case "guest":
                if (auth.isAuthenticated) {
                    await router.replace({
                        name: "manage-content",
                    });
                }
                break;

            case "auth":
                if (!auth.isAuthenticated) {
                    await router.replace({
                        name: "login",
                    });
                }
                break;
        }
    });
}
