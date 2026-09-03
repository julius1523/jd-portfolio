import { ref, watch } from "vue";
import Typed from "typed.js";

export function useTyped(strings, options = {}) {
    const el = ref(null);
    let instance = null;

    watch(
        [el, strings],
        ([element, value]) => {
            if (!element || !value?.length) return;

            instance?.destroy();

            instance = new Typed(element, {
                ...options,
                strings: value,
            });
        },
        { immediate: true },
    );

    return { el };
}
