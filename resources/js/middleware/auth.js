import { useAuthStore } from "@/stores/auth";

export function resolveAuthRedirect(route) {
    const auth = useAuthStore();

    switch (route.meta.middleware) {
        case "guest":
            if (auth.isAuthenticated) {
                return { name: "manage-content" };
            }
            break;

        case "auth":
            if (!auth.isAuthenticated) {
                return { name: "login" };
            }
            break;
    }

    return null;
}
