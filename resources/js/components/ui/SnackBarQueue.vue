<script setup>
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";

const { messages } = useSnackBarQueue();
</script>

<template>
    <v-snackbar-queue v-model="messages" :location="$vuetify.display.mdAndUp ? 'top end' : 'bottom center'"
        :collapsed="$vuetify.display.smAndDown" timeout="5000" transition="bouncy-slide-auto" total-visible="5"
        rounded="lg" closable>
        <template v-slot:text="{ item }">
            <div class="d-flex align-center">
                <v-icon v-if="item.icon" :icon="item.icon" :color="item.iconColor" class="me-2"></v-icon>
                <span>{{ item.text }}</span>
            </div>
        </template>
        <template v-slot:actions="{ props }">
            <v-btn icon="$close" size="small" variant="text" v-bind="props"></v-btn>
        </template>
    </v-snackbar-queue>
</template>