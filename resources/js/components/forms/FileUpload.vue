<script setup>
import { ref, computed, watch, useAttrs } from "vue";
const props = defineProps({
    modelValue: { type: [File, Array, String], default: null },
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
    icon: { type: String, default: 'mdi-cloud-upload' },
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
const resolvingPreview = ref(false);
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
async function urlToFile(url) {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`Failed to fetch ${url} (${response.status})`);
    const blob = await response.blob();
    const filename = decodeURIComponent(url.split('/').pop().split('?')[0]) || 'file';
    return new File([blob], filename, { type: blob.type });
};
async function resolveSingle(item) {
    if (typeof item === 'string') {
        return urlToFile(item);
    }
    return item;
};
async function resolveModelValue(val) {
    if (!val) return null;
    if (Array.isArray(val)) {
        return Promise.all(val.map(resolveSingle));
    }
    return resolveSingle(val);
}
const files = computed(() => {
    if (!internalValue.value) return [];
    return Array.isArray(internalValue.value) ? internalValue.value : [internalValue.value];
});
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
}
function validate(selected) {
    const list = Array.isArray(selected) ? selected : selected ? [selected] : [];
    if (props.maxFiles && list.length > props.maxFiles) {
        return `You can upload a maximum of ${props.maxFiles} file(s).`;
    }
    for (const file of list) {
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
    internalValue.value = selected;
    emit('update:modelValue', selected);
};
function removeFile(index) {
    if (Array.isArray(internalValue.value)) {
        const updated = [...internalValue.value];
        updated.splice(index, 1);
        internalValue.value = updated;
        emit('update:modelValue', updated);
    } else {
        internalValue.value = null;
        emit('update:modelValue', null);
    }
};
watch(
    () => props.modelValue,
    async (val) => {
        const hasUrlToResolve = Array.isArray(val)
            ? val.some((v) => typeof v === 'string')
            : typeof val === 'string';
        if (!hasUrlToResolve) {
            internalValue.value = val;
            return;
        }
        resolvingPreview.value = true;
        try {
            const resolved = await resolveModelValue(val);
            internalValue.value = resolved;
        } catch (e) {
            internalError.value = 'Could not load the existing file for preview.';
            emit('error', internalError.value);
        } finally {
            resolvingPreview.value = false;
        }
    },
    { immediate: true }
);
</script>

<template>
    <div>
        <v-file-upload v-model="internalValue" :inset-file-list="inset" bg-color="primary" :scrim="scrim" :color="color"
            :accept="computedAccept" :multiple="multiple" :density="density" :variant="variant" :title="title"
            :subtitle="subtitle" :icon="icon" :disabled="disabled" :clearable="clearable" :show-size="showSize"
            :hint="hint" :persistent-hint="persistent" :error="hasError" :error-messages="displayedErrorMessages"
            v-bind="filteredAttrs" @update:model-value="handleChange">
            <template v-for="(_, slot) in $slots" #[slot]="scope">
                <slot :name="slot" v-bind="scope" />
            </template>
            <template #icon>
                <v-icon icon="mdi-cloud-upload-outline" size="x-small"></v-icon>
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