<script setup>
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useSystemSettingsStore } from "@/stores/systemSettings";
import { useLayoutStore } from "@/stores/layout";
import { useLayoutType } from "@/composables/useLayoutType";
import { confirm } from "@/composables/useConfirmDialog";

const router = useRouter();
const auth = useAuthStore();
const settings = useSystemSettingsStore();
const layout = useLayoutStore();
const layoutType = useLayoutType();
const logout = () => {
    confirm({
        title: "Log Out",
        message: "Are you sure you want to log out?",
        confirmText: "Log out",
        cancelText: "Cancel",
        confirmColor: "error",
        onConfirm: async () => {
            auth.logout();
            await router.replace({ name: "login" });
        },
    });
};
</script>

<template>
    <v-navigation-drawer :key="$vuetify.display.smAndDown ? 'mobile' : 'desktop'" v-model="layout.drawer"
        :rail="layout.rail" floating :permanent="$vuetify.display.mdAndUp"
        :location="layoutType === 'app' ? 'left' : 'right'" class="shadow-md">
        <v-list density="compact" nav :prepend-gap="$vuetify.display.mdAndUp ? 17 : 20" color="primary">
            <template v-if="layoutType === 'app'">
                <v-list-item variant="plain" class="rounded-[10px] opacity-100 mb-2" :ripple="false"
                    :to="{ name: 'home' }">
                    <template #title>
                        <span v-if="!layout.rail" class="text-title-medium">{{ settings.systemName }}</span>
                    </template>
                    <template v-if="$vuetify.display.smAndDown" #append>
                        <v-icon-btn color="surface-light" icon="i-mdi-close" size="small" class="me-[-7px]"
                            @click.stop.prevent="layout.toggleDrawer()" />
                    </template>
                    <template v-else #append>
                        <v-icon icon="i-ri-side-bar-line opacity-100" size="24" class="rounded-[10px] me-[-7px]"
                            :class="{ 'ms-[-40px]': layout.rail }" @click.stop.prevent="layout.toggleNav()"
                            v-tooltip="{ text: layout.rail ? 'Open sidebar' : 'Close sidebar', location: 'end' }" />
                    </template>
                </v-list-item>
                <v-list-item prepend-icon="i-mdi-pencil-outline" title="Manage Content" value="home-content"
                    class="rounded-[10px]" :to="{ name: 'manage-content' }"
                    v-tooltip="{ text: 'Manage Content', location: 'end', disabled: !layout.rail }" />
                <v-list-item prepend-icon="i-mdi-cog-outline" title="System Settings" exact value="system-settings"
                    class="rounded-[10px]" :to="{ name: 'system-settings' }"
                    v-tooltip="{ text: 'System Settings', location: 'end', disabled: !layout.rail }" />
                <v-list-item prepend-icon="i-mdi-account-cog-outline" title="Account Settings" exact
                    value="account-settings" class="rounded-[10px]" :to="{ name: 'account-settings' }"
                    v-tooltip="{ text: 'Account Settings', location: 'end', disabled: !layout.rail }" />
            </template>
            <template v-else>
                <v-list-item title="Close sidebar" rounded="pill" class="text-center border"
                    @click="layout.toggleDrawer()" />
                <v-list-item title="Home" exact value="home" rounded="pill" class="text-center"
                    :to="{ name: 'home' }" />
                <v-list-item title="About" exact value="about" rounded="pill" class="text-center"
                    :to="{ name: 'about' }" />
                <v-list-item title="Projects" exact value="projects" rounded="pill" class="text-center"
                    :to="{ name: 'projects' }" />
                <v-list-item title="Contact" exact value="contact" rounded="pill" class="text-center"
                    :to="{ name: 'contact' }" />
            </template>
        </v-list>
        <template v-if="layoutType === 'app'" #append>
            <v-list :activatable="false" density="compact" nav :prepend-gap="$vuetify.display.mdAndUp ? 17 : 20"
                color="primary">
                <v-list-item prepend-icon="i-ri-logout-box-line" title="Log out" class="rounded-[10px]" @click="logout"
                    v-tooltip="{ text: 'Log out', location: 'end', disabled: !layout.rail }" />
            </v-list>
        </template>
    </v-navigation-drawer>
</template>