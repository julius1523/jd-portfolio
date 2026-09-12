import { ref, computed } from "vue";
import { useValidatedForm } from "@/composables/useValidatedForm";

export function useEntryDialog(
    entrySchema,
    getList,
    emptyValues,
    options = {},
) {
    const dialog = ref(false);
    const editingIndex = ref(-1);
    const isEditing = computed(() => editingIndex.value > -1);

    const form = useValidatedForm(
        entrySchema,
        async (values) => {
            const list = getList();
            const row = { ...values, id: values.id ?? crypto.randomUUID() };
            if (isEditing.value) {
                list.splice(editingIndex.value, 1, row);
            } else {
                list.push(row);
            }
            dialog.value = false;
        },
        { resetOnSuccess: false, ...options },
    );

    function open(item = null) {
        const list = getList();
        editingIndex.value = item
            ? list.findIndex((row) => row.id === item.id)
            : -1;
        form.resetForm({ values: item ? { ...item } : { ...emptyValues } });
        dialog.value = true;
    }

    function close() {
        dialog.value = false;
    }

    function remove(item) {
        const list = getList();
        const idx = list.findIndex((row) => row.id === item.id);
        if (idx > -1) list.splice(idx, 1);
    }

    return {
        dialog,
        editingIndex,
        isEditing,
        fields: form.fields,
        errors: form.errors,
        loading: form.loading,
        meta: form.meta,
        submit: form.submit,
        open,
        close,
        remove,
    };
}
