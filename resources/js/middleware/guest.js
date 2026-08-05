import { useAuthStore } from "@/stores/auth";

export default async function guest(to) {
    if (to.name !== "login") {
        return true;
    }

    const authStore = useAuthStore();
    await authStore.ensureFetched();

    if (authStore.isAuthenticated) {
        return { name: "manage-content" };
    }

    return true;
}
