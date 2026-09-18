<template>
    <v-container max-width="1050">
        <Shimmer :loading="pageLoading">
            <v-card flat class="rounded-lg">
                <v-form @submit.prevent="submit" :disabled="loading">
                    <v-row :gap="55">
                        <v-col cols="12">
                            <v-row :gap="13">
                                <v-col cols="12">
                                    <div class="mb-4">
                                        <span class="text-title-medium font-weight-bold">System Logo</span><br />
                                        <span class="text-title-small text-medium-emphasis">
                                            Update the system logo
                                        </span>
                                    </div>
                                    <FileUpload v-model="fields.systemLogo" file-type="image" :max-files="1" inset
                                        :disabled="loading" :show-size="true" density="comfortable"
                                        :error-messages="errors.systemLogo" data-shimmer-no-children />
                                </v-col>
                                <v-col cols="12">
                                    <div class="mb-4">
                                        <span class="text-title-medium font-weight-bold">System Name</span><br />
                                        <span class="text-title-small text-medium-emphasis">
                                            Update the system name
                                        </span>
                                    </div>
                                    <v-text-field v-model="fields.systemName" color="primary" variant="solo" flat
                                        label="System Name" density="comfortable" :single-line="true" clearable
                                        :error-messages="errors.systemName" autocomplete="off" class="vfield-outline"
                                        data-shimmer-no-children />
                                </v-col>
                                <v-col cols="12">
                                    <div class="mb-4">
                                        <span class="text-title-medium font-weight-bold">System Color</span><br />
                                        <span class="text-title-small text-medium-emphasis">
                                            Update the system color
                                        </span>
                                    </div>
                                    <Select v-model="fields.systemColor" :items="COLORS" variant="solo"
                                        label="System Color" :multiple="false" :single-line="true" :chip="false"
                                        :error-messages="errors.systemColor" class="vfield-outline"
                                        data-shimmer-no-children>
                                        <template #item="{ item, props: itemProps }">
                                            <v-list-item v-bind="itemProps" :title="undefined">
                                                <template #prepend>
                                                    <v-avatar :color="item?.value" size="18" class="mr-2" />
                                                </template>

                                                <template #title>
                                                    <span class="text-label-medium">{{ item?.name }}</span>
                                                </template>
                                            </v-list-item>
                                        </template>

                                        <template #selection="{ item }">
                                            <v-avatar :color="item?.value" size="16" class="mr-2" />
                                            {{ item?.name }}
                                        </template>
                                    </Select>
                                </v-col>
                                <v-col cols="12">
                                    <FormActions :dirty="meta.dirty" :ready="ready" :loading="loading"
                                        @cancel="cancelEdit" />
                                </v-col>
                            </v-row>
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
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackbarQueue } from "@/composables/useSnackbarQueue";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import FormActions from "@/components/forms/FormActions";
import { COLORS } from "@/src/constants/constants";

const schema = yup.object({
    systemLogo: yup.mixed().label("System Logo").nullable(),
    systemName: yup.string().label("System Name").required(),
    systemColor: yup.string().label("System Color").required(),
});
const { error } = useSnackbarQueue();
const pageLoading = ref(true);
const { fields, errors, loading, submit, cancelEdit, resetForm, meta, ready } = useValidatedForm(
    schema,
    async (values) => {
        const formData = new FormData();
        const systemLogoFile =
            values.systemLogo instanceof File || values.systemLogo instanceof Blob
                ? values.systemLogo
                : null;

        if (systemLogoFile) {
            formData.append("systemLogo", systemLogoFile);
        } else if (!values.profileImage) {
            formData.append("remove_systemLogo", "1");
        }

        const payload = {
            systemLogo: values.systemLogo,
            systemName: values.systemName,
            systemColor: values.systemColor,
        };
        formData.append("payload", JSON.stringify(payload));

        const response = await axios.post("/api/updateSystemSettings", formData);
        await getSystemSettings();
        return { message: response.data.message };
    },
    { resetOnSuccess: false }
);

async function getSystemSettings() {
    try {
        const { data } = await axios.get("/api/getSystemSettings");
        if (!data) return;
        resetForm({ values: data });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load system settings.");
    } finally {
        pageLoading.value = false;
    }
}

onMounted(() => {
    getSystemSettings();
});
</script>