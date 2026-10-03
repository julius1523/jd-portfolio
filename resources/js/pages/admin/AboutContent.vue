<template>
    <Shimmer :loading="pageLoading">
        <v-form @submit.prevent="submit" :disabled="loading">
            <div class="d-flex flex-column ga-16 mt-8">
                <div>
                    <div class="d-inline-flex flex-column ga-1 mb-4">
                        <div class="text-title-medium font-semibold">Profile Image</div>
                        <div class="text-title-small opacity-60">
                            Update your profile image to display to your about page
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
                                        :error-messages="errors.description" autocomplete="off" class="vfield-outline"
                                        hide-details="auto" data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>
                    </div>
                </div>

                <div>
                    <div class="d-inline-flex flex-column ga-1 mb-4">
                        <div class="text-title-medium font-semibold">Skills</div>
                        <div class="text-title-small opacity-60">
                            Update what skills to showcase per category
                        </div>
                    </div>
                    <DataTable :items="fields.skills" :headers="skillHeaders" :addable="true" :expandable="false"
                        add-label="New Skill" v-model:sort-by="skillsOptions.sortBy" v-model:page="skillsOptions.page"
                        v-model:items-per-page="skillsOptions.itemsPerPage" :items-length="skillsTotal"
                        :loading="skillsLoading" no-data-text="No skills added yet." :disabled="loading"
                        density="compact" @add="openSkillsDialog()" @edit="openSkillsDialog($event)"
                        @remove="removeSkillsItem($event)">
                        <template #item.category="{ item }">
                            {{ item.category }}
                        </template>
                        <template #item.skill="{ item }">
                            <div class="d-flex flex-wrap ga-1 py-2"
                                :class="{ 'justify-end': $vuetify.display.smAndDown }">
                                <v-chip v-for="skillName in item.skill" :key="skillName" size="small" color="primary"
                                    variant="tonal">
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
                </div>

                <div>
                    <div class="d-inline-flex flex-column ga-1 mb-4">
                        <div class="text-title-medium font-semibold">Random Facts</div>
                        <div class="text-title-small opacity-60">
                            Update what the users must know about you
                        </div>
                    </div>
                    <DataTable :items="fields.randomFacts" :headers="randomFactHeaders" :addable="true"
                        :expandable="false" add-label="New Fact" v-model:sort-by="randomFacts.sortBy"
                        v-model:page="randomFacts.page" v-model:items-per-page="randomFacts.itemsPerPage"
                        :items-length="factsTotal" :loading="factsLoading" no-data-text="No facts added yet."
                        :disabled="loading" density="compact" @add="openRandomFactsDialog()"
                        @edit="openRandomFactsDialog($event)" @remove="removeFactsItem($event)">
                        <template #item.icon="{ item }">
                            <v-icon color="primary">
                                <span v-html="item.iconSvg" class="inline-flex items-center" />
                            </v-icon>
                        </template>
                    </DataTable>
                </div>

                <div>
                    <div class="d-inline-flex flex-column ga-1 mb-4">
                        <div class="text-title-medium font-semibold">
                            Others
                        </div>
                        <div class="text-title-small opacity-60">
                            Other details to showcase to viewers
                        </div>
                    </div>

                    <div class="d-flex flex-column ga-4">
                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Heading
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <v-text-field v-model="fields.others.title" color="primary" variant="solo" flat
                                        single-line label="Title" density="compact" clearable
                                        :error-messages="errors['others.title']" autocomplete="off"
                                        class="vfield-outline" hide-details="auto" data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>

                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Description
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <v-textarea v-model="fields.others.description" rows="3" color="primary" auto-grow
                                        variant="solo" flat single-line label="Description" density="compact"
                                        :error-messages="errors['others.description']" autocomplete="off"
                                        class="vfield-outline" hide-details="auto" data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>

                        <v-card class="p-5 shadow-sm border rounded-[12px]">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="sm:shrink-0 text-label-large font-medium">
                                    Secondary Button File
                                </div>
                                <div class="w-full sm:flex-1 sm:max-w-md sm:ml-auto min-w-0">
                                    <FileUpload v-model="fields.others.image" file-type="image" :max-files="1" inset
                                        :disabled="loading" :show-size="true" density="comfortable"
                                        :error-messages="errors['others.image']" hide-details="auto"
                                        data-shimmer-no-children />
                                </div>
                            </div>
                        </v-card>
                    </div>
                </div>
            </div>

            <FormActions :dirty="meta.dirty" :ready="ready" :loading="loading" @cancel="cancelEdit" />
        </v-form>
    </Shimmer>

    <SkillsDialog ref="dialogSkillsRef" :list="fields.skills" />
    <RandomFactsDialog ref="dialogRandomFactsRef" :list="fields.randomFacts" />
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, reactive, onMounted, watch } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import DataTable from "@/components/data/DataTable";
import FileUpload from "@/components/forms/FileUpload";
import AvatarUpload from "@/components/forms/AvatarUpload";
import FormActions from "@/components/forms/FormActions";
import SkillsDialog, { skillsSchema } from "./dialogs/SkillsDialog";
import RandomFactsDialog, { randomFactsSchema } from "./dialogs/RandomFactsDialog";

const skillHeaders = [
    { title: "Category", key: "category", align: "start" },
    { title: "Skills", key: "skill", align: "start", sortable: false },
    { title: "Icon", key: "icon", align: "center", width: '10%', sortable: false },
];
const randomFactHeaders = [
    { title: "Fact", key: "randomFact", align: "start" },
    { title: "Icon", key: "icon", align: "center", width: '10%', sortable: false },
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
const { error } = useSnackBarQueue();
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