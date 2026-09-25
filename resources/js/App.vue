<template>
    <v-app v-if="route.name" :key="layoutType">
        <appbar v-if="showAppBar" />
        <sidebar v-if="showSidebar" />
        <snackbar />
        <confirm />
        <v-main>
            <router-view v-slot="{ Component, route: viewRoute }">
                <Transition name="fade" mode="out-in" appear @enter="onEnter">
                    <div :key="viewRoute.name" class="route-view">
                        <component :is="Component" />
                    </div>
                </Transition>
            </router-view>
        </v-main>
        <footr v-if="showFooter" />

        <v-fab :active="visible" icon elevation="1" border app :layout="true" appear
            transition="slide-y-reverse-transition" :size="$vuetify.display.mdAndUp ? undefined : 'small'"
            @click="scrollToTop">
            <v-icon icon="i-mdi-arrow-up" color="primary"></v-icon>
        </v-fab>
    </v-app>
</template>

<script setup>
import { watch, computed, defineAsyncComponent, watchEffect, nextTick } from "vue";
import { useTheme, useDisplay } from "vuetify";
import { useRoute } from "vue-router";
import { useThemeStore } from "@/stores/theme";
import { provideShimmerConfig } from "@shimmer-from-structure/vue";
import { useLayoutType } from "@/composables/useLayoutType";
import { useSystemColor } from "@/composables/useSystemColor";
import { useScrollToTop } from "@/composables/useScrollToTop";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { scrollGate } from "@/router/scrollGate";
import appbar from "@/components/layout/AppBar";
import sidebar from "@/components/layout/SideBar";
import footr from "@/components/layout/Footer";

const snackbar = defineAsyncComponent(() => import("@/components/ui/SnackBarQueue"));
const confirm = defineAsyncComponent(() => import("@/components/ui/ConfirmDialog"));

useSystemColor();

provideShimmerConfig({
    shimmerColor: "rgba(156, 163, 175, 0.4)",
    backgroundColor: "rgba(156, 163, 175, 0.15)",
    duration: 1.5,
    fallbackBorderRadius: 8,
});

const route = useRoute();
const theme = useTheme();
const themeStore = useThemeStore();
const { smAndDown } = useDisplay();
const layoutType = useLayoutType();
const { visible, scrollToTop } = useScrollToTop(300);
const showAppBar = computed(() => layoutType.value !== "login");
const showSidebar = computed(
    () =>
        layoutType.value !== "login" &&
        (layoutType.value === "app" || smAndDown.value)
);
const showFooter = computed(() => layoutType.value === "public");

const onEnter = () => {
    nextTick(() => {
        ScrollTrigger.refresh();
        scrollGate.open();
    });
};

watch(
    () => themeStore.isDark,
    (isDark) => theme.change(isDark ? "dark" : "light")
);

watchEffect(() => {
    const bg = theme.current.value.colors.background;
    document.documentElement.style.backgroundColor = bg;
    document.body.style.backgroundColor = bg;
});
</script>