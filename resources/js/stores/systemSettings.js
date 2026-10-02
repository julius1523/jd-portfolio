import { defineStore } from "pinia";
import axios from "@/plugins/axios";
import vuetify from "@/plugins/vuetify";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";

export const useSystemSettingsStore = defineStore("systemSettings", {
    state: () => ({
        systemLogo: null,
        systemName: null,
        systemOwner: null,
        systemColor: null,
        loaded: false,
    }),

    actions: {
        hydrate() {
            const boot = window.__SYSTEM_SETTINGS__;
            if (!boot) return false;

            this.systemLogo = boot.systemLogo;
            this.systemName = boot.systemName;
            this.systemOwner = boot.systemOwner;
            this.systemColor = boot.systemColor;
            this.applyTheme();
            this.loaded = true;
            return true;
        },

        async fetch() {
            const { error: showError } = useSnackBarQueue();
            try {
                const { data } = await axios.get("/api/getSystemSettings");
                this.systemLogo = data.systemLogo;
                this.systemName = data.systemName;
                this.systemOwner = data.systemOwner;
                this.systemColor = data.systemColor;
                this.applyTheme();
                return data;
            } catch (err) {
                showError(
                    err.response?.data?.message ??
                        "Failed to load system settings.",
                );
                return null;
            } finally {
                this.loaded = true;
            }
        },

        applyTheme() {
            if (!this.systemColor) return;
            const themes = vuetify.theme.themes.value;
            for (const name of Object.keys(themes)) {
                themes[name].colors.primary = this.systemColor;
            }
        },
    },
});
