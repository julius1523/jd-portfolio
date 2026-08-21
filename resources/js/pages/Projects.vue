<template>
    <section id="projects" ref="projectsSection">
        <v-card flat tile>
            <v-container class="my-5 my-md-13">
                <v-row class="my-0 my-md-8">
                    <v-col cols="12" md="7">
                        <div class="d-flex flex-column ga-3 ga-md-5">
                            <div class="text-uppercase text-title-large font-weight-semibold text-primary reveal-item">
                                Projects
                            </div>
                            <div class="text-headline-small reveal-item">
                                Please see the projects I have worked on below.
                            </div>
                            <div class="text-medium-emphasis reveal-item">
                                This collection highlights the systems I've developed and maintained for my
                                city's Local Government Unit. Alongside each project, you'll find user manuals
                                of how the systems work and an audio video presentation.
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="5">
                        <v-img src="/images/projects.png" :position="$vuetify.display.mdAndUp ? 'right' : 'center'"
                            alt="Projects Character Image" class="clamped-img fade-bottom reveal-item" :style="{
                                '--img-min-h': '180px',
                                '--img-max-h': '285px',
                            }" />
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </section>

    <section id="projects-body" ref="projectsBodySection">
        <v-card flat tile color="surface-light">
            <v-container class="my-5 my-md-13 px-7">
                <div class="d-flex flex-column ga-2">
                    <div class="reveal-item">
                        <div class="text-headline-small text-md-headline-medium font-weight-medium">
                            Software Development
                        </div>
                    </div>

                    <SnapCarousel :items="software">
                        <template #default="{ item, index }">
                            <v-card width="330" height="440" rounded="xl" flat
                                class="border d-flex flex-column scale-down-content" @click="openProject(item.url)">

                                <div class="h-50">
                                    <v-img :src="item.preview" width="fit-content" position="top"
                                        :alt="`Software Development Preview ${index}`" aspect-ratio="16/9"
                                        class="border-b" />
                                </div>

                                <div class="pa-5 d-flex flex-column flex-grow-1">
                                    <div class="text-title two-line">
                                        {{ item.title }}
                                    </div>

                                    <div class="text-label-large text-medium-emphasis mt-2 three-line">
                                        {{ item.description }}
                                    </div>

                                    <div class="d-flex flex-row ga-1 mt-auto">
                                        <v-chip v-for="material in item.materials.slice(0, 3)" :key="material"
                                            color="primary" size="small" variant="tonal">
                                            {{ material }}
                                        </v-chip>
                                        <v-chip v-if="item.materials.length > 3" size="small" variant="text"
                                            class="text-medium-emphasis">
                                            +{{ item.materials.length - 3 }} more
                                        </v-chip>
                                    </div>
                                </div>
                            </v-card>
                        </template>
                    </SnapCarousel>
                </div>

                <div class="d-flex flex-column ga-2">
                    <div class="reveal-item">
                        <div class="text-headline-small text-md-headline-medium font-weight-medium mt-10 mt-md-15">
                            Technical Documentation
                        </div>
                    </div>

                    <SnapCarousel :items="documentation">
                        <template #default="{ item, index }">
                            <v-card width="330" height="440" rounded="xl" flat
                                class="border d-flex flex-column scale-down-content" @click="openProject(item.url)">

                                <div class="h-50">
                                    <v-img :src="item.preview" width="fit-content" position="top"
                                        :alt="`Technical Documentation Preview ${index}`" aspect-ratio="16/9"
                                        class="border-b" />
                                </div>

                                <div class="pa-5 d-flex flex-column flex-grow-1">
                                    <div class="text-title two-line">
                                        {{ item.title }}
                                    </div>

                                    <div class="text-label-large text-medium-emphasis mt-2 three-line">
                                        {{ item.description }}
                                    </div>

                                    <div class="d-flex flex-row ga-1 mt-auto">
                                        <v-chip v-for="material in item.materials.slice(0, 3)" :key="material"
                                            color="primary" size="small" variant="tonal">
                                            {{ material }}
                                        </v-chip>
                                        <v-chip v-if="item.materials.length > 3" size="small" variant="text"
                                            class="text-medium-emphasis">
                                            +{{ item.materials.length - 3 }} more
                                        </v-chip>
                                    </div>
                                </div>
                            </v-card>
                        </template>
                    </SnapCarousel>
                </div>

                <div class="d-flex flex-column ga-2">
                    <div class="reveal-item">
                        <div class="text-headline-small text-md-headline-medium font-weight-medium mt-10 mt-md-15">
                            Presentations/Multimedia
                        </div>
                    </div>
                    <SnapCarousel :items="media">
                        <template #default="{ item, index }">
                            <v-card width="330" height="440" rounded="xl" flat
                                class="border d-flex flex-column scale-down-content" @click="openProject(item.url)">

                                <div class="h-50">
                                    <v-img :src="item.preview" width="fit-content" position="top"
                                        :alt="`Presentations/Multimedia Preview ${index}`" aspect-ratio="16/9"
                                        class="border-b" />
                                </div>

                                <div class="pa-5 d-flex flex-column flex-grow-1">
                                    <div class="text-title two-line">
                                        {{ item.title }}
                                    </div>

                                    <div class="text-label-large text-medium-emphasis mt-2 three-line">
                                        {{ item.description }}
                                    </div>

                                    <div class="d-flex flex-row ga-1 mt-auto">
                                        <v-chip v-for="material in item.materials.slice(0, 3)" :key="material"
                                            color="primary" size="small" variant="tonal">
                                            {{ material }}
                                        </v-chip>
                                        <v-chip v-if="item.materials.length > 3" size="small" variant="text"
                                            class="text-medium-emphasis">
                                            +{{ item.materials.length - 3 }} more
                                        </v-chip>
                                    </div>
                                </div>
                            </v-card>
                        </template>
                    </SnapCarousel>
                </div>
            </v-container>
        </v-card>
    </section>
