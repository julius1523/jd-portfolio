<script setup>
import { watch } from "vue";
import { useConfirmDialogState } from "@/composables/useConfirmDialog";
const state = useConfirmDialogState();

function confirm() {
    state.visible = false;
    state.onConfirm?.();
    state.onConfirm = null;
    state.onCancel = null;
}

function cancel() {
    state.visible = false;
    state.onCancel?.();
    state.onConfirm = null;
    state.onCancel = null;
}

watch(
    () => state.visible,
    (visible) => {
        if (!visible && (state.onConfirm || state.onCancel)) {
            cancel();
        }
    }
);
</script>

<template>
    <v-dialog v-model="state.visible" max-width="400" persistent>
        <v-card class="pa-2" rounded="xl">
            <v-card-title class="pt-3 px-6 font-weight-bold">{{ state.title }}</v-card-title>
            <v-card-text class="pt-0 px-6 text-medium-emphasis">{{ state.message }}</v-card-text>
            <v-card-actions>
                <v-row gap="7">
                    <v-col cols="6">
                        <v-btn :text="state.cancelText" rounded="pill" size="large" block class="border"
                            @click="cancel" />
                    </v-col>
                    <v-col cols="6">
                        <v-btn variant="flat" :color="state.confirmColor" :text="state.confirmText" rounded="pill"
                            size="large" block @click="confirm" />
                    </v-col>
                </v-row>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>