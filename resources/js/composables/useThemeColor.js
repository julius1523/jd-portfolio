import { computed } from "vue";
import { useTheme } from "vuetify";

export function useThemeColor() {
    const theme = useTheme();

    const hexToRgba = (hex, opacity) => {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    };

    const primaryColor = computed(() => theme.current.value.colors.primary);
    const primarySoft = computed(() => hexToRgba(primaryColor.value, 0.7));

    return {
        primaryColor,
        primarySoft,
    };
}
