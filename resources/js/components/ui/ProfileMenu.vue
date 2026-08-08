<script setup>
import { ref } from "vue";
import { useFormLoading } from "@/composables/useFormLoading";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import { showConfirmDialog } from "@/composables/useConfirmDialog";
import { useAuthStore } from "@/stores/auth";
import { useLayoutStore } from "@/stores/layout";
import { useThemeStore } from "@/stores/theme";
import { useRouter } from "vue-router";
defineProps({
    variant: { type: String, default: "icon" },
});
const page = ref("main");
const openTheme = () => {
    page.value = "theme";
};
const back = () => {
    page.value = "main";
};
const layout = useLayoutStore();
const auth = useAuthStore();
const themeStore = useThemeStore();
const { loading, wrap } = useFormLoading();
const { error: notifyError } = useSnackBarQueue();
const router = useRouter();
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
                    router.replace({ name: "login" })
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
    <v-menu @after-leave="page = 'main'" :close-on-content-click="false">
        <template #activator="{ props: activatorProps }">
            <v-icon-btn v-if="variant === 'icon'" v-bind="activatorProps" variant="tonal" color="primary"
                icon="mdi-account" />

            <template v-else>
                <v-divider />
                <v-list density="comfortable" slim nav :prepend-gap="8">
                    <v-list-item v-bind="activatorProps" :title="auth.user?.name" subtitle="Administrator"
                        v-tooltip="{ text: 'Account', location: 'end', disabled: !layout.rail }">
                        <template #prepend>
                            <v-icon-btn color="primary" variant="tonal" icon="mdi-account" />
                        </template>
                        <template #append>
                            <v-icon size="18" icon="mdi-dots-horizontal" />
                        </template>
                    </v-list-item>
                </v-list>
            </template>
        </template>

        <v-slide-x-transition mode="out-in">
            <v-list v-if="page === 'main'" key="main" density="compact" nav min-width="220" prepend-gap="15">
                <v-list-item title="Account Settings">
                    <template #prepend>
                        <v-icon-btn variant="tonal" icon="mdi-account-cog"></v-icon-btn>
                    </template>
                </v-list-item>

                <v-list-item title="Theme Settings" append-icon="mdi-chevron-right" @click.stop="openTheme">
                    <template #prepend>
                        <v-icon-btn variant="tonal" icon="mdi-brush-variant"></v-icon-btn>
                    </template>
                </v-list-item>

                <v-list-item title="Log Out" @click="logout">
                    <template #prepend>
                        <v-icon-btn variant="tonal" icon="mdi-logout-variant"></v-icon-btn>
                    </template>
                </v-list-item>
            </v-list>

            <v-list v-else density="compact" key="theme" slim nav min-width="220">
                <div class="d-flex flex-row align-center ga-2">
                    <v-icon-btn icon="mdi-arrow-left" variant="text" @click.stop="back" />
                    <v-list-item title="Dark Mode" />
                </div>

                <v-list-item title="Off" class="ml-12" @click="themeStore.setDark(false)">
                    <template #append>
                        <v-radio :model-value="!themeStore.isDark" color="primary" :value="true" density="compact"
                            :ripple="false"></v-radio>
                    </template>
                </v-list-item>

                <v-list-item title="On" class="ml-12" @click="themeStore.setDark(true)">
                    <template #append>
                        <v-radio :model-value="themeStore.isDark" color="primary" :value="true" density="compact"
                            :ripple="false" :inline="true"></v-radio>
                    </template>
                </v-list-item>
            </v-list>
        </v-slide-x-transition>
    </v-menu>
</template>