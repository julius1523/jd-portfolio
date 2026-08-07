import { useAuthStore } from "@/stores/auth";

export default async function auth() {
    const authStore = useAuthStore();

    if (!authStore.isAuthenticated) {
        return { name: "login" };
    }

    return true;
}
