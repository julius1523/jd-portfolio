<script setup>
import { computed, watch, useAttrs, useSlots } from "vue";
import { useFileUpload } from "@/composables/useFileUpload";

const slots = useSlots();
const reservedSlotNames = ["single", "item", "title"];
const forwardedSlotNames = computed(() => Object.keys(slots).filter((name) => !reservedSlotNames.includes(name)));
const props = defineProps({
    modelValue: { type: [File, Object, Array, String], default: null },
    fileType: { type: String, default: "any" },
    inset: { type: Boolean, default: false },
    scrim: { type: Boolean, default: false },
    color: { type: String, default: null },
    accept: { type: String, default: null },
    multiple: { type: Boolean, default: false },
    maxSize: { type: Number, default: null },
    maxFiles: { type: Number, default: null },
    title: { type: String, default: "Choose a file or drag and drop it here" },
    subtitle: { type: String, default: undefined },
    icon: { type: String, default: "i-mdi-cloud-upload" },
    density: { type: String, default: "default" },
    hideBrowse: { type: Boolean, default: false },
    hideDetails: { type: [String, Boolean], default: false },
    variant: { type: String, default: "default" },
    disabled: { type: Boolean, default: false },
    clearable: { type: Boolean, default: true },
    showSize: { type: Boolean, default: false },
    hint: { type: String, default: null },
    persistent: { type: Boolean, default: false },
    rules: { type: Array, default: () => [] },
    errorMessage: { type: [String, Array], default: null },
    errorMessages: { type: [String, Array], default: null },
});
const attrs = useAttrs();
const filteredAttrs = computed(() => {
    const { error, errorMessage: _em, errorMessages: _ems, "error-messages": _emk, ...rest } = attrs;
    return rest;
});
const emit = defineEmits(["update:modelValue", "error"]);
const {
    internalValue,
    computedAccept,
    getPreviewUrl,
    handleChange,
    syncFromModelValue,
} = useFileUpload(props, emit);
const FRIENDLY_LABELS = {
    image: "Images (JPG, PNG, GIF, WEBP)",
    pdf: "PDF",
    document: "Word documents (DOC, DOCX, ODT)",
    spreadsheet: "Spreadsheets (XLS, XLSX, CSV)",
    video: "Video files",
    audio: "Audio files",
    any: "Any file type",
};
const acceptedTypesLabel = computed(() => {
    if (props.accept) {
        return props.accept
            .split(",")
            .map((a) => a.trim().replace(/^\./, "").replace("/*", "").toUpperCase())
            .join(", ");
    }
    return FRIENDLY_LABELS[props.fileType] || "Any file type";
});
const maxSizeLabel = computed(() => (props.maxSize ? `Max ${props.maxSize} MB per file` : null));
const helperText = computed(() => {
    const parts = [acceptedTypesLabel.value];
    if (maxSizeLabel.value) parts.push(maxSizeLabel.value);
    if (props.multiple && props.maxFiles) parts.push(`Up to ${props.maxFiles} files`);
    return parts.join(" • ");
});
const externalErrorMessages = computed(() => {
    const raw = props.errorMessage ?? props.errorMessages;
    if (!raw) return [];
    return Array.isArray(raw) ? raw : [raw];
});
const displayedErrorMessages = computed(() => externalErrorMessages.value);
const hasError = computed(() => displayedErrorMessages.value.length > 0);
const FILE_TYPE_ICONS = {
    pdf: "i-ri-file-pdf-2-fill",
    word: "i-ri-file-word-fill",
    ppt: "i-ri-file-ppt-fill",
    excel: "i-ri-file-excel-fill",
    default: "i-ri-file-3-fill",
};

function getFileIconComponent(file) {
    if (!file) return FILE_TYPE_ICONS.default;
    const name = (file.name || file.file_name || "").toLowerCase();
    const type = (file.type || file.mime_type || "").toLowerCase();
    const ext = name.includes(".") ? name.split(".").pop() : "";

    if (type === "application/pdf" || ext === "pdf") return FILE_TYPE_ICONS.pdf;
    if (["doc", "docx", "odt"].includes(ext) || type.includes("msword") || type.includes("wordprocessingml")) {
        return FILE_TYPE_ICONS.word;
    }
    if (["ppt", "pptx"].includes(ext) || type.includes("ms-powerpoint") || type.includes("presentationml")) {
        return FILE_TYPE_ICONS.ppt;
    }
    if (
        ["xls", "xlsx", "csv"].includes(ext) ||
        type.includes("ms-excel") ||
        type.includes("spreadsheetml") ||
        type === "text/csv"
    ) {
        return FILE_TYPE_ICONS.excel;
    }
    return FILE_TYPE_ICONS.default;
}

watch(() => props.modelValue, syncFromModelValue, { immediate: true });
</script>

<template>
    <v-file-upload :model-value="internalValue" :inset-file-list="inset" bg-color="primary" :scrim="scrim"
        :color="color" :accept="computedAccept" :multiple="multiple" :density="density" :hide-browse="hideBrowse"
        :hide-details="hideDetails" :variant="variant" :title="title" :subtitle="subtitle" :icon="icon"
        :disabled="disabled" :clearable="clearable" :show-size="showSize" :hint="hint" :persistent-hint="persistent"
        :error="hasError" :error-messages="displayedErrorMessages" v-bind="filteredAttrs"
        @update:model-value="handleChange">
        <template v-for="name in forwardedSlotNames" #[name]="scope" :key="name">
            <slot :name="name" v-bind="scope" />
        </template>

        <template #single="{ file, props: itemProps }">
            <v-file-upload-item v-bind="itemProps" :file="file" :show-size="showSize" :clearable="clearable"
                class="border-0">
                <template #prepend>
                    <v-avatar size="46" class="border">
                        <v-img v-if="file.type?.startsWith('image/')" :src="getPreviewUrl(file)" :cover="false"
                            alt="Uploaded Image" eager />
                        <v-icon v-else :class="getFileIconComponent(file)" size="24" />
                    </v-avatar>
                </template>
                <template v-slot:clear="{ props: clearProps }">
                    <v-icon icon="i-mdi-trash-can" v-bind="clearProps"></v-icon>
                </template>
            </v-file-upload-item>
        </template>

        <template #item="{ file, props: itemProps }">
            <v-file-upload-item v-bind="itemProps" :file="file" :show-size="showSize" :clearable="clearable"
                class="border-0">
                <template #prepend>
                    <v-avatar size="46" class="border">
                        <v-img v-if="file.type?.startsWith('image/')" :src="getPreviewUrl(file)" :cover="false"
                            alt="Uploaded Image" eager />
                        <v-icon v-else :class="getFileIconComponent(file)" size="24" />
                    </v-avatar>
                </template>
                <template v-slot:clear="{ props: clearProps }">
                    <v-icon icon="i-mdi-trash-can" v-bind="clearProps"></v-icon>
                </template>
            </v-file-upload-item>
        </template>

        <template #title>
            <div class="text-title-medium font-weight-bold">{{ props.title }}</div>
            <div v-if="props.density != 'compact'" class="text-title-small text-medium-emphasis mt-1">
                {{ helperText }}
            </div>
        </template>
    </v-file-upload>
</template>