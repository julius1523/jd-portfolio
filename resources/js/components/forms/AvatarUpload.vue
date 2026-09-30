<script setup>
import { ref, computed, watch } from "vue";
import { useFileUpload } from "@/composables/useFileUpload";

const props = defineProps({
    modelValue: { type: [File, Object, String], default: null },
    maxSize: { type: Number, default: null },
    size: { type: [Number, String], default: 96 },
    rounded: { type: String, default: "circle" },
    disabled: { type: Boolean, default: false },
});
const emit = defineEmits(["update:modelValue"]);
const avatarRef = ref(null);
const avatarTarget = computed(() => avatarRef.value?.$el ?? null);
const uploadOptions = {
    fileType: "image",
    multiple: false,
    maxFiles: 1,
    get maxSize() {
        return props.maxSize;
    },
};
const { files, getPreviewUrl, handleChange, syncFromModelValue } = useFileUpload(uploadOptions, emit);
const uploadInputRef = ref(null);
const menu = ref(false);
const currentFile = computed(() => files.value[0] ?? null);
const previewUrl = computed(() => getPreviewUrl(currentFile.value) || "/images/default-profile.png");
const hasPhoto = computed(() => !!currentFile.value);
const btnSize = computed(() => Math.max(24, Math.round(Number(props.size) * 0.2)));
const iconSize = computed(() => Math.max(14, Math.round(btnSize.value * 0.55)));

function pickFile() {
    menu.value = false;
    uploadInputRef.value?.click();
};
function removePhoto() {
    menu.value = false;
    handleChange(null);
};
function onInputChange(e) {
    const file = e.target.files?.[0] ?? null;
    if (file) handleChange(file);
    e.target.value = "";
};

watch(() => props.modelValue, syncFromModelValue, { immediate: true });
</script>

<template>
    <div class="relative inline-flex" data-shimmer-no-children>
        <v-avatar ref="avatarRef" :size="size" :image="previewUrl" :rounded="rounded" border />

        <v-menu v-model="menu" :target="avatarTarget" location="bottom center">
            <template #activator="{ props: menuProps }">
                <v-icon-btn v-bind="menuProps" variant="flat" icon="i-mdi-pencil" :icon-size="iconSize" :size="btnSize"
                    class="border absolute right-[14.6%] bottom-[14.6%] translate-x-1/2 translate-y-1/2" />
            </template>
            <v-list density="compact" nav :prepend-gap="17" class="shadow-sm border rounded-[10px]">
                <v-list-item prepend-icon="i-mdi-image-outline" :title="hasPhoto ? 'Change photo' : 'Upload photo'"
                    @click="pickFile" />
                <template v-if="hasPhoto">
                    <v-list-item prepend-icon="i-mdi-trash-can-outline" title="Remove photo" @click="removePhoto" />
                </template>
            </v-list>
        </v-menu>
    </div>

    <input ref="uploadInputRef" type="file" accept="image/*" hidden @change="onInputChange" />
</template>