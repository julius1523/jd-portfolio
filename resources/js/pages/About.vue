<template>
    <section id="about" ref="aboutSection">
        <v-card flat tile>
            <v-container class="my-5 my-md-13">
                <v-row class="my-0 my-md-8">
                    <v-col cols="12" md="7">
                        <div class="d-flex flex-column ga-3 ga-md-5">
                            <div class="text-uppercase text-title-large font-weight-semibold text-primary reveal-item">
                                About
                            </div>
                            <div class="text-headline-small reveal-item">
                                {{ data.heading }}
                            </div>
                            <div class="text-medium-emphasis whitespace-pre-line reveal-item">
                                {{ data.description }}
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="5" class="d-flex">
                        <v-img :src="data.profileImage?.url" :position="$vuetify.display.mdAndUp ? 'right' : 'center'"
                            aspect-ratio="1" alt="About Character Image"
                            class="clamped-img [--img-min-h:180px] [--img-max-h:285px] fade-bottom reveal-item" />
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </section>
    <section id="skills" ref="skillsSection">
        <v-card flat tile color="surface-light">
            <v-container class="d-flex flex-column align-center ga-5 my-5 my-md-13">
                <div class="reveal-item">
                    <div class="text-center text-headline-small text-md-headline-medium font-weight-medium">
                        Skills
                    </div>
                </div>
                <div class="d-flex flex-column ga-6 ga-md-9 reveal-item">
                    <div v-for="item in data.skills" :key="item.category" class="d-flex flex-column ga-2">
                        <div class="text-title-large">
                            <v-icon icon="i-ri-stack-line" color="primary" class="mr-2" />
                            {{ item.category }}
                        </div>
                        <div class="d-flex flex-row flex-wrap ga-2 ml-12">
                            <v-chip v-for="skill in item.skill" :key="skill" variant="flat"
                                class="rounded-[10px] transition-colors duration-200 hover:bg-primary">
                                {{ skill }}
                            </v-chip>
                        </div>
                    </div>
                </div>
            </v-container>
        </v-card>
    </section>
    <section id="random-facts" ref="factsSection">
        <v-card flat tile>
            <v-container class="d-flex flex-column align-center ga-5 my-5 my-md-13">
                <div class="reveal-item">
                    <div class="text-center text-headline-small text-md-headline-medium font-weight-medium">
                        Random Facts
                    </div>
                </div>
                <div class="d-flex flex-row flex-wrap ga-4 justify-center">
                    <div v-for="item in data.randomFacts" :key="item.text" class="reveal-item">
                        <v-card color="surface-light" flat height="175" width="175" rounded="xl"
                            class="pa-3 d-flex flex-column align-center">
                            <div class="h-50 w-100 d-flex align-center justify-center">
                                <v-icon color="primary" size="50">
                                    <span v-html="item.iconSvg" class="inline-flex items-center" />
                                </v-icon>
                            </div>
                            <div class="flex-grow-1 w-100 d-flex align-center justify-center">
                                <div class="text-center text-balance line-clamp-3">
                                    {{ item.randomFact }}
                                </div>
                            </div>
                        </v-card>
                    </div>
                </div>
            </v-container>
        </v-card>
    </section>
    <section id="design-system" ref="designSection">
        <v-card flat tile color="surface-light">
            <v-container class="d-flex flex-column align-center ga-5 my-5 my-md-13">
                <div class="reveal-item">
                    <div class="text-center text-headline-small text-md-headline-medium font-weight-medium">
                        {{ data.others?.title }}
                    </div>
                </div>
                <v-row>
                    <v-col cols="12" class="text-center whitespace-pre-line reveal-item">
                        {{ data.others?.description }}
                    </v-col>
                    <v-col cols="12">
                        <v-img :src="data.others?.image?.url" alt="Design System Image" aspect-ratio="1.6"
                            class="clamped-img [--img-max-h:500px] [--img-max-w:800px] mx-auto reveal-item" />
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </section>
</template>

<script setup>
import { ref, nextTick, watch } from "vue";
import { storeToRefs } from "pinia";
import { useAboutStore } from "@/stores/resources";
import { useScrollReveal } from "@/composables/useScrollReveal";
const aboutStore = useAboutStore();
const { data: data, loaded } = storeToRefs(aboutStore);
const aboutSection = ref(null);
const skillsSection = ref(null);
const factsSection = ref(null);
const designSection = ref(null);
const aboutReveal = useScrollReveal(aboutSection, { selector: ".reveal-item", stagger: 0.15, y: 40 });
const skillsReveal = useScrollReveal(skillsSection, { selector: ".reveal-item", stagger: 0.15, y: 40 });
const factsReveal = useScrollReveal(factsSection, { selector: ".reveal-item", stagger: 0.15, y: 40 });
const designReveal = useScrollReveal(designSection, { selector: ".reveal-item", stagger: 0.15, y: 40 });
watch(
    loaded,
    async (isLoaded) => {
        if (!isLoaded) return;
        await nextTick();
        aboutReveal.refresh();
        skillsReveal.refresh();
        factsReveal.refresh();
        designReveal.refresh();
    },
    { immediate: true }
);
</script>