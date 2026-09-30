<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="pa-1 mt-8 rounded-[10px]">
            <v-form @submit.prevent="submit" :disabled="loading">
                <div class="d-flex flex-column ga-16">
                    <div>
                        <div class="d-inline-flex flex-column ga-1 mb-4">
                            <div class="text-title-medium font-semibold">Profile Image</div>
                            <div class="text-title-small opacity-60">
                                Update your profile image to display to your contact page
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
                                Update what the users can see and read from your site
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
                                        Description
                                    </div>
                                    <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                        <v-textarea v-model="fields.description" rows="3" color="primary" auto-grow
                                            variant="solo" flat single-line label="Description" density="compact"
                                            :error-messages="errors.description" autocomplete="off"
                                            class="vfield-outline" hide-details="auto" data-shimmer-no-children />
                                    </div>
                                </div>
                            </v-card>
                        </div>
                    </div>

                    <div>
                        <div class="d-inline-flex flex-column ga-1 mb-4">
                            <div class="text-title-medium font-semibold">Socials</div>
                            <div class="text-title-small opacity-60">
                                Update your social accounts to link
                            </div>
                        </div>
                        <DataTable :items="fields.socials" :headers="socialHeaders" :addable="true" :expandable="false"
                            add-label="New Social" v-model:sort-by="socialsOptions.sortBy"
                            v-model:page="socialsOptions.page" v-model:items-per-page="socialsOptions.itemsPerPage"
                            :items-length="socialsTotal" :loading="socialsLoading" no-data-text="No socials added yet."
                            :disabled="loading" density="comfortable" @add="openDialog()" @edit="openDialog($event)"
                            @remove="removeItem($event)">
                            <template #item.name="{ item }">
                                <div class="d-flex ga-3 align-center"
                                    :class="{ 'justify-end': $vuetify.display.smAndDown }">
                                    <v-icon :class="item.icon" color="primary" />
                                    <span>{{ item.name }}</span>
                                </div>
                            </template>
                            <template #item.linkUrl="{ item }">
                                <div class="break-all">
                                    {{ item.linkUrl }}
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </div>

                <FormActions :dirty="meta.dirty" :ready="ready" :loading="loading" @cancel="cancelEdit" />
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
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import AvatarUpload from "@/components/forms/AvatarUpload";
import FormActions from "@/components/forms/FormActions";
import SocialsDialog from "./dialogs/SocialsDialog";

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
const { error } = useSnackBarQueue();
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