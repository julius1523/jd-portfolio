import Axios from "axios";
import router from "@/router";
import { useAuthStore } from "@/stores/auth";
import { useSnackbarQueue } from "@/composables/useSnackbarQueue";

const axios = Axios.create({
    baseURL: import.meta.env.VITE_API_URL,
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        Accept: "application/json",
    },
});

const isAuthEndpoint = (url = "") => {
    return (
        url.includes("/login") ||
        url.includes("/register") ||
        url.includes("/logout")
    );
};

const handleUnauthenticated = (reason = "unauthenticated") => {
    const auth = useAuthStore();
    if (auth.isAuthenticated) {
        auth.clearUser();
    }

    const currentRoute = router.currentRoute.value;

    if (currentRoute.meta?.middleware === "auth") {
        router.replace({
            name: "login",
            query: { reason },
        });
    }
};

axios.interceptors.response.use(
    (response) => {
        return response;
    },

    (error) => {
        if (!error.response) {
            const { error: showError } = useSnackbarQueue();
            showError("Network error. Please check your connection.");
            return Promise.reject(error);
        }

        const { status } = error.response;
        const url = error.config?.url ?? "";

        switch (status) {
            case 401:
                if (!isAuthEndpoint(url)) {
                    handleUnauthenticated("unauthenticated");
                }
                break;

            case 419:
                if (!isAuthEndpoint(url)) {
                    handleUnauthenticated("session_expired");
                }
                break;
        }

        return Promise.reject(error);
    },
);

export default axios;
