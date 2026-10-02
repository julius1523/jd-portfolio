<script setup>
import { ref, computed } from "vue";
import { useTheme, useDisplay } from "vuetify";
import { useRoute } from "vue-router";
import { useLayoutStore } from "@/stores/layout";
import { useThemeStore } from "@/stores/theme";
import { useSystemSettingsStore } from "@/stores/systemSettings";
import { useLayoutType } from "@/composables/useLayoutType";

const theme = useTheme();
const display = useDisplay();
const route = useRoute();
const layout = useLayoutStore();
const themeStore = useThemeStore();
const layoutType = useLayoutType();
const systemSettings = useSystemSettingsStore();
const isAppLayout = computed(() => layoutType.value === "app");
const menuOpen = computed(() => layout.drawer && display.smAndDown.value);
const links = [
    { title: "Home", to: "home" },
    { title: "About", to: "about" },
    { title: "Projects", to: "projects" },
    { title: "Contact", to: "contact" },
];
const toggleTheme = (e) => {
    if (menuOpen.value) layout.toggleDrawer();

    theme.setTransitionOrigin(e.target);
    themeStore.setDark(!themeStore.isDark);
};
const ownerInitials = computed(() => {
    if (!systemSettings.systemOwner) return "";
    return systemSettings.systemOwner
        .trim()
        .split(/\s+/)
        .map((word) => word[0])
        .join("")
        .slice(0, 2)
        .toUpperCase();
});
const scrolled = ref(false);

function onScroll() {
    scrolled.value = window.scrollY > 10;
};
</script>

<template>
    <v-app-bar v-scroll="onScroll" app flat :order="1" density="comfortable" class="px-1" :class="[
        layoutType === 'app'
            ? 'border-b'
            : [menuOpen ? 'bg-surface' : 'bg-background', 'topbar !z-[1011]', { 'border-b': scrolled }]
    ]">
        <template v-slot:prepend v-if="!isAppLayout">
            <router-link :to="{ name: 'home' }">
                <v-img v-if="systemSettings.systemLogo?.url" :src="systemSettings.systemLogo.url" height="38" width="38"
                    rounded="circle" eager />
                <v-avatar v-else variant="flat" size="38"
                    class="bg-gradient-to-br from-[rgb(var(--v-theme-primary))] to-[rgb(var(--v-theme-primary))]/85 text-white">
                    {{ ownerInitials }}
                </v-avatar>
            </router-link>
        </template>
        <template v-slot:prepend v-else>
            <v-icon-btn v-if="$vuetify.display.smAndDown" icon="i-ri-menu-fill" size="38" icon-size="18"
                class="bg-transparent border" @click="layout.toggleNav()" />
            <router-link :to="{ name: 'manage-content' }" class="text-decoration-none ml-2">
                <v-app-bar-title :text="route.meta.title" class="text-title-medium font-weight-regular" />
            </router-link>
        </template>

        <template v-slot:append v-if="!isAppLayout">
            <div class="d-flex ga-1 align-center">
                <v-toolbar v-if="$vuetify.display.mdAndUp" color="surface" height="38" location="top end" floating
                    rounded="pill">
                    <div class="d-flex ga-1">
                        <v-btn v-for="link in links" :key="link.to" height="32" active-color="primary" rounded="pill"
                            :text="link.title" :to="{ name: link.to }" />
                    </div>
                </v-toolbar>
                <v-icon-btn :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" size="38" icon-size="18"
                    v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                    class="border" @click="toggleTheme" />
                <v-icon-btn v-if="$vuetify.display.smAndDown" :icon="menuOpen ? 'i-mdi-close' : 'i-ri-menu-fill'"
                    size="38" icon-size="18" class="border" @click="layout.toggleDrawer()" />
            </div>
        </template>
        <template v-slot:append v-else>
            <v-icon-btn :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" size="38" icon-size="18"
                v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }" class="border"
                @click="toggleTheme" />
        </template>
    </v-app-bar>

    <v-overlay v-if="!isAppLayout" :model-value="menuOpen" persistent no-click-animation :scrim="false"
        scroll-strategy="block" location-strategy="static" transition="fade-transition" :z-index="1010"
        content-class="w-100 h-100">
        <v-list density="compact" nav class="h-100 w-100 px-[13px] overflow-y-auto pt-[85px]">
            <v-list-item v-for="link in links" :key="link.to" :to="{ name: link.to }" exact color="primary"
                rounded="pill" class="text-center">
                <template #title>
                    <span class="text-title-medium">{{ link.title }}</span>
                </template>
            </v-list-item>
        </v-list>
    </v-overlay>
</template>

<style scoped>
.topbar :deep(.v-toolbar__content) {
    max-width: 1400px;
    margin: auto;
    width: 100%;
    padding-inline: 3px;
}
</style>