import { reactive } from "vue";

const state = reactive({
    visible: false,
    title: "Confirm",
    message: "",
    confirmText: "Confirm",
    cancelText: "Cancel",
    confirmColor: "primary",
    onConfirm: null,
    onCancel: null,
});

export function showConfirmDialog({
    title = "Confirm",
    message,
    confirmText = "Confirm",
    cancelText = "Cancel",
    confirmColor = "primary",
    onConfirm,
    onCancel,
}) {
    state.title = title;
    state.message = message;
    state.confirmText = confirmText;
    state.cancelText = cancelText;
    state.confirmColor = confirmColor;
    state.onConfirm = onConfirm;
    state.onCancel = onCancel;
    state.visible = true;
}

export function useConfirmDialogState() {
    return state;
}
