import { ref, computed } from "vue";
import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(null);
    const ready = ref(false);
    let inFlight = null;

    const isAuthenticated = computed(() => !!user.value);

    function setUser(value) {
        user.value = value;
    }

    async function getCsrfCookie() {
        await axios.get("/sanctum/csrf-cookie");
    }

    async function login(credentials) {
        await getCsrfCookie();
        await axios.post("/api/login", credentials);
        await checkStatus();
    }

    async function register(payload) {
        await getCsrfCookie();
        await axios.post("/api/register", payload);
        await checkStatus();
    }

    async function checkStatus() {
        const { data } = await axios.get("/api/auth/status");
        user.value = data.authenticated ? data.user : null;
        ready.value = true;
    }

    function ensureFetched() {
        if (ready.value) return Promise.resolve();
        if (!inFlight)
            inFlight = checkStatus().finally(() => (inFlight = null));
        return inFlight;
    }

    async function logout() {
        await axios.post("/api/logout");
        user.value = null;
    }

    return {
        user,
        isAuthenticated,
        ready,
        setUser,
        login,
        register,
        checkStatus,
        ensureFetched,
        logout,
    };
});
