<template>
    <v-container>
        <div class="inline-flex text-headline-medium font-semibold mb-4">
            {{ route.meta.title }}
        </div>
        <Shimmer :loading="pageLoading">
            <v-form @submit.prevent="submit" :disabled="loading">
                <div class="d-flex flex-column ga-16">
                    <div>
                        <div class="d-flex flex-column ga-4">
                            <v-card class="p-5 shadow-sm border rounded-[15px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        Account Image
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <div class="d-flex justify-center justify-sm-end">
                                            <AvatarUpload v-model="fields.accountImage" :disabled="loading"
                                                :max-size="1" :size="160" />
                                        </div>
                                    </div>
                                </div>
                            </v-card>

                            <v-card class="p-5 shadow-sm border rounded-[15px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        First Name
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <v-text-field v-model="fields.firstName" color="primary" variant="solo" flat
                                            single-line label="First Name" density="compact" clearable
                                            :error-messages="errors.firstName" autocomplete="off" class="vfield-outline"
                                            hide-details="auto" data-shimmer-no-children />
                                    </div>
                                </div>
                            </v-card>

                            <v-card class="p-5 shadow-sm border rounded-[15px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        Last Name
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <v-text-field v-model="fields.lastName" color="primary" variant="solo" flat
                                            single-line label="First Name" density="compact" clearable
                                            :error-messages="errors.lastName" autocomplete="off" class="vfield-outline"
                                            hide-details="auto" data-shimmer-no-children />
                                    </div>
                                </div>
                            </v-card>

                            <v-card class="p-5 shadow-sm border rounded-[15px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        Email
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <v-text-field v-model="fields.email" color="primary" variant="solo" flat
                                            single-line label="First Name" density="compact" clearable
                                            :error-messages="errors.email" autocomplete="off" class="vfield-outline"
                                            hide-details="auto" data-shimmer-no-children />
                                    </div>
                                </div>
                            </v-card>

                            <v-card class="p-5 shadow-sm border rounded-[15px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        Password
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <PasswordField v-model="fields.password" variant="solo" flat single-line
                                            density="compact" label="Password" class="vfield-outline"
                                            :error-messages="errors.password" hide-details="auto"
                                            data-shimmer-no-children />
                                    </div>
                                </div>
                            </v-card>

                            <v-slide-y-transition>
                                <v-card v-if="fields.password" class="p-5 shadow-sm border rounded-[15px]">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                        <div class="sm:shrink-0 text-label-large font-medium">
                                            Confirm Password
                                        </div>
                                        <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                            <PasswordField v-model="fields.passwordConfirmation" variant="solo" flat
                                                single-line density="compact" label="Confirm Password"
                                                class="vfield-outline" :error-messages="errors.passwordConfirmation"
                                                hide-details="auto" data-shimmer-no-children />
                                        </div>
                                    </div>
                                </v-card>
                            </v-slide-y-transition>
                        </div>
                    </div>
                </div>

                <FormActions :dirty="meta.dirty" :ready="ready" :loading="loading" @cancel="cancelEdit" />
            </v-form>
        </Shimmer>
    </v-container>
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import PasswordField from "@/components/forms/PasswordField";
import AvatarUpload from "@/components/forms/AvatarUpload";
import FormActions from "@/components/forms/FormActions";

const route = useRoute();
const schema = yup.object({
    accountImage: yup.mixed().label("Account Image").nullable(),
    firstName: yup.string().label("First Name").required(),
    lastName: yup.string().label("Last Name").required(),
    email: yup.string().label("Email").email().required(),
    password: yup.string().label("Password").nullable().matches(/^.{8,}$/, { message: "Password must be at least 8 characters", excludeEmptyString: true }),
    passwordConfirmation: yup.string().label("Confirm Password").nullable().when("password", { is: (password) => !!password, then: (schema) => schema.required("Please confirm your password").oneOf([yup.ref("password")], "Passwords do not match") }),
});
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