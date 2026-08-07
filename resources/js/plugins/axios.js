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

axios.interceptors.request.use(
    (config) => config,
    (error) => Promise.reject(error),
);

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const auth = useAuthStore();
        const status = error.response?.status;

        if (!error.response) {
            console.error("Network error.");
            return Promise.reject(error);
        }

        if (
            status === 401 &&
            auth.isAuthenticated &&
            !error.config.url?.includes("/login") &&
            !error.config.url?.includes("/logout")
        ) {
            auth.clearUser();
        }

        if (status === 403) {
            console.warn("Access denied.");
        }

        return Promise.reject(error);
    },
);

export default axios;
