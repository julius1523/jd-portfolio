<script setup>
import { ref, computed } from "vue";
import { storeToRefs } from "pinia";
import { useTheme } from "vuetify";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import { useThemeStore } from "@/stores/theme";
import ProfileMenu from "../ui/ProfileMenu";

const isScrolled = ref(false);
const onScroll = () => {
    isScrolled.value = window.scrollY > 0;
};
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
    <v-app-bar v-scroll="onScroll" v-if="!isAppLayout || $vuetify.display.smAndDown" app density="compact" flat
        :order="$vuetify.display.mdAndDown ? 1 : 0" :class="{ 'shadow-sm': isScrolled }">
        <v-container class="d-flex flex-row align-center ga-2">
            <template v-if="!isAppLayout">
                <router-link :to="{ name: 'home' }">
                    <v-avatar variant="elevated" class="bg-gradient-to-br from-blue-500 to-blue-900 text-white">
                        JD
                    </v-avatar>
                </router-link>
                <div class="d-flex ga-2 align-center ml-auto">
                    <v-toolbar v-if="$vuetify.display.mdAndUp" color="transparent" height="38" location="top end"
                        floating rounded="pill">
                        <div class="d-flex ga-1">
                            <v-btn height="32" active-color="primary" rounded="pill" text="Home"
                                :to="{ name: 'home' }" />
                            <v-btn height="32" active-color="primary" rounded="pill" text="About"
                                :to="{ name: 'about' }" />
                            <v-btn height="32" active-color="primary" rounded="pill" text="Projects"
                                :to="{ name: 'projects' }" />
                            <v-btn height="32" active-color="primary" rounded="pill" text="Contact"
                                :to="{ name: 'contact' }" />
                        </div>
                    </v-toolbar>
                    <v-divider v-if="$vuetify.display.mdAndUp" vertical :thickness="2" class="my-2"></v-divider>
                    <v-icon-btn :icon="themeStore.isDark ? 'i-ri-moon-line' : 'i-ri-sun-line'" size="32" icon-size="18"
                        v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                        @click="toggleTheme" />
                    <v-divider v-if="$vuetify.display.smAndDown" vertical :thickness="2" class="my-2"></v-divider>
                    <v-icon-btn v-if="$vuetify.display.smAndDown" icon="i-ri-menu-fill" size="32" icon-size="18"
                        @click="layout.toggleDrawer()" />
                </div>
            </template>
            <template v-else>
                <div class="d-flex flex-row justify-space-between align-center w-100">
                    <v-icon-btn variant="flat" icon="i-ri-menu-fill" size="32" icon-size="18"
                        @click=" layout.toggleDrawer()" />
                    <div class="text-title-medium font-weight-bold">Portfolio</div>
                    <ProfileMenu />
                </div>
            </template>
        </v-container>
    </v-app-bar>
</template>