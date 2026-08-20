import { defineConfig } from "unocss";
import { presetVuetify } from "unocss-preset-vuetify";

export default defineConfig({
    presets: [
        presetVuetify({
            exclude: ["cursor"],
        }),
    ],
    safelist: [
        ...Array.from({ length: 6 }, (_, i) => `elevation-${i}`),
        ["", "-0", "-sm", "-lg", "-xl", "-pill", "-circle", "-shaped"].map(
            (suffix) => `rounded${suffix}`,
        ),
    ],
});
