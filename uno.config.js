import { defineConfig, presetIcons } from "unocss";
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
const rules = Object.entries(borderSides).map(([suffix, sides]) => [
    `border${suffix}`,
    Object.fromEntries(
        sides.flatMap((side) => [
            [`border-${side}-width`, "1px"],
            [`border-${side}-style`, "solid"],
            [`border-${side}-color`, borderColor],
        ]),
    ),
]);

export default defineConfig({
    content: {
        pipeline: {
            include: [
                /\.(vue|svelte|[jt]sx|mdx?|astro|elm|php|phtml|html)($|\?)/,
                "resources/js/composables/**/*.{js,ts}",
            ],
        },
    },
    presets: [presetVuetify(), presetIcons()],
    rules,
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
