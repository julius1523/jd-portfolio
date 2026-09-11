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
                                    <span class="text-title-medium font-weight-bold">Skills</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update what skills to showcase per category
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="fields.skills" :headers="skillHeaders" :addable="true"
                                    :expandable="false" add-label="New Skill" no-data-text="No skills added yet."
                                    :disabled="loading" @add="skillDialog.open" @edit="skillDialog.open"
                                    @remove="skillDialog.remove">
                                    <template #item.category="{ item }">
                                        {{ item.category }}
                                    </template>
                                    <template #item.skill="{ item }">
                                        <div class="d-flex flex-wrap ga-1 py-2"
                                            :class="{ 'justify-end': $vuetify.display.smAndDown }">
                                            <v-chip v-for="skillName in item.skill" :key="skillName" size="small"
                                                color="primary" variant="tonal">
                                                {{ skillName }}
                                            </v-chip>
                                        </div>
                                    </template>
                                    <template #item.icon="{ item }">
                                        <v-icon v-if="item.iconSvg" color="primary" size="35">
                                            <span v-html="item.iconSvg" class="inline-flex items-center" />
                                        </v-icon>
                                        <span v-else class="text-medium-emphasis">—</span>
                                    </template>
                                </DataTable>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <v-row no-gutters>
                            <v-col cols="12">
                                <div class="mb-4">
                                    <span class="text-title-medium font-weight-bold">Random Facts</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update the random facts to showcase
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="fields.randomFacts" :headers="randomFactHeaders" :addable="true"
                                    :expandable="false" add-label="New Fact" no-data-text="No facts added yet."
                                    :disabled="loading" @add="factDialog.open" @edit="factDialog.open"
                                    @remove="factDialog.remove">
                                    <template #item.icon="{ item }">
                                        <v-icon color="primary" size="35">
                                            <span v-html="item.iconSvg" class="inline-flex items-center" />
                                        </v-icon>
                                    </template>
                                </DataTable>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <v-row no-gutters>
                            <v-col cols="12">
                                <div class="mb-4">
                                    <span class="text-title-medium font-weight-bold">Others</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Other details to showcase to viewers
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="fields.others.title" color="primary" variant="solo" flat
                                    label="Title" density="comfortable" clearable
                                    :error-messages="errors['others.title']" autocomplete="off" class="vfield-outline"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="fields.others.description" color="primary" auto-grow variant="solo"
                                    flat label="Description" density="comfortable"
                                    :error-messages="errors['others.description']" autocomplete="off"
                                    class="vfield-outline" data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <FileUpload v-model="fields.others.image" file-type="image" :max-files="1" inset
                                    :disabled="loading" :show-size="true" density="comfortable"
                                    :error-messages="errors['others.image']" data-shimmer-no-children />
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

    <Dialog v-model="skillDialog.dialog.value" :is-editing="skillDialog.isEditing.value" add-title="Add Skill"
        edit-title="Edit Skill" save-text="Add skill" edit-save-text="Save changes" cancel-text="Cancel"
        edit-cancel-text="Cancel edit" :loading="skillDialog.loading.value" @save="skillDialog.submit"
        @cancel="skillDialog.close">
        <v-form @submit.prevent="skillDialog.submit">
            <v-row no-gutters>
                <v-col cols="12">
                    <Select v-model="skillDialog.fields.category" :items="skillCategoryOptions" label="Skill Category"
                        variant="solo" flat :multiple="false" :chip="false"
                        :error-messages="skillDialog.errors.category" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="skillDialog.fields.skill" :items="availableSkills" label="Skills" variant="solo"
                        :multiple="true" :chip="true" :error-messages="skillDialog.errors.skill"
                        class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <v-text-field :model-value="skillDialog.fields.icon" label="Icon (optional)" color="primary"
                        variant="solo" flat density="comfortable" :error-messages="skillDialog.errors.icon" readonly
                        class="vfield-outline">
                        <template #default>
                            <v-icon v-if="skillDialog.fields.iconSvg" color="primary" size="20" class="mr-2">
                                <span v-html="skillDialog.fields.iconSvg" class="inline-flex items-center" />
                            </v-icon>
                        </template>
                        <template #append-inner>
                            <IconPicker v-model="skillDialog.fields.icon"
                                @selected="skillDialog.fields.iconSvg = $event.svg" />
                        </template>
                    </v-text-field>
                </v-col>
            </v-row>
        </v-form>
    </Dialog>

    <Dialog v-model="factDialog.dialog.value" :is-editing="factDialog.isEditing.value" add-title="Add Fact"
        edit-title="Edit Fact" save-text="Add fact" edit-save-text="Save changes" cancel-text="Cancel"
        edit-cancel-text="Cancel edit" :loading="factDialog.loading.value" @save="factDialog.submit"
        @cancel="factDialog.close">
        <v-form @submit.prevent="factDialog.submit">
            <v-row no-gutters>
                <v-col cols="12">
                    <v-text-field :model-value="factDialog.fields.icon" label="Icon" color="primary" variant="solo" flat
                        density="comfortable" :error-messages="factDialog.errors.icon" readonly class="vfield-outline">
                        <template #default>
                            <v-icon v-if="factDialog.fields.iconSvg" color="primary" size="20" class="mr-2">
                                <span v-html="factDialog.fields.iconSvg" class="inline-flex items-center" />
                            </v-icon>
                        </template>
                        <template #append-inner>
                            <IconPicker v-model="factDialog.fields.icon"
                                @selected="factDialog.fields.iconSvg = $event.svg" />
                        </template>
                    </v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="factDialog.fields.text" label="Fact" color="primary" variant="solo" flat
                        density="comfortable" clearable :error-messages="factDialog.errors.text" class="vfield-outline"
                        autocomplete="off" />
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>

