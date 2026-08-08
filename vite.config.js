import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import vuetify from "vite-plugin-vuetify";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.scss", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
        vuetify({
            autoImport: {
                ignore: ["Tooltip"],
            },
            styles: {
                configFile: "resources/css/styles/settings.scss",
            },
        }),
    ],
    optimizeDeps: {
        exclude: ["vuetify"],
    },
    resolve: {
        alias: {
            "~": path.resolve(import.meta.dirname, "resources/js"),
            "@": path.resolve(import.meta.dirname, "resources/js"),
        },
        extensions: [".mjs", ".js", ".ts", ".jsx", ".tsx", ".json", ".vue"],
    },
    server: {
        host: "0.0.0.0",
        port: 5173,

        hmr: {
            host: "192.168.1.31",
            port: 5173,
            protocol: "ws",
        },

        cors: {
            origin: [
                "http://127.0.0.1:8000",
                "http://localhost:8000",
                "http://192.168.1.31:8000",
            ],
            credentials: true,
        },
    },
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes("node_modules")) {
                        if (id.includes("vuetify")) return "vuetify";
                        if (id.includes("vue")) return "vue";
                        return "vendor";
                    }
                },
                entryFileNames: `assets/[name]-[hash].js`,
                chunkFileNames: `assets/[name]-[hash].js`,
                assetFileNames: `assets/[name]-[hash].[ext]`,
            },
        },
    },
});
