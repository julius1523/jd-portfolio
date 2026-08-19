<template>
    <v-app v-if="route.name">
        <appbar v-if="showAppBar" />
        <sidebar v-if="showSidebar" />
        <snackbar />
        <confirm />
        <v-main :class="{ 'bg-surface-light': layoutType === 'login' }">
            <router-view v-slot="{ Component, route }">
                <transition name="fade" mode="out-in">
                    <div :key="route.name">
                        <component :is="Component" />
                    </div>
                </transition>
            </router-view>
        </v-main>
        <footr v-if="showFooter" />
    </v-app>
</template>

<script setup>
import { watch, computed, defineAsyncComponent } from "vue";
import { useTheme, useDisplay } from "vuetify";
import { storeToRefs } from "pinia";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useThemeStore } from "@/stores/theme";
import { provideShimmerConfig } from "@shimmer-from-structure/vue";
import appbar from "@/components/layout/AppBar";
import sidebar from "@/components/layout/SideBar";
import footr from "@/components/layout/Footer";
const snackbar = defineAsyncComponent(() => import("@/components/ui/SnackBarQueue"));
const confirm = defineAsyncComponent(() => import("@/components/ui/ConfirmDialog"));
provideShimmerConfig({
    shimmerColor: 'rgba(156, 163, 175, 0.4)',
    backgroundColor: 'rgba(156, 163, 175, 0.15)',
    duration: 1.5,
    fallbackBorderRadius: 8,
});
const { isAuthenticated } = storeToRefs(useAuthStore());
const route = useRoute();
const theme = useTheme();
const themeStore = useThemeStore();
const { smAndDown } = useDisplay();
const layoutType = computed(() => {
    if (route.name === "not-found") {
        return isAuthenticated ? "app" : "public";
    }
    return route.meta.layout ?? "public";
});
const showAppBar = computed(() => layoutType.value !== "login");
const showSidebar = computed(() =>
    layoutType.value !== "login" && (isAuthenticated.value || smAndDown.value)
);
const showFooter = computed(() => layoutType.value === "public");
watch(
    () => themeStore.isDark,
    (isDark) => theme.change(isDark ? "dark" : "light")
);
</script>