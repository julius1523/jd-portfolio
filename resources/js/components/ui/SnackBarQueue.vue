<script setup>
import { useSnackBarQueue } from "@/composables/useSnackBarQueue";
const { messages } = useSnackBarQueue();
</script>

<template>
    <v-snackbar-queue v-model="messages" variant="flat" color="surface"
        :location="$vuetify.display.mdAndUp ? 'top end' : 'top center'" :collapsed="$vuetify.display.smAndDown"
        timeout="5000" transition="bouncy-slide-auto" total-visible="5" :content-class="[
            'rounded-[15px] border shadow-sm',
            { 'w-100': $vuetify.display.smAndDown }
        ]">
        <template v-slot:text="{ item }">
            <div class="d-flex align-start">
                <v-icon v-if="item.icon" :icon="item.icon" :color="item.iconColor" class="me-2 flex-shrink-0" />
                <span class="min-w-0 [overflow-wrap:anywhere]">{{ item.text }}</span>
            </div>
        </template>
    </v-snackbar-queue>
</template>

<style lang="scss">
@use "../../../css/styles/transitions";
</style>