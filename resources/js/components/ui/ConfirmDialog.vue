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
    <v-dialog v-model="state.visible" max-width="400" persistent no-click-animation>
        <v-card class="pa-3 shadow-lg rounded-[20px] border">
            <div class="pt-2 px-3 text-label-large font-weight-bold">{{ state.title }}</div>
            <div class="pb-3 px-3 text-label-large text-medium-emphasis">{{ state.message }}</div>
            <v-card-actions class="d-flex flex-column flex-md-row">
                <div class="order-1 order-md-0" :class="{ 'w-100': $vuetify.display.smAndDown }">
                    <v-btn variant="tonal" :text="state.cancelText" block :slim="false" class="rounded-[10px]"
                        @click="cancel" />
                </div>
                <div :class="{ 'w-100': $vuetify.display.smAndDown }">
                    <v-btn variant="flat" :color="state.confirmColor" :text="state.confirmText" block :slim="false"
                        class="rounded-[10px]" @click="confirm" />
                </div>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>