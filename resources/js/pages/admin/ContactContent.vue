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
                                    <span class="text-title-medium font-weight-bold">Socials</span><br />
                                    <span class="text-title-small text-medium-emphasis">
                                        Update the socials to showcase
                                    </span>
                                </div>
                            </v-col>
                            <v-col cols="12">
                                <DataTable :items="socials" :headers="socialHeaders" :addable="true" :expandable="false"
                                    add-label="New Social" no-data-text="No socials added yet." @add="openSocialDialog"
                                    @edit="openSocialDialog" @remove="removeSocial">
                                    <template #item.name="{ item }">
                                        <div class="d-flex ga-2" :class="{ 'justify-end': $vuetify.display.smAndDown }">
                                            <v-icon :class="item.icon" color="primary" />
                                            <span>{{ item.name }}</span>
                                        </div>
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

    <Dialog v-model="socialDialog" :is-editing="editingIndex > -1" add-title="Add Social" edit-title="Edit Social"
        save-text="Add Social" edit-save-text="Save Changes" cancel-text="Cancel" edit-cancel-text="Cancel Edit"
        @save="addSocial" @cancel="closeSocialDialog">
        <v-form @submit.prevent="addSocial">
            <v-row :gap="13">
                <v-col cols="12">
                    <v-text-field v-model="sName" label="Social Name" color="primary" variant="solo" flat rounded="lg"
                        density="comfortable" clearable :error-messages="socialErrors.name" autocomplete="off" />
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="sLinkUrl" label="Link URL" color="primary" variant="solo" flat rounded="lg"
                        density="comfortable" clearable placeholder="https://..." :error-messages="socialErrors.linkUrl"
                        autocomplete="off" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="sIcon" label="Icon" color="primary" variant="solo" flat rounded="lg"
                        density="comfortable" :items="SOCIAL_ICONS" item-title="name" item-value="value"
                        :multiple="false" :chip="false" :error-messages="socialErrors.icon">
                        <template #item="{ item, props: itemProps }">
                            <v-list-item v-bind="itemProps" :prepend-icon="resolveIcon(item)?.value" color="primary"
                                :title="undefined">
                                <template #title>
                                    <span class="text-label-medium">{{ resolveIcon(item)?.name }}</span>
                                </template>
                            </v-list-item>
                        </template>

                        <template #selection="{ item }">
                            <template v-if="resolveIcon(item)">
                                <v-icon :icon="resolveIcon(item).value" color="primary" size="small" class="mr-2" />
                                {{ resolveIcon(item).name }}
                            </template>
                        </template>
                    </Select>
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
import Dialog from "@/components/forms/Dialog";
import { SOCIAL_ICONS } from "@/src/constants/constants";
const { info, error } = useSnackBarQueue();
const pageLoading = ref(true);
const schema = yup.object({
    profileImage: yup.mixed().label('Profile Image').nullable(),
    heading: yup.string().label('Heading').required(),
    description: yup.string().label('Description').required(),
    socials: yup.array().label('Socials').default([]),
});
const { defineField, errors, loading, submit, resetForm, meta } = useValidatedForm(schema, async (values) => {
    const formData = new FormData();
    const profileImageFile = (values.profileImage instanceof File || values.profileImage instanceof Blob) ? values.profileImage : null;
    if (profileImageFile) {
        formData.append('profileImage', profileImageFile);
    } else if (!values.profileImage) {
        formData.append('remove_profileImage', '1');
    };
    const payload = {
        heading: values.heading,
        description: values.description,
        socials: values.socials,
    };
    formData.append('payload', JSON.stringify(payload));
    const response = await axios.post('/api/updateContactContent', formData);
    await getContactContent();
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
async function getContactContent() {
    try {
        const { data } = await axios.get('/api/getContactContent');
        if (!data) return;
        resetForm({ values: data });
    } catch (err) {
        error(err?.response?.data?.message ?? "Failed to load home content.");
    } finally {
        pageLoading.value = false;
    };
};
const socialHeaders = [
    { title: 'Social Name', key: 'name', align: 'start' },
    { title: 'Link URL', key: 'linkUrl', align: 'start' },
];
const [socials] = defineField('socials');
const socialDialog = ref(false);
const editingIndex = ref(-1);
const socialFormResetKey = ref(0);
const socialSchema = yup.object({
    name: yup.string().label('Social Name').required(),
    linkUrl: yup.string().label('Link URL').required(),
    icon: yup.string().label('Icon').required(),
});
const {
    defineField: defineSocialField,
    errors: socialErrors,
    submit: submitSocialForm,
    resetForm: resetSocialForm,
} = useValidatedForm(socialSchema, async (values) => {
    const social = {
        name: values.name,
        linkUrl: values.linkUrl,
        icon: values.icon,
    };
    if (editingIndex.value > -1) {
        socials.value.splice(editingIndex.value, 1, social);
    } else {
        socials.value.push(social);
    }
    closeSocialDialog();
}, { resetOnSuccess: false });
const [sName] = defineSocialField('name');
const [sLinkUrl] = defineSocialField('linkUrl');
const [sIcon] = defineSocialField('icon');
function addSocial() {
    submitSocialForm();
};
function openSocialDialog(item = null, index = -1) {
    editingIndex.value = index;
    socialFormResetKey.value++;
    resetSocialForm({
        values: item ? { ...item } : {
            name: '',
            linkUrl: '',
            icon: null,
        },
    });
    socialDialog.value = true;
};
function closeSocialDialog() {
    socialDialog.value = false;
};
function removeSocial(index) {
    socials.value.splice(index, 1);
};
function resolveIcon(item) {
    const raw = item?.raw ?? item;
    if (!raw || typeof raw !== 'object') return null;
    return raw;
};
onMounted(() => {
    getContactContent();
});
</script>