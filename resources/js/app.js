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
import allowChars from "@/src/directives/allowChars";

const app = createApp(App);
const pinia = createPinia();

app.component("Shimmer", Shimmer);
app.use(pinia);
app.use(vuetify);
app.use(router);
app.use(tooltipPlugin);
app.directive("allow-chars", allowChars);
historyGuard(router);

const settingsStore = useSystemSettingsStore();
const hydrated = settingsStore.hydrate();

Promise.all([
    router.isReady(),
    hydrated ? Promise.resolve() : settingsStore.fetch(),
]).then(() => {
    app.mount("#app");
});
