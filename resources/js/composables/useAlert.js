import { ref } from "vue";

const alert = ref(null);

const stateConfig = {
    success: { icon: "mdi-check-circle-outline", color: "success" },
    error: { icon: "mdi-alert-circle-outline", color: "error" },
    warning: { icon: "mdi-alert-outline", color: "warning" },
    info: { icon: "mdi-information-outline", color: "info" },
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
