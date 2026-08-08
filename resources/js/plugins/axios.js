import Axios from "axios";
import { useAuthStore } from "@/stores/auth";

const axios = Axios.create({
    baseURL: import.meta.env.VITE_API_URL,

    withCredentials: true,
    withXSRFToken: true,

    headers: {
        Accept: "application/json",
    },
});

axios.interceptors.response.use(
    (response) => response,

    (error) => {
        const response = error.response;

        if (!response) {
            console.error("Network error.");
            return Promise.reject(error);
        }

        const status = response.status;
        const url = error.config?.url ?? "";

        const isAuthEndpoint =
            url.includes("/login") ||
            url.includes("/register") ||
            url.includes("/logout");

        if (status === 401 && !isAuthEndpoint) {
            const auth = useAuthStore();
            auth.clearUser();
        }

        if (status === 403) {
            console.warn("Access denied.");
        }

        if (status === 419) {
            console.warn("CSRF token/session expired.");
        }

        return Promise.reject(error);
    },
);

export default axios;
