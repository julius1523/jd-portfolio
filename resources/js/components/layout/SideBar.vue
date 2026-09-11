<script setup>
import { storeToRefs } from "pinia";
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import { useDragToClose } from '@/composables/useDragToClose'
import ProfileMenu from "../ui/ProfileMenu";

const { isAuthenticated } = storeToRefs(useAuthStore());
const layout = useLayoutStore();
const { onDragStart, y, dragging } = useDragToClose(() => (layout.drawer = false))
</script>

<template>
    <v-navigation-drawer :key="$vuetify.display.smAndDown ? 'mobile' : 'desktop'" v-model="layout.drawer"
        :rail="layout.rail" :location="$vuetify.display.smAndDown ? 'bottom' : undefined" floating
        :permanent="$vuetify.display.mdAndUp" width="250" :class="[
            $vuetify.display.smAndDown ? 'rounded-t-xl translate-y-[var(--ty)]' : '',
            $vuetify.display.smAndDown && !dragging ? 'transition-transform duration-200 ease-out' : '',
        ]" :style="$vuetify.display.smAndDown ? { '--ty': `${y}px` } : undefined" color="surface-light">
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
                            <v-icon icon="i-ri-side-bar-line opacity-70" class="ms-n5" v-tooltip="{
                                text: layout.rail ? 'Open sidebar' : 'Close sidebar',
                                location: 'end',
                                disabled: !layout.rail && $vuetify.display.smAndDown
                            }" @click.stop.prevent="layout.toggleRail()" />
                        </template>
                    </v-list-item>
                </v-list>
            </template>
            <template v-else>
                <div class="d-flex justify-center my-3 touch-none cursor-grab" @pointerdown="onDragStart">
                    <v-icon-btn rounded="pill" color="surface-variant" size="4" width="35"></v-icon-btn>
                </div>
            </template>
        </template>
        <v-list :class="$vuetify.display.smAndDown ? 'px-7' : undefined" density="compact" nav
            :prepend-gap="$vuetify.display.mdAndUp ? 8 : 20" color="primary">
            <v-list-subheader v-if="$vuetify.display.mdAndUp" class="text-uppercase">
                <template v-if="!layout.rail">
                    Menu
                </template>
            </v-list-subheader>
            <template v-if="isAuthenticated">
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
            </template>
            <template v-else>
                <v-list-item title="Home" exact value="home" class="text-center rounded-[10px]"
                    :to="{ name: 'home' }" />
                <v-list-item title="About" exact value="about" class="text-center rounded-[10px]"
                    :to="{ name: 'about' }" />
                <v-list-item title="Projects" exact value="projects" class="text-center rounded-[10px]"
                    :to="{ name: 'projects' }" />
                <v-list-item title="Contact" exact value="contact" class="text-center rounded-[10px]"
                    :to="{ name: 'contact' }" />
            </template>
        </v-list>
        <template #append>
            <ProfileMenu v-if="$vuetify.display.mdAndUp" variant="list-item" />
        </template>
    </v-navigation-drawer>
</template>