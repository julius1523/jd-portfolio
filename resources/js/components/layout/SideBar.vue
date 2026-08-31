<script setup>
import { storeToRefs } from "pinia";
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import ProfileMenu from "../ui/ProfileMenu";
const { isAuthenticated } = storeToRefs(useAuthStore());
const layout = useLayoutStore();
</script>

<template>
    <v-navigation-drawer :key="$vuetify.display.smAndDown ? 'mobile' : 'desktop'" v-model="layout.drawer" elevation="0"
        :rail="layout.rail" :location="$vuetify.display.smAndDown ? 'bottom' : undefined"
        :permanent="$vuetify.display.mdAndUp" width="250">
        <template #prepend>
            <template v-if="$vuetify.display.mdAndUp">
                <v-list variant="plain" density="compact" slim nav class="bg-transparent">
                    <v-list-item exact rounded="lg" :ripple="false" class="opacity-100"
                        :class="{ 'justify-center': layout.rail }" :to="{ name: 'home' }">
                        <template #title>
                            <span class="text-title-medium font-weight-bold">
                                Portfolio
                            </span>
                        </template>

                        <template #append>
                            <v-icon icon="i-ri-layout-left-2-line opacity-70" class="ms-n5" v-tooltip="{
                                text: layout.rail ? 'Open sidebar' : 'Close sidebar',
                                location: 'end',
                                disabled: !layout.rail && $vuetify.display.smAndDown
                            }" @click.stop.prevent="layout.toggleRail()" />
                        </template>
                    </v-list-item>
                </v-list>
            </template>
        </template>

        <v-list :class="$vuetify.display.smAndDown ? 'pa-7' : undefined" density="compact" nav
            :prepend-gap="$vuetify.display.mdAndUp ? 8 : 20" color="primary">
            <v-list-subheader v-if="$vuetify.display.mdAndUp" class="text-uppercase">
                <template v-if="!layout.rail">
                    Menu
                </template>
            </v-list-subheader>

            <template v-if="isAuthenticated">
                <v-list-item prepend-icon="i-mdi-pencil-outline" title="Manage Content" exact rounded="lg"
                    value="home-content" :to="{ name: 'manage-content' }" v-tooltip="{
                        text: 'Manage Content',
                        location: 'end',
                        disabled: !layout.rail
                    }" />

                <v-list-item prepend-icon="i-mdi-cog-outline" title="System Settings" exact rounded="lg"
                    value="system-settings" :to="{ name: 'system-settings' }" v-tooltip="{
                        text: 'System Settings',
                        location: 'end',
                        disabled: !layout.rail
                    }" />
            </template>

            <template v-else>
                <v-list-item title="Home" exact rounded="lg" value="home" class="text-center" :to="{ name: 'home' }" />

                <v-list-item title="About" exact rounded="lg" value="about" class="text-center"
                    :to="{ name: 'about' }" />

                <v-list-item title="Projects" exact rounded="lg" value="projects" class="text-center"
                    :to="{ name: 'projects' }" />

                <v-list-item title="Contact" exact rounded="lg" value="contact" class="text-center"
                    :to="{ name: 'contact' }" />
            </template>
        </v-list>

        <template #append>
            <ProfileMenu v-if="$vuetify.display.mdAndUp" variant="list-item" />
        </template>
    </v-navigation-drawer>
</template>