</template>

<script setup>
import { ref } from "vue";
import SnapCarousel from "@/components/ui/SnapCarousel";
import { useScrollReveal } from "@/composables/useScrollReveal";
const projectsSection = ref(null);
const projectsBodySection = ref(null);
const software = ref([
    {
        title: "RPTOPS",
        description: "Web-based system for requesting statement of accounts and processing payments.",
        preview: "/images/rptops.png",
        url: "https://rptops.egovcarmona.ph",
        materials: ['Laravel', 'VueJS', 'Vuetify', 'MySQL'],
        type: "software"
    },
    {
        title: "Feedback Mechanism",
        description: "Web-based system for submitting, tracking, and resolving citizen concerns and feedback.",
        preview: "/images/feedback.png",
        url: "https://feedback.egovcarmona.ph",
        materials: ['Laravel', 'VueJS', 'Vuetify', 'MySQL'],
        type: "software"
    },
    {
        title: "Citizen's Charter",
        description: "Web-based system that aid the users to easily view the services of each department.",
        preview: "/images/charter.png",
        url: "https://citizencharter.egovcarmona.ph",
        materials: ['Laravel', 'VueJS', 'Vuetify', 'MySQL'],
        type: "software"
    },
]);
const documentation = ref([
    {
        title: "RPT Online User's Manual",
        description: "Guide for end users covering requesting, checking, and paying statement of account.",
        preview: "/images/rptops_user_manual.png",
        url: "/files/RPT Online User Manual.pdf",
        materials: ['Photoshop'],
        type: "documentation"
    },
    {
        title: "Feedback Mechanism User's Manual",
        description: "Guide for end users covering requesting, checking, and paying statement of account.",
        preview: "/images/feedback_user_manual.png",
        url: "/files/Feedback Mechanism User Manual.pdf",
        materials: ['Photoshop'],
        type: "documentation"
    },
    {
        title: "Citizen's Charter User's Manual",
        description: "Guide for administrator users covering managing the system, and guide for end users on how to use the system.",
        preview: "/images/charter_user_manual.png",
        url: "/files/Citizen Charter User Manual.pdf",
        materials: ['Photoshop'],
        type: "documentation"
    },
    {
        title: "EBOSS Tri-Fold",
        description: "Printable tri-fold for Electronic Business One-Stop Shop (EBOSS) containing the requirements and steps for new businesses and renewal.",
        preview: "/images/eboss_tri_fold.png",
        url: "/files/EBOSS Tri-Fold.pdf",
        materials: ['Photoshop'],
        type: "documentation"
    },
    {
        title: "EBOSS Online User Manual",
        description: "Guide for clients covering how to use the online Electronic Business One-Stop Shop (EBOSS) system.",
        preview: "/images/eboss_online_user_manual.png",
        url: "/files/EBOSS User Manual.pdf",
        materials: ['Photoshop'],
        type: "documentation"
    },
    {
        title: "RPT Collection User Manual",
        description: "Guide for administrator covering how to use the system for managing the collection of real property tax payments.",
        preview: "/images/rptcol_user_manual.png",
        url: "/files/RPT Collection User Manual.pdf",
        materials: ['Photoshop', 'Canva'],
        type: "documentation"
    },
]);
const media = ref([
    {
        title: "Emergency Response Audio Visual Presentation",
        description: "AVP for the emergency response system showcasing its features and capabilities for the City of Carmona.",
        preview: "/images/avp_preview.png",
        url: "https://drive.google.com/file/d/14Fxqkbm4wNeh94Ni4ht0SKaF0VehTdsi/view?usp=drive_link",
        materials: ['CapCut', 'Photoshop'],
        type: "video"
    },
]);
const openProject = (url) => {
    window.open(
        url,
        '_blank',
        'noopener,noreferrer'
    )
};
useScrollReveal(projectsSection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
useScrollReveal(projectsBodySection, { selector: '.reveal-item', stagger: 0.15, y: 40 });
</script>