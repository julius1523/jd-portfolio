import { ref, computed } from "vue";
import { defineStore } from "pinia";
import axios from "@/plugins/axios";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(null);

    const isAuthenticated = computed(() => !!user.value);

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
        await axios.post("/api/login", credentials);
    }

    async function register(payload) {
        await getCsrfCookie();
        await axios.post("/api/register", payload);
    }

    async function logout() {
        try {
            await axios.post("/api/logout");
        } finally {
            clearUser();
        }
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
