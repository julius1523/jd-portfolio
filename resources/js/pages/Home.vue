<template>
    <section id="home" ref="homeSection">
        <v-container>
            <v-row align="center" class="mt-0 mt-md-15">
                <v-col class="order-1 order-md-0" cols="12" md="7">
                    <div class="d-flex flex-column ga-2 ga-md-4">
                        <div
                            class="text-headline-medium text-lg-headline-large font-weight-semibold text-center text-md-left reveal-item">
                            {{ data.heading }}
                        </div>
                        <div
                            class="text-headline-large text-lg-display-medium font-weight-bold text-primary text-center text-md-left reveal-item">
                            <span ref="el" />
                        </div>
                        <div
                            class="text-medium-emphasis text-center text-md-left w-100 w-sm-75 w-md-100 mx-auto whitespace-pre-line reveal-item">
                            {{ data.description }}
                        </div>
                        <div
                            class="mt-5 d-flex flex-row flex-wrap ga-3 ga-sm-2 justify-center justify-md-start reveal-item">
                            <v-btn color="primary" width="160" size="large" rounded="pill" variant="flat"
                                append-icon="i-mdi-chevron-right" :href="data.primaryBtnLink">
                                {{ data.primaryBtnText }}
                            </v-btn>
                            <v-btn color="primary" width="160" size="large" rounded="pill" variant="outlined"
                                :href="data.secondaryBtnFile?.url" :download="data.secondaryBtnFile?.orig_name">
                                {{ data.secondaryBtnText }}
                            </v-btn>
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" md="5">
                    <v-img :src="data.profileImage?.url" rounded="circle" contain position="center 120%"
                        class="clamped-img clamped-img--square clamped-img [--img-min-w:230px] [--img-max-w:420px] bg-[linear-gradient(to_top,rgb(var(--v-theme-primary))_30%,rgb(var(--v-theme-primary))_0%,#fff_115%)] reveal-item"
                        :class="[$vuetify.display.mdAndUp ? 'ml-auto' : 'mx-auto']" alt="Profile Character Image" />
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script setup>
import { ref, nextTick, watch } from "vue";
import { useTyped } from "@/composables/useTyped";
import { storeToRefs } from "pinia";
import { useHomeStore } from "@/stores/resources";
import { useScrollReveal } from "@/composables/useScrollReveal";

const homeStore = useHomeStore();
const { data, loaded } = storeToRefs(homeStore);
const subheading = ref([]);
const { el } = useTyped(subheading, {
    typeSpeed: 80,
    backSpeed: 40,
    backDelay: 1500,
    loop: true,
});
const homeSection = ref(null);
const homeReveal = useScrollReveal(homeSection, { selector: ".reveal-item", stagger: 0.15, y: 40 });

watch(
    loaded,
    async (isLoaded) => {
        if (!isLoaded) return;
        subheading.value = data.value.subheading ?? [];
        await nextTick();
        homeReveal.refresh();
    },
    { immediate: true }
);
</script>