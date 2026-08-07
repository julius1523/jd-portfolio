import { useAuthStore } from "@/stores/auth";

export default async function guest() {
    const authStore = useAuthStore();

    if (authStore.isAuthenticated) {
        return { name: "manage-content" };
    }

    return true;
}
