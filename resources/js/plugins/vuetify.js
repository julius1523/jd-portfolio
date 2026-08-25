import "@/../css/layers.css";
import "vuetify/styles";
import { h } from "vue";
import { createVuetify } from "vuetify";
import { aliases, mdi } from "vuetify/iconsets/mdi-unocss";
const savedTheme = localStorage.getItem("theme") ?? "dark";
const unoIcons = {
    component: (props) =>
        h(props.tag ?? "span", {
            class: [props.icon],
        }),
};
export default createVuetify({
    icons: {
        defaultSet: "uno",
        aliases,
        sets: { mdi, uno: unoIcons },
    },
    theme: {
        defaultTheme: savedTheme,
        themes: {
            light: {
                dark: false,
                colors: {
                    "surface-light": "#f5f5f7",
                    "revert-color": "#272727",
                    grey: "#757575",
                },
            },
            dark: {
                dark: true,
                colors: {
                    background: "#1e1e1e",
                    surface: "#1e1e1e",
                    "surface-light": "#232323",
                    "revert-color": "#f5f5f7",
                    grey: "#757575",
                },
            },
        },
    },
    defaults: {
        VCard: { ripple: "center" },
        VChip: { style: { userSelect: "none" } },
        VImg: { draggable: false },
        VAppBar: { style: { userSelect: "none" } },
        VCarousel: { style: { height: "100%" } },
        VCardTitle: { style: { textWrap: "wrap" } },
        VNavigationDrawer: { style: { userSelect: "none" } },
        VFileUploadItem: {
            fileIcon: "i-mdi-file-document",
            density: "compact",
        },
        VFileUploadDropzone: { rounded: "lg" },
        VMenu: { offset: "5px" },
        VBtn: { class: "text-label-large" },
        VContainer: { maxWidth: 1400 },
        VTabs: { activeClass: "opacity-100" },
    },
});
