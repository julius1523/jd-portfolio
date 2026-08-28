<script setup>
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
const { messages } = useSnackBarQueue();
</script>

<template>
    <v-snackbar-queue v-model="messages" color="surface" :location="$vuetify.display.mdAndUp ? 'top end' : 'top center'"
        :collapsed="$vuetify.display.smAndDown" timeout="5000" transition="bouncy-slide-auto" total-visible="5"
        rounded="lg" closable class="border">
        <template v-slot:text="{ item }">
            <div class="d-flex align-center">
                <v-icon v-if="item.icon" :icon="item.icon" :color="item.iconColor" class="me-2"></v-icon>
                <span>{{ item.text }}</span>
            </div>
        </template>
        <template v-slot:actions="{ props }">
            <v-icon-btn icon="$close" size="small" variant="text" v-bind="props" />
        </template>
    </v-snackbar-queue>
</template>

<style lang="scss">
@use "../../../css/styles/transitions";
</style>