import { ref } from "vue";
import {
    mdiCheckCircleOutline,
    mdiAlertCircleOutline,
    mdiAlertOutline,
    mdiInformationOutline,
} from "@mdi/js";
const messages = ref([]);
const stateConfig = {
    success: { icon: mdiCheckCircleOutline, color: "success" },
    error: { icon: mdiAlertCircleOutline, color: "error" },
    warning: { icon: mdiAlertOutline, color: "warning" },
    info: { icon: mdiInformationOutline, color: "info" },
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
export function useSnackBarQueue() {
    return {
        messages,
        success: (text, extra) => push(text, "success", extra),
        error: (text, extra) => push(text, "error", extra),
        warning: (text, extra) => push(text, "warning", extra),
        info: (text, extra) => push(text, "info", extra),
    };
}
