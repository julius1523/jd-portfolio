import { ref } from "vue";
import {
    mdiCheckCircleOutline,
    mdiAlertCircleOutline,
    mdiAlertOutline,
    mdiInformationOutline,
} from "@mdi/js";
const alert = ref(null);
const stateConfig = {
    success: { icon: mdiCheckCircleOutline, color: "success" },
    error: { icon: mdiAlertCircleOutline, color: "error" },
    warning: { icon: mdiAlertOutline, color: "warning" },
    info: { icon: mdiInformationOutline, color: "info" },
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
