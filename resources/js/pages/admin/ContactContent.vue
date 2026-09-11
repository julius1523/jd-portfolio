<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="pa-3 mt-2 rounded-lg">
            <v-form @submit.prevent="submit" :disabled="loading">
                <v-row :gap="45">
                    <v-col cols="12">
                        <v-row no-gutters>
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
                                <v-textarea v-model="fields.description" color="primary" auto-grow variant="solo" flat
                                    label="Description" density="comfortable" :error-messages="errors.description"
                                    autocomplete="off" class="vfield-outline" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <v-row no-gutters>
                            <v-col cols="12">
                                <div class="mb-4">
                                    <span class="text-title-medium font-weight-bold">Socials</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update the socials to showcase
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="fields.socials" :headers="socialHeaders" :addable="true"
                                    :expandable="false" add-label="New Social" no-data-text="No socials added yet."
                                    :disabled="loading" @add="socialDialog.open" @edit="socialDialog.open"
                                    @remove="socialDialog.remove">
                                    <template #item.name="{ item }">
                                        <div class="d-flex ga-3 align-center"
                                            :class="{ 'justify-end': $vuetify.display.smAndDown }">
                                            <v-icon :class="item.icon" size="35" color="primary" />
                                            <span>{{ item.name }}</span>
                                        </div>
                                    </template>
                                </DataTable>
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

    <Dialog v-model="socialDialog.dialog.value" :is-editing="socialDialog.isEditing.value" add-title="Add Social"
        edit-title="Edit Social" save-text="Add social" edit-save-text="Save changes" cancel-text="Cancel"
        edit-cancel-text="Cancel Edit" :loading="socialDialog.loading.value" @save="socialDialog.submit"
        @cancel="socialDialog.close">
        <v-form @submit.prevent="socialDialog.submit">
            <v-row no-gutters>
                <v-col cols="12">
                    <v-text-field v-model="socialDialog.fields.name" label="Social Name" color="primary" variant="solo"
                        flat density="comfortable" clearable :error-messages="socialDialog.errors.name"
                        autocomplete="off" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="socialDialog.fields.linkUrl" label="Link URL" color="primary" variant="solo"
                        flat density="comfortable" clearable :error-messages="socialDialog.errors.linkUrl"
                        autocomplete="off" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="socialDialog.fields.icon" label="Icon" color="primary" variant="solo" flat
                        density="comfortable" :items="SOCIAL_ICONS" item-title="name" item-value="value"
                        :multiple="false" :chip="false" :error-messages="socialDialog.errors.icon"
                        class="vfield-outline">
                        <template #item="{ item, props: itemProps }">
                            <v-list-item v-bind="itemProps" :prepend-icon="item?.value" color="primary"
                                :title="undefined">
                                <template #title>
                                    <span class="text-label-medium">{{ item?.name }}</span>
                                </template>
                            </v-list-item>
                        </template>

                        <template #selection="{ item }">
                            <v-icon :icon="item?.value" color="primary" size="small" class="mr-2" />
                            {{ item?.name }}
                        </template>
                    </Select>
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, onMounted } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useEntryDialog } from "@/composables/useEntryDialog";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import FormActions from "@/components/forms/FormActions";
import Dialog from "@/components/forms/FormDialog";
import { SOCIAL_ICONS } from "@/src/constants/constants";

const socialHeaders = [
    { title: "Social Name", key: "name", align: "start" },
    { title: "Link URL", key: "linkUrl", align: "start" },
];
const schema = yup.object({
    profileImage: yup.mixed().label("Profile Image").nullable(),
    heading: yup.string().label("Heading").required(),
    description: yup.string().label("Description").required(),
    socials: yup.array().label("Socials").default([]),
});
const socialSchema = yup.object({
    id: yup.mixed().nullable(),
    name: yup.string().label("Social Name").required(),
    linkUrl: yup.string().label("Link URL").required(),
    icon: yup.string().label("Icon").required(),
});
const { error } = useSnackBarQueue();
const pageLoading = ref(true);
const { fields, errors, loading, submit, cancelEdit, resetForm, meta, ready } = useValidatedForm(
    schema,
    async (values) => {
        const formData = new FormData();
        const profileImageFile =
            values.profileImage instanceof File || values.profileImage instanceof Blob
                ? values.profileImage
                : null;

        if (profileImageFile) {
            formData.append("profileImage", profileImageFile);
        } else if (!values.profileImage) {
            formData.append("remove_profileImage", "1");
        }

        formData.append(
            "payload",
            JSON.stringify({
                heading: values.heading,
                description: values.description,
                socials: values.socials,
            })
        );

        const response = await axios.post("/api/updateContactContent", formData);
        await getContactContent();
        return { message: response.data.message };
    },
    { resetOnSuccess: false }
);
const socialDialog = useEntryDialog(socialSchema, () => fields.socials, {
    name: "",
    linkUrl: "",
    icon: null,
});

async function getContactContent() {
    try {
        const { data } = await axios.get("/api/getContactContent");
        if (!data) return;
        resetForm({ values: data });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load contact content.");
    } finally {
        pageLoading.value = false;
    }
}

onMounted(() => {
    getContactContent();
});
</script>