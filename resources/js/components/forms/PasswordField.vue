<script setup>
import { ref } from 'vue';

const model = defineModel({
    type: String,
    default: '',
});

const props = defineProps({
    label: {
        type: String,
        default: 'Password',
    },
    errorMessages: {
        type: [String, Array],
        default: () => [],
    },
});

const show = ref(false);
</script>

<template>
    <v-text-field v-model="model" :type="show ? 'text' : 'password'" :label="label" :error-messages="errorMessages"
        :append-inner-icon="show ? 'mdi-eye-off' : 'mdi-eye'" @click:append-inner="show = !show" color="primary"
        variant="solo" flat rounded="lg" density="comfortable" autocomplete="off" />
</template>