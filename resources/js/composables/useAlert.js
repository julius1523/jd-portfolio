import { ref } from "vue";
const alert = ref(null);
const stateConfig = {
    success: { icon: "i-mdi-check-circle-outline", color: "success" },
    error: { icon: "i-mdi-alert-circle-outline", color: "error" },
    warning: { icon: "i-mdi-alert-outline", color: "warning" },
    info: { icon: "i-mdi-information-outline", color: "primary" },
};
function show(text, type = "info", extra = {}) {
    const { icon, color } = stateConfig[type] ?? stateConfig.info;
    alert.value = {
        text,
        type,
        icon,
        color,
        ...extra,
    };
}
function clear() {
    alert.value = null;
}
export function useAlert() {
    return {
        alert,
        clear,
        success: (text, extra) => show(text, "success", extra),
        error: (text, extra) => show(text, "error", extra),
        warning: (text, extra) => show(text, "warning", extra),
        info: (text, extra) => show(text, "info", extra),
    };
}