<script setup>
import axios from "@/plugins/axios";
import { computed, onMounted, ref } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useEntryDialog } from "@/composables/useEntryDialog";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import FormActions from "@/components/forms/FormActions";
import Dialog from "@/components/forms/FormDialog";
import IconPicker from "@/components/forms/IconPicker";
import { SKILL_CATEGORIES } from "@/src/constants/constants";

const skillHeaders = [
    { title: "Category", key: "category", align: "start" },
    { title: "Skills", key: "skill", align: "start" },
    { title: "Icon", key: "icon", align: "center", sortable: false },
];
const randomFactHeaders = [
    { title: "Icon", key: "icon", align: "start" },
    { title: "Fact", key: "text", align: "start" },
];
const skillCategoryOptions = SKILL_CATEGORIES.map((category) => ({
    title: category.title,
    value: category.title,
}));
const skillEntrySchema = yup.object({
    id: yup.mixed().nullable(),
    category: yup.string().label("Category").required(),
    skill: yup.array().of(yup.string()).label("Skills").min(1, "At least one skill is required"),
    icon: yup.string().label("Icon").nullable().default(null),
    iconSvg: yup.string().nullable().default(null),
});
const factSchema = yup.object({
    id: yup.mixed().nullable(),
    icon: yup.string().label("Icon").required(),
    iconSvg: yup.string().nullable().default(null),
    text: yup.string().label("Fact").required(),
});
const schema = yup.object({
    profileImage: yup.mixed().label("Image").nullable(),
    heading: yup.string().label("Heading").required(),
    description: yup.string().label("Description").required(),
    skills: yup.array().of(skillEntrySchema).min(1, "At least one skill is required").label("Skills"),
    randomFacts: yup.array().of(factSchema).min(1, "At least one fact is required").label("Random Facts"),
    others: yup.object({
        title: yup.string().label("Title").required(),
        description: yup.string().label("Description").required(),
        image: yup.mixed().label("Image").nullable(),
    }).label("Others"),
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
        const othersImageFile =
            values.others?.image instanceof File || values.others?.image instanceof Blob
                ? values.others.image
                : null;

        if (profileImageFile) {
            formData.append("profile_image", profileImageFile);
        } else if (!values.profileImage) {
            formData.append("remove_profile_image", "1");
        }
        if (othersImageFile) {
            formData.append("others_image", othersImageFile);
        } else if (!values.others?.image) {
            formData.append("remove_others_image", "1");
        }

        formData.append(
            "payload",
            JSON.stringify({
                heading: values.heading,
                description: values.description,
                skills: values.skills,
                randomFacts: values.randomFacts,
                others: {
                    title: values.others?.title,
                    description: values.others?.description,
                },
            })
        );

        const response = await axios.post("/api/updateAboutContent", formData);
        await getAboutContent();
        return { message: response.data.message };
    },
    { resetOnSuccess: false }
);
const skillDialog = useEntryDialog(skillEntrySchema, () => fields.skills, {
    category: null,
    skill: [],
    icon: null,
    iconSvg: null,
});
const factDialog = useEntryDialog(factSchema, () => fields.randomFacts, {
    icon: null,
    iconSvg: null,
    text: "",
});
const availableSkills = computed(() => {
    return SKILL_CATEGORIES.find((c) => c.title === skillDialog.fields.category)?.skills ?? [];
});

async function getAboutContent() {
    try {
        const { data } = await axios.get("/api/getAboutContent");
        if (!data) return;
        resetForm({ values: { ...data } });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load about content.");
    } finally {
        pageLoading.value = false;
    }
}

onMounted(() => {
    getAboutContent();
});
</script>