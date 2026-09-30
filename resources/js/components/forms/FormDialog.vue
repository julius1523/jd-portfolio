<script setup>
import { OverlayScrollbarsComponent } from "overlayscrollbars-vue";

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    isEditing: { type: Boolean, default: false },
    addTitle: { type: String, default: "Add Item" },
    editTitle: { type: String, default: "Edit Item" },
    saveText: { type: String, default: "Add" },
    editSaveText: { type: String, default: "Save changes" },
    cancelText: { type: String, default: "Cancel" },
    editCancelText: { type: String, default: "Cancel edit" },
    loading: { type: Boolean, default: false },
    disableSave: { type: Boolean, default: true },
    maxWidth: { type: [String, Number], default: 500 },
    maxHeight: { type: [String, Number], default: 630 },
});
const emit = defineEmits(["update:modelValue", "save", "cancel"]);

function onCancel() {
    emit("update:modelValue", false);
    emit("cancel");
}
</script>

<template>
    <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" scrollable
        :max-width="maxWidth" :max-height="maxHeight">
        <v-card class="rounded-[18px] shadow-sm">
            <v-toolbar density="compact" color="surface" class="border-b">
                <div class="grid w-full grid-cols-[1fr_auto_1fr] items-center">
                    <div></div>
                    <span class="min-w-0 truncate text-center text-title-medium">
                        {{ isEditing ? editTitle : addTitle }}
                    </span>
                    <div class="flex justify-end">
                        <v-icon icon="i-mdi-close" size="small" class="me-[18px]" @click="onCancel"
                            v-tooltip="{ text: 'Close' }" />
                    </div>
                </div>
            </v-toolbar>

            <OverlayScrollbarsComponent element="div" class="pa-5 overflow-y-auto"
                :options="{ scrollbars: { autoHide: 'move', theme: $vuetify.theme.current.dark ? 'os-theme-light' : 'os-theme-dark', } }"
                defer>
                <slot />
            </OverlayScrollbarsComponent>

            <v-card-actions class="pa-[15px] mt-auto border-t flex-column flex-sm-row">
                <div class="order-1 order-sm-0" :class="{ 'w-100': $vuetify.display.smAndDown }">
                    <v-btn variant="flat" color="surface-light" height="40" block :slim="false" rounded="pill"
                        class="border" @click="onCancel">
                        {{ isEditing ? editCancelText : cancelText }}
                    </v-btn>
                </div>
                <div :class="{ 'w-100': $vuetify.display.smAndDown }">
                    <v-btn variant="flat" color="primary" height="40" block :slim="false" rounded="pill"
                        :loading="loading" :disabled="disableSave" @click="$emit('save')">
                        {{ isEditing ? editSaveText : saveText }}
                    </v-btn>
                </div>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>