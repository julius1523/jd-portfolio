<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="pa-4 mt-2 rounded-lg">
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
                                <FileUpload :key="`file-${formResetKey}`" v-model="profileImage" file-type="image"
                                    :max-files="1" inset :disabled="loading" :show-size="true" density="comfortable"
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
                                <v-textarea v-model="description" color="primary" auto-grow variant="solo" flat
                                    label="Description" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.description" autocomplete="off" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <v-row :gap="13">
                            <v-col cols="12">
                                <div>
                                    <span class="text-title-medium font-weight-bold">Projects</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update the projects to showcase
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="projects" :headers="projectHeaders" :addable="true"
                                    add-label="New Project" expand-key="description"
                                    no-data-text="No projects added yet." @add="openProjectDialog"
                                    @edit="openProjectDialog" @remove="removeProject">
                                    <template #item.materials="{ item }">
                                        <div class="d-flex flex-wrap ga-1 py-2"
                                            :class="{ 'justify-end': $vuetify.display.smAndDown }">
                                            <v-chip v-for="(material, i) in item.materials" :key="i" size="small"
                                                color="primary" variant="tonal">
                                                {{ material }}
                                            </v-chip>
                                        </div>
                                    </template>

                                    <template #item.image="{ item }">
                                        <v-img v-if="projectImagePreview(item)" height="48" width="48" rounded
                                            class="border" :class="{ 'ml-auto': $vuetify.display.smAndDown }"
                                            :src="projectImagePreview(item)" eager />
                                        <span v-else class="text-medium-emphasis">—</span>
                                    </template>
                                </DataTable>
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

    <Dialog v-model="projectDialog" :is-editing="editingIndex > -1" add-title="Add Project" edit-title="Edit Project"
        save-text="Add Project" edit-save-text="Save Changes" cancel-text="Cancel" edit-cancel-text="Cancel Edit"
        @save="addProject" @cancel="closeProjectDialog">
        <v-form @submit.prevent="addProject">
            <v-row :gap="13">
                <v-col cols="12">
                    <Select v-model="pCategory" :items="categoryOptions" label="Category" color="primary" variant="solo"
                        flat rounded="lg" density="comfortable" :multiple="false" :chip="false"
                        :error-messages="projectErrors.category" />
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="pName" label="Project Name" color="primary" variant="solo" flat rounded="lg"
                        density="comfortable" clearable :error-messages="projectErrors.name" autocomplete="off" />
                </v-col>
                <v-col cols="12">
                    <v-textarea v-model="pDescription" label="Description" color="primary" auto-grow variant="solo" flat
                        rounded="lg" density="comfortable" clearable :error-messages="projectErrors.description"
                        autocomplete="off" />
                </v-col>
                <v-col cols="12">
                    <Select :key="`file-${formResetKey}`" v-model="pMaterials" :items="activeCategoryItems"
                        item-title="text" item-value="text" label="Materials" :error-messages="projectErrors.materials"
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
                    <FileUpload :key="`project-image-${projectFormResetKey}`" v-model="pImage" file-type="image"
                        :max-files="1" inset density="comfortable" :show-size="true" hint="The image for this project"
                        :error-messages="projectErrors.image" />
                </v-col>
                <v-col cols="12">
                    <div class="text-title-small text-medium-emphasis mb-2">Link</div>
                    <v-btn-toggle v-model="pLinkType" color="primary" variant="outlined" density="compact" rounded="lg"
                        mandatory divided class="mb-3">
                        <v-btn value="upload" text="Upload" />
                        <v-btn value="link" text="Link" />
                    </v-btn-toggle>
                    <FileUpload v-if="pLinkType === 'upload'" :key="`project-link-file-${projectFormResetKey}`"
                        v-model="pLinkFile" :max-files="1" inset density="comfortable" :show-size="true"
                        hint="File to link to this project" :error-messages="projectErrors.linkFile" />
                    <v-text-field v-else v-model="pLinkUrl" label="Link URL" color="primary" variant="solo" flat
                        rounded="lg" density="comfortable" clearable placeholder="https://..."
                        :error-messages="projectErrors.linkUrl" autocomplete="off" />
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, onMounted, computed } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useUnsavedChanges } from "@/composables/useUnsavedChanges";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import Dialog from "@/components/forms/FormDialog";
import { PROJECT_MATERIALS } from "@/src/constants/constants";
const materialsTab = ref(Object.keys(PROJECT_MATERIALS)[0]);
const activeCategoryItems = computed(() => PROJECT_MATERIALS[materialsTab.value] ?? []);
const { info, error } = useSnackBarQueue();
const pageLoading = ref(true);
const schema = yup.object({
    profileImage: yup.mixed().label('Profile Image').nullable(),
    heading: yup.string().label('Heading').required(),
    description: yup.string().label('Description').required(),
    projects: yup.array().label('Projects').default([]),
});
const { defineField, errors, loading, submit, resetForm, meta } = useValidatedForm(schema, async (values) => {
    const formData = new FormData();
    const profileImageFile = (values.profileImage instanceof File || values.profileImage instanceof Blob) ? values.profileImage : null;
    const secondaryBtnFileFile = (values.secondaryBtnFile instanceof File || values.secondaryBtnFile instanceof Blob) ? values.secondaryBtnFile : null;
    if (profileImageFile) {
        formData.append('profileImage', profileImageFile);
    } else if (!values.profileImage) {
        formData.append('remove_profileImage', '1');
    };
    if (secondaryBtnFileFile) {
        formData.append('secondaryBtnFile', secondaryBtnFileFile);
    } else if (!values.secondaryBtnFile) {
        formData.append('remove_secondaryBtnFile', '1');
    };
    const projectsPayload = (values.projects ?? []).map((p, index) => {
        const projectPayload = { ...p };
        if (p.image instanceof File || p.image instanceof Blob) {
            formData.append(`projectImages[${index}]`, p.image);
            delete projectPayload.image;
        };
        if (p.linkType === 'upload' && (p.linkFile instanceof File || p.linkFile instanceof Blob)) {
            formData.append(`projectLinkFiles[${index}]`, p.linkFile);
            delete projectPayload.linkFile;
        } else if (p.linkType !== 'upload') {
            delete projectPayload.linkFile;
        };
        return projectPayload;
    });
    const payload = {
        heading: values.heading,
        description: values.description,
        projects: projectsPayload,
    };
    formData.append('payload', JSON.stringify(payload));
    const response = await axios.post('/api/updateProjectContent', formData);
    await getProjectContent();
    return { message: response.data.message };
},
    { resetOnSuccess: false }
);
useUnsavedChanges(meta);
const [profileImage] = defineField('profileImage');
const [heading] = defineField('heading');
const [description] = defineField('description');
const formResetKey = ref(0);
const cancelEdit = () => {
    resetForm();
    formResetKey.value++;
    info("No changes made.");
};
async function getProjectContent() {
    try {
        const { data } = await axios.get('/api/getProjectContent');
        if (!data) return;
        resetForm({ values: data });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load home content.");
    } finally {
        pageLoading.value = false;
    };
};
const projectHeaders = [
    { title: 'Category', key: 'category', align: 'start' },
    { title: 'Project Name', key: 'name', align: 'start' },
    { title: 'Materials', key: 'materials', align: 'start' },
    { title: 'Image', key: 'image', align: 'middle' },
];
const [projects] = defineField('projects');
const projectDialog = ref(false);
const editingIndex = ref(-1);
const projectFormResetKey = ref(0);
const categoryOptions = [
    'Software Development',
    'Technical Documentation',
    'Presentations/Multimedia',
];
const projectSchema = yup.object({
    category: yup.string().label('Category').oneOf(categoryOptions, 'Select a valid category').required(),
    name: yup.string().label('Project name').required(),
    description: yup.string().label('Description').required(),
    materials: yup.array().label('Materials').min(1, 'At least one material is required'),
    image: yup.mixed().label('Image').required('Image is required'),
    linkType: yup.string().oneOf(['upload', 'link']).required(),
    linkFile: yup.mixed().nullable().when('linkType', {
        is: 'upload',
        then: (s) => s.required('File is required'),
    }),
    linkUrl: yup.string().nullable().when('linkType', {
        is: 'link',
        then: (s) => s.url('Must be a valid URL').required('Link URL is required'),
    }),
});
const {
    defineField: defineProjectField,
    errors: projectErrors,
    submit: submitProjectForm,
    resetForm: resetProjectForm,
} = useValidatedForm(projectSchema, async (values) => {
    const project = {
        category: values.category,
        name: values.name,
        description: values.description,
        materials: values.materials,
        image: values.image,
        linkType: values.linkType,
        linkFile: values.linkType === 'upload' ? values.linkFile : null,
        linkUrl: values.linkType === 'link' ? values.linkUrl : '',
    };
    if (editingIndex.value > -1) {
        projects.value.splice(editingIndex.value, 1, project);
    } else {
        projects.value.push(project);
    }
    closeProjectDialog();
}, { resetOnSuccess: false });
const [pCategory] = defineProjectField('category');
const [pName] = defineProjectField('name');
const [pDescription] = defineProjectField('description');
const [pMaterials] = defineProjectField('materials');
const [pImage] = defineProjectField('image');
const [pLinkType] = defineProjectField('linkType');
const [pLinkFile] = defineProjectField('linkFile');
const [pLinkUrl] = defineProjectField('linkUrl');
function formatLabel(key) {
    return key.charAt(0).toUpperCase() + key.slice(1);
}
function addProject() {
    submitProjectForm();
};
function openProjectDialog(item = null, index = -1) {
    editingIndex.value = index;
    projectFormResetKey.value++;
    materialsTab.value = Object.keys(PROJECT_MATERIALS)[0];
    resetProjectForm({
        values: item ? { ...item } : {
            category: categoryOptions[0],
            name: '',
            description: '',
            materials: [],
            image: null,
            linkType: 'upload',
            linkFile: null,
            linkUrl: '',
        },
    });
    projectDialog.value = true;
};
function closeProjectDialog() {
    projectDialog.value = false;
    editingIndex.value = -1;
};
function removeProject(index) {
    projects.value.splice(index, 1);
};
function projectImagePreview(item) {
    if (item.image instanceof File || item.image instanceof Blob) {
        return URL.createObjectURL(item.image);
    };
    return item.image.url ?? null;
};
onMounted(() => {
    getProjectContent();
});
</script>