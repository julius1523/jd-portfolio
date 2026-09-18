<template>
    <v-container max-width="1050">
        <v-sheet elevation="0">
            <v-tabs v-model="tab" :grow="$vuetify.display.smAndDown" color="primary" selected-class="opacity-100">
                <v-tab variant="plain" :value="1" :ripple="false">Home</v-tab>
                <v-tab variant="plain" :value="2" :ripple="false">About</v-tab>
                <v-tab variant="plain" :value="3" :ripple="false">Projects</v-tab>
                <v-tab variant="plain" :value="4" :ripple="false">Contact</v-tab>
            </v-tabs>

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
import { ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import HomeContent from "./HomeContent";
import AboutContent from "./AboutContent";
import ProjectsContent from "./ProjectsContent";
import ContactContent from "./ContactContent";

const route = useRoute();
const router = useRouter();
const tabNames = { 1: "home", 2: "about", 3: "projects", 4: "contact" };
const nameToTab = { home: 1, about: 2, projects: 3, contact: 4 };
const tab = ref(nameToTab[route.params.tab] ?? 1);

watch(
    tab,
    (newTab) => {
        const name = tabNames[newTab];
        if (route.params.tab !== name) {
            router.replace({ name: "manage-content", params: { tab: name } });
        }
    },
    { immediate: true }
);

watch(
    () => route.params.tab,
    (newParam) => {
        const mapped = nameToTab[newParam] ?? 1;
        if (tab.value !== mapped) tab.value = mapped;
    }
);
</script>