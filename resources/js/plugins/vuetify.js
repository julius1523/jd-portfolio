import "@mdi/font/css/materialdesignicons.min.css";
import "vuetify/styles";

import { createVuetify } from "vuetify";
import { VPie } from "vuetify/labs/VPie";

const savedTheme = localStorage.getItem("theme") ?? "dark";

export default createVuetify({
    theme: {
        defaultTheme: savedTheme,
        themes: {
            light: {
                dark: false,
                colors: {
                    "surface-light": "#f5f5f7",
                    "revert-color": "#272727",
                },
            },
            dark: {
                dark: true,
                colors: {
                    background: "#1e1e1e",
                    surface: "#1e1e1e",
                    "surface-light": "#232323",
                    "revert-color": "#f5f5f7",
                },
            },
        },
    },
    defaults: {
        VCard: {
            ripple: "center",
        },
        VChip: {
            style: {
                userSelect: "none",
            },
        },
        VImg: {
            draggable: false,
        },
        VAppBar: {
            style: {
                background: "rgba(var(--v-theme-surface), 0.7)",
                backdropFilter: "blur(12px)",
                WebkitBackdropFilter: "blur(12px)",
                userSelect: "none",
            },
        },
        VBottomNavigation: {
            style: {
                width: "auto",
                maxWidth: "400px",
                bottom: "10px",
                left: "10px",
                right: "10px",
                background: "rgba(var(--v-theme-surface), 0.7)",
                backdropFilter: "blur(12px)",
                WebkitBackdropFilter: "blur(12px)",
                userSelect: "none",
            },
        },
        VCarousel: {
            style: {
                height: "100%",
            },
        },
        VCardTitle: {
            style: {
                textWrap: "wrap",
            },
        },
        VNavigationDrawer: {
            style: {
                userSelect: "none",
            },
        },
        VFileUploadDropzone: {
            rounded: "lg",
        },
        VMenu: {
            offset: "7px",
        },
        VContainer: {
            maxWidth: 1400,
        },
        VField: {
            style: {
                overflow: "hidden",
            },
        },
    },
    components: {
        VPie,
    },
});
