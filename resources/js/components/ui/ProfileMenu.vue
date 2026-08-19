<script setup>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { useTheme } from "vuetify";
import { mdiAccount, mdiDotsHorizontal, mdiAccountCog } from "@mdi/js";
import { RiLogoutBoxRFill, RiPaintBrushFill } from "vue-remix-icons";
import { useFormLoading } from "@/composables/useFormLoading";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import { showConfirmDialog } from "@/composables/useConfirmDialog";
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
const { loading, wrap } = useFormLoading();
const { error: notifyError } = useSnackBarQueue();
const router = useRouter();
const toggleTheme = (e) => {
    theme.setTransitionOrigin(e.target);
    themeStore.setDark(!themeStore.isDark);
};
const logout = () => {
    showConfirmDialog({
        title: "Log Out",
        message: "Are you sure you want to log out?",
        confirmText: "Log Out",
        cancelText: "Cancel",
        confirmColor: "error",
        onConfirm: () => {
            wrap(async () => {
                try {
                    await auth.logout();
                    router.replace({ name: "login" });
                } catch (error) {
                    notifyError(
                        error.response?.data?.message ?? "Failed to log out."
                    );
                }
            });
        },
    });
};
</script>

<template>
    <v-menu :close-on-content-click="false">
        <template #activator="{ props: activatorProps }">
            <v-icon-btn v-if="variant === 'icon'" v-bind="activatorProps" variant="tonal" color="primary"
                :icon="mdiAccount" />

            <template v-else>
                <v-divider />
                <v-list density="comfortable" class="pa-0" :prepend-gap="10">
                    <v-list-item v-bind="activatorProps" :height="60" :class="layout.rail ? 'px-2' : undefined"
                        v-tooltip="{ text: 'Account', location: 'end', disabled: !layout.rail }">
                        <template #prepend>
                            <v-icon-btn color="primary" variant="tonal" :icon="mdiAccount" />
                        </template>
                        <template #title>
                            <span class="text-label-large">{{ user?.name }}</span>
                        </template>
                        <template #subtitle>
                            <span class="text-label-medium">Administrator</span>
                        </template>
                        <template #append>
                            <v-icon size="18" :icon="mdiDotsHorizontal" />
                        </template>
                    </v-list-item>
                </v-list>
            </template>
        </template>

        <v-slide-x-transition mode="out-in">
            <v-list density="compact" nav prepend-gap="15" rounded="lg">
                <v-list-item title="Account Settings" rounded="lg">
                    <template #prepend>
                        <v-icon-btn size="36" variant="tonal" :icon="mdiAccountCog" />
                    </template>
                </v-list-item>

                <v-list-item title="Theme Settings" rounded="lg" @click="toggleTheme">
                    <template #prepend>
                        <v-icon-btn size="36" variant="tonal" :icon="RiPaintBrushFill" />
                    </template>

                    <template #append>
                        <v-switch :model-value="themeStore.isDark" color="primary" size="small" density="compact"
                            hide-details @click.stop="toggleTheme" />
                    </template>
                </v-list-item>

                <v-list-item title="Log Out" rounded="lg" @click="logout">
                    <template #prepend>
                        <v-icon-btn size="36" variant="tonal" :icon="RiLogoutBoxRFill" />
                    </template>
                </v-list-item>
            </v-list>
        </v-slide-x-transition>
    </v-menu>
</template>