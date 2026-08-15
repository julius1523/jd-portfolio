<template>
    <Shimmer :loading="pageLoading">
        <v-card flat class="py-5 px-2 rounded-b-lg">
            <v-form @submit.prevent="submit" :disabled="loading">
                <v-row>
                    <v-col cols="12" lg="6">
                        <v-row>
                            <v-col cols="12">
                                <div class="d-flex flex-column ga-1">
                                    <div class="text-title-medium font-weight-bold">Text</div>
                                    <div class="text-title-small text-medium-emphasis">Update what users can see and
                                        read
                                        from your site
                                    </div>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="greeting" color="primary" variant="solo" flat label="Greeting"
                                    rounded="lg" density="comfortable" clearable :error-messages="errors.greeting"
                                    autocomplete="off" data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-combobox v-model="title" v-model:search="search" :hide-no-data="false"
                                    :items="jobTitles" variant="solo" flat label="Job Title" rounded="lg"
                                    density="comfortable" chips hide-selected hint="Maximum of 4 tags" persistent-hint
                                    multiple :list-props="{ rounded: 'xl' }" :error-messages="errors.title"
                                    autocomplete="off" data-shimmer-no-children>
                                    <template v-slot:no-data>
                                        <v-list-item>
                                            <v-list-item-subtitle>
                                                No results matching "<strong>{{ search }}</strong>". Press
                                                <kbd>enter</kbd>
                                                to create a new one
                                            </v-list-item-subtitle>
                                        </v-list-item>
                                    </template>
                                </v-combobox>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea v-model="description" color="primary" auto-grow variant="solo" flat
                                    label="Description" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.description" autocomplete="off" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12" lg="6">
                        <v-row>
                            <v-col cols="12">
                                <div class="d-flex flex-column ga-1">
                                    <div class="text-title-medium font-weight-bold">Buttons</div>
                                    <div class="text-title-small text-medium-emphasis">Update what and how buttons
                                        should
                                        look
                                    </div>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="primary_btn_text" color="primary" variant="solo" flat
                                    label="Primary Button Text" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.primary_btn_text" autocomplete="off"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="primary_btn_link" color="primary" variant="solo" flat
                                    label="Primary Button Link" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.primary_btn_link" autocomplete="off"
                                    data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12">
                                <v-text-field v-model="secondary_btn_text" color="primary" variant="solo" flat
                                    label="Secondary Button Text" rounded="lg" density="comfortable" clearable
                                    :error-messages="errors.secondary_btn_text" autocomplete="off"
                                    data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <v-row>
                            <v-col cols="12">
                                <div class="d-flex flex-column ga-1">
                                    <div class="text-title-medium font-weight-bold">Uploads</div>
                                    <div class="text-title-small text-medium-emphasis">Update the files to show on your
                                        site
                                    </div>
                                </div>
                            </v-col>
                            <v-col cols="12" lg="6">
                                <FileUpload v-model="cv" file-type="pdf" :max-files="1" inset :disabled="loading"
                                    :show-size="true" density="comfortable" hint="The CV file for download"
                                    :persistent-hint="true" :error-messages="errors.cv" data-shimmer-no-children />
                            </v-col>
                            <v-col cols="12" lg="6">
                                <FileUpload v-model="image" file-type="image" :max-files="1" inset :disabled="loading"
                                    :show-size="true" density="comfortable"
                                    hint="The image to display on your home page" :persistent-hint="true"
                                    :error-messages="errors.image" data-shimmer-no-children />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <div class="d-flex flex-column flex-md-row ga-3 justify-end mt-8">
                            <v-btn variant="flat" text="Cancel Edit" rounded="pill" color="grey" size="x-large"
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
</template>

<script setup>
import axios from "@/plugins/axios";
import { ref, onMounted, watch, nextTick } from "vue";
import * as yup from "yup";
import { useValidatedForm } from "@/composables/useValidatedForm";
import { useUnsavedChanges } from "@/composables/useUnsavedChanges";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
import FileUpload from "@/components/forms/FileUpload";
const { info, error } = useSnackBarQueue();
const search = ref(null);
const pageLoading = ref(true);
const jobTitles = ref([
    "Full Stack Developer",
    "Web Developer",
    "Laravel Developer",
    "VueJS Developer",
    "Frontend Developer",
    "Backend Developer",
    "Software Developer",
    "Application Developer",
    "System Developer",
    "PHP Developer",
    "UI/UX-Focused Developer",
    "JavaScript Developer",
    "Database Developer",
    "API Developer",
    "Information Systems Developer",
]);
const schema = yup.object({
    greeting: yup.string().label('Greeting').required(),
    title: yup.array().of(yup.string().required()).min(1, "At least one tag is required").max(4, "Maximum of 4 tags").label("Title"),
    description: yup.string().label('Description').required(),
    primary_btn_text: yup.string().label('Primary button text').required(),
    primary_btn_link: yup.string().label('Primary button link').required(),
    secondary_btn_text: yup.string().label('Secondary button text').required(),
    cv: yup.mixed().label('CV').nullable(),
    image: yup.mixed().label('Image').nullable(),
});
const { defineField, errors, loading, submit, resetForm, meta } = useValidatedForm(schema, async (values) => {
    const formData = new FormData();
    const fileFields = ['cv', 'image'];
    for (const [key, value] of Object.entries(values)) {
        if (fileFields.includes(key)) {
            if (value instanceof File || value instanceof Blob) {
                formData.append(key, value);
            } else if (!value || (Array.isArray(value) && value.length === 0)) {
                formData.append(`remove_${key}`, '1');
            }
            continue;
        }
        if (value === null || value === undefined) continue;
        if (Array.isArray(value)) {
            value.forEach((item) => formData.append(`${key}[]`, item));
        } else {
            formData.append(key, value);
        }
    }
    const response = await axios.post('/api/updateHomeContent', formData);
    await getHomeContent();
    return { message: response.data.message };
},
    { resetOnSuccess: false }
);
useUnsavedChanges(meta);
async function getHomeContent() {
    try {
        const { data } = await axios.get('/api/getHomeContent');
        if (!data) return;
        resetForm({
            values: {
                greeting: data.greeting,
                title: data.title ?? [],
                description: data.description,
                primary_btn_text: data.primary_btn_text,
                primary_btn_link: data.primary_btn_link,
                secondary_btn_text: data.secondary_btn_text,
                cv: data.cv_url,
                image: data.image_url,
            },
        });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load home content.");
    } finally {
        await nextTick();
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                pageLoading.value = false;
            });
        });
    }
};
const [greeting] = defineField('greeting');
const [title] = defineField('title');
const [description] = defineField('description');
const [primary_btn_text] = defineField('primary_btn_text');
const [primary_btn_link] = defineField('primary_btn_link');
const [secondary_btn_text] = defineField('secondary_btn_text');
const [cv] = defineField('cv');
const [image] = defineField('image');
const cancelEdit = () => {
    resetForm();
    info("No changes made.");
};
watch(title, async (val) => {
    if (!Array.isArray(val)) return;
    if (val.length > 4) {
        await nextTick();
        title.value = val.slice(0, 4);
    }
}, { deep: true });
onMounted(() => {
    getHomeContent();
});
</script>