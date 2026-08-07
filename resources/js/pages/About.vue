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
                                I'm a web developer based in the Philippines.
                            </div>
                            <div class="text-medium-emphasis reveal-item">
                                I like building apps that don't just work—but actually look and feel good to
                                use, using Vue.js, particularly Vuetify.
                            </div>
                            <div class="text-medium-emphasis reveal-item">
                                I pay a lot of attention to the small details most people overlook. Clean, simple,
                                and consistent UI, smooth interactions, and overall user experience matter to me.
                            </div>
                            <div class="text-medium-emphasis reveal-item">
                                In addition to development, I design clear and visually engaging user manuals
                                using tools like Photoshop and Canva to help users better understand and
                                navigate systems.
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="5">
                        <div class="reveal-item">
                            <v-img src="/images/about.png" cover height="300" width="300" class="fade-bottom"
                                :class="$vuetify.display.mdAndUp ? 'ml-auto' : 'mx-auto'" alt="About Person" />
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </section>

    <section id="skills" ref="skillsSection">
        <v-card flat tile color="surface-light">
            <v-container class="d-flex flex-column align-center ga-5 my-5 my-md-13">
                <div class="reveal-item">
                    <div class="text-center text-headline-small font-weight-medium">
                        Skills
                    </div>
                    <v-divider :thickness="4" color="primary" class="border-opacity-75 mx-auto my-3" length="50" />
                </div>
                <div class="d-flex flex-column ga-10 reveal-item">
                    <div v-for="item in skills" :key="item.title" class="d-flex flex-column ga-3">
                        <div class="text-subtitle font-weight-bold">
                            <v-icon icon="mdi-layers-outline" color="primary" class="mr-2" />
                            {{ item.title }}
                        </div>
                        <div class="d-flex flex-row flex-wrap ga-2 ml-11">
                            <v-chip v-for="skill in item.skills" :key="skill" rounded="lg" variant="flat"
                                class="skills">
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
                    <div class="text-center text-headline-small font-weight-medium">
                        Random Facts
                    </div>
                    <v-divider :thickness="4" color="primary" class="border-opacity-75 mx-auto my-3" length="50" />
                </div>
                <div class="d-flex flex-row flex-wrap ga-4 justify-center">
                    <div v-for="item in facts" :key="item.text" class="reveal-item">
                        <v-card color="surface-light" flat height="180" width="180" rounded="xl"
                            class="pa-3 d-flex flex-column align-center">
                            <div class="h-50 w-100 d-flex align-center justify-center">
                                <v-icon :icon="item.icon" size="37" color="primary" class="opacity-80" />
                            </div>
                            <div class="flex-grow-1 w-100 d-flex justify-center text-center three-line">
                                {{ item.text }}
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
                    <div class="text-center text-headline-small font-weight-medium">
                        Design System
                    </div>
                    <v-divider :thickness="4" color="primary" class="border-opacity-75 mx-auto my-3" length="50" />
                </div>
                <v-row>
                    <v-col cols="12" class="text-center reveal-item">
                        <div>I design websites using a practical, research-driven approach.</div>
                        <div>
                            I often reference well-established UI patterns and layouts to understand what works
                            best, then adapt and refine them.
                        </div>
                    </v-col>
                    <v-col cols="12">
                        <div class="reveal-item">
                            <v-img src="/images/design.png" max-width="800" min-height="200" max-height="450"
                                aspect-ratio="16/9" class="mx-auto scale-up-content" alt="Design System" />
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </section>
</template>

<script setup>
import { ref } from "vue";
import { useScrollReveal } from "@/composables/useScrollReveal";
const aboutSection = ref(null);
const skillsSection = ref(null);
const factsSection = ref(null);
const designSection = ref(null);
const skills = ref([
    {
        title: "Front-End Development",
        skills: ["HTML5", "CSS3", "Bootstrap", "VueJS", "Vuetify", "JavaScript", "Responsive Design"]
    },
    {
        title: "Back-End & Systems",
        skills: ["PHP", "Laravel", "MySQL", "GitHub Desktop"]
    },
    {
        title: "Design & Creative Tools",
        skills: ["Canva", "Photoshop", "Capcut", "Figma (Basic)"]
    },
    {
        title: "Soft Skills",
        skills: ["Communication", "Attention to Details", "Continuous Learning", "Collaboration", "Creativity", "Focus"]
    }
]);
const facts = ref([
    { icon: "mdi-controller", text: "My peak mmr in DotA 2 is 14k" },
    { icon: "mdi-hand-okay", text: "I am a perfectionist" },
    { icon: "mdi-paw", text: "I love cats and dogs" },
    { icon: "mdi-spotify", text: "I love listening to pop music" },
    { icon: "mdi-emoticon-excited", text: "I am an introvert and a picky eater" }
]);
useScrollReveal(aboutSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
useScrollReveal(skillsSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
useScrollReveal(factsSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
useScrollReveal(designSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
</script>