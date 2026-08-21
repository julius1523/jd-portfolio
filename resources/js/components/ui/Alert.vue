<script setup>
import { onUnmounted } from "vue";
import { useAlert } from "@/composables/useAlert";

const { alert, clear } = useAlert();

onUnmounted(() => {
    clear();
});
</script>

<template>
    <v-fade-transition hide-on-leave>
        <v-alert v-if="alert" :type="alert.type" :color="alert.color" variant="tonal" density="compact" rounded="lg"
            :class="`border border-opacity-100 border-${alert.color} my-4`" @click:close="clear">
            <template #prepend>
                <v-icon :icon="alert.icon" size="20" />
            </template>
            <template #text>
                {{ alert.text }}
            </template>
        </v-alert>
    </v-fade-transition>
</template>