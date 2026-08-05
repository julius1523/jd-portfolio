<template>
    <v-app v-if="route.name">
        <appbar v-if="showAppBar" />
        <sidebar v-if="showSidebar" />
        <snackbar />
        <confirm />
        <v-main :class="{ 'bg-surface-light': layoutType === 'login' }" :style="{
            '--v-layout-bottom': $vuetify.display.mdAndDown ? '75px' : '68px'
        }">
            <router-view v-slot="{ Component, route }">
                <transition name="fade" mode="out-in">
                    <div :key="route.fullPath">
                        <component :is="Component" />
                    </div>
                </transition>
            </router-view>
        </v-main>
        <footr v-if="showFooter" />
    </v-app>
</template>

<script setup>
import appbar from "@/components/layout/AppBar";
import sidebar from "@/components/layout/SideBar";
import footr from "@/components/layout/Footer";
import snackbar from "@/components/ui/SnackBarQueue";
import confirm from "@/components/ui/ConfirmDialog";
import { watch, computed } from "vue";
import { useTheme } from "vuetify";
import { useThemeStore } from "@/stores/theme";
import { useRoute } from "vue-router";

import { provideShimmerConfig } from '@shimmer-from-structure/vue';

provideShimmerConfig({
    shimmerColor: 'rgba(156, 163, 175, 0.4)',
    backgroundColor: 'rgba(156, 163, 175, 0.15)',
    duration: 1.5,
    fallbackBorderRadius: 8,
});

const route = useRoute();
const theme = useTheme();
const themeStore = useThemeStore();

const layoutType = computed(() => route.meta.layout ?? "public");
const showAppBar = computed(() => layoutType.value !== "login");
const showSidebar = computed(() => layoutType.value === "app");
const showFooter = computed(() => layoutType.value === "public");

watch(
    () => themeStore.isDark,
    (isDark) => theme.change(isDark ? "dark" : "light")
);
</script>