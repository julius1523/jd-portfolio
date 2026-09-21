<script setup>
import { ref, computed } from "vue";
import { useTheme } from "vuetify";
import { useRoute } from "vue-router";
import { useLayoutStore } from "@/stores/layout";
import { useThemeStore } from "@/stores/theme";
import { useLayoutType } from "@/composables/useLayoutType";

const theme = useTheme();
const route = useRoute();
const layout = useLayoutStore();
const themeStore = useThemeStore();
const layoutType = useLayoutType();
const isAppLayout = computed(() => layoutType.value === "app");
const toggleTheme = (e) => {
    theme.setTransitionOrigin(e.target);
    themeStore.setDark(!themeStore.isDark);
};
const scrolled = ref(false);

function onScroll() {
    scrolled.value = window.scrollY > 10;
};
</script>

<template>
    <v-app-bar v-scroll="onScroll" app flat :order="1" density="comfortable"
        :class="[layoutType === 'app' ? 'px-2' : 'topbar', { 'shadow-sm': scrolled }]">
        <template v-slot:prepend v-if="!isAppLayout">
            <router-link :to="{ name: 'home' }">
                <v-avatar variant="elevated"
                    class="bg-gradient-to-br from-[rgb(var(--v-theme-primary))] to-[rgb(var(--v-theme-primary))]/85 text-white">
                    JD
                </v-avatar>
            </router-link>
        </template>
        <template v-slot:prepend v-else>
            <v-icon-btn v-if="$vuetify.display.smAndDown" icon="i-ri-menu-fill" size="38" icon-size="18"
                class="bg-transparent" @click="layout.toggleNav()" />
            <router-link :to="{ name: 'manage-content' }" class="text-decoration-none">
                <v-app-bar-title :text="route.meta.title" class="ml-2 text-title-medium" />
            </router-link>
        </template>

        <template v-slot:append v-if="!isAppLayout">
            <div class="d-flex ga-1 align-center">
                <v-toolbar v-if="$vuetify.display.mdAndUp" color="transparent" height="38" location="top end" floating
                    rounded="pill">
                    <div class="d-flex ga-1">
                        <v-btn height="32" active-color="primary" rounded="pill" text="Home" :to="{ name: 'home' }" />
                        <v-btn height="32" active-color="primary" rounded="pill" text="About" :to="{ name: 'about' }" />
                        <v-btn height="32" active-color="primary" rounded="pill" text="Projects"
                            :to="{ name: 'projects' }" />
                        <v-btn height="32" active-color="primary" rounded="pill" text="Contact"
                            :to="{ name: 'contact' }" />
                    </div>
                </v-toolbar>
                <v-icon-btn :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" size="38" icon-size="18"
                    v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                    class="bg-transparent" @click="toggleTheme" />
                <v-icon-btn v-if="$vuetify.display.smAndDown" :key="layout.drawer" icon="i-ri-menu-fill" size="38"
                    icon-size="18" class="bg-transparent" @click="layout.toggleDrawer()" />
            </div>
        </template>
        <template v-slot:append v-else>
            <v-icon-btn :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" size="38" icon-size="18"
                v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                class="bg-transparent" @click="toggleTheme" />
        </template>
    </v-app-bar>
</template>

<style scoped>
.topbar :deep(.v-toolbar__content) {
    max-width: 1400px;
    margin: auto;
    width: 100%;
    padding-inline: 6px;
}
</style>