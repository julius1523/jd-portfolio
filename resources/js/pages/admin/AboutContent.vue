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
                                <FileUpload v-model="fields.profileImage" file-type="image" :max-files="1" inset
                                    :disabled="loading" :show-size="true" density="comfortable"
                                    hint="The image to display on your home page" :persistent-hint="true"
                                    :error-messages="errors.profileImage" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
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
                        <v-row :gap="13">
                            <v-col cols="12">
                                <div>
                                    <span class="text-title-medium font-weight-bold">Skills</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update what skills to showcase per category
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="fields.skills" :headers="skillHeaders" :addable="true"
                                    :expandable="false" add-label="New Skill" no-data-text="No skills added yet."
                                    :disabled="loading" @add="openSkillDialog" @edit="openSkillDialog"
                                    @remove="removeSkill">
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
                        <v-row :gap="13">
                            <v-col cols="12">
                                <div>
                                    <span class="text-title-medium font-weight-bold">Random Facts</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update the random facts to showcase
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="fields.randomFacts" :headers="randomFactHeaders" :addable="true"
                                    :expandable="false" add-label="New Fact" no-data-text="No facts added yet."
                                    :disabled="loading" @add="openFactDialog" @edit="openFactDialog"
                                    @remove="removeFact">
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
                        <v-row :gap="13">
                            <v-col cols="12">
                                <div>
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
                                    hint="The image to display to others section" :persistent-hint="true"
                                    :error-messages="errors['others.image']" data-shimmer-no-children />
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

    <Dialog v-model="skillDialog" :is-editing="editingSkillIndex > -1" add-title="Add Skill" edit-title="Edit Skill"
        save-text="Add Skill" edit-save-text="Save Changes" cancel-text="Cancel" edit-cancel-text="Cancel Edit"
        @save="addSkill" @cancel="skillDialog = false">
        <v-form @submit.prevent="addSkill">
            <v-row no-gutters>
                <v-col cols="12">
                    <Select v-model="skillFields.category" :items="skillCategoryOptions" label="Skill Category"
                        variant="solo" flat :multiple="false" :chip="false" :error-messages="skillErrors.category"
                        class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="skillFields.skill" :items="availableSkills" label="Skills" variant="solo"
                        :multiple="true" :chip="true" :error-messages="skillErrors.skill" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <v-text-field :model-value="skillFields.icon" label="Icon (optional)" color="primary" variant="solo"
                        flat density="comfortable" :error-messages="skillErrors.icon" readonly class="vfield-outline">
                        <template #default>
                            <v-icon v-if="skillFields.iconSvg" color="primary" size="20" class="mr-2">
                                <span v-html="skillFields.iconSvg" class="inline-flex items-center" />
                            </v-icon>
                        </template>
                        <template #append-inner>
                            <IconPicker v-model="skillFields.icon" @selected="skillFields.iconSvg = $event.svg" />
                        </template>
                    </v-text-field>
                </v-col>
            </v-row>
        </v-form>
    </Dialog>

    <Dialog v-model="factDialog" :is-editing="editingFactIndex > -1" add-title="Add Fact" edit-title="Edit Fact"
        save-text="Add Fact" edit-save-text="Save Changes" cancel-text="Cancel" edit-cancel-text="Cancel Edit"
        @save="addFact" @cancel="factDialog = false">
        <v-form @submit.prevent="addFact">
            <v-row no-gutters>
                <v-col cols="12">
                    <v-text-field :model-value="factFields.icon" label="Icon" color="primary" variant="solo" flat
                        density="comfortable" :error-messages="factErrors.icon" readonly class="vfield-outline">
                        <template #default>
                            <v-icon v-if="factFields.iconSvg" color="primary" size="20" class="mr-2">
                                <span v-html="factFields.iconSvg" class="inline-flex items-center" />
                            </v-icon>
                        </template>
                        <template #append-inner>
                            <IconPicker v-model="factFields.icon" @selected="factFields.iconSvg = $event.svg" />
                        </template>
                    </v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="factFields.text" label="Fact" color="primary" variant="solo" flat
                        density="comfortable" clearable :error-messages="factErrors.text" class="vfield-outline"
                        autocomplete="off" />
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, computed, onMounted } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import Dialog from "@/components/forms/FormDialog";
import IconPicker from "@/components/forms/IconPicker";
import { SKILL_CATEGORIES } from "@/src/constants/constants";

const { info, error } = useSnackBarQueue();
const pageLoading = ref(true);

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

const { fields, errors, loading, submit, resetForm, meta } = useValidatedForm(schema, async (values) => {
    const formData = new FormData();
    const profileImageFile = (values.profileImage instanceof File || values.profileImage instanceof Blob)
        ? values.profileImage
        : null;
    const othersImageFile = (values.others?.image instanceof File || values.others?.image instanceof Blob)
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

    formData.append("payload", JSON.stringify({
        heading: values.heading,
        description: values.description,
        skills: values.skills,
        randomFacts: values.randomFacts,
        others: {
            title: values.others?.title,
            description: values.others?.description,
        },
    }));

    const response = await axios.post("/api/updateAboutContent", formData);
    await getAboutContent();
    return { message: response.data.message };
}, { resetOnSuccess: false });

function cancelEdit() {
    resetForm();
    info("No changes made.");
}

function useEntryDialog(entrySchema, getList, emptyValues) {
    const dialog = ref(false);
    const editingIndex = ref(-1);

    const form = useValidatedForm(entrySchema, async (values) => {
        const list = getList();
        const row = { ...values, id: values.id ?? crypto.randomUUID() };
        if (editingIndex.value > -1) list.splice(editingIndex.value, 1, row);
        else list.push(row);
        dialog.value = false;
    }, { resetOnSuccess: false });

    function open(item = null) {
        const list = getList();
        editingIndex.value = item ? list.findIndex((row) => row.id === item.id) : -1;
        form.resetForm({ values: item ? { ...item } : { ...emptyValues } });
        dialog.value = true;
    }

    function remove(item) {
        const list = getList();
        const idx = list.findIndex((row) => row.id === item.id);
        if (idx > -1) list.splice(idx, 1);
    }

    return {
        dialog,
        editingIndex,
        fields: form.fields,
        errors: form.errors,
        submit: form.submit,
        open,
        remove,
    };
}

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

const {
    dialog: skillDialog,
    editingIndex: editingSkillIndex,
    fields: skillFields,
    errors: skillErrors,
    submit: addSkill,
    open: openSkillDialog,
    remove: removeSkill,
} = useEntryDialog(skillEntrySchema, () => fields.skills, {
    category: null,
    skill: [],
    icon: null,
    iconSvg: null,
});

const {
    dialog: factDialog,
    editingIndex: editingFactIndex,
    fields: factFields,
    errors: factErrors,
    submit: addFact,
    open: openFactDialog,
    remove: removeFact,
} = useEntryDialog(factSchema, () => fields.randomFacts, {
    icon: null,
    iconSvg: null,
    text: "",
});

const availableSkills = computed(() => {
    return SKILL_CATEGORIES.find((c) => c.title === skillFields.category)?.skills ?? [];
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