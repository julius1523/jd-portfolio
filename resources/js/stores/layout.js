import { defineStore } from "pinia";
import { ref, watch } from "vue";
import { useDisplay } from "vuetify";

export const useLayoutStore = defineStore("layout", () => {
    const { smAndDown } = useDisplay();

    const drawer = ref(!smAndDown.value);
    const rail = ref(false);

    watch(smAndDown, (isSmAndDown) => {
        if (isSmAndDown) {
            rail.value = false;
        }
    });

    const toggleDrawer = () => {
        drawer.value = !drawer.value;
    };

    const toggleRail = () => {
        if (smAndDown.value) return;
        rail.value = !rail.value;
    };

    return { drawer, toggleDrawer, rail, toggleRail };
});
