import { computed } from "vue";
import { useRoute } from "vue-router";
import { storeToRefs } from "pinia";
import { useAuthStore } from "@/stores/auth";

export function useLayoutType() {
    const route = useRoute();
    const { isAuthenticated } = storeToRefs(useAuthStore());

    return computed(() => {
        if (route.name === "not-found") {
            return isAuthenticated.value ? "app" : "public";
        }
        return route.meta.layout ?? "public";
    });
}
