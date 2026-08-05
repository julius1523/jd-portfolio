import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "@/App.vue";
import vuetify from "@/plugins/vuetify";
import "@/plugins/axios";
import router from "@/router";
import tooltipPlugin from "@/plugins/tooltip";
import LazyLoad from "@/components/ui/LazyLoad";
import { Shimmer } from "@shimmer-from-structure/vue";

const app = createApp(App);
const pinia = createPinia();

app.component("Shimmer", Shimmer);
app.component("LazyLoad", LazyLoad);

app.use(pinia);
app.use(vuetify);
app.use(router);
app.use(tooltipPlugin);
app.mount("#app");
