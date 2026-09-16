<script setup>
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import { confirm } from "@/composables/useConfirmDialog";

const { isAuthenticated } = storeToRefs(useAuthStore());
const layout = useLayoutStore();
const auth = useAuthStore();
const router = useRouter();
const logout = () => {
    confirm({
        title: "Log Out",
        message: "Are you sure you want to log out?",
        confirmText: "Log Out",
        cancelText: "Cancel",
        confirmColor: "error",
        onConfirm: () => {
            router.replace({ name: "login" });
            auth.logout();
        },
    });
};
</script>

<template>
    <v-navigation-drawer :key="$vuetify.display.smAndDown ? 'mobile' : 'desktop'" v-model="layout.drawer"
        :rail="layout.rail" floating :permanent="$vuetify.display.mdAndUp"
        :location="isAuthenticated ? 'left' : 'right'">
        <v-list density="compact" nav :prepend-gap="$vuetify.display.mdAndUp ? 17 : 20" color="primary">
            <template v-if="isAuthenticated">
                <v-list-item v-if="$vuetify.display.smAndDown" variant="plain" prepend-icon="i-ri-folder-4-line"
                    class="rounded-[10px] opacity-100" :ripple="false" :to="{ name: 'home' }">
                    <template #title>
                        <span class="text-title-medium">Portfolio</span>
                    </template>
                    <template #append>
                        <v-icon-btn color="surface-light" icon="i-mdi-close" size="small"
                            @click.stop.prevent="layout.toggleDrawer()" />
                    </template>
                </v-list-item>
                <v-list-item prepend-icon="i-mdi-pencil-outline" title="Manage Content" exact value="home-content"
                    class="rounded-[10px]" :to="{ name: 'manage-content' }" v-tooltip="{
                        text: 'Manage Content',
                        location: 'end',
                        disabled: !layout.rail
                    }" />
                <v-list-item prepend-icon="i-mdi-cog-outline" title="System Settings" exact value="system-settings"
                    class="rounded-[10px]" :to="{ name: 'system-settings' }" v-tooltip="{
                        text: 'System Settings',
                        location: 'end',
                        disabled: !layout.rail
                    }" />
                <v-list-item prepend-icon="i-mdi-account-cog-outline" title="Account Settings" exact
                    value="account-settings" class="rounded-[10px]" :to="{ name: 'account-settings' }" v-tooltip="{
                        text: 'Account Settings',
                        location: 'end',
                        disabled: !layout.rail
                    }" />
            </template>
            <template v-else>
                <v-list-item title="Home" exact value="home" class="rounded-[10px]" :to="{ name: 'home' }" />
                <v-list-item title="About" exact value="about" class="rounded-[10px]" :to="{ name: 'about' }" />
                <v-list-item title="Projects" exact value="projects" class="rounded-[10px]"
                    :to="{ name: 'projects' }" />
                <v-list-item title="Contact" exact value="contact" class="rounded-[10px]" :to="{ name: 'contact' }" />
            </template>
        </v-list>
        <template v-if="isAuthenticated" #append>
            <v-list :activatable="false" density="compact" nav :prepend-gap="$vuetify.display.mdAndUp ? 17 : 20"
                color="primary">
                <v-list-item prepend-icon="i-ri-logout-box-line" title="Log out" class="rounded-[10px]"
                    @click="logout" />
            </v-list>
        </template>
    </v-navigation-drawer>
</template>