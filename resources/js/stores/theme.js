import { defineStore } from "pinia";
import { ref } from "vue";
export const useThemeStore = defineStore("theme", () => {
    const isDark = ref((localStorage.getItem("theme") ?? "dark") === "dark");
    function setDark(value) {
        requestAnimationFrame(() => {
            isDark.value = value;
            localStorage.setItem("theme", value ? "dark" : "light");
        });
    }
    return {
        isDark,
        setDark,
    };
});
