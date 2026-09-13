import { ref } from "vue";

const messages = ref([]);
const stateConfig = {
    success: { icon: "i-mdi-check-circle-outline", color: "success" },
    error: { icon: "i-mdi-alert-circle-outline", color: "error" },
    warning: { icon: "i-mdi-alert-outline", color: "warning" },
    info: { icon: "i-mdi-information-outline", color: "primary" },
};

function push(text, type = "info", extra = {}) {
    const { icon, color } = stateConfig[type] ?? stateConfig.info;
    messages.value.push({
        text,
        icon,
        iconColor: color,
        timerColor: color,
        ...extra,
    });
}

export function useSnackbarQueue() {
    return {
        messages,
        success: (text, extra) => push(text, "success", extra),
        error: (text, extra) => push(text, "error", extra),
        warning: (text, extra) => push(text, "warning", extra),
        info: (text, extra) => push(text, "info", extra),
    };
}
