<template>
    <v-container max-width="1050">
        <Shimmer :loading="pageLoading">
            <v-card flat class="pa-1 rounded-lg">
                <v-form @submit.prevent="submit" :disabled="loading">
                    <v-row :gap="55">
                        <v-col cols="12">
                            <v-row :gap="10">
                                <v-col cols="12">
                                    <div class="mb-4">
                                        <span class="text-title-medium font-weight-bold">Account Image</span><br />
                                        <span class="text-title-small text-medium-emphasis">
                                            Update your account image
                                        </span>
                                    </div>
                                    <FileUpload v-model="fields.accountImage" file-type="image" :max-files="1" inset
                                        :disabled="loading" :show-size="true" density="comfortable"
                                        :error-messages="errors.accountImage" data-shimmer-no-children />
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="12">
                            <v-row :gap="10">
                                <v-col cols="12">
                                    <div class="mb-4">
                                        <span class="text-title-medium font-weight-bold">Account Details</span><br />
                                        <span class="text-title-small text-medium-emphasis">
                                            Update your first name, last name, and email
                                        </span>
                                    </div>
                                    <v-text-field v-model="fields.firstName" color="primary" variant="solo" flat
                                        label="First Name" density="comfortable" clearable
                                        :error-messages="errors.firstName" autocomplete="off" class="vfield-outline"
                                        data-shimmer-no-children />
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field v-model="fields.lastName" color="primary" variant="solo" flat
                                        label="Last Name" density="comfortable" clearable
                                        :error-messages="errors.lastName" autocomplete="off" class="vfield-outline"
                                        data-shimmer-no-children />
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field v-model="fields.email" color="primary" variant="solo" flat
                                        label="Email" density="comfortable" clearable :error-messages="errors.email"
                                        autocomplete="off" class="vfield-outline" data-shimmer-no-children />
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="12">
                            <v-row :gap="10">
                                <v-col cols="12">
                                    <div class="mb-4">
                                        <span class="text-title-medium font-weight-bold">Account Password</span><br />
                                        <span class="text-title-small text-medium-emphasis">
                                            Update your account password
                                        </span>
                                    </div>
                                    <PasswordField v-model="fields.password" variant="solo" flat density="comfortable"
                                        label="Password" class="vfield-outline" :error-messages="errors.password" />

                                    <v-expand-transition>
                                        <div v-if="fields.password" class="mt-4">
                                            <PasswordField v-model="fields.passwordConfirmation" variant="solo" flat
                                                density="comfortable" label="Confirm Password" class="vfield-outline"
                                                :error-messages="errors.passwordConfirmation" />
                                        </div>
                                    </v-expand-transition>
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="12">
                            <FormActions :dirty="meta.dirty" :ready="ready" :loading="loading" @cancel="cancelEdit" />
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>
        </Shimmer>
    </v-container>
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, onMounted } from "vue";
import * as yup from "yup";
import { useSystemSettingsStore } from "@/stores/systemSettings";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import PasswordField from "@/components/forms/PasswordField";
import FileUpload from "@/components/forms/FileUpload";
import FormActions from "@/components/forms/FormActions";

const schema = yup.object({
    accountImage: yup.mixed().label("Account Image").nullable(),
    firstName: yup.string().label("First Name").required(),
    lastName: yup.string().label("Last Name").required(),
    email: yup.string().label("Email").email().required(),
    password: yup.string().label("Password").nullable().matches(/^.{8,}$/, { message: "Password must be at least 8 characters", excludeEmptyString: true }),
    passwordConfirmation: yup.string().label("Confirm Password").nullable().when("password", { is: (password) => !!password, then: (schema) => schema.required("Please confirm your password").oneOf([yup.ref("password")], "Passwords do not match") }),
});
const settingsStore = useSystemSettingsStore();
const { error } = useSnackBarQueue();
const pageLoading = ref(true);
const { fields, errors, loading, submit, cancelEdit, resetForm, meta, ready } = useValidatedForm(
    schema,
    async (values) => {
        const formData = new FormData();
        const accountImageFile =
            values.accountImage instanceof File || values.accountImage instanceof Blob
                ? values.accountImage
                : null;

        if (accountImageFile) {
            formData.append("accountImage", accountImageFile);
        } else if (!values.accountImage) {
            formData.append("remove_accountImage", "1");
        }

        const payload = {
            firstName: values.firstName,
            lastName: values.lastName,
            email: values.email,
            password: values.password
        };
        formData.append("payload", JSON.stringify(payload));

        const response = await axios.post("/api/updateAccountSettings", formData);
        await getAccountSettings();
        await settingsStore.fetch();
        return { message: response.data.message };
    },
    { resetOnSuccess: false }
);

async function getAccountSettings() {
    try {
        const { data } = await axios.get("/api/getAccountSettings");
        if (!data) return;
        resetForm({ values: data });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load account settings.");
    } finally {
        pageLoading.value = false;
    }
};

onMounted(() => {
    getAccountSettings();
});
</script>