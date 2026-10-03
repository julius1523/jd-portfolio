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
                            <v-card class="p-5 shadow-sm border rounded-[12px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        System Logo
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <div class="d-flex justify-center justify-sm-end">
                                            <AvatarUpload v-model="fields.systemLogo" :disabled="loading" :max-size="1"
                                                :size="160" />
                                        </div>
                                    </div>
                                </div>
                            </v-card>

                            <v-card class="p-5 shadow-sm border rounded-[12px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        System Name
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <v-text-field v-model="fields.systemName" color="primary" variant="solo" flat
                                            label="System Name" density="compact" single-line clearable
                                            :error-messages="errors.systemName" autocomplete="off"
                                            class="vfield-outline" hide-details="auto" data-shimmer-no-children />
                                    </div>
                                </div>
                            </v-card>

                            <v-card class="p-5 shadow-sm border rounded-[12px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        System Owner Name
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <v-text-field v-model="fields.systemOwner" color="primary" variant="solo" flat
                                            label="Owner" density="compact" single-line clearable
                                            :error-messages="errors.systemOwner" autocomplete="off"
                                            class="vfield-outline" hide-details="auto" data-shimmer-no-children />
                                    </div>
                                </div>
                            </v-card>

                            <v-card class="p-5 shadow-sm border rounded-[12px]">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="sm:shrink-0 text-label-large font-medium">
                                        System Theme Color
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <v-text-field ref="colorField" v-model="fields.systemColor" color="primary"
                                            variant="solo" flat density="compact" :error-messages="errors.systemColor"
                                            autocomplete="off" class="vfield-outline" hide-details="auto"
                                            data-shimmer-no-children>
                                            <template #prepend-inner>
                                                <v-icon icon="i-mdi-circle" :color="fields.systemColor" size="20" />
                                            </template>
                                            <template #append-inner>
                                                <ColorPicker v-model="fields.systemColor" :anchor="colorTarget"
                                                    location="bottom end" />
                                            </template>
                                        </v-text-field>
                                    </div>
                                </div>
                            </v-card>
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
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import * as yup from "yup";
import { useSystemSettingsStore } from "@/stores/systemSettings";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import ColorPicker from "@/components/forms/ColorPicker";
import AvatarUpload from "@/components/forms/AvatarUpload";
import FormActions from "@/components/forms/FormActions";

const route = useRoute();
const schema = yup.object({
    systemLogo: yup.mixed().label("System Logo").nullable(),
    systemName: yup.string().label("System Name").required(),
    systemOwner: yup.string().label("Owner Name").required(),
    systemColor: yup.string().label("System Color").required(),
});
const settingsStore = useSystemSettingsStore();
const { error } = useSnackBarQueue();
const colorField = ref(null);
const colorTarget = computed(() => colorField.value?.$el?.querySelector(".v-field") ?? undefined);
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
        } else if (!values.systemLogo) {
            formData.append("remove_systemLogo", "1");
        }

        const payload = {
            systemName: values.systemName,
            systemOwner: values.systemOwner,
            systemColor: values.systemColor,
        };
        formData.append("payload", JSON.stringify(payload));

        const response = await axios.post("/api/updateSystemSettings", formData);
        const data = await settingsStore.fetch();
        if (data) resetForm({ values: data });
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
};

onMounted(() => {
    getSystemSettings();
});
</script>