import axios from "@/plugins/axios";
import { defineStore } from "pinia";

function createResourceStore(id, endpoint) {
    return defineStore(id, {
        state: () => ({
            data: null,
            loading: false,
            loaded: false,
            error: null,
            _inflight: null,
        }),
        actions: {
            async fetch({ force = false } = {}) {
                if (this.loaded && !force) return this.data;
                if (this.loading) return this._inflight;

                this.loading = true;
                this.error = null;
                this._inflight = axios
                    .get(endpoint)
                    .then(({ data }) => {
                        this.data = data;
                        this.loaded = true;
                        return data;
                    })
                    .catch((err) => {
                        this.error =
                            err?.response?.data?.message ??
                            "Failed to load content.";
                        throw err;
                    })
                    .finally(() => {
                        this.loading = false;
                    });

                return this._inflight;
            },
            reset() {
                this.data = null;
                this.loaded = false;
                this.error = null;
            },
        },
    });
}

export const useHomeStore = createResourceStore("home", "/api/public/home");
export const useAboutStore = createResourceStore("about", "/api/public/about");
export const useProjectsStore = createResourceStore(
    "projects",
    "/api/public/projects",
);
export const useContactStore = createResourceStore(
    "contact",
    "/api/public/contact",
);
