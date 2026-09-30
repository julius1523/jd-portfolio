import "unfonts.css";
import "virtual:uno.css";
import "../css/app.css";
import "overlayscrollbars/overlayscrollbars.css";
import { createApp } from "vue";
import { createPinia } from "pinia";
import "@/plugins/axios";
import App from "@/App.vue";
import vuetify from "@/plugins/vuetify";
import router from "@/router";
import tooltipPlugin from "@/plugins/tooltip";
import { Shimmer } from "@shimmer-from-structure/vue";
import { historyGuard } from "@/router/guard";
import { useSystemSettingsStore } from "@/stores/systemSettings";

const app = createApp(App);
const pinia = createPinia();

app.component("Shimmer", Shimmer);
app.use(pinia);
app.use(vuetify);
app.use(router);
app.use(tooltipPlugin);
historyGuard(router);

const settingsStore = useSystemSettingsStore();

Promise.all([
    router.isReady(),
    settingsStore.fetch(),
    Promise.all([
        document.fonts.load("400 1em Roboto"),
        document.fonts.load("500 1em Roboto"),
        document.fonts.load("700 1em Roboto"),
    ]).catch(() => {}),
]).then(() => {
    app.mount("#app");
});
