<script setup>
import { onMounted } from "vue";
import { useAlert } from "@/composables/useAlert";

const { alert, clear } = useAlert();

onMounted(() => {
    clear();
});
</script>

<template>
    <v-scale-transition mode="out-in">
        <v-alert v-if="alert" :type="alert.type" :icon="alert.icon" :color="alert.color" variant="tonal"
            density="compact" rounded="lg" :class="`border border-opacity-100 border-${alert.color} my-4`"
            @click:close="clear">
            {{ alert.text }}
        </v-alert>
    </v-scale-transition>
</template>