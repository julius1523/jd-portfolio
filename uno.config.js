import { defineConfig, presetIcons } from "unocss";
import { presetWind4 } from "unocss/preset-wind4";
import { presetVuetify } from "unocss-preset-vuetify";

const breakpoints = {
    sm: "600px",
    md: "960px",
    lg: "1280px",
    xl: "1920px",
    xxl: "2560px",
};
const borderColor = "rgba(var(--v-border-color), var(--v-border-opacity))";
const borderSides = {
    "": ["top", "right", "bottom", "left"],
    "-t": ["top"],
    "-r": ["right"],
    "-b": ["bottom"],
    "-l": ["left"],
    "-x": ["left", "right"],
    "-y": ["top", "bottom"],
};
const borderRules = Object.entries(borderSides).map(([suffix, sides]) => [
    `border${suffix}`,
    Object.fromEntries(
        sides.flatMap((side) => [
            [`border-${side}-width`, "1px"],
            [`border-${side}-style`, "solid"],
            [`border-${side}-color`, borderColor],
        ]),
    ),
]);

const rules = [...borderRules];

export default defineConfig({
    content: {
        pipeline: {
            include: [
                /\.(vue|svelte|[jt]sx|mdx?|astro|elm|php|phtml|html)($|\?)/,
                "resources/js/composables/**/*.{js,ts}",
                "resources/js/src/constants/**/*.{js,ts}",
            ],
        },
    },
    presets: [
        presetWind4({
            preflights: {
                reset: false,
            },
            dark: {
                dark: ".v-theme--dark",
                light: ".v-theme--light",
            },
        }),
        presetVuetify(),
        presetIcons({
            collections: {
                ri: () =>
                    import("@iconify-json/ri/icons.json", {
                        with: { type: "json" },
                    }).then((i) => i.default),
                mdi: () =>
                    import("@iconify-json/mdi/icons.json", {
                        with: { type: "json" },
                    }).then((i) => i.default),
            },
        }),
    ],
    rules,
    shortcuts: {
        "fade-bottom":
            "[mask-image:linear-gradient(to_bottom,black_0%,black_60%,transparent_100%)] " +
            "[-webkit-mask-image:linear-gradient(to_bottom,black_0%,black_60%,transparent_100%)]",
        "clamped-img":
            "w-full min-w-[var(--img-min-w,0)] max-w-[min(var(--img-max-w,100%),90vw)] " +
            "h-[clamp(var(--img-min-h,150px),40vw,var(--img-max-h,450px))]",
        "clamped-img--square":
            "h-auto aspect-square max-w-[min(var(--img-max-w,100%),35vw)]",
        "translate-y-hover":
            "transition-transform duration-700 ease-out hover:-translate-y-2",
        "scale-up-hover":
            "transition-transform duration-500 ease-in-out hover:scale-105",
        "scale-down-hover":
            "transition-transform duration-500 ease-in-out hover:scale-98",
    },
    variants: [
        (matcher) => {
            const match = matcher.match(/^(.+)-(sm|md|lg|xl|xxl)-(.+)$/);
            if (!match) {
                return matcher;
            }
            const [, utility, breakpoint, value] = match;
            return {
                matcher: `${utility}-${value}-${breakpoint}`,
                parent: `@media (min-width: ${breakpoints[breakpoint]})`,
            };
        },
    ],
    safelist: [
        ...Array.from({ length: 6 }, (_, i) => `elevation-${i}`),
        ...["", "-0", "-sm", "-lg", "-xl", "-pill", "-circle", "-shaped"].map(
            (suffix) => `rounded${suffix}`,
        ),
        "bg-primary",
        "text-primary",
        "bg-success",
        "text-success",
        "bg-error",
        "text-error",
    ],
});
