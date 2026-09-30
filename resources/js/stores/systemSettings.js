import { defineStore } from "pinia";
import axios from "@/plugins/axios";
import vuetify from "@/plugins/vuetify";

export const useSystemSettingsStore = defineStore("systemSettings", {
    state: () => ({
        systemLogo: null,
        systemName: null,
        systemOwner: null,
        systemColor: null,
        loaded: false,
    }),

    actions: {
        async fetch() {
            try {
                const { data } = await axios.get("/api/getSystemSettings");
                this.systemLogo = data.systemLogo;
                this.systemName = data.systemName;
                this.systemOwner = data.systemOwner;
                this.systemColor = data.systemColor;
                this.applyTheme();
                return data;
            } catch (err) {
                console.error("Failed to load system settings", err);
                return null;
            } finally {
                this.loaded = true;
            }
        },

        preloadLogo() {
            const url = this.systemLogo?.url;
            if (!url) return Promise.resolve();
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = img.onerror = resolve;
                img.src = url;
            });
        },

        applyTheme() {
            if (!this.systemColor) return;
            vuetify.theme.themes.value[
                vuetify.theme.global.name.value
            ].colors.primary = this.systemColor;
        },
    },
});
