<template>
    <Shimmer :loading="pageLoading">
        <v-form @submit.prevent="submit" :disabled="loading">
            <div class="d-flex flex-column ga-16 mt-8">
                <div>
                    <div class="d-inline-flex flex-column ga-1 mb-4">
                        <div class="text-title-medium font-semibold">Profile Image</div>
                        <div class="text-title-small opacity-60">
                            Update your profile image to display to your home page
                        </div>
                    </div>
                    <v-card class="p-5 shadow-sm border rounded-[12px]">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="sm:shrink-0 text-label-large font-medium">
                                Image
                            </div>
                            <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                <div class="d-flex justify-center justify-sm-end">
                                    <AvatarUpload v-model="fields.profileImage" :disabled="loading" :max-size="1"
                                        :size="160" />
                                </div>
                            </div>
                        </div>
                    </v-card>
                </div>

                <div>
                    <div class="d-inline-flex flex-column ga-1 mb-4">
                        <div class="text-title-medium font-semibold">
                            Text
                        </div>
                        <div class="text-title-small opacity-60">
                            Update what users can see and read from your site
                        </div>
                    </div>

                    <div class="d-flex flex-column ga-4">
                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Heading
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <v-text-field v-model="fields.heading" color="primary" variant="solo" flat
                                        single-line density="compact" clearable :error-messages="errors.heading"
                                        autocomplete="off" class="vfield-outline" hide-details="auto"
                                        data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>

                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Subheading
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <Select v-model="fields.subheading" :items="subHeadingItems" label="Subheading"
                                        variant="solo" flat single-line density="compact" :chip="false"
                                        :error-messages="errors.subheading" hide-details="auto"
                                        data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>

                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Description
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <v-textarea v-model="fields.description" rows="3" color="primary" auto-grow
                                        variant="solo" flat single-line label="Description" density="compact"
                                        :error-messages="errors.description" autocomplete="off" class="vfield-outline"
                                        hide-details="auto" data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>
                    </div>
                </div>

                <div>
                    <div class="d-inline-flex flex-column ga-1 mb-4">
                        <div class="text-title-medium font-semibold">
                            Buttons
                        </div>
                        <div class="text-title-small opacity-60">
                            Update the contents of your buttons
                        </div>
                    </div>

                    <div class="d-flex flex-column ga-4">
                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Primary Button Text
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <v-text-field v-model="fields.primaryBtnText" color="primary" variant="solo" flat
                                        single-line label="Primary Button Text" density="compact" clearable
                                        :error-messages="errors.primaryBtnText" autocomplete="off"
                                        class="vfield-outline" hide-details="auto" data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>

                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Primary Button Link
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <v-text-field v-model="fields.primaryBtnLink" color="primary" variant="solo" flat
                                        single-line label="Primary Button Link" density="compact" clearable
                                        :error-messages="errors.primaryBtnLink" autocomplete="off"
                                        class="vfield-outline" hide-details="auto" data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>

                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Secondary Button Text
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <v-text-field v-model="fields.secondaryBtnText" color="primary" variant="solo" flat
                                        single-line label="Secondary Button Text" density="compact" clearable
                                        :error-messages="errors.secondaryBtnText" autocomplete="off"
                                        class="vfield-outline" hide-details="auto" data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>

                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Secondary Button File
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <FileUpload v-model="fields.secondaryBtnFile" file-type="pdf" :max-files="1" inset
                                        :disabled="loading" :show-size="true" density="comfortable" :hide-browse="true"
                                        :error-messages="errors.secondaryBtnFile" hide-details="auto"
                                        data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>
                    </div>
                </div>
            </div>

            <FormActions :dirty="meta.dirty" :ready="ready" :loading="loading" @cancel="cancelEdit" />
        </v-form>
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
import AvatarUpload from "@/components/forms/AvatarUpload";
import FormActions from "@/components/forms/FormActions";
import { DEVELOPER_TITLES } from "@/src/constants/constants";

const schema = yup.object({
    profileImage: yup.mixed().label("Profile Image").nullable(),
    heading: yup.string().label("Heading").required(),
    subheading: yup.array().of(yup.string()).label("Subheading").min(1, "At least one tag is required").max(4, "Maximum of 4 tags").default([]),
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