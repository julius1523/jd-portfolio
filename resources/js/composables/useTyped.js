import { ref, watch, onUnmounted } from "vue";
import Typed from "typed.js";

export function useTyped(options) {
    const el = ref(null);
    let instance = null;

    watch(el, (element) => {
        if (element && !instance) {
            instance = new Typed(element, options);
        }
    });
    return { el };
}
