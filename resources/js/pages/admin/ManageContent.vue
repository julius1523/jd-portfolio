<template>
    <v-container max-width="1000">
        <h1 class="text-headline-small text-headline-md-medium mt-0">{{ route.meta.title }}</h1>

        <v-sheet class="mt-8" elevation="0">
            <v-divider />

            <div class="tabs-fade-wrapper">
                <v-tabs v-model="tab" density="compact" hide-slider :grow="$vuetify.display.smAndDown" color="primary"
                    slider-transition="grow" class="my-2">
                    <v-tab variant="flat" :value="1" class="me-2 rounded-lg">Home</v-tab>
                    <v-tab variant="flat" :value="2" class="me-2 rounded-lg">About</v-tab>
                    <v-tab variant="flat" :value="3" class="me-2 rounded-lg">Projects</v-tab>
                    <v-tab variant="flat" :value="4" class="me-2 rounded-lg">Contact</v-tab>
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
.tabs-fade-wrapper {
    position: relative;
    overflow: hidden;
}

.tabs-fade-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 40px;
    pointer-events: none;
    background: linear-gradient(to right, transparent, rgb(var(--v-theme-surface)) 90%);
}
</style>