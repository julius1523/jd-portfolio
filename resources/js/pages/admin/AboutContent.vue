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
                    <v-col cols="12" lg="6">
                        <v-row :gap="13">
                            <v-col cols="12">
                                <div>
                                    <span class="text-title-medium font-weight-bold">Skills</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update what skills to showcase per category
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12" v-for="category in SKILL_CATEGORIES" :key="category.key">
                                <Select :key="`file-${formResetKey}`" v-model="skillFields[category.key].value"
                                    :items="category.skills" :label="category.title"
                                    :error-messages="errors[`skills.${category.key}`]" data-shimmer-no-children />
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
                                    @add="openFactDialog" @edit="openFactDialog" @remove="removeFact">
                                    <template #item.icon="{ item }">
                                        <v-icon color="primary">
                                            <span v-html="item.icon?.svg ?? ''" class="icon-lg" />
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
                                <FileUpload :key="`file-${formResetKey}`" v-model="othersImage" file-type="image"
                                    :max-files="1" inset :disabled="loading" :show-size="true" density="comfortable"
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

    <Dialog v-model="factDialog" :is-editing="editingFactIndex > -1" add-title="Add Fact" edit-title="Edit Fact"
        save-text="Add Fact" edit-save-text="Save Changes" cancel-text="Cancel" edit-cancel-text="Cancel Edit"
        @save="addFact" @cancel="closeFactDialog">
        <v-form @submit.prevent="addFact">
            <v-row :gap="13">
                <v-col cols="12">
                    <v-text-field :model-value="fIcon?.name" label="Icon" color="primary" variant="solo" flat
                        rounded="lg" density="comfortable" :error-messages="factErrors.icon" readonly>
                        <template #default>
                            <v-icon color="primary">
                                <span v-html="fIcon?.svg" class="icon-sm" />
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
import { ref, onMounted } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useUnsavedChanges } from "@/composables/useUnsavedChanges";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import Dialog from "@/components/forms/FormDialog";
import IconPicker from "@/components/forms/IconPicker";
import { SKILL_CATEGORIES } from "@/src/constants/constants";
const { info, error } = useSnackBarQueue();
const pageLoading = ref(true);
const iconSchema = yup.object({
    name: yup.string().required(),
    svg: yup.string().required(),
});
const schema = yup.object({
    profileImage: yup.mixed().label('Image').nullable(),
    heading: yup.string().label('Heading').required(),
    description: yup.string().label('Description').required(),
    skills: yup.object(
        Object.fromEntries(
            SKILL_CATEGORIES.map((category) => [
                category.key,
                yup
                    .array()
                    .of(yup.string().required())
                    .min(1, `At least one ${category.title.toLowerCase()} is required.`)
                    .label(category.title),
            ])
        )
    ).label('Skills'),
    randomFacts: yup
        .array()
        .of(
            yup.object({
                icon: iconSchema.label('Icon').required(),
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
const skillFields = Object.fromEntries(
    SKILL_CATEGORIES.map((category) => {
        const [field] = defineField(`skills.${category.key}`);
        return [category.key, field];
    })
);
const formResetKey = ref(0);
const cancelEdit = () => {
    resetForm();
    formResetKey.value++;
    info("No changes made.");
};
const randomFactHeaders = [
    { title: 'Icon', key: 'icon', align: 'start' },
    { title: 'Fact', key: 'text', align: 'start' },
];
const [randomFacts] = defineField('randomFacts');
const factDialog = ref(false);
const editingFactIndex = ref(-1);
const factFormResetKey = ref(0);
const factSchema = yup.object({
    icon: iconSchema.label('Icon').required(),
    text: yup.string().label('Fact').required(),
});
const {
    defineField: defineFactField,
    errors: factErrors,
    submit: submitFactForm,
    resetForm: resetFactForm,
} = useValidatedForm(factSchema, async (values) => {
    const fact = {
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
function openFactDialog(item = null, index = -1) {
    editingFactIndex.value = index;
    factFormResetKey.value++;
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
function removeFact(index) {
    randomFacts.value.splice(index, 1);
};
async function getAboutContent() {
    try {
        const { data } = await axios.get('/api/getAboutContent');
        if (!data) return;
        resetForm({
            values: {
                ...data,
                skills: Object.fromEntries(
                    SKILL_CATEGORIES.map((category) => [category.key, data.skills?.[category.key] ?? []])
                ),
            },
        });
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