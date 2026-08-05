import { useAuthStore } from "@/stores/auth";

export default async function auth(to) {
    const authStore = useAuthStore();
    await authStore.ensureFetched();

    if (!authStore.isAuthenticated) {
        return { name: "login", query: { redirect: to.fullPath } };
    }

    return true;
}
