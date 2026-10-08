<script setup>
import { ref } from "vue";
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
const isScrollable = ref(false);

function onCancel() {
    emit("update:modelValue", false);
    emit("cancel");
};
function checkOverflow(instance) {
    const { viewport } = instance.elements()
    isScrollable.value = viewport.scrollHeight > viewport.clientHeight
};
</script>

<template>
    <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" scrollable
        :max-width="maxWidth" :max-height="maxHeight">
        <v-card class="rounded-[24px] shadow-sm">
            <v-toolbar density="compact" color="surface">
                <div class="grid w-full grid-cols-[1fr_auto_1fr] items-center">
                    <div></div>
                    <span class="min-w-0 truncate text-center text-title-medium">
                        {{ isEditing ? editTitle : addTitle }}
                    </span>
                    <div class="flex justify-end">
                        <v-icon icon="i-mdi-close" size="small" class="me-[20px]" @click="onCancel"
                            v-tooltip="{ text: 'Close' }" />
                    </div>
                </div>
            </v-toolbar>

            <OverlayScrollbarsComponent element="div" class="pa-5 overflow-y-auto"
                :options="{ scrollbars: { autoHide: 'move', theme: $vuetify.theme.current.dark ? 'os-theme-light' : 'os-theme-dark' } }"
                :events="{
                    initialized: checkOverflow,
                    updated: checkOverflow,
                }" defer>
                <slot />
            </OverlayScrollbarsComponent>

            <v-card-actions class="py-[15px] px-[20px] mt-auto flex-column flex-sm-row"
                :class="{ 'border-t': isScrollable }">
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