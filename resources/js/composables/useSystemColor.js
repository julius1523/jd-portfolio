import { watch } from "vue";
import { useTheme } from "vuetify";
import { useSystemSettingsStore } from "@/stores/systemSettings";

export function useSystemColor() {
    const theme = useTheme();
    const settings = useSystemSettingsStore();

    watch(
        () => settings.systemColor,
        (color) => {
            if (!color) return;
            theme.themes.value[theme.global.name.value].colors.primary = color;
        },
    );
}
