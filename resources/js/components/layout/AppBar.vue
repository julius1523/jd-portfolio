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
const isScrolled = ref(false);
const { isActive } = useActiveRoute();
const layoutType = computed(() => {
    if (route.name === 'not-found') {
        return isAuthenticated.value ? 'app' : 'public';
    }
    return route.meta.layout ?? 'public';
});
const isAppLayout = computed(() => layoutType.value === 'app');
const onScroll = () => {
    isScrolled.value = window.scrollY > 10;
};
const toggleTheme = (e) => {
    theme.setTransitionOrigin(e.target);
    themeStore.setDark(!themeStore.isDark);
};
</script>

<template>
    <v-app-bar v-if="!isAppLayout || $vuetify.display.smAndDown" v-scroll="onScroll" app flat density="comfortable"
        :order="$vuetify.display.mdAndDown ? 1 : 0" :class="{ 'border-b': isScrolled }">
        <v-container class="d-flex flex-row align-center ga-2">
            <template v-if="!isAppLayout">
                <router-link :to="{ name: 'home' }">
                    <v-avatar size="small" color="primary">J</v-avatar>
                </router-link>

                <div class="d-flex align-center ga-2 ml-auto">
                    <template v-if="$vuetify.display.mdAndUp">
                        <v-btn :color="isActive('home') ? 'primary' : undefined" height="28" rounded="pill" text="Home"
                            :to="{ name: 'home' }" />
                        <v-btn :color="isActive('about') ? 'primary' : undefined" height="28" rounded="pill"
                            text="About" :to="{ name: 'about' }" />
                        <v-btn :color="isActive('projects') ? 'primary' : undefined" height="28" rounded="pill"
                            text="Projects" :to="{ name: 'projects' }" />
                        <v-btn :color="isActive('contact') ? 'primary' : undefined" height="28" rounded="pill"
                            text="Contact" :to="{ name: 'contact' }" />
                    </template>
                    <template v-else>
                        <v-btn size="x-small" :icon="mdiMenu" class="order-1" @click="layout.toggleDrawer()"></v-btn>
                    </template>
                    <v-btn :icon="themeStore.isDark ? mdiMoonWaningCrescent : mdiWhiteBalanceSunny"
                        v-tooltip="{ text: themeStore.isDark ? 'Light Mode' : 'Dark Mode', location: 'bottom' }"
                        @click="toggleTheme" variant="text" size="x-small" />
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