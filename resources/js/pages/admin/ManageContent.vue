<template>
    <v-container max-width="1000">
        <h1 class="text-headline-medium mt-0">{{ route.meta.title }}</h1>

        <v-sheet class="mt-8" elevation="0">
            <v-tabs v-model="tab" density="compact" :grow="$vuetify.display.smAndDown" color="primary"
                slider-transition="grow">
                <v-tab :value="1" class="rounded-t-lg">Home</v-tab>
                <v-tab :value="2" class="rounded-t-lg">About</v-tab>
                <v-tab :value="3" class="rounded-t-lg">Projects</v-tab>
                <v-tab :value="4" class="rounded-t-lg">Contact</v-tab>
            </v-tabs>

            <v-divider />

            <v-tabs-window v-model="tab" crossfade :transition-duration="500">
                <v-tabs-window-item :value="1">
                    <HomeContent />
                </v-tabs-window-item>
                <v-tabs-window-item :value="2">
                    <AboutContent />
                </v-tabs-window-item>
                <v-tabs-window-item :value="3">
                    <ProjectsContent />
                </v-tabs-window-item>
                <v-tabs-window-item :value="4">
                    <ContactContent />
                </v-tabs-window-item>
            </v-tabs-window>
        </v-sheet>
    </v-container>
</template>

<script setup>
import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import HomeContent from "./HomeContent";
import AboutContent from "./AboutContent";
import ProjectsContent from "./ProjectsContent";
import ContactContent from "./ContactContent";
const tab = ref(1);
const route = useRoute();
</script>