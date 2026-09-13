import { reactive, ref, onMounted, nextTick } from "vue";
import { useForm } from "vee-validate";
import { useSnackbarQueue } from "@/composables/useSnackbarQueue";

export function useValidatedForm(schema, onSubmit, options = {}) {
    const { resetOnSuccess = true, cancelMessage = "No changes made." } =
        options;
    const {
        success: notifySuccess,
        error: notifyError,
        info: notifyInfo,
    } = useSnackbarQueue();

    const loading = ref(false);

    const { defineField, errors, handleSubmit, resetForm, resetField, meta } =
        useForm({
            validationSchema: schema,
            initialValues: schema.getDefault(),
        });

    const fields = reactive(
        Object.fromEntries(
            Object.keys(schema.fields).map((name) => {
                const [field] = defineField(name);
                return [name, field];
            }),
        ),
    );

    const ready = ref(false);
    onMounted(() => {
        nextTick(() => {
            ready.value = true;
        });
    });

    const submit = handleSubmit(async (values, actions) => {
        loading.value = true;
        try {
            const result = await onSubmit(values, actions);
            if (result?.message) notifySuccess(result.message);
            if (resetOnSuccess) actions.resetForm();
            return result;
        } catch (error) {
            const fieldErrors =
                error.response?.status === 422
                    ? error.response?.data?.errors
                    : null;
            if (fieldErrors) {
                for (const [field, messages] of Object.entries(fieldErrors)) {
                    actions.setFieldError(field, messages[0]);
                }
            }
            notifyError(
                error.response?.data?.message ?? "Something went wrong.",
            );
        } finally {
            loading.value = false;
        }
    });

    function cancelEdit() {
        resetForm();
        notifyInfo(cancelMessage);
    }

    return {
        fields,
        defineField,
        errors,
        loading,
        submit,
        resetForm,
        resetField,
        cancelEdit,
        meta,
        ready,
    };
}
