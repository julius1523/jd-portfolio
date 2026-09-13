<script setup>
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { useTheme } from "vuetify";
import { confirm } from "@/composables/useConfirmDialog";
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import { useThemeStore } from "@/stores/theme";

defineProps({
    variant: { type: String, default: "icon" },
});

const layout = useLayoutStore();
const auth = useAuthStore();
const { user } = storeToRefs(auth);
const theme = useTheme();
const themeStore = useThemeStore();
const router = useRouter();
const toggleTheme = (e) => {
    theme.setTransitionOrigin(e.target);
    themeStore.setDark(!themeStore.isDark);
};
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
    <v-menu :offset="[8, 0]">
        <template #activator="{ props: activatorProps }">
            <v-icon-btn v-if="variant === 'icon'" v-bind="activatorProps" variant="tonal" color="primary"
                icon="i-mdi-account" />
            <template v-else>
                <v-divider />
                <v-list variant="plain" density="comfortable" class="pa-0 opacity-100" :prepend-gap="10">
                    <v-list-item v-bind="activatorProps" :height="60" class="opacity-100"
                        :class="layout.rail ? 'px-2' : undefined"
                        v-tooltip="{ text: 'Account', location: 'end', disabled: !layout.rail }">
                        <template #prepend>
                            <v-icon-btn color="primary" variant="tonal" icon="i-mdi-account" />
                        </template>
                        <template #title>
                            <span class="text-label-large">{{ user?.name }}</span>
                        </template>
                        <template #subtitle>
                            <span class="text-label-medium">Administrator</span>
                        </template>
                        <template #append>
                            <v-icon size="18" icon="i-mdi-dots-horizontal" />
                        </template>
                    </v-list-item>
                </v-list>
            </template>
        </template>

        <v-slide-x-transition mode="out-in">
            <v-list density="compact" nav prepend-gap="15" rounded="lg">
                <v-list-item title="Account Settings" rounded="lg">
                    <template #prepend>
                        <v-icon-btn size="36" variant="tonal" icon="i-mdi-account-cog" />
                    </template>
                </v-list-item>

                <v-list-item title="Theme Settings" rounded="lg" @click="toggleTheme">
                    <template #prepend>
                        <v-icon-btn size="36" variant="tonal" icon="i-ri-paint-brush-fill" />
                    </template>

                    <template #append>
                        <v-switch :model-value="themeStore.isDark" color="primary" size="small" density="compact"
                            hide-details @click.stop="toggleTheme" />
                    </template>
                </v-list-item>

                <v-list-item title="Log Out" rounded="lg" @click="logout">
                    <template #prepend>
                        <v-icon-btn size="36" variant="tonal" icon="i-ri-logout-box-r-fill" />
                    </template>
                </v-list-item>
            </v-list>
        </v-slide-x-transition>
    </v-menu>
</template>