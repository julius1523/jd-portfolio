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
                                    @add="openProjectDialog" @edit="openProjectDialog" @remove="projectDialog.remove">
                                    <template #item.image="{ item }">
                                        <v-img v-if="projectImagePreview(item)" height="48" width="48" :aspect-ratio="1"
                                            class="border rounded-[10px] [&_img]:object-fill"
                                            :class="{ 'ml-auto': $vuetify.display.smAndDown }"
                                            :src="projectImagePreview(item)" eager />
                                        <span v-else class="text-medium-emphasis">—</span>
                                    </template>
                                    <template #item.name="{ item }">
                                        <div class="d-flex flex-column ga-1 py-2">
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

    <Dialog v-model="projectDialog.dialog.value" :is-editing="projectDialog.isEditing.value" add-title="Add Project"
        edit-title="Edit Project" save-text="Add project" edit-save-text="Save changes" cancel-text="Cancel"
        edit-cancel-text="Cancel edit" :loading="projectDialog.loading.value"
        :disable-save="!projectDialog.meta.value.valid || (projectDialog.isEditing.value && !projectDialog.meta.value.dirty)"
        @save="projectDialog.submit" @cancel="projectDialog.close">
        <v-form @submit.prevent="projectDialog.submit">
            <v-row no-gutters>
                <v-col cols="12">
                    <Select v-model="projectDialog.fields.category" :items="categoryOptions" label="Category"
                        color="primary" variant="solo" flat density="comfortable" :multiple="false" :chip="false"
                        :error-messages="projectDialog.errors.category" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="projectDialog.fields.name" label="Project Name" color="primary"
                        variant="solo" flat density="comfortable" clearable :error-messages="projectDialog.errors.name"
                        class="vfield-outline" autocomplete="off" />
                </v-col>
                <v-col cols="12">
                    <v-textarea v-model="projectDialog.fields.description" label="Description" color="primary" auto-grow
                        variant="solo" flat density="comfortable" clearable
                        :error-messages="projectDialog.errors.description" class="vfield-outline" autocomplete="off" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="projectDialog.fields.materials" :items="activeCategoryItems" variant="solo"
                        item-title="text" item-value="text" label="Materials"
                        :error-messages="projectDialog.errors.materials" class="vfield-outline"
                        data-shimmer-no-children>
                        <template v-slot:menu-header>
                            <v-tabs v-model="materialsTab" slider-color="primary" density="comfortable" grow
                                class="border-b" @keydown.enter.stop>
                                <v-tab v-for="(_, category) in PROJECT_MATERIALS" :key="category" :value="category">
                                    {{ formatLabel(category) }}
                                </v-tab>
                            </v-tabs>
                        </template>
                    </Select>
                </v-col>
                <v-col cols="12">
                    <FileUpload v-model="projectDialog.fields.image" file-type="image" :max-files="1" inset
                        density="comfortable" :show-size="true" :error-messages="projectDialog.errors.image" />
                </v-col>
                <v-col cols="12">
                    <div class="text-title-small text-medium-emphasis mb-2">Link</div>
                    <v-btn-toggle v-model="projectDialog.fields.linkType" color="primary" variant="outlined"
                        density="compact" mandatory divided class="rounded-[10px] mb-3">
                        <v-btn value="upload" text="Upload" />
                        <v-btn value="link" text="Link" />
                    </v-btn-toggle>
                    <FileUpload v-if="projectDialog.fields.linkType === 'upload'"
                        v-model="projectDialog.fields.linkFile" :max-files="1" inset density="comfortable"
                        :show-size="true" :error-messages="projectDialog.errors.linkFile" />
                    <v-text-field v-else v-model="projectDialog.fields.linkUrl" label="Link URL" color="primary"
                        variant="solo" flat density="comfortable" clearable
                        :error-messages="projectDialog.errors.linkUrl" class="vfield-outline" autocomplete="off" />
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, reactive, computed, onMounted, watch } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useEntryDialog } from "@/composables/useEntryDialog";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import FormActions from "@/components/forms/FormActions";
import Dialog from "@/components/forms/FormDialog";
import { PROJECT_MATERIALS } from "@/src/constants/constants";

const categoryOptions = [
    "Software Development",
    "Technical Documentation",
    "Presentations/Multimedia",
];
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
const projectSchema = yup.object({
    id: yup.mixed().nullable(),
    category: yup.string().label("Category").oneOf(categoryOptions, "Select a valid category").required(),
    name: yup.string().label("Project name").required(),
    description: yup.string().label("Description").required(),
    materials: yup.array().label("Materials").min(1, "At least one material is required"),
    image: yup.mixed().label("Image").required("Image is required"),
    linkType: yup.string().oneOf(["upload", "link"]).required(),
    linkFile: yup.mixed().nullable().when("linkType", {
        is: "upload",
        then: (s) => s.required("File is required"),
    }),
    linkUrl: yup.string().nullable().when("linkType", {
        is: "link",
        then: (s) => s.url("Must be a valid URL").required("Link URL is required"),
    }),
});
const { error } = useSnackBarQueue();
const pageLoading = ref(true);
const materialsTab = ref(Object.keys(PROJECT_MATERIALS)[0]);
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
const projectDialog = useEntryDialog(projectSchema, () => fields.projects, {
    category: categoryOptions[0],
    name: "",
    description: "",
    materials: [],
    image: null,
    linkType: "upload",
    linkFile: null,
    linkUrl: "",
});
const activeCategoryItems = computed(() => PROJECT_MATERIALS[materialsTab.value] ?? []);
const projectsOptions = reactive({ page: 1, itemsPerPage: 10, sortBy: [] });
const projectsTotal = ref(0);
const projectsLoading = ref(false);

function formatLabel(key) {
    return key.charAt(0).toUpperCase() + key.slice(1);
};
function projectImagePreview(item) {
    if (item.image instanceof File || item.image instanceof Blob) {
        return URL.createObjectURL(item.image);
    }
    return item.image.url ?? null;
};
function openProjectDialog(item = null) {
    materialsTab.value = Object.keys(PROJECT_MATERIALS)[0];
    projectDialog.open(item);
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