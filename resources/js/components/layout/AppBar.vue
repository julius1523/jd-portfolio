<script setup>
import { computed } from "vue";
import { storeToRefs } from "pinia";
import { useTheme } from "vuetify";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import { useThemeStore } from '@/stores/theme';
import ProfileMenu from "../ui/ProfileMenu";
import useActiveRoute from "@/composables/useActiveRoute";
const auth = useAuthStore();
const { isAuthenticated } = storeToRefs(auth);
const theme = useTheme();
const route = useRoute();
const layout = useLayoutStore();
const themeStore = useThemeStore();
const { isActive } = useActiveRoute();
const layoutType = computed(() => {
    if (route.name === 'not-found') {
        return isAuthenticated.value ? 'app' : 'public';
    }
    return route.meta.layout ?? 'public';
});
const isAppLayout = computed(() => layoutType.value === 'app');
const toggleTheme = (e) => {
    theme.setTransitionOrigin(e.target);
    themeStore.setDark(!themeStore.isDark);
};
</script>

<template>
    <v-app-bar v-if="!isAppLayout || $vuetify.display.smAndDown" app density="compact" scroll-behavior="elevate"
        :order="$vuetify.display.mdAndDown ? 1 : 0">
        <v-container class="d-flex flex-row align-center ga-2">
            <template v-if="!isAppLayout">
                <router-link :to="{ name: 'home' }">
                    <v-avatar size="40" color="primary">JD</v-avatar>
                </router-link>
                <div class="d-flex ga-2 align-center ml-auto">
                    <v-toolbar v-if="$vuetify.display.mdAndUp" color="transparent" height="40" location="top end"
                        floating rounded="pill">
                        <div class="d-flex ga-1">
                            <v-btn height="40" :color="isActive('home') ? 'primary' : undefined" rounded="pill"
                                text="Home" :to="{ name: 'home' }" />
                            <v-btn height="40" :color="isActive('about') ? 'primary' : undefined" rounded="pill"
                                text="About" :to="{ name: 'about' }" />
                            <v-btn height="40" :color="isActive('projects') ? 'primary' : undefined" rounded="pill"
                                text="Projects" :to="{ name: 'projects' }" />
                            <v-btn height="40" :color="isActive('contact') ? 'primary' : undefined" rounded="pill"
                                text="Contact" :to="{ name: 'contact' }" />
                        </div>
                    </v-toolbar>
                    <v-divider v-if="$vuetify.display.mdAndUp" vertical :thickness="2" class="my-2"></v-divider>
                    <v-icon-btn :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" icon-size="18"
                        v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                        @click="toggleTheme" />
                    <v-divider v-if="$vuetify.display.smAndDown" vertical :thickness="2" class="my-2"></v-divider>
                    <v-icon-btn v-if="$vuetify.display.smAndDown" icon="i-ri-menu-fill" icon-size="18"
                        @click="layout.toggleDrawer()" />
                </div>
            </template>
            <template v-else>
                <div class="d-flex flex-row justify-space-between align-center w-100">
                    <v-icon-btn variant="flat" icon="i-ri-menu-fill" icon-size="18" @click=" layout.toggleDrawer()" />
                    <div class="text-title-medium font-weight-bold">Portfolio</div>
                    <ProfileMenu />
                </div>
            </template>
        </v-container>
    </v-app-bar>
</template>