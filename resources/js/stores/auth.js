import { ref, computed } from "vue";
import { defineStore } from "pinia";
import axios from "@/plugins/axios";
import { getInitialUser } from "@/bootstrap/auth";
import { skipNextUnsavedChangesGuard } from "@/composables/useUnsavedChanges";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(getInitialUser());

    const isAuthenticated = computed(() => user.value !== null);

    function setUser(value) {
        user.value = value;
    }

    function clearUser() {
        user.value = null;
    }

    async function getCsrfCookie() {
        await axios.get("/sanctum/csrf-cookie");
    }

    async function login(credentials) {
        await getCsrfCookie();
        const response = await axios.post("/api/login", credentials);
        setUser(response.data.user);
    }

    async function register(payload) {
        await getCsrfCookie();
        const response = await axios.post("/api/register", payload);
    }

    async function logout() {
        skipNextUnsavedChangesGuard();
        clearUser();
        axios.post("/api/logout").catch(() => {});
    }

    return {
        user,
        isAuthenticated,
        setUser,
        clearUser,
        login,
        register,
        logout,
    };
});
