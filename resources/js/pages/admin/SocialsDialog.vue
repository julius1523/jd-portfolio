<script setup>
import { ref, computed, reactive } from "vue";
import * as yup from "yup";
import { useForm } from "vee-validate";
import Dialog from "@/components/forms/FormDialog";
import Select from "@/components/forms/Select";
import { SOCIAL_ICONS } from "@/src/constants/constants";

const props = defineProps({ list: { type: Array, required: true } });
const dialog = ref(false);
const editingIndex = ref(-1);
const isEditing = computed(() => editingIndex.value > -1);
const schema = yup.object({
    id: yup.mixed().nullable(),
    name: yup.string().label("Social Name").required(),
    linkUrl: yup.string().label("Link URL").required(),
    icon: yup.string().label("Icon").required(),
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
    const row = { ...values, id: values.id ?? crypto.randomUUID() };
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
    <Dialog v-model="dialog" :is-editing="isEditing" add-title="Add Social" edit-title="Edit Social"
        save-text="Add social" edit-save-text="Save changes" cancel-text="Cancel" edit-cancel-text="Cancel Edit"
        :disable-save="!meta.valid || (isEditing && !meta.dirty)" @save="submit" @cancel="close">
        <v-form @submit.prevent="submit">
            <v-row :gap="10">
                <v-col cols="12">
                    <v-text-field v-model="fields.name" label="Social Name" color="primary" variant="solo" flat
                        density="comfortable" clearable :error-messages="errors.name" autocomplete="off"
                        class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="fields.linkUrl" label="Link URL" color="primary" variant="solo" flat
                        density="comfortable" clearable :error-messages="errors.linkUrl" autocomplete="off"
                        class="vfield-outline" />
                </v-col>
                <v-col cols="12">
                    <Select v-model="fields.icon" label="Icon" color="primary" variant="solo" flat density="comfortable"
                        :items="SOCIAL_ICONS" item-title="name" item-value="value" :multiple="false" :chip="false"
                        :error-messages="errors.icon" class="vfield-outline">
                        <template #item="{ item, props: itemProps }">
                            <v-list-item v-bind="itemProps" :prepend-icon="item?.value" color="primary"
                                :title="undefined">
                                <template #title>
                                    <span class="text-label-medium">{{ item?.name }}</span>
                                </template>
                            </v-list-item>
                        </template>

                        <template #selection="{ item }">
                            <v-icon :icon="item?.value" color="primary" size="small" class="mr-2" />
                            {{ item?.name }}
                        </template>
                    </Select>
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>