<script setup>
import { ref, computed } from "vue";

const model = defineModel({
    type: String,
    default: '',
});
const props = defineProps({
    label: {
        type: String,
        default: 'Password',
    },
    placeholder: {
        type: String,
        default: '',
    },
    errorMessages: {
        type: [String, Array],
        default: () => [],
    },
});
const show = ref(false);
const usePlaceholder = computed(() => !!props.placeholder);
const resolvedLabel = computed(() => (usePlaceholder.value ? undefined : props.label));
const resolvedPlaceholder = computed(() => (usePlaceholder.value ? props.placeholder : undefined));
</script>

<template>
    <v-text-field v-model="model" :type="show ? 'text' : 'password'" :label="resolvedLabel"
        :placeholder="resolvedPlaceholder" :error-messages="errorMessages"
        :append-inner-icon="show ? 'i-ri-eye-off-line opacity-50' : 'i-ri-eye-line opacity-50'"
        @click:append-inner="show = !show" color="primary" rounded="lg" autocomplete="off" />
</template>