<script setup>
defineProps({
    dirty: { type: Boolean, default: false },
    ready: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    cancelText: { type: String, default: "Cancel edit" },
    saveText: { type: String, default: "Save changes" },
});
defineEmits(["cancel"]);
</script>

<template>
    <div class="d-flex flex-column flex-md-row ga-3 justify-end mt-8">
        <v-btn variant="plain" :text="cancelText" rounded="pill" height="50" :disabled="!ready || !dirty || loading"
            @click="$emit('cancel')" />
        <v-btn type="submit" :text="saveText" variant="flat" rounded="pill" color="primary" height="50"
            class="order-first order-md-last" :disabled="!ready || !dirty || loading" :loading="loading" />
    </div>
</template>