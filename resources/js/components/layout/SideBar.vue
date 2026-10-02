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
    <v-navigation-drawer v-if="layoutType === 'app'" :key="$vuetify.display.smAndDown ? 'mobile' : 'desktop'"
        v-model="layout.drawer" :rail="layout.rail" :permanent="$vuetify.display.mdAndUp" location="left"
        :rail-width="67">
        <v-list density="compact" nav :prepend-gap="$vuetify.display.mdAndUp ? 17 : 20" color="primary"
            class="px-[13px]">
            <v-list-item variant="plain" height="51" rounded="pill" class="opacity-100 pt-0 mt-[-3px]" :ripple="false"
                :to="{ name: 'home' }">
                <template #title>
                    <span v-if="!layout.rail" class="text-title-medium font-weight-bold">
                        {{ settings.systemName }}
                    </span>
                </template>
                <template v-if="$vuetify.display.smAndDown" #append>
                    <v-icon-btn color="surface" icon="i-mdi-close" size="38" icon-size="18" class="border me-[-8px]"
                        @click.stop.prevent="layout.toggleDrawer()" />
                </template>
                <template v-else #append>
                    <v-icon-btn icon="i-ri-side-bar-line" size="38" class="rounded-[10px] me-[-7px]"
                        :class="{ 'ms-[-40px]': layout.rail }" @click.stop.prevent="layout.toggleNav()"
                        v-tooltip="{ text: layout.rail ? 'Open sidebar' : 'Close sidebar', location: 'end' }" />
                </template>
            </v-list-item>
            <v-divider class="mx-n3 mb-[15px]" />
            <v-list-item prepend-icon="i-mdi-pencil-outline" icon-size="14" title="Manage Content" value="home-content"
                rounded="pill" :to="{ name: 'manage-content' }"
                v-tooltip="{ text: 'Manage Content', location: 'end', disabled: !layout.rail }" />
            <v-list-item prepend-icon="i-mdi-cog-outline" title="System Settings" exact value="system-settings"
                rounded="pill" :to="{ name: 'system-settings' }"
                v-tooltip="{ text: 'System Settings', location: 'end', disabled: !layout.rail }" />
            <v-list-item prepend-icon="i-mdi-account-cog-outline" title="Account Settings" exact
                value="account-settings" rounded="pill" :to="{ name: 'account-settings' }"
                v-tooltip="{ text: 'Account Settings', location: 'end', disabled: !layout.rail }" />
        </v-list>
        <template #append>
            <v-list :activatable="false" density="compact" nav :prepend-gap="$vuetify.display.mdAndUp ? 17 : 20"
                color="primary" class="px-[13px] mb-1">
                <v-list-item prepend-icon="i-ri-logout-box-line" title="Log out" rounded="pill" @click="logout"
                    v-tooltip="{ text: 'Log out', location: 'end', disabled: !layout.rail }" />
            </v-list>
        </template>
    </v-navigation-drawer>
</template>