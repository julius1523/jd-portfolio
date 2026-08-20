import "../../css/styles/_layers.scss";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import { aliases, mdi } from "vuetify/iconsets/mdi-svg";
import { mdiFileDocument } from "@mdi/js";
const savedTheme = localStorage.getItem("theme") ?? "dark";
export default createVuetify({
    icons: {
        defaultSet: "mdi",
        aliases,
        sets: { mdi },
    },
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
        VBtn: {
            style: {
                "webkit-user-drag": "none",
            },
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
        VFileUploadItem: {
            fileIcon: mdiFileDocument,
            density: "compact",
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
});
