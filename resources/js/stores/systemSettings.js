import { defineStore } from "pinia";
import axios from "@/plugins/axios";
import vuetify from "@/plugins/vuetify";

export const useSystemSettingsStore = defineStore("systemSettings", {
    state: () => ({
        systemName: null,
        systemLogo: null,
        systemColor: null,
        loaded: false,
    }),

    actions: {
        async fetch() {
            try {
                const { data } = await axios.get("/api/getSystemSettings");
                this.systemName = data.systemName;
                this.systemLogo = data.systemLogo;
                this.systemColor = data.systemColor;
                this.applyTheme();
            } catch (err) {
                console.error("Failed to load system settings", err);
            } finally {
                this.loaded = true;
            }
        },

        applyTheme() {
            if (!this.systemColor) return;
            vuetify.theme.themes.value[
                vuetify.theme.global.name.value
            ].colors.primary = this.systemColor;
        },
    },
});
