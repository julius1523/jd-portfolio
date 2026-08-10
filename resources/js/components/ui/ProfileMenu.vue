<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useTheme } from "vuetify";
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
                icon="mdi-account" />

            <template v-else>
                <v-divider />
                <v-list density="comfortable" slim nav :prepend-gap="8">
                    <v-list-item v-bind="activatorProps" :title="auth.user?.name" subtitle="Administrator" rounded="lg"
                        v-tooltip="{ text: 'Account', location: 'end', disabled: !layout.rail }">
                        <template #prepend>
                            <v-icon-btn color="primary" variant="tonal" icon="mdi-account"
                                :size="layout.rail ? 24 : undefined" />
                        </template>
                        <template #append>
                            <v-icon size="18" icon="mdi-dots-horizontal" />
                        </template>
                    </v-list-item>
                </v-list>
            </template>
        </template>

        <v-slide-x-transition mode="out-in">
            <v-list density="compact" nav prepend-gap="15" rounded="lg">
                <v-list-item title="Account Settings" rounded="lg">
                    <template #prepend>
                        <v-icon-btn variant="tonal" icon="mdi-account-cog" />
                    </template>
                </v-list-item>

                <v-list-item title="Theme Settings" rounded="lg" @click="toggleTheme">
                    <template #prepend>
                        <v-icon-btn variant="tonal" icon="mdi-brush-variant" />
                    </template>

                    <template #append>
                        <v-switch :model-value="themeStore.isDark" @update:model-value="themeStore.setDark"
                            color="primary" size="small" density="compact" hide-details @click.stop />
                    </template>
                </v-list-item>

                <v-list-item title="Log Out" rounded="lg" @click="logout">
                    <template #prepend>
                        <v-icon-btn variant="tonal" icon="mdi-logout-variant" />
                    </template>
                </v-list-item>
            </v-list>
        </v-slide-x-transition>
    </v-menu>
</template>