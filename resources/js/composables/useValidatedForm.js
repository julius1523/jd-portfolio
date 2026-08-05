import { useForm } from "vee-validate";
import { watch } from "vue";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import { useAlert } from "@/composables/useAlert";
import { useFormLoading } from "@/composables/useFormLoading";

const TYPE_FALLBACKS = {
    string: "",
    boolean: false,
    number: null,
    array: () => [],
    object: () => ({}),
};

function buildInitialValues(schema) {
    const defaults = schema.getDefault() ?? {};
    return Object.fromEntries(
        Object.entries(schema.fields).map(([key, field]) => {
            if (defaults[key] !== undefined) {
                return [key, defaults[key]];
            }
            const fallback = TYPE_FALLBACKS[field.type];
            const value =
                typeof fallback === "function" ? fallback() : fallback;
            return [key, value ?? ""];
        }),
    );
}

export function useValidatedForm(schema, onSubmit, options = {}) {
    const { resetOnSuccess = true, useAlertForErrors = false } = options;
    const { loading, wrap } = useFormLoading();
    const { success: notifySuccess, error: notifyError } = useSnackBarQueue();
    const {
        success: alertSuccess,
        error: alertError,
        clear: clearAlerts,
    } = useAlert();

    const initialValues = buildInitialValues(schema);

    const {
        defineField: rawDefineField,
        errors,
        handleSubmit,
        resetForm,
        meta,
    } = useForm({
        validationSchema: schema,
        initialValues,
    });

    function defineField(name, opts) {
        const [field, props] = rawDefineField(name, opts);
        return [field, props];
    }

    const submit = handleSubmit((values, actions) =>
        wrap(async () => {
            if (useAlertForErrors) clearAlerts();
            try {
                const result = await onSubmit(values, actions);
                if (result?.message) notifySuccess(result.message);
                if (resetOnSuccess) actions.resetForm();
                return result;
            } catch (error) {
                const isFieldValidationError =
                    error.response?.status === 422 &&
                    error.response?.data?.errors;

                if (isFieldValidationError) {
                    const fieldErrors = error.response.data.errors;
                    for (const [field, messages] of Object.entries(
                        fieldErrors,
                    )) {
                        actions.setFieldError(field, messages[0]);
                    }
                }

                const msg =
                    error.response?.data?.message ?? "Something went wrong.";
                if (useAlertForErrors) {
                    alertError(msg);
                } else {
                    notifyError(msg);
                }
            }
        }),
    );

    return { defineField, errors, loading, submit, resetForm, meta };
}
