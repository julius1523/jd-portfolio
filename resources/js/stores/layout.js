import { defineStore } from "pinia";
import { ref, watch } from "vue";
import { useDisplay } from "vuetify";
import router from "@/router";

export const useLayoutStore = defineStore("layout", () => {
    const { mdAndUp } = useDisplay();

    const drawer = ref(mdAndUp.value);
    const rail = ref(mdAndUp.value);

    watch(mdAndUp, (isMdAndUp) => {
        if (isMdAndUp) {
            drawer.value = true;
        } else {
            drawer.value = false;
            rail.value = false;
        }
    });

    router.afterEach(() => {
        if (!mdAndUp.value) {
            drawer.value = false;
        }
    });

    const toggleDrawer = () => (drawer.value = !drawer.value);
    const toggleRail = () => {
        if (!mdAndUp.value) return;
        rail.value = !rail.value;
    };
    const toggleNav = () => {
        if (mdAndUp.value) rail.value = !rail.value;
        else drawer.value = !drawer.value;
    };

    return { drawer, toggleDrawer, rail, toggleRail, toggleNav };
});
