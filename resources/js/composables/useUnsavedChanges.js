import { reactive, watch, onUnmounted } from "vue";
import { onBeforeRouteLeave } from "vue-router";
import { showConfirmDialog } from "@/composables/useConfirmDialog";

const dirtyForms = reactive(new Set());

export function useUnsavedChanges(
    metaRef,
    message = "You have unsaved changes. Are you sure you want to leave?",
) {
    const id = Symbol();

    watch(
        () => metaRef.value?.dirty,
        (isDirty) => {
            if (isDirty) dirtyForms.add(id);
            else dirtyForms.delete(id);
        },
        { immediate: true },
    );

    onUnmounted(() => dirtyForms.delete(id));

    onBeforeRouteLeave(() => {
        if (!metaRef.value?.dirty) return true;
        return new Promise((resolve) => {
            showConfirmDialog({
                title: "Unsaved Changes",
                message,
                confirmText: "Leave",
                cancelText: "Stay",
                confirmColor: "red",
                onConfirm: () => resolve(true),
                onCancel: () => resolve(false),
            });
        });
    });
}

export function hasUnsavedChanges() {
    return dirtyForms.size > 0;
}
