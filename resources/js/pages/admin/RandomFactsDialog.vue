<script>
import * as yup from "yup";

export const randomFactsSchema = yup.object({
    id: yup.mixed().nullable(),
    icon: yup.string().label("Icon").required(),
    iconSvg: yup.string().nullable().default(null),
    randomFact: yup.string().label("Random Fact").required(),
});
</script>

<script setup>
import { ref, computed, reactive } from "vue";
import { useForm } from "vee-validate";
import Dialog from "@/components/forms/FormDialog";
import IconPicker from "@/components/forms/IconPicker";

const props = defineProps({ list: { type: Array, required: true } });
const dialog = ref(false);
const editingIndex = ref(-1);
const isEditing = computed(() => editingIndex.value > -1);
const initialValues = randomFactsSchema.getDefault();
const { defineField, errors, handleSubmit, resetForm, meta, validate } = useForm({
    validationSchema: randomFactsSchema,
    initialValues,
});
const fields = reactive(
    Object.fromEntries(
        Object.keys(randomFactsSchema.fields).map((name) => {
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
    <Dialog v-model="dialog" :is-editing="isEditing" add-title="Add Fact" edit-title="Edit Fact" save-text="Add fact"
        edit-save-text="Save changes" cancel-text="Cancel" edit-cancel-text="Cancel edit"
        :disable-save="!meta.valid || (isEditing && !meta.dirty)" @save="submit" @cancel="close">
        <v-form @submit.prevent="submit">
            <v-row :gap="10">
                <v-col cols="12">
                    <v-text-field :model-value="fields.icon" label="Icon" color="primary" variant="solo" flat
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
                <v-col cols="12">
                    <v-text-field v-model="fields.randomFact" label="Fact" color="primary" variant="solo" flat
                        density="comfortable" clearable :error-messages="errors.randomFact" class="vfield-outline"
                        autocomplete="off" />
                </v-col>
            </v-row>
        </v-form>
    </Dialog>
</template>