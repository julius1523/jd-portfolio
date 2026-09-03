<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="pa-3 mt-2 rounded-lg">
            <v-form @submit.prevent="submit" :disabled="loading">
                <v-row>
                    <v-col cols="12">
                        <v-row :gap="13">
                            <v-col cols="12" lg="6">
                                <div class="mb-2">
                                    <span class="text-title-medium font-weight-bold">Profile</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update your profile to display to home page
                                    </span>
                                </div>
                                <FileUpload v-model="profileImage" file-type="image" :max-files="1" inset
                                    :disabled="loading" :show-size="true" density="comfortable"
                                    hint="The image to display on your home page" :persistent-hint="true"
                                    :error-messages="errors.profileImage" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" lg="6">
                        <v-row :gap="13">
                            <v-col cols="12">
                                <div>
                                    <span class="text-title-medium font-weight-bold">Text</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update what users can see and read from your site
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="heading" color="primary" variant="solo" flat label="Heading"
                                    rounded="lg" density="comfortable" clearable :error-messages="errors.heading"
                                    autocomplete="off" data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <Select v-model="subheading" :items="subHeadingItems" label="Subheading"
                                    hint="Maximum of 4 tags" persistent-hint :error-messages="errors.title"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="description" color="primary" auto-grow variant="solo" flat
                                    label="Description" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.description" autocomplete="off" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" lg="6">
                        <v-row :gap="13">
                            <v-col cols="12">
                                <div>
                                    <span class="text-title-medium font-weight-bold">Buttons</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update the contents of your buttons
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="primaryBtnText" color="primary" variant="solo" flat
                                    label="Primary Button Text" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.primaryBtnText" autocomplete="off"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="primaryBtnLink" color="primary" variant="solo" flat
                                    label="Primary Button Link" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.primaryBtnLink" autocomplete="off"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="secondaryBtnText" color="primary" variant="solo" flat
                                    label="Secondary Button Text" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.secondaryBtnText" autocomplete="off"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <FileUpload v-model="secondaryBtnFile" file-type="pdf" :max-files="1" inset
                                    :disabled="loading" :show-size="true" density="comfortable" :hide-browse="true"
                                    hint="The CV file for download" :persistent-hint="true"
                                    :error-messages="errors.secondaryBtnFile" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <div class="d-flex flex-column flex-md-row ga-3 justify-end mt-8">
                            <v-btn variant="plain" text="Cancel Edit" rounded="pill" size="x-large"
                                :disabled="!meta.dirty || loading" @click="cancelEdit" />
                            <v-btn type="submit" text="Save Changes" variant="flat" rounded="pill" color="primary"
                                size="x-large" class="order-first order-md-last" :disabled="!meta.dirty || loading"
                                :loading="loading" />
                        </div>
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
import { useUnsavedChanges } from "@/composables/useUnsavedChanges";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import { DEVELOPER_TITLES } from "@/src/constants/constants";
const { info, error } = useSnackBarQueue();
const pageLoading = ref(true);
const subHeadingItems = ref(DEVELOPER_TITLES);
const schema = yup.object({
    profileImage: yup.mixed().label('Profile Image').nullable(),
    heading: yup.string().label('Heading').required(),
    subheading: yup.array().of(yup.string().required()).min(1, "At least one tag is required").max(4, "Maximum of 4 tags").label("Subheading"),
    description: yup.string().label('Description').required(),
    primaryBtnText: yup.string().label('Primary button text').required(),
    primaryBtnLink: yup.string().label('Primary button link').required(),
    secondaryBtnText: yup.string().label('Secondary button text').required(),
    secondaryBtnFile: yup.mixed().label('Secondary button file').nullable(),
});
const { defineField, errors, loading, submit, resetForm, meta } = useValidatedForm(schema, async (values) => {
    const formData = new FormData();
    const profileImageFile = (values.profileImage instanceof File || values.profileImage instanceof Blob) ? values.profileImage : null;
    const secondaryBtnFileFile = (values.secondaryBtnFile instanceof File || values.secondaryBtnFile instanceof Blob) ? values.secondaryBtnFile : null;
    if (profileImageFile) {
        formData.append('profileImage', profileImageFile);
    } else if (!values.profileImage) {
        formData.append('remove_profileImage', '1');
    }
    if (secondaryBtnFileFile) {
        formData.append('secondaryBtnFile', secondaryBtnFileFile);
    } else if (!values.secondaryBtnFile) {
        formData.append('remove_secondaryBtnFile', '1');
    }
    const payload = {
        heading: values.heading,
        subheading: values.subheading,
        description: values.description,
        primaryBtnText: values.primaryBtnText,
        primaryBtnLink: values.primaryBtnLink,
        secondaryBtnText: values.secondaryBtnText,
    };
    formData.append('payload', JSON.stringify(payload));
    const response = await axios.post('/api/updateHomeContent', formData);
    await getHomeContent();
    return { message: response.data.message };
},
    { resetOnSuccess: false }
);
useUnsavedChanges(meta);
const [profileImage] = defineField('profileImage');
const [heading] = defineField('heading');
const [subheading] = defineField('subheading');
const [description] = defineField('description');
const [primaryBtnText] = defineField('primaryBtnText');
const [primaryBtnLink] = defineField('primaryBtnLink');
const [secondaryBtnText] = defineField('secondaryBtnText');
const [secondaryBtnFile] = defineField('secondaryBtnFile');
const cancelEdit = () => {
    resetForm();
    info("No changes made.");
};
async function getHomeContent() {
    try {
        const { data } = await axios.get('/api/getHomeContent');
        if (!data) return;
        resetForm({ values: data });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load home content.");
    } finally {
        pageLoading.value = false;
    }
};
watch(subheading, async (val) => {
    if (!Array.isArray(val)) return;
    if (val.length > 4) {
        await nextTick();
        subheading.value = val.slice(0, 4);
    }
}, { deep: true });
onMounted(() => {
    getHomeContent();
});
</script>