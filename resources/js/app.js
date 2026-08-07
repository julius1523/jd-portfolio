import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "@/App.vue";
import vuetify from "@/plugins/vuetify";
import router from "@/router";
import tooltipPlugin from "@/plugins/tooltip";
import { Shimmer } from "@shimmer-from-structure/vue";
import "@/plugins/axios";

const app = createApp(App);
const pinia = createPinia();

app.component("Shimmer", Shimmer);

app.use(pinia);
app.use(vuetify);
app.use(router);
app.use(tooltipPlugin);
app.mount("#app");
