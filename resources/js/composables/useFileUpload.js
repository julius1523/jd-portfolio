import { ref, computed, onBeforeUnmount } from "vue";
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";

export function useFileUpload(props, emit) {
    const { error: showError } = useSnackBarQueue();

    const PRESETS = {
        image: "image/*",
        pdf: "application/pdf",
        document:
            ".doc,.docx,.odt,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        spreadsheet:
            ".xls,.xlsx,.csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        video: "video/*",
        audio: "audio/*",
        any: undefined,
    };

    const computedAccept = computed(
        () => props.accept || PRESETS[props.fileType],
    );

    const internalValue = ref(null);

    function isExistingFileMeta(item) {
        return (
            !!item &&
            typeof item === "object" &&
            !(item instanceof File) &&
            !(item instanceof Blob) &&
            "file_name" in item
        );
    }

    function metaToPseudoFile(item) {
        const file = new File([], item.orig_name || item.file_name, {
            type: item.mime_type,
        });
        Object.defineProperty(file, "size", {
            value: item.file_size || 0,
            enumerable: true,
            configurable: true,
        });
        Object.defineProperty(file, "file_name", {
            value: item.file_name,
            enumerable: true,
        });
        Object.defineProperty(file, "__existing", {
            value: true,
            enumerable: true,
        });
        Object.defineProperty(file, "__url", {
            value: item.url,
            enumerable: true,
        });
        return file;
    }

    function resolveSingle(item) {
        if (isExistingFileMeta(item)) return metaToPseudoFile(item);
        return item;
    }

    function resolveModelValue(val) {
        if (!val) return null;
        if (Array.isArray(val)) return val.map(resolveSingle);
        return resolveSingle(val);
    }

    const files = computed(() => {
        if (!internalValue.value) return [];
        return Array.isArray(internalValue.value)
            ? internalValue.value
            : [internalValue.value];
    });

    const previewUrls = new WeakMap();
    function getPreviewUrl(file) {
        if (!file) return undefined;
        if (file.__existing) return file.__url;
        if (!file.type?.startsWith("image/")) return undefined;
        if (!previewUrls.has(file))
            previewUrls.set(file, URL.createObjectURL(file));
        return previewUrls.get(file);
    }

    function revokePreview(file) {
        if (file && !file.__existing && previewUrls.has(file)) {
            URL.revokeObjectURL(previewUrls.get(file));
            previewUrls.delete(file);
        }
    }

    function validate(selected) {
        const list = Array.isArray(selected)
            ? selected
            : selected
              ? [selected]
              : [];

        if (props.maxFiles && list.length > props.maxFiles) {
            return `You can upload a maximum of ${props.maxFiles} file(s)`;
        }

        for (const file of list) {
            if (file?.__existing) continue;

            if (props.maxSize && file.size > props.maxSize * 1024 * 1024) {
                return `"${file.name}" exceeds the maximum size of ${props.maxSize} MB`;
            }

            if (computedAccept.value) {
                const accepted = computedAccept.value
                    .split(",")
                    .map((a) => a.trim());
                const matches = accepted.some((pattern) => {
                    if (pattern.endsWith("/*"))
                        return file.type.startsWith(pattern.replace("/*", "/"));
                    if (pattern.startsWith("."))
                        return file.name
                            .toLowerCase()
                            .endsWith(pattern.toLowerCase());
                    return file.type === pattern;
                });
                if (!matches)
                    return `"${file.name}" is not an accepted file type`;
            }
        }

        for (const rule of props.rules || []) {
            const result = rule(list);
            if (typeof result === "string") return result;
        }

        return "";
    }

    function handleChange(selected) {
        const message = validate(selected);
        if (message) {
            showError(message);
            emit("error", message);
            return;
        }

        const prevFiles = files.value;
        const nextList = Array.isArray(selected)
            ? selected
            : selected
              ? [selected]
              : [];
        prevFiles.forEach((f) => {
            if (!nextList.includes(f)) revokePreview(f);
        });

        internalValue.value = selected;
        emit("update:modelValue", selected);
    }

    function syncFromModelValue(val) {
        internalValue.value = resolveModelValue(val);
    }

    onBeforeUnmount(() => {
        files.value.forEach((f) => revokePreview(f));
    });

    return {
        internalValue,
        computedAccept,
        files,
        getPreviewUrl,
        revokePreview,
        validate,
        handleChange,
        syncFromModelValue,
    };
}
