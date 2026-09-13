<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="pa-3 mt-2 rounded-lg">
            <v-form @submit.prevent="submit" :disabled="loading">
                <v-row :gap="55">
                    <v-col cols="12">
                        <v-row :gap="10">
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
                                    <span class="text-title-medium font-weight-bold">Skills</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update what skills to showcase per category
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="fields.skills" :headers="skillHeaders" :addable="true"
                                    :expandable="false" add-label="New Skill" v-model:sort-by="skillsOptions.sortBy"
                                    v-model:page="skillsOptions.page"
                                    v-model:items-per-page="skillsOptions.itemsPerPage" :items-length="skillsTotal"
                                    :loading="skillsLoading" no-data-text="No skills added yet." :disabled="loading"
                                    @add="openSkillsDialog()" @edit="openSkillsDialog($event)"
                                    @remove="removeSkillsItem($event)">
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
                        <v-row :gap="10">
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
                                    :expandable="false" add-label="New Fact" v-model:sort-by="randomFacts.sortBy"
                                    v-model:page="randomFacts.page" v-model:items-per-page="randomFacts.itemsPerPage"
                                    :items-length="factsTotal" :loading="factsLoading"
                                    no-data-text="No facts added yet." :disabled="loading"
                                    @add="openRandomFactsDialog()" @edit="openRandomFactsDialog($event)"
                                    @remove="removeFactsItem($event)">
                                    <template #item.icon="{ item }">
                                        <v-icon color="primary">
                                            <span v-html="item.iconSvg" class="inline-flex items-center" />
                                        </v-icon>
                                    </template>
                                </DataTable>
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <v-row :gap="10">
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

    <SkillsDialog ref="dialogSkillsRef" :list="fields.skills" />
    <RandomFactsDialog ref="dialogRandomFactsRef" :list="fields.randomFacts" />

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
import SkillsDialog, { skillsSchema } from "./SkillsDialog";
import RandomFactsDialog, { randomFactsSchema } from "./RandomFactsDialog";

const skillHeaders = [
    { title: "Category", key: "category", align: "start" },
    { title: "Skills", key: "skill", align: "start", sortable: false },
    { title: "Icon", key: "icon", align: "center", sortable: false },
];
const randomFactHeaders = [
    { title: "Icon", key: "icon", align: "start", sortable: false },
    { title: "Fact", key: "randomFact", align: "start" },
];
const schema = yup.object({
    profileImage: yup.mixed().label("Image").nullable(),
    heading: yup.string().label("Heading").required(),
    description: yup.string().label("Description").required(),
    skills: yup.array().of(skillsSchema).min(1, "At least one skill is required").label("Skills").default([]),
    randomFacts: yup.array().of(randomFactsSchema).min(1, "At least one fact is required").label("Random Facts").default([]),
    others: yup.object({
        title: yup.string().label("Title").required(),
        description: yup.string().label("Description").required(),
        image: yup.mixed().label("Image").nullable(),
    }).label("Others"),
});
const { error } = useSnackbarQueue();
const pageLoading = ref(true);
const dialogSkillsRef = ref();
const dialogRandomFactsRef = ref();
const { fields, errors, loading, submit, cancelEdit, resetForm, resetField, meta, ready } = useValidatedForm(
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
const skillsOptions = reactive({ page: 1, itemsPerPage: 10, sortBy: [] });
const skillsTotal = ref(0);
const skillsLoading = ref(false);
const randomFacts = reactive({ page: 1, itemsPerPage: 10, sortBy: [] });
const factsTotal = ref(0);
const factsLoading = ref(false);

function openSkillsDialog(item = null) {
    dialogSkillsRef.value?.open(item);
};
function removeSkillsItem(item) {
    dialogSkillsRef.value?.remove(item);
};
function openRandomFactsDialog(item = null) {
    dialogRandomFactsRef.value?.open(item);
};
function removeFactsItem(item) {
    dialogRandomFactsRef.value?.remove(item);
};

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
};
async function fetchSkills() {
    skillsLoading.value = true;
    try {
        const [sort] = skillsOptions.sortBy ?? [];
        const { data } = await axios.get("/api/getAboutContent", {
            params: {
                skillsPage: skillsOptions.page,
                skillsPerPage: skillsOptions.itemsPerPage,
                skillsSortBy: sort?.key,
                skillsSortOrder: sort?.order,
            },
        });
        if (!data) return;
        resetField('skills', { value: data.skills ?? [] });
        skillsTotal.value = data.skillsMeta?.total ?? 0;
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load skills.");
    } finally {
        skillsLoading.value = false;
    }
};
async function fetchRandomFacts() {
    factsLoading.value = true;
    try {
        const [sort] = randomFacts.sortBy ?? [];
        const { data } = await axios.get("/api/getAboutContent", {
            params: {
                randomFactsPage: randomFacts.page,
                randomFactsPerPage: randomFacts.itemsPerPage,
                randomFactsSortBy: sort?.key,
                randomFactsSortOrder: sort?.order,
            },
        });
        if (!data) return;
        resetField('randomFacts', { value: data.randomFacts ?? [] });
        factsTotal.value = data.randomFactsMeta?.total ?? 0;
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load facts.");
    } finally {
        factsLoading.value = false;
    }
};

watch(() => [skillsOptions.page, skillsOptions.itemsPerPage, skillsOptions.sortBy], fetchSkills);
watch(() => [randomFacts.page, randomFacts.itemsPerPage, randomFacts.sortBy], fetchRandomFacts);

onMounted(() => {
    getAboutContent();
});
</script>