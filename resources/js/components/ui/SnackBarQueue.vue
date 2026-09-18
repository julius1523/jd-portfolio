<script setup>
import { useSnackbarQueue } from "@/composables/useSnackbarQueue";
const { messages } = useSnackbarQueue();
</script>

<template>
    <v-snackbar-queue v-model="messages" variant="flat" color="surface"
        :location="$vuetify.display.mdAndUp ? 'bottom end' : 'top center'" :collapsed="$vuetify.display.smAndDown"
        timeout="5000" transition="bouncy-slide-auto" total-visible="5" closable :content-class="[
            'rounded-2xl border shadow-lg',
            { 'w-100': $vuetify.display.smAndDown }
        ]">
        <template v-slot:text="{ item }">
            <div class="d-flex align-center">
                <v-icon v-if="item.icon" :icon="item.icon" :color="item.iconColor" class="me-2"></v-icon>
                <span>{{ item.text }}</span>
            </div>
        </template>
        <template v-slot:actions="{ props }">
            <v-icon icon="$close" size="x-small" v-bind="props" class="me-2" />
        </template>
    </v-snackbar-queue>
</template>

<style lang="scss">
@use "../../../css/styles/transitions";
</style>