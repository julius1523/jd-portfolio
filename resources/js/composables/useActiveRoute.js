import { useRoute } from "vue-router";

export default function useActiveRoute() {
    const route = useRoute();

    const isActive = (name) => route.name === name;

    return { isActive };
}
