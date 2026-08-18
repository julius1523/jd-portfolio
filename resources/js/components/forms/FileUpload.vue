<script setup>
import { ref, computed, watch, onBeforeUnmount, useAttrs } from "vue";
import { mdiCloudUpload, mdiCloudUploadOutline, mdiTrashCan } from "@mdi/js";
import { RiFilePdf2Fill, RiFileWordFill, RiFilePptFill, RiFileExcelFill, RiFile3Fill } from "@remixicon/vue";
const props = defineProps({
    modelValue: { type: [File, Object, Array, String], default: null },
    fileType: { type: String, default: 'any' },
    inset: { type: Boolean, default: false },
    scrim: { type: Boolean, default: false },
    color: { type: String, default: null },
    accept: { type: String, default: null },
    multiple: { type: Boolean, default: false },
    maxSize: { type: Number, default: null },
    maxFiles: { type: Number, default: null },
    title: { type: String, default: 'Choose a file or drag and drop it here' },
    subtitle: { type: String, default: undefined },
    icon: { type: String, default: mdiCloudUpload },
    density: { type: String, default: 'default' },
    variant: { type: String, default: 'default' },
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
    const { error, errorMessage: _em, errorMessages: _ems, 'error-messages': _emk, ...rest } = attrs;
    return rest;
});
const fileUploadRef = ref(null);
const emit = defineEmits(['update:modelValue', 'error']);
const PRESETS = {
    image: 'image/*',
    pdf: 'application/pdf',
    document:
        '.doc,.docx,.odt,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    spreadsheet:
        '.xls,.xlsx,.csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    video: 'video/*',
    audio: 'audio/*',
    any: undefined,
};
const FRIENDLY_LABELS = {
    image: 'Images (JPG, PNG, GIF, WEBP)',
    pdf: 'PDF',
    document: 'Word documents (DOC, DOCX, ODT)',
    spreadsheet: 'Spreadsheets (XLS, XLSX, CSV)',
    video: 'Video files',
    audio: 'Audio files',
    any: 'Any file type',
};
const computedAccept = computed(() => props.accept || PRESETS[props.fileType]);
const acceptedTypesLabel = computed(() => {
    if (props.accept) {
        return props.accept
            .split(',')
            .map((a) => a.trim().replace(/^\./, '').replace('/*', '').toUpperCase())
            .join(', ');
    }
    return FRIENDLY_LABELS[props.fileType] || 'Any file type';
});
const maxSizeLabel = computed(() =>
    props.maxSize ? `Max ${props.maxSize} MB per file` : null
);
const helperText = computed(() => {
    const parts = [acceptedTypesLabel.value];
    if (maxSizeLabel.value) parts.push(maxSizeLabel.value);
    if (props.multiple && props.maxFiles) parts.push(`Up to ${props.maxFiles} files`);
    return parts.join(' • ');
});
const internalError = ref('');
const internalValue = ref(null);
const externalErrorMessages = computed(() => {
    const raw = props.errorMessage ?? props.errorMessages;
    if (!raw) return [];
    return Array.isArray(raw) ? raw : [raw];
});
const displayedErrorMessages = computed(() => {
    if (internalError.value) return [internalError.value];
    return externalErrorMessages.value;
});
const hasError = computed(() => displayedErrorMessages.value.length > 0);
function isExistingFileMeta(item) {
    return !!item && typeof item === 'object' && !(item instanceof File) && !(item instanceof Blob) && 'file_name' in item;
};
function metaToPseudoFile(item) {
    const file = new File([], item.orig_name || item.file_name, { type: item.mime_type });
    Object.defineProperty(file, 'size', { value: item.file_size || 0, enumerable: true, configurable: true });
    Object.defineProperty(file, 'file_name', { value: item.file_name, enumerable: true });
    Object.defineProperty(file, '__existing', { value: true, enumerable: true });
    Object.defineProperty(file, '__url', { value: item.url, enumerable: true });
    return file;
};
function resolveSingle(item) {
    if (isExistingFileMeta(item)) return metaToPseudoFile(item);
    return item;
};
function resolveModelValue(val) {
    if (!val) return null;
    if (Array.isArray(val)) {
        return val.map(resolveSingle);
    }
    return resolveSingle(val);
};
const files = computed(() => {
    if (!internalValue.value) return [];
    return Array.isArray(internalValue.value) ? internalValue.value : [internalValue.value];
});
const FILE_TYPE_ICONS = {
    pdf: RiFilePdf2Fill,
    word: RiFileWordFill,
    ppt: RiFilePptFill,
    excel: RiFileExcelFill,
    default: RiFile3Fill,
};
function getFileIconComponent(file) {
    if (!file) return FILE_TYPE_ICONS.default;
    const name = (file.name || file.file_name || '').toLowerCase();
    const type = (file.type || file.mime_type || '').toLowerCase();
    const ext = name.includes('.') ? name.split('.').pop() : '';
    if (type === 'application/pdf' || ext === 'pdf') {
        return FILE_TYPE_ICONS.pdf;
    }
    if (
        ['doc', 'docx', 'odt'].includes(ext) ||
        type.includes('msword') ||
        type.includes('wordprocessingml')
    ) {
        return FILE_TYPE_ICONS.word;
    }
    if (
        ['ppt', 'pptx'].includes(ext) ||
        type.includes('ms-powerpoint') ||
        type.includes('presentationml')
    ) {
        return FILE_TYPE_ICONS.ppt;
    }
    if (
        ['xls', 'xlsx', 'csv'].includes(ext) ||
        type.includes('ms-excel') ||
        type.includes('spreadsheetml') ||
        type === 'text/csv'
    ) {
        return FILE_TYPE_ICONS.excel;
    }
    return FILE_TYPE_ICONS.default;
};
function formatSize(bytes) {
    if (!bytes) return '0 B';
    const units = ['B', 'KB', 'MB', 'GB'];
    let i = 0;
    let size = bytes;
    while (size >= 1024 && i < units.length - 1) {
        size /= 1024;
        i++;
    }
    return `${size.toFixed(1)} ${units[i]}`;
};
const previewUrls = new WeakMap();
function getPreviewUrl(file) {
    if (file.__existing) return file.__url;
    if (!file.type?.startsWith('image/')) return undefined;
    if (!previewUrls.has(file)) previewUrls.set(file, URL.createObjectURL(file));
    return previewUrls.get(file);
};
function revokePreview(file) {
    if (!file.__existing && previewUrls.has(file)) {
        URL.revokeObjectURL(previewUrls.get(file));
        previewUrls.delete(file);
    }
};
function validate(selected) {
    const list = Array.isArray(selected) ? selected : selected ? [selected] : [];
    if (props.maxFiles && list.length > props.maxFiles) {
        return `You can upload a maximum of ${props.maxFiles} file(s).`;
    }
    for (const file of list) {
        if (file?.__existing) continue;
        if (props.maxSize && file.size > props.maxSize * 1024 * 1024) {
            return `"${file.name}" exceeds the maximum size of ${props.maxSize} MB.`;
        }
        if (computedAccept.value) {
            const accepted = computedAccept.value.split(',').map((a) => a.trim());
            const matches = accepted.some((pattern) => {
                if (pattern.endsWith('/*')) {
                    return file.type.startsWith(pattern.replace('/*', '/'));
                }
                if (pattern.startsWith('.')) {
                    return file.name.toLowerCase().endsWith(pattern.toLowerCase());
                }
                return file.type === pattern;
            })
            if (!matches) {
                return `"${file.name}" is not an accepted file type.`;
            }
        }
    }
    for (const rule of props.rules) {
        const result = rule(list);
        if (typeof result === 'string') return result;
    }
    return "";
};
function handleChange(selected) {
    const message = validate(selected);
    internalError.value = message;
    if (message) {
        emit('error', message);
        return;
    }
    const prevFiles = files.value;
    const nextList = Array.isArray(selected) ? selected : selected ? [selected] : [];
    prevFiles.forEach((f) => { if (!nextList.includes(f)) revokePreview(f); });

    internalValue.value = selected;
    emit('update:modelValue', selected);
};
function removeFile(index) {
    if (Array.isArray(internalValue.value)) {
        const updated = [...internalValue.value];
        const [removed] = updated.splice(index, 1);
        if (removed) revokePreview(removed);
        internalValue.value = updated;
        emit('update:modelValue', updated);
    } else {
        if (internalValue.value) revokePreview(internalValue.value);
        internalValue.value = null;
        emit('update:modelValue', null);
    }
};
function onDropzoneClick(e) {
    if (props.disabled || e.target.closest('.v-btn')) return;
    fileUploadRef.value?.controlRef?.click();
};
watch(
    () => props.modelValue,
    (val) => {
        internalError.value = '';
        internalValue.value = resolveModelValue(val);
    },
    { immediate: true }
);
onBeforeUnmount(() => {
    files.value.forEach((f) => revokePreview(f));
});
</script>

<template>
    <div class="cursor-pointer" @click="onDropzoneClick">
        <v-file-upload ref="fileUploadRef" v-model="internalValue" :inset-file-list="inset" bg-color="primary"
            :scrim="scrim" :color="color" :accept="computedAccept" :multiple="multiple" :density="density"
            :variant="variant" :title="title" :subtitle="subtitle" :icon="icon" :disabled="disabled"
            :clearable="clearable" :show-size="showSize" :hint="hint" :persistent-hint="persistent" :error="hasError"
            :error-messages="displayedErrorMessages" v-bind="filteredAttrs" @update:model-value="handleChange">

            <template v-for="(_, slot) in $slots" #[slot]="scope">
                <slot :name="slot" v-bind="scope" />
            </template>

            <template #single="{ file, props: itemProps }">
                <v-file-upload-item v-bind="itemProps" :file="file" :show-size="showSize" :clearable="clearable"
                    class="border-0">
                    <template #prepend>
                        <v-avatar size="46" class="border">
                            <v-img v-if="file.type?.startsWith('image/')" :src="getPreviewUrl(file)" :cover="false"
                                alt="" />
                            <component v-else :is="getFileIconComponent(file)" size="24" />
                        </v-avatar>
                    </template>
                    <template v-slot:clear="{ props: clearProps }">
                        <v-btn :icon="mdiTrashCan" v-bind="clearProps"></v-btn>
                    </template>
                </v-file-upload-item>
            </template>

            <template #item="{ file, props: itemProps }">
                <v-file-upload-item v-bind="itemProps" :file="file" :show-size="showSize" :clearable="clearable"
                    class="border-0">
                    <template #prepend>
                        <v-avatar size="46" class="border">
                            <v-img v-if="file.type?.startsWith('image/')" :src="getPreviewUrl(file)" :cover="false"
                                class="border" alt="" />
                            <component v-else :is="getFileIconComponent(file)" size="24" />
                        </v-avatar>
                    </template>
                    <template v-slot:clear="{ props: clearProps }">
                        <v-btn :icon="mdiTrashCan" v-bind="clearProps"></v-btn>
                    </template>
                </v-file-upload-item>
            </template>

            <template #browse="{ props: browseProps }">
                <v-btn v-bind="browseProps" variant="tonal" color="primary" rounded="lg" text="Browse File"
                    class="mt-3" />
            </template>

            <template #title>
                <div class="text-title-medium font-weight-bold">{{ props.title }}</div>
                <div v-if="props.density != 'compact'" class="text-title-small text-medium-emphasis mt-1">
                    {{ helperText }}
                </div>
            </template>
        </v-file-upload>
    </div>
</template>