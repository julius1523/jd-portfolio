<script setup>
import { computed } from "vue";
import { storeToRefs } from "pinia";
import { useTheme } from "vuetify";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import { useThemeStore } from "@/stores/theme";

const auth = useAuthStore();
const { isAuthenticated } = storeToRefs(auth);
const theme = useTheme();
const route = useRoute();
const layout = useLayoutStore();
const themeStore = useThemeStore();
const layoutType = computed(() => {
    if (route.name === "not-found") {
        return isAuthenticated.value ? "app" : "public";
    }
    return route.meta.layout ?? "public";
});
const isAppLayout = computed(() => layoutType.value === "app");
const toggleTheme = (e) => {
    theme.setTransitionOrigin(e.target);
    themeStore.setDark(!themeStore.isDark);
};
</script>

<template>
    <v-app-bar app density="comfortable" flat :order="$vuetify.display.smAndDown ? 1 : 0"
        :class="{ 'topbar': !isAuthenticated }">
        <template v-slot:prepend v-if="!isAppLayout">
            <router-link :to="{ name: 'home' }">
                <v-avatar variant="elevated" class="bg-gradient-to-br from-blue-500 to-blue-900 text-white">
                    JD
                </v-avatar>
            </router-link>
        </template>
        <template v-slot:prepend v-else>
            <v-app-bar-nav-icon density="comfortable" size="40" class="ml-1" @click="layout.toggleNav()" />
            <router-link :to="{ name: 'manage-content' }" class="text-decoration-none">
                <v-app-bar-title text="Portfolio" class="ml-2 text-title-medium" />
            </router-link>
        </template>

        <template v-slot:append v-if="!isAppLayout">
            <div class="d-flex ga-2 align-center">
                <v-toolbar v-if="$vuetify.display.mdAndUp" color="surface" height="38" location="top end" floating
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
                <v-icon-btn :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" size="32" icon-size="18"
                    v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                    @click="toggleTheme" />
                <v-icon-btn v-if="$vuetify.display.smAndDown" icon="i-ri-menu-fill" size="32" icon-size="18"
                    @click="layout.toggleDrawer()" />
            </div>
        </template>
        <template v-slot:append v-else>
            <v-icon-btn :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" size="36" icon-size="20"
                v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                @click="toggleTheme" />
        </template>
    </v-app-bar>
</template>

<style scoped>
.topbar :deep(.v-toolbar__content) {
    max-width: 1400px;
    margin: auto;
    width: 100%;
    padding-inline: 8px;
}
</style>