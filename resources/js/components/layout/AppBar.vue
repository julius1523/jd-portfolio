<script setup>
import { mdiMenu, mdiMoonWaningCrescent, mdiWhiteBalanceSunny } from "@mdi/js";
import { ref, watch, computed } from "vue";
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
                    <v-avatar size="38" color="primary">J</v-avatar>
                </router-link>

                <div class="border pa-1 rounded-pill d-flex ga-1 align-center ml-auto">
                    <template v-if="$vuetify.display.mdAndUp">
                        <v-btn density="comfortable" :color="isActive('home') ? 'primary' : undefined" rounded="pill"
                            text="Home" :to="{ name: 'home' }" />
                        <v-btn density="comfortable" :color="isActive('about') ? 'primary' : undefined" rounded="pill"
                            text="About" :to="{ name: 'about' }" />
                        <v-btn density="comfortable" :color="isActive('projects') ? 'primary' : undefined"
                            rounded="pill" text="Projects" :to="{ name: 'projects' }" />
                        <v-btn density="comfortable" :color="isActive('contact') ? 'primary' : undefined" rounded="pill"
                            text="Contact" :to="{ name: 'contact' }" />
                    </template>
                    <template v-else>
                        <v-icon-btn size="30" icon-size="23" class="order-1" :icon="mdiMenu"
                            @click="layout.toggleDrawer()" />
                    </template>
                    <v-icon-btn size="30" icon-size="small"
                        :icon="themeStore.isDark ? mdiMoonWaningCrescent : mdiWhiteBalanceSunny" class="order-0"
                        v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                        @click="toggleTheme" />
                </div>
            </template>
            <template v-else>
                <div class="d-flex flex-row justify-space-between align-center w-100">
                    <v-btn size="small" :icon="mdiMenu" class="border" @click="layout.toggleDrawer()" />
                    <div class="text-title-medium font-weight-bold">Portfolio</div>
                    <ProfileMenu />
                </div>
            </template>
        </v-container>
    </v-app-bar>
</template>