<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="pa-3 mt-2 rounded-lg">
            <v-form @submit.prevent="submit" :disabled="loading">
                <v-row :gap="45">
                    <v-col cols="12">
                        <v-row :gap="13">
                            <v-col cols="12" lg="6">
                                <div class="mb-4">
                                    <span class="text-title-medium font-weight-bold">Profile</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update your profile to display to home page
                                    </span>
                                </div>
                                <FileUpload v-model="fields.profileImage" file-type="image" :max-files="1" inset
                                    :disabled="loading" :show-size="true" density="comfortable"
                                    :error-messages="errors.profileImage" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <v-row no-gutters>
                            <v-col cols="12">
                                <div class="mb-4">
                                    <span class="text-title-medium font-weight-bold">Text</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update what users can see and read from your site
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="fields.heading" color="primary" variant="solo" flat
                                    label="Heading" density="comfortable" clearable :error-messages="errors.heading"
                                    autocomplete="off" class="vfield-outline" data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <Select v-model="fields.subheading" :items="subHeadingItems" variant="solo"
                                    label="Subheading" :error-messages="errors.title" class="vfield-outline"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="fields.description" color="primary" auto-grow variant="solo" flat
                                    label="Description" density="comfortable" :error-messages="errors.description"
                                    autocomplete="off" class="vfield-outline" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" lg="6">
                        <v-row no-gutters>
                            <v-col cols="12">
                                <div class="mb-4">
                                    <span class="text-title-medium font-weight-bold">Buttons</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update the contents of your buttons
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="fields.primaryBtnText" color="primary" variant="solo" flat
                                    label="Primary Button Text" density="comfortable" clearable
                                    :error-messages="errors.primaryBtnText" autocomplete="off" class="vfield-outline"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="fields.primaryBtnLink" color="primary" variant="solo" flat
                                    label="Primary Button Link" density="comfortable" clearable
                                    :error-messages="errors.primaryBtnLink" autocomplete="off" class="vfield-outline"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="fields.secondaryBtnText" color="primary" variant="solo" flat
                                    label="Secondary Button Text" density="comfortable" clearable
                                    :error-messages="errors.secondaryBtnText" autocomplete="off" class="vfield-outline"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <FileUpload v-model="fields.secondaryBtnFile" file-type="pdf" :max-files="1" inset
                                    :disabled="loading" :show-size="true" density="comfortable" :hide-browse="true"
                                    :error-messages="errors.secondaryBtnFile" data-shimmer-no-children />
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
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, onMounted, watch, nextTick } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import FormActions from "@/components/forms/FormActions";
import { DEVELOPER_TITLES } from "@/src/constants/constants";

const schema = yup.object({
    profileImage: yup.mixed().label("Profile Image").nullable(),
    heading: yup.string().label("Heading").required(),
    subheading: yup
        .array()
        .of(yup.string().required())
        .min(1, "At least one tag is required")
        .max(4, "Maximum of 4 tags")
        .label("Subheading"),
    description: yup.string().label("Description").required(),
    primaryBtnText: yup.string().label("Primary button text").required(),
    primaryBtnLink: yup.string().label("Primary button link").required(),
    secondaryBtnText: yup.string().label("Secondary button text").required(),
    secondaryBtnFile: yup.mixed().label("Secondary button file").nullable(),
});
const { error } = useSnackBarQueue();
const pageLoading = ref(true);
const subHeadingItems = ref(DEVELOPER_TITLES);
const { fields, errors, loading, submit, cancelEdit, resetForm, meta, ready } = useValidatedForm(
    schema,
    async (values) => {
        const formData = new FormData();
        const profileImageFile =
            values.profileImage instanceof File || values.profileImage instanceof Blob
                ? values.profileImage
                : null;
        const secondaryBtnFileFile =
            values.secondaryBtnFile instanceof File || values.secondaryBtnFile instanceof Blob
                ? values.secondaryBtnFile
                : null;

        if (profileImageFile) {
            formData.append("profileImage", profileImageFile);
        } else if (!values.profileImage) {
            formData.append("remove_profileImage", "1");
        }
        if (secondaryBtnFileFile) {
            formData.append("secondaryBtnFile", secondaryBtnFileFile);
        } else if (!values.secondaryBtnFile) {
            formData.append("remove_secondaryBtnFile", "1");
        }

        const payload = {
            heading: values.heading,
            subheading: values.subheading,
            description: values.description,
            primaryBtnText: values.primaryBtnText,
            primaryBtnLink: values.primaryBtnLink,
            secondaryBtnText: values.secondaryBtnText,
        };
        formData.append("payload", JSON.stringify(payload));

        const response = await axios.post("/api/updateHomeContent", formData);
        await getHomeContent();
        return { message: response.data.message };
    },
    { resetOnSuccess: false }
);

watch(
    () => fields.subheading,
    async (val) => {
        if (!Array.isArray(val)) return;
        if (val.length > 4) {
            await nextTick();
            fields.subheading = val.slice(0, 4);
        }
    },
    { deep: true }
);

async function getHomeContent() {
    try {
        const { data } = await axios.get("/api/getHomeContent");
        if (!data) return;
        resetForm({ values: data });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load home content.");
    } finally {
        pageLoading.value = false;
    }
}

onMounted(() => {
    getHomeContent();
});
</script>