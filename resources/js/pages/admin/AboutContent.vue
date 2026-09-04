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
                                    <span class="text-title-medium font-weight-bold">Skills</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update what skills to showcase per category
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="skills" :headers="skillHeaders" :addable="true" :expandable="false"
                                    add-label="New Skill" no-data-text="No skills added yet." :disabled="loading"
                                    @add="openSkillDialog" @edit="openSkillDialog" @remove="removeSkill">
                                    <template #item.category="{ item }">
                                        {{ categoryTitle(item.category) }}
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
                                        <v-icon v-if="item.icon" color="primary" size="x-large">
                                            <span v-html="getIconSvg(item.icon)" />
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
                                <DataTable :items="randomFacts" :headers="randomFactHeaders" :addable="true"
                                    :expandable="false" add-label="New Fact" no-data-text="No facts added yet."
                                    :disabled="loading" @add="openFactDialog" @edit="openFactDialog"
                                    @remove="removeFact">
                                    <template #item.icon="{ item }">
                                        <v-icon color="primary" size="x-large">
                                            <span v-html="getIconSvg(item.icon)" />
                                        </v-icon>
                                    </template>
                                </DataTable>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" lg="6">
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
                                <v-text-field v-model="othersTitle" color="primary" variant="solo" flat label="Title"
                                    rounded="lg" density="comfortable" clearable
                                    :error-messages="errors['others.title']" autocomplete="off"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="othersDescription" color="primary" auto-grow variant="solo" flat
                                    label="Description" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors['others.description']" autocomplete="off"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <FileUpload v-model="othersImage" file-type="image" :max-files="1" inset
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
        @save="addSkill" @cancel="closeSkillDialog">
        <v-form @submit.prevent="addSkill">
            <v-row :gap="13">
                <v-col cols="12">
                    <Select v-model="sCategory" :items="skillCategoryOptions" label="Skill Category" :multiple="false"
                        :chip="false" :error-messages="skillErrors.category" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="sSkill" :items="availableSkills" label="Skills" :multiple="true" :chip="true"
                        :error-messages="skillErrors.skill" />
                </v-col>
                <v-col cols="12">
                    <v-text-field :model-value="sIcon" label="Icon (optional)" color="primary" variant="solo" flat
                        rounded="lg" density="comfortable" :error-messages="skillErrors.icon" readonly clearable
                        @click:clear="sIcon = null">
                        <template #default>
                            <v-icon v-if="sIcon" color="primary" class="mr-2">
                                <span v-html="getIconSvg(sIcon)" />
                            </v-icon>
                        </template>
                        <template #append-inner>
                            <IconPicker v-model="sIcon" />
                        </template>
                    </v-text-field>
                </v-col>
            </v-row>
        </v-form>
    </Dialog>

    <Dialog v-model="factDialog" :is-editing="editingFactIndex > -1" add-title="Add Fact" edit-title="Edit Fact"
        save-text="Add Fact" edit-save-text="Save Changes" cancel-text="Cancel" edit-cancel-text="Cancel Edit"
        @save="addFact" @cancel="closeFactDialog">
        <v-form @submit.prevent="addFact">
            <v-row :gap="13">
                <v-col cols="12">
                    <v-text-field :model-value="fIcon" label="Icon" color="primary" variant="solo" flat rounded="lg"
                        density="comfortable" :error-messages="factErrors.icon" readonly>
                        <template #default>
                            <v-icon v-if="fIcon" color="primary" class="mr-2">
                                <span v-html="getIconSvg(fIcon)" />
                            </v-icon>
                        </template>
                        <template #append-inner>
                            <IconPicker v-model="fIcon" />
                        </template>
                    </v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="fText" label="Fact" color="primary" variant="solo" flat rounded="lg"
                        density="comfortable" clearable :error-messages="factErrors.text" autocomplete="off" />
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
import { useUnsavedChanges } from "@/composables/useUnsavedChanges";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import Dialog from "@/components/forms/FormDialog";
import IconPicker from "@/components/forms/IconPicker";
import { getIconSvg } from "@/src/utils/icon";
import { SKILL_CATEGORIES } from "@/src/constants/constants";
const { info, error } = useSnackBarQueue();
const pageLoading = ref(true);
const skillEntrySchema = yup.object({
    category: yup.string().label('Category').required(),
    skill: yup
        .array()
        .of(yup.string())
        .label('Skills')
        .min(1, 'At least one skill is required'),
    icon: yup.string().label('Icon').nullable().default(null),
});
const schema = yup.object({
    profileImage: yup.mixed().label('Image').nullable(),
    heading: yup.string().label('Heading').required(),
    description: yup.string().label('Description').required(),
    skills: yup
        .array()
        .of(skillEntrySchema)
        .min(1, "At least one skill is required")
        .label("Skills"),
    randomFacts: yup
        .array()
        .of(
            yup.object({
                icon: yup.string().label('Icon').required(),
                text: yup.string().label('Fact').required(),
            })
        )
        .min(1, "At least one fact is required")
        .label("Random Facts"),
    others: yup.object({
        title: yup.string().label('Title').required(),
        description: yup.string().label('Description').required(),
        image: yup.mixed().label('Image').nullable(),
    }).label('Others'),
});
const { defineField, errors, loading, submit, resetForm, meta } = useValidatedForm(schema, async (values) => {
    const formData = new FormData();
    const profileImageFile = (values.profileImage instanceof File || values.profileImage instanceof Blob) ? values.profileImage : null;
    const othersImageFile = (values.others?.image instanceof File || values.others?.image instanceof Blob) ? values.others.image : null;
    if (profileImageFile) {
        formData.append('profile_image', profileImageFile);
    } else if (!values.profileImage) {
        formData.append('remove_profile_image', '1');
    }
    if (othersImageFile) {
        formData.append('others_image', othersImageFile);
    } else if (!values.others?.image) {
        formData.append('remove_others_image', '1');
    }
    const payload = {
        heading: values.heading,
        description: values.description,
        skills: values.skills,
        randomFacts: values.randomFacts,
        others: {
            title: values.others?.title,
            description: values.others?.description,
        },
    };
    formData.append('payload', JSON.stringify(payload));
    const response = await axios.post('/api/updateAboutContent', formData);
    await getAboutContent();
    return { message: response.data.message };
},
    { resetOnSuccess: false }
);
useUnsavedChanges(meta);
const [profileImage] = defineField('profileImage');
const [heading] = defineField('heading');
const [description] = defineField('description');
const [othersTitle] = defineField('others.title');
const [othersDescription] = defineField('others.description');
const [othersImage] = defineField('others.image');
const cancelEdit = () => {
    resetForm();
    info("No changes made.");
};
const skillCategoryOptions = SKILL_CATEGORIES.map((category) => ({
    title: category.title,
    value: category.title,
}));
function categoryTitle(title) {
    return SKILL_CATEGORIES.find((c) => c.title === title)?.title ?? title;
};
const skillHeaders = [
    { title: 'Category', key: 'category', align: 'start' },
    { title: 'Skills', key: 'skill', align: 'start' },
    { title: 'Icon', key: 'icon', align: 'center', sortable: false },
];
const [skills] = defineField('skills');
const skillDialog = ref(false);
const editingSkillIndex = ref(-1);
const {
    defineField: defineSkillField,
    errors: skillErrors,
    submit: submitSkillForm,
    resetForm: resetSkillForm,
} = useValidatedForm(skillEntrySchema, async (values) => {
    const skill = {
        id: values.id,
        category: values.category,
        skill: values.skill,
        icon: values.icon ?? null,
    };
    if (editingSkillIndex.value > -1) {
        skills.value.splice(editingSkillIndex.value, 1, skill);
    } else {
        skills.value.push(skill);
    }
    closeSkillDialog();
}, { resetOnSuccess: false });
const [sCategory] = defineSkillField('category');
const [sSkill] = defineSkillField('skill');
const [sIcon] = defineSkillField('icon');
const availableSkills = computed(() => {
    return SKILL_CATEGORIES.find((c) => c.title === sCategory.value)?.skills ?? [];
});
function addSkill() {
    submitSkillForm();
};
function openSkillDialog(item = null) {
    editingSkillIndex.value = item ? skills.value.findIndex(s => s.id === item.id) : -1;
    resetSkillForm({
        values: item ? { ...item } : {
            category: null,
            skill: [],
            icon: null,
        },
    });
    skillDialog.value = true;
};
function closeSkillDialog() {
    skillDialog.value = false;
};
function removeSkill(item) {
    const idx = skills.value.findIndex((s) => s.id === item.id);
    if (idx > -1) skills.value.splice(idx, 1);
};
const randomFactHeaders = [
    { title: 'Icon', key: 'icon', align: 'start' },
    { title: 'Fact', key: 'text', align: 'start' },
];
const [randomFacts] = defineField('randomFacts');
const factDialog = ref(false);
const editingFactIndex = ref(-1);
const factSchema = yup.object({
    icon: yup.string().label('Icon').required(),
    text: yup.string().label('Fact').required(),
});
const {
    defineField: defineFactField,
    errors: factErrors,
    submit: submitFactForm,
    resetForm: resetFactForm,
} = useValidatedForm(factSchema, async (values) => {
    const fact = {
        id: values.id,
        icon: values.icon,
        text: values.text,
    };
    if (editingFactIndex.value > -1) {
        randomFacts.value.splice(editingFactIndex.value, 1, fact);
    } else {
        randomFacts.value.push(fact);
    }
    closeFactDialog();
}, { resetOnSuccess: false });
const [fIcon] = defineFactField('icon');
const [fText] = defineFactField('text');
function addFact() {
    submitFactForm();
};
function openFactDialog(item = null) {
    editingFactIndex.value = item ? randomFacts.value.findIndex(r => r.id === item.id) : -1;
    resetFactForm({
        values: item ? { ...item } : {
            icon: null,
            text: '',
        },
    });
    factDialog.value = true;
};
function closeFactDialog() {
    factDialog.value = false;
};
function removeFact(item) {
    const idx = randomFacts.value.findIndex(r => r.id === item.id);
    if (idx > -1) randomFacts.value.splice(idx, 1);
};
async function getAboutContent() {
    try {
        const { data } = await axios.get('/api/getAboutContent');
        if (!data) return;
        resetForm({ values: { ...data } });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load home content.");
    } finally {
        pageLoading.value = false;
    }
};
onMounted(() => {
    getAboutContent();
});
</script>