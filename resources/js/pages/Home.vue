<template>
    <LazyLoad>
        <section id="home">
            <v-container>
                <v-row align="center" class="mt-0 mt-md-15">
                    <v-col order="1" order-md="0" cols="12" md="7">
                        <div class="d-flex flex-column ga-2 ga-md-4">
                            <div
                                class="text-headline-medium text-lg-headline-large font-weight-semibold text-center text-md-start animate">
                                Hello, I'm
                                <span class="text-no-wrap">Julius</span>
                            </div>
                            <div
                                class="text-display-small text-lg-display-medium font-weight-bold text-primary text-center text-md-start animate">
                                <span ref="el">
                                </span>
                            </div>
                            <div
                                class="text-medium-emphasis text-center text-md-start w-100 w-sm-75 w-md-100 mx-auto animate">
                                I’m passionate about designing intuitive and visually appealing web
                                applications—with a sharp eye for small details that enhance user experience.
                            </div>
                            <div class="mt-5 d-flex flex-row flex-wrap ga-2 justify-center justify-md-start">
                                <v-btn color="primary" width="160" size="large" rounded="pill" variant="flat"
                                    text="Hire Me" append-icon="mdi-chevron-right" class="animate"></v-btn>
                                <v-btn color="primary" width="160" size="large" rounded="pill" variant="outlined"
                                    text="Download CV" class="animate"></v-btn>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="5">
                        <div class="animate">
                            <v-img :style="gradientStyle" src="/images/profile-1.png" rounded="circle" contain
                                class="responsive-img square" position="center 120%"
                                :class="[$vuetify.display.mdAndUp ? 'ml-auto' : 'mx-auto']" alt="Profile Picture" />
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>
    </LazyLoad>
</template>

<script setup>
import { ref, computed } from "vue";
import { useTyped } from "@/composables/useTyped";
const { el } = useTyped({
    strings: [
        "VueJS Developer",
        "Frontend Developer",
        "UI/UX Enthusiast",
    ],
    typeSpeed: 80,
    backSpeed: 40,
    backDelay: 1500,
    loop: true,
});
const gradientStyle = computed(() => ({
    background: `
        linear-gradient(
            to top,
            rgb(var(--v-theme-primary)) 30%,
            rgb(var(--v-theme-primary)) 0%,
            #ffffff 115%
        )
    `
}));
</script>