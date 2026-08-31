<template>
    <v-container max-width="1000">
        <h1 class="text-headline-small text-headline-md-medium mt-0">{{ route.meta.title }}</h1>

        <v-sheet class="mt-8" elevation="0">
            <div class="tabs-fade-wrapper">
                <v-tabs v-model="tab" density="comfortable" :grow="$vuetify.display.smAndDown" color="primary"
                    slider-transition="shift">
                    <v-tab variant="plain" :value="1" class="rounded-t-md" :ripple="false">Home</v-tab>
                    <v-tab variant="plain" :value="2" class="rounded-t-md" :ripple="false">About</v-tab>
                    <v-tab variant="plain" :value="3" class="rounded-t-md" :ripple="false">Projects</v-tab>
                    <v-tab variant="plain" :value="4" class="rounded-t-md" :ripple="false">Contact</v-tab>
                </v-tabs>
            </div>

            <v-divider />

            <v-tabs-window v-model="tab" crossfade>
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
import { useRoute } from "vue-router";
import HomeContent from "./HomeContent";
import AboutContent from "./AboutContent";
import ProjectsContent from "./ProjectsContent";
import ContactContent from "./ContactContent";
const tab = ref(1);
const route = useRoute();
</script>

<style lang="css" scoped>
.v-tab--selected {
    opacity: 1;
}
</style>