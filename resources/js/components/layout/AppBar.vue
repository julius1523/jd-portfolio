<script setup>
import { ref, watch, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import useActiveRoute from "@/composables/useActiveRoute";
import { useThemeStore } from '@/stores/theme';
import { useLayoutStore } from "@/stores/layout";
import { useRoute } from "vue-router";
import { useDisplay } from "vuetify";
import ProfileMenu from "../ui/ProfileMenu";
const auth = useAuthStore();
const isScrolled = ref(false);
const route = useRoute();
const themeStore = useThemeStore();
const layout = useLayoutStore();
const { isActive } = useActiveRoute();
const { smAndDown } = useDisplay();
const layoutType = computed(() => {
    if (route.name === 'not-found') {
        return auth.isAuthenticated ? 'app' : 'public';
    }
    return route.meta.layout ?? 'public';
});
const isAppLayout = computed(() => layoutType.value === 'app');
const onScroll = () => {
    isScrolled.value = window.scrollY > 10;
};
</script>

<template>
    <v-app-bar v-if="!isAppLayout || $vuetify.display.smAndDown" v-scroll="onScroll" app flat density="comfortable"
        :order="$vuetify.display.mdAndDown ? 1 : 0" :class="{ 'border-b': isScrolled }">
        <v-container class="d-flex flex-row align-center ga-2">
            <template v-if="!isAppLayout">
                <router-link :to="{ name: 'home' }">
                    <v-avatar size="33" color="primary" rounded="lg">J</v-avatar>
                </router-link>

                <div class="d-flex align-center ga-2 ml-auto">
                    <template v-if="$vuetify.display.mdAndUp">
                        <v-btn :color="isActive('home') ? 'primary' : undefined" height="28" rounded="pill" text="Home"
                            :to="{ name: 'home' }"></v-btn>
                        <v-btn :color="isActive('about') ? 'primary' : undefined" height="28" rounded="pill"
                            text="About" :to="{ name: 'about' }"></v-btn>
                        <v-btn :color="isActive('projects') ? 'primary' : undefined" height="28" rounded="pill"
                            text="Projects" :to="{ name: 'projects' }"></v-btn>
                        <v-btn :color="isActive('contact') ? 'primary' : undefined" height="28" rounded="pill"
                            text="Contact" :to="{ name: 'contact' }"></v-btn>
                    </template>
                    <v-btn
                        :icon="themeStore.isDark ? 'mdi-moon-waning-crescent mdi-rotate-315 opacity-80' : 'mdi-white-balance-sunny opacity-80'"
                        v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                        @click="themeStore.setDark(!themeStore.isDark)" variant="text" size="x-small"></v-btn>
                </div>
            </template>
            <template v-else>
                <div class="d-flex flex-row justify-space-between align-center w-100">
                    <v-btn size="small" icon="mdi-menu" class="border" @click="layout.toggleDrawer()" />
                    <div class="text-title-medium font-weight-bold">Portfolio</div>
                    <ProfileMenu />
                </div>
            </template>
        </v-container>
    </v-app-bar>

    <v-bottom-navigation v-if="!isAppLayout && $vuetify.display.smAndDown" :elevation="0" grow rounded="pill"
        class="border mx-auto pa-1">
        <v-btn :color="isActive('home') ? 'primary' : undefined" rounded="pill" :to="{ name: 'home' }">
            <v-icon size="27">mdi-home</v-icon>
            Home
        </v-btn>

        <v-btn :color="isActive('about') ? 'primary' : undefined" rounded="pill" :to="{ name: 'about' }">
            <v-icon size="27">mdi-information</v-icon>
            About
        </v-btn>

        <v-btn :color="isActive('projects') ? 'primary' : undefined" rounded="pill" :to="{ name: 'projects' }">
            <v-icon size="27">mdi-briefcase-variant</v-icon>
            Projects
        </v-btn>

        <v-btn :color="isActive('contact') ? 'primary' : undefined" rounded="pill" :to="{ name: 'contact' }">
            <v-icon size="27">mdi-phone</v-icon>
            Contact
        </v-btn>
    </v-bottom-navigation>
</template>