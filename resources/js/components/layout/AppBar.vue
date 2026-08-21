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
    <v-app-bar v-if="!isAppLayout || $vuetify.display.smAndDown" app flat density="comfortable"
        :order="$vuetify.display.mdAndDown ? 1 : 0">
        <v-container class="d-flex flex-row align-center ga-2">
            <template v-if="!isAppLayout">
                <router-link :to="{ name: 'home' }">
                    <v-avatar size="40" color="primary">J</v-avatar>
                </router-link>

                <div class="d-flex ga-2 align-center ml-auto">
                    <div v-if="$vuetify.display.mdAndUp"
                        class="border border-opacity-25 pa-1 order-1 order-md-0 rounded-pill d-flex ga-1 align-center">
                        <v-btn height="31" :color="isActive('home') ? 'primary' : undefined" rounded="pill" text="Home"
                            :to="{ name: 'home' }" />
                        <v-btn height="31" :color="isActive('about') ? 'primary' : undefined" rounded="pill"
                            text="About" :to="{ name: 'about' }" />
                        <v-btn height="31" :color="isActive('projects') ? 'primary' : undefined" rounded="pill"
                            text="Projects" :to="{ name: 'projects' }" />
                        <v-btn height="31" :color="isActive('contact') ? 'primary' : undefined" rounded="pill"
                            text="Contact" :to="{ name: 'contact' }" />
                    </div>

                    <v-icon-btn size="40" icon-size="small"
                        :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" class="border border-opacity-25"
                        v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                        @click="toggleTheme" />

                    <v-icon-btn v-if="$vuetify.display.smAndDown" size="40" icon-size="23" icon="i-ri-menu-3-fill"
                        class="border border-opacity-25" @click="layout.toggleDrawer()" />

                </div>
            </template>
            <template v-else>
                <div class="d-flex flex-row justify-space-between align-center w-100">
                    <v-btn size="small" icon="i-ri-menu-2-fill" class="border" @click="layout.toggleDrawer()" />
                    <div class="text-title-medium font-weight-bold">Portfolio</div>
                    <ProfileMenu />
                </div>
            </template>
        </v-container>
    </v-app-bar>
</template>