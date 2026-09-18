<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="mt-4 rounded-lg">
            <v-form @submit.prevent="submit" :disabled="loading">
                <v-row :gap="55">
                    <v-col cols="12">
                        <v-row :gap="10">
                            <v-col cols="12">
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
                        <v-row :gap="10">
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
                        <v-row :gap="10">
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
                                    :expandable="false" add-label="New Social" v-model:sort-by="socialsOptions.sortBy"
                                    v-model:page="socialsOptions.page"
                                    v-model:items-per-page="socialsOptions.itemsPerPage" :items-length="socialsTotal"
                                    :loading="socialsLoading" no-data-text="No socials added yet." :disabled="loading"
                                    density="comfortable" @add="openDialog()" @edit="openDialog($event)"
                                    @remove="removeItem($event)">
                                    <template #item.name="{ item }">
                                        <div class="d-flex ga-3 align-center"
                                            :class="{ 'justify-end': $vuetify.display.smAndDown }">
                                            <v-icon :class="item.icon" color="primary" />
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

    <SocialsDialog ref="dialogRef" :list="fields.socials" />
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, reactive, onMounted, watch } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackbarQueue } from "@/composables/useSnackbarQueue";
import DataTable from "@/components/data/DataTable";
import FileUpload from "@/components/forms/FileUpload";
import FormActions from "@/components/forms/FormActions";
import SocialsDialog from "./SocialsDialog";

const socialHeaders = [
    { title: "Social Name", key: "name", align: "start" },
    { title: "Link URL", key: "linkUrl", align: "start", sortable: false },
];
const schema = yup.object({
    profileImage: yup.mixed().label("Profile Image").nullable(),
    heading: yup.string().label("Heading").required(),
    description: yup.string().label("Description").required(),
    socials: yup.array().label("Socials").default([]),
});
const { error } = useSnackbarQueue();
const pageLoading = ref(true);
const dialogRef = ref();
const { fields, errors, loading, submit, cancelEdit, resetForm, resetField, meta, ready } = useValidatedForm(
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
const socialsOptions = reactive({ page: 1, itemsPerPage: 10, sortBy: [] });
const socialsTotal = ref(0);
const socialsLoading = ref(false);

function openDialog(item = null) {
    dialogRef.value?.open(item);
};
function removeItem(item) {
    dialogRef.value?.remove(item);
};

async function getContactContent() {
    try {
        const { data } = await axios.get("/api/getContactContent");
        if (!data) return;
        resetForm({ values: { ...data } });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load contact content.");
    } finally {
        pageLoading.value = false;
    }
};
async function fetchSocials() {
    socialsLoading.value = true;
    try {
        const [sort] = socialsOptions.sortBy ?? [];
        const { data } = await axios.get("/api/getContactContent", {
            params: {
                page: socialsOptions.page,
                perPage: socialsOptions.itemsPerPage,
                sortBy: sort?.key,
                sortOrder: sort?.order,
            },
        });
        if (!data) return;
        resetField('socials', { value: data.socials ?? [] });
        socialsTotal.value = data.total ?? 0;
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load socials.");
    } finally {
        socialsLoading.value = false;
    }
};

watch(() => [socialsOptions.page, socialsOptions.itemsPerPage, socialsOptions.sortBy], fetchSocials);

onMounted(() => {
    getContactContent();
});
</script>