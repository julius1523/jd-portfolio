<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="pa-1 mt-8 rounded-lg">
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
                                    <span class="text-title-medium font-weight-bold">Projects</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update the projects to showcase
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="fields.projects" :headers="projectHeaders" :addable="true"
                                    add-label="New Project" expand-key="description"
                                    v-model:sort-by="projectsOptions.sortBy" v-model:page="projectsOptions.page"
                                    v-model:items-per-page="projectsOptions.itemsPerPage" :items-length="projectsTotal"
                                    :loading="projectsLoading" no-data-text="No projects added yet." :disabled="loading"
                                    density="comfortable" @add="openDialog()" @edit="openDialog($event)"
                                    @remove="removeItem($event)">
                                    <template #item.image="{ item }">
                                        <v-img v-if="projectImagePreview(item)" height="48" width="48" :aspect-ratio="1"
                                            rounded class="border [&_img]:object-fill"
                                            :class="{ 'ml-auto': $vuetify.display.smAndDown }"
                                            :src="projectImagePreview(item)" eager />
                                        <span v-else class="text-medium-emphasis">—</span>
                                    </template>
                                    <template #item.name="{ item }">
                                        <div class="d-flex flex-column ga-1 mb-1">
                                            <div>{{ item.name }}</div>
                                            <div class="d-flex flex-wrap ga-1"
                                                :class="{ 'justify-end': $vuetify.display.smAndDown }">
                                                <v-chip v-for="(material, i) in item.materials" :key="i" size="small"
                                                    color="primary" variant="tonal">
                                                    {{ material }}
                                                </v-chip>
                                            </div>
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

    <ProjectsDialog ref="dialogRef" :list="fields.projects" />
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, reactive, onMounted, watch } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackBarQueue } from "@/composables/useSnackbarQueue";
import DataTable from "@/components/data/DataTable";
import FileUpload from "@/components/forms/FileUpload";
import FormActions from "@/components/forms/FormActions";
import ProjectsDialog from "./ProjectsDialog";

const projectHeaders = [
    { title: "Category", key: "category", align: "start" },
    { title: "Project", key: "name", align: "start" },
    { title: "Image", key: "image", align: "middle", sortable: false },
];
const schema = yup.object({
    profileImage: yup.mixed().label("Profile Image").nullable(),
    heading: yup.string().label("Heading").required(),
    description: yup.string().label("Description").required(),
    projects: yup.array().label("Projects").default([]),
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

        const projectsPayload = (values.projects ?? []).map((p, index) => {
            const projectPayload = { ...p };
            if (p.image instanceof File || p.image instanceof Blob) {
                formData.append(`projectImages[${index}]`, p.image);
                delete projectPayload.image;
            }
            if (p.linkType === "upload" && (p.linkFile instanceof File || p.linkFile instanceof Blob)) {
                formData.append(`projectLinkFiles[${index}]`, p.linkFile);
                delete projectPayload.linkFile;
            } else if (p.linkType !== "upload") {
                delete projectPayload.linkFile;
            }
            return projectPayload;
        });

        formData.append(
            "payload",
            JSON.stringify({
                heading: values.heading,
                description: values.description,
                projects: projectsPayload,
            })
        );

        const response = await axios.post("/api/updateProjectContent", formData);
        await getProjectContent();
        return { message: response.data.message };
    },
    { resetOnSuccess: false }
);
const projectsOptions = reactive({ page: 1, itemsPerPage: 10, sortBy: [] });
const projectsTotal = ref(0);
const projectsLoading = ref(false);

function openDialog(item = null) {
    dialogRef.value?.open(item);
};
function removeItem(item) {
    dialogRef.value?.remove(item);
};
function projectImagePreview(item) {
    if (item.image instanceof File || item.image instanceof Blob) {
        return URL.createObjectURL(item.image);
    }
    return item.image.url ?? null;
};

async function getProjectContent() {
    try {
        const { data } = await axios.get("/api/getProjectContent", {
            params: { page: projectsOptions.page, perPage: projectsOptions.itemsPerPage },
        });
        if (!data) return;
        resetForm({ values: { ...data, projects: data.projects ?? [] } });
        projectsTotal.value = data.total ?? 0;
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load project content.");
    } finally {
        pageLoading.value = false;
    }
};
async function fetchProjects() {
    projectsLoading.value = true;
    try {
        const [sort] = projectsOptions.sortBy ?? [];
        const { data } = await axios.get("/api/getProjectContent", {
            params: {
                page: projectsOptions.page,
                perPage: projectsOptions.itemsPerPage,
                sortBy: sort?.key,
                sortOrder: sort?.order,
            },
        });
        if (!data) return;
        resetField('projects', { value: data.projects ?? [] });
        projectsTotal.value = data.total ?? 0;
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load projects.");
    } finally {
        projectsLoading.value = false;
    }
};

watch(() => [projectsOptions.page, projectsOptions.itemsPerPage, projectsOptions.sortBy], fetchProjects);

onMounted(() => {
    getProjectContent();
});
</script>