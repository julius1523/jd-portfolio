<script setup>
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import ProfileMenu from "../ui/ProfileMenu";
const auth = useAuthStore();
const layout = useLayoutStore();
</script>

<template>
    <v-navigation-drawer v-model="layout.drawer" elevation="0" :rail="layout.rail" color="surface-light" floating
        :floating="$vuetify.display.smAndDown" :permanent="$vuetify.display.mdAndUp" width="250">
        <template #prepend>
            <template v-if="$vuetify.display.mdAndUp">
                <v-list variant="plain" density="compact" slim nav class="mt-1">
                    <v-list-item exact :ripple="false" class="opacity-100" :class="{ 'justify-center': layout.rail }"
                        :to="{ name: 'home' }">
                        <template #title>
                            <span class="text-title-medium font-weight-bold">Portfolio</span>
                        </template>
                        <template #append>
                            <v-icon-btn variant="text" height="32" rounded="lg" icon="mdi-dock-left opacity-70"
                                v-tooltip="{ text: layout.rail ? 'Open sidebar' : 'Close sidebar', location: 'end', disabled: !layout.rail && $vuetify.display.smAndDown }"
                                @click.stop.prevent="layout.toggleRail()">
                            </v-icon-btn>
                        </template>
                    </v-list-item>
                </v-list>
            </template>
            <template v-else>
                <v-btn size="small" icon="mdi-close" variant="text" class="border ma-2"
                    @click="layout.toggleDrawer()" />
            </template>
        </template>


        <v-list density="compact" nav color="primary" :prepend-gap="8">
            <v-list-subheader v-if="$vuetify.display.mdAndUp" class="text-uppercase">
                <template v-if="!layout.rail">
                    Menu
                </template>
            </v-list-subheader>
            <v-list-item prepend-icon="mdi-pencil-outline" title="Manage Content" exact value="home-content"
                :to="{ name: 'manage-content' }"
                v-tooltip="{ text: 'Manage Content', location: 'end', disabled: !layout.rail }" />
            <v-list-item prepend-icon="mdi-cog-outline" title="System Settings" exact value="system-settings"
                :to="{ name: 'system-settings' }"
                v-tooltip="{ text: 'System Settings', location: 'end', disabled: !layout.rail }" />
        </v-list>

        <template #append>
            <ProfileMenu v-if="$vuetify.display.mdAndUp" variant="list-item" />
        </template>
    </v-navigation-drawer>
</template>