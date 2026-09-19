<script setup>
import { ref, computed, reactive } from "vue";
import * as yup from "yup";
import { useForm } from "vee-validate";
import Dialog from "@/components/forms/FormDialog";
import Select from "@/components/forms/Select";
import FileUpload from "@/components/forms/FileUpload";
import { PROJECT_MATERIALS } from "@/src/constants/constants";

const props = defineProps({ list: { type: Array, required: true } });
const categoryOptions = ["Software Development", "Technical Documentation", "Presentations/Multimedia"];
const dialog = ref(false);
const editingIndex = ref(-1);
const isEditing = computed(() => editingIndex.value > -1);
const materialItems = computed(() =>
    PROJECT_MATERIALS.flatMap((category, index) => [
        ...(index > 0 ? [{ type: "divider" }] : []),
        { type: "subheader", text: category.title },
        ...category.skills.map((skill) => ({ text: skill })),
    ])
);
const schema = yup.object({
    id: yup.mixed().nullable(),
    category: yup.string().label("Category").oneOf(categoryOptions, "Select a valid category").required(),
    name: yup.string().label("Project name").required(),
    description: yup.string().label("Description").required(),
    materials: yup.array().label("Materials").min(1, "At least one material is required"),
    image: yup.mixed().label("Image").required("Image is required"),
    linkType: yup.string().oneOf(["upload", "link"]).default("upload").required(),
    linkFile: yup.mixed().nullable().when("linkType", { is: "upload", then: (s) => s.required("File is required") }),
    linkUrl: yup.string().nullable().when("linkType", { is: "link", then: (s) => s.url("Must be a valid URL").required("Link URL is required") }),
});
const initialValues = schema.getDefault();
const { defineField, errors, handleSubmit, resetForm, meta, validate } = useForm({
    validationSchema: schema,
    initialValues,
});
const fields = reactive(
    Object.fromEntries(
        Object.keys(schema.fields).map((name) => {
            const [field] = defineField(name);
            return [name, field];
        }),
    ),
);
const submit = handleSubmit((values) => {
    const row = { ...values, id: values.id ?? Date.now() };
    if (isEditing.value) {
        props.list.splice(editingIndex.value, 1, row);
    } else {
        props.list.push(row);
    }
    dialog.value = false;
});

async function open(item = null) {
    editingIndex.value = item ? props.list.findIndex((row) => row.id === item.id) : -1;
    resetForm({ values: item ? { ...item } : { ...initialValues } });
    await validate({ mode: "silent" });
    dialog.value = true;
};

function close() {
    dialog.value = false;
};
function remove(item) {
    const idx = props.list.findIndex((row) => row.id === item.id);
    if (idx > -1) props.list.splice(idx, 1);
};

defineExpose({ open, remove });
</script>

<template>
    <Dialog v-model="dialog" :is-editing="isEditing" add-title="Add Project" edit-title="Edit Project"
        save-text="Add project" edit-save-text="Save changes" cancel-text="Cancel" edit-cancel-text="Cancel edit"
        :disable-save="!meta.valid || (isEditing && !meta.dirty)" @save="submit" @cancel="close">
        <v-form @submit.prevent="submit">
            <v-row :gap="10">
                <v-col cols="12">
                    <Select v-model="fields.category" :items="categoryOptions" label="Category" color="primary"
                        variant="solo" flat density="comfortable" :error-messages="errors.category" :multiple="false"
                        :chip="false" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="fields.name" label="Project Name" color="primary" variant="solo" flat
                        density="comfortable" clearable :error-messages="errors.name" class="vfield-outline"
                        autocomplete="off" name="project-title" />
                </v-col>
                <v-col cols="12">
                    <v-textarea v-model="fields.description" label="Description" color="primary" auto-grow
                        variant="solo" flat density="comfortable" :error-messages="errors.description"
                        class="vfield-outline" autocomplete="off" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="fields.materials" :items="materialItems" item-title="text" item-value="text"
                        label="Materials" color="primary" variant="solo" density="comfortable" flat :chip="false"
                        :error-messages="errors.materials" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <FileUpload v-model="fields.image" file-type="image" :max-files="1" inset density="comfortable"
                        :show-size="true" :error-messages="errors.image" />
                </v-col>
                <v-col cols="12">
                    <div class="text-title-small text-medium-emphasis mb-2">Link</div>
                    <v-btn-toggle v-model="fields.linkType" color="primary" variant="tonal" density="compact" mandatory
                        divided class="rounded-[10px] mb-3">
                        <v-btn prepend-icon="i-mdi-cloud-upload-outline" value="upload" text="Upload" />
                        <v-btn prepend-icon="i-mdi-link-variant" value="link" text="Link" />
                    </v-btn-toggle>
                    <FileUpload v-if="fields.linkType === 'upload'" v-model="fields.linkFile" :max-files="1" inset
                        density="comfortable" :show-size="true" :error-messages="errors.linkFile" />
                    <v-text-field v-else v-model="fields.linkUrl" label="Link URL" color="primary" variant="solo" flat
                        density="comfortable" clearable :error-messages="errors.linkUrl" class="vfield-outline"
                        autocomplete="off" />
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>