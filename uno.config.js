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

const iconSizes = {
    sm: "18px",
    md: "20px",
    lg: "24px",
};
const iconRules = Object.entries(iconSizes).map(([suffix, size]) => [
    `icon-${suffix}`,
    {
        display: "inline-flex",
        width: size,
        height: size,
    },
]);

const rules = [...borderRules, ...iconRules];

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
    presets: [presetVuetify(), presetIcons()],
    rules,
    shortcuts: {
        "dialog-style": {
            "border-radius": "15px",
            "box-shadow": "0 4px 12px rgba(0, 0, 0, 0.08)",
        },
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
