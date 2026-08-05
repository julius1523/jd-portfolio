// resources/js/plugins/tooltip.js
import { createApp, h, reactive } from "vue";
import { VTooltip } from "vuetify/components";
import vuetify from "@/plugins/vuetify";

function normalizeOptions(value) {
    return typeof value === "string" ? { text: value } : value || {};
}

const state = reactive({
    isActive: false,
    text: "",
    location: "top",
    activator: null,
});

const SHOW_DELAY = 500;

let mounted = false;
function ensureSharedTooltipMounted() {
    if (mounted) return;
    mounted = true;

    const container = document.createElement("div");
    document.body.appendChild(container);

    const tooltipApp = createApp({
        render() {
            return h(
                VTooltip,
                {
                    modelValue: state.isActive,
                    "onUpdate:modelValue": (v) => (state.isActive = v),
                    activator: state.activator,
                    location: state.location,
                    openOnHover: false,
                    openOnFocus: false,
                    openOnClick: false,
                },
                { default: () => state.text },
            );
        },
    });

    tooltipApp.use(vuetify);
    tooltipApp.mount(container);
}

const suppressed = new WeakMap();

export default {
    install(app) {
        ensureSharedTooltipMounted();

        app.directive("tooltip", {
            mounted(el, binding) {
                el.__tooltipOptions = normalizeOptions(binding.value);
                el.__tooltipTimer = null;

                const clearTimer = () => {
                    if (el.__tooltipTimer) {
                        clearTimeout(el.__tooltipTimer);
                        el.__tooltipTimer = null;
                    }
                };

                const onPointerEnter = () => {
                    if (suppressed.get(el)) return;
                    clearTimer();
                    el.__tooltipTimer = setTimeout(() => {
                        el.__tooltipTimer = null;
                        const opts = el.__tooltipOptions;
                        if (opts.disabled) return;
                        state.text = opts.text ?? "";
                        state.location = opts.location ?? "top";
                        state.activator = el;
                        state.isActive = true;
                    }, SHOW_DELAY);
                };
                const onPointerLeave = () => {
                    clearTimer();
                    suppressed.set(el, false);
                    if (state.activator === el) state.isActive = false;
                };
                const onPointerDown = () => {
                    clearTimer();
                    suppressed.set(el, true);
                    if (state.activator === el) state.isActive = false;
                };

                el.addEventListener("pointerenter", onPointerEnter);
                el.addEventListener("pointerleave", onPointerLeave);
                el.addEventListener("pointerdown", onPointerDown);

                el.__tooltip = {
                    onPointerEnter,
                    onPointerLeave,
                    onPointerDown,
                    clearTimer,
                };
            },
            updated(el, binding) {
                el.__tooltipOptions = normalizeOptions(binding.value);
                if (state.activator === el && el.__tooltipOptions.disabled) {
                    el.__tooltip?.clearTimer();
                    state.isActive = false;
                }
            },
            unmounted(el) {
                const t = el.__tooltip;
                if (!t) return;
                t.clearTimer();
                el.removeEventListener("pointerenter", t.onPointerEnter);
                el.removeEventListener("pointerleave", t.onPointerLeave);
                el.removeEventListener("pointerdown", t.onPointerDown);
                suppressed.delete(el);
                if (state.activator === el) state.isActive = false;
            },
        });
    },
};
