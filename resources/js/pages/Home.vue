<template>
    <section id="home" ref="homeSection">
        <v-container>
            <v-row align="center" class="mt-0 mt-md-15">
                <v-col class="order-1 order-md-0" cols="12" md="7">
                    <div class="d-flex flex-column ga-2 ga-md-4">
                        <div
                            class="text-headline-medium text-lg-headline-large font-weight-semibold text-center text-md-left reveal-item">
                            Hello, I'm
                            <span class="text-no-wrap">Julius</span>
                        </div>
                        <div
                            class="text-display-small text-lg-display-medium font-weight-bold text-primary text-center text-md-left reveal-item">
                            <span ref="el">
                            </span>
                        </div>
                        <div
                            class="text-medium-emphasis text-center text-md-left w-100 w-sm-75 w-md-100 mx-auto reveal-item">
                            I'm passionate about designing intuitive and visually appealing web
                            applications—with a sharp eye for small details that enhance user experience.
                        </div>
                        <div
                            class="mt-5 d-flex flex-row flex-wrap ga-3 ga-sm-2 justify-center justify-md-start reveal-item">
                            <v-btn color="primary" width="160" size="large" rounded="pill" variant="flat" text="Hire Me"
                                append-icon="i-mdi-chevron-right"></v-btn>
                            <v-btn color="primary" width="160" size="large" rounded="pill" variant="outlined"
                                text="Download CV"></v-btn>
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" md="5">
                    <v-img :style="{
                        ...gradientStyle,
                        '--img-min-w': '230px',
                        '--img-max-w': '420px'
                    }" src="/images/profile-1.png" rounded="circle" contain position="center 120%"
                        class="clamped-img clamped-img--square reveal-item"
                        :class="[$vuetify.display.mdAndUp ? 'ml-auto' : 'mx-auto']" alt="Profile Character Image" />
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script setup>
import { ref, computed } from "vue";
import { useTyped } from "@/composables/useTyped";
import { useScrollReveal } from '@/composables/useScrollReveal';
const homeSection = ref(null);
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
useScrollReveal(homeSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
</script>