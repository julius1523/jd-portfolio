<script setup>
const props = defineProps({
    modelValue: { type: Boolean, default: false },
    isEditing: { type: Boolean, default: false },
    addTitle: { type: String, default: "Add Item" },
    editTitle: { type: String, default: "Edit Item" },
    saveText: { type: String, default: "Add" },
    editSaveText: { type: String, default: "Save Changes" },
    cancelText: { type: String, default: "Cancel" },
    editCancelText: { type: String, default: "Cancel Edit" },
    loading: { type: Boolean, default: false },
    maxWidth: { type: [String, Number], default: 500 },
    maxHeight: { type: [String, Number], default: 630 },
});
const emit = defineEmits(["update:modelValue", "save", "cancel"]);
function onCancel() {
    emit("update:modelValue", false);
    emit("cancel");
};
</script>

<template>
    <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" scrollable
        :max-width="maxWidth" :max-height="maxHeight" :fullscreen="$vuetify.display.smAndDown">
        <v-card :class="$vuetify.display.mdAndUp ? 'rounded-2xl' : undefined">
            <v-toolbar density="compact" color="surface" class="border-b">
                <template #title>
                    <span class="ms-2 text-title-medium font-weight-bold">
                        {{ isEditing ? editTitle : addTitle }}
                    </span>
                </template>
                <template #append>
                    <v-btn icon="i-mdi-close" variant="text" size="x-small" class="me-3" @click="onCancel" />
                </template>
            </v-toolbar>

            <v-card-text class="pa-5">
                <slot />
            </v-card-text>

            <v-card-actions class="d-flex flex-column flex-md-row bg-surface pa-4 border-t">
                <div class="order-1 order-md-0" :class="{ 'w-100': $vuetify.display.smAndDown }">
                    <v-btn variant="text" class="border border-opacity-50" rounded="lg" block :slim="false" size="large"
                        @click="onCancel">
                        {{ isEditing ? editCancelText : cancelText }}
                    </v-btn>
                </div>
                <div :class="{ 'w-100': $vuetify.display.smAndDown }">
                    <v-btn variant="flat" color="primary" rounded="lg" block :slim="false" size="large"
                        :loading="loading" @click="$emit('save')">
                        {{ isEditing ? editSaveText : saveText }}
                    </v-btn>
                </div>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>