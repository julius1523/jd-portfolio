<script>
import * as yup from "yup";

export const skillsSchema = yup.object({
    id: yup.mixed().nullable(),
    category: yup.string().label("Category").required(),
    skill: yup.array().of(yup.string()).label("Skills").default([]).min(1, "At least one skill is required"),
    icon: yup.string().label("Icon").nullable().default(null),
    iconSvg: yup.string().nullable().default(null),
});
</script>

<script setup>
import { ref, computed, reactive } from "vue";
import { useForm } from "vee-validate";
import Dialog from "@/components/forms/FormDialog";
import Select from "@/components/forms/Select";
import IconPicker from "@/components/forms/IconPicker";
import { SKILL_CATEGORIES } from "@/src/constants/constants";

const skillCategoryOptions = SKILL_CATEGORIES.map((category) => ({
    title: category.title,
    value: category.title,
}));
const availableSkills = computed(() => {
    return SKILL_CATEGORIES.find((c) => c.title === fields.category)?.skills ?? [];
});
const props = defineProps({ list: { type: Array, required: true } });
const dialog = ref(false);
const editingIndex = ref(-1);
const isEditing = computed(() => editingIndex.value > -1);
const initialValues = skillsSchema.getDefault();
const { defineField, errors, handleSubmit, resetForm, meta, validate } = useForm({
    validationSchema: skillsSchema,
    initialValues,
});
const fields = reactive(
    Object.fromEntries(
        Object.keys(skillsSchema.fields).map((name) => {
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
    await validate();
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
    <Dialog v-model="dialog" :is-editing="isEditing" add-title="Add Skill" edit-title="Edit Skill" save-text="Add skill"
        edit-save-text="Save changes" cancel-text="Cancel" edit-cancel-text="Cancel edit"
        :disable-save="!meta.valid || (isEditing && !meta.dirty)" @save="submit" @cancel="close">
        <v-form @submit.prevent="submit">
            <v-row :gap="10">
                <v-col cols="12">
                    <Select v-model="fields.category" :items="skillCategoryOptions" label="Skill Category"
                        color="primary" variant="solo" flat density="comfortable" :multiple="false" :chip="false"
                        :error-messages="errors.category" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="fields.skill" :items="availableSkills" label="Skills" color="primary"
                        variant="solo" flat density="comfortable" :multiple="true" :chip="false"
                        :error-messages="errors.skill" class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <v-text-field :model-value="fields.icon" label="Icon (optional)" color="primary" variant="solo" flat
                        density="comfortable" :error-messages="errors.icon" readonly class="vfield-outline">
                        <template #default>
                            <v-icon v-if="fields.iconSvg" color="primary" size="20" class="mr-2">
                                <span v-html="fields.iconSvg" class="inline-flex items-center" />
                            </v-icon>
                        </template>
                        <template #append-inner>
                            <IconPicker v-model="fields.icon" @selected="fields.iconSvg = $event.svg" />
                        </template>
                    </v-text-field>
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